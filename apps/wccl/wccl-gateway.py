#!/bin/python
# -*- coding: utf-8 -*-

import corpus2
import wccl
import codecs
import os
import json
import sys
import traceback

_nkjp = {}
_nkjp["nmb"] = set(['sg','pl'])
_nkjp["cas"] = set(['nom', 'gen', 'dat', 'acc', 'inst', 'loc', 'voc'])
_nkjp["gnd"] = set(['m1','m2','m3','f','n'])
_nkjp["per"] = set(['pri','sec','ter'])
_nkjp["deg"] = set(['pos','com','sup'])
_nkjp["asp"] = set(['imperf','perf'])
_nkjp["ngt"] = set(['aff','neg'])
_nkjp["acm"] = set(['congr','rec'])
_nkjp["acn"] = set(['akc','nakc'])
_nkjp["ppr"] = set(['npraep','praep'])
_nkjp["agg"] = set(['agl','nagl'])
_nkjp["vcl"] = set(['nwok','wok'])
_nkjp["dot"] = set(['pun','npun'])

_nkjp_val_to_attr = {}
for attr in _nkjp.keys():
    for val in _nkjp[attr]:
        _nkjp_val_to_attr[val] = attr
        

def lexem_to_title(lexem, ctagset):
    ctag = ctagset.tag_to_string(lexem.tag()).split(":")
    title = "orth: %s" % lexem.lemma_utf8()
    title += "\nclass: %s" % ctag[0]
    for val in ctag[1:]:
        if val in _nkjp_val_to_attr:
            title += "\n%s: %s" % (_nkjp_val_to_attr[val], val)
        else:
            title += "\n?: %s" % (val)        
    return title


def process_files(start, offset, wccl_rules, corpus_path):
    tagset = "nkjp"
    ctagset = corpus2.get_named_tagset(tagset)
    root = os.path.dirname(corpus_path) + "/"
    list = corpus_path
    channels = ['t3_range', 't3_date', 't3_time', 't3_set', 't3_duration']

    p = wccl.Parser(ctagset)
    wc = p.parseWcclFile(wccl_rules)


    processed = 0
    docs = {}
    filenames = codecs.open(list, "r", "utf-8").readlines()
    docs_filenames = []
    for line in filenames[start:start+offset]:
        processed += 1
        line = line.strip()
        xml_file = (root + line).encode("utf-8")
        if os.path.isfile(xml_file):
            docs_filenames.append(line)
            docs[line] = corpus2.TokenReader.create_path_reader('ccl', corpus2.get_named_tagset(tagset), xml_file)
        else:
            #print "ERROR: File not found " + xml_file
            pass

    items = []
    for filename in docs_filenames:
        tok_reader = docs[filename]
        while True:
            sent = tok_reader.get_next_sentence()
            if sent:
                asent = corpus2.AnnotatedSentence.wrap_sentence(sent)
                match_rules = wc.get_match_rules_ptr()
                
                ans_ref = set()
                ans_cmp = set()

                for channel_name in channels: 
                    if asent.has_channel(channel_name):
                        for ann in asent.get_channel(channel_name).make_annotation_vector():
                            ans_ref.add("%s:%d:%d" % (channel_name, ann.indices[0], ann.indices[-1]) )                    
                        ch = asent.get_channel(channel_name)
                        for i in range(0, len(asent.tokens())):
                            ch.set_segment_at(i, 0)
                                    
                match_rules.apply_all(asent)

                for channel_name in channels: 
                    if asent.has_channel(channel_name):
                        for ann in asent.get_channel(channel_name).make_annotation_vector():
                            ans_cmp.add("%s:%d:%d" % (channel_name, ann.indices[0], ann.indices[-1]) )                    

                ends = set()
                tp = set()
                fp = set()
                fn = set()
                fn_ends = set()
                
                for an in ans_cmp:
                    (chan, start, end) = an.split(":")
                    ends.add(chan+":"+end)
                    if an in ans_ref:
                        tp.add(chan+":"+start)
                    else:
                        fp.add(chan+":"+start)
                        
                for an in ans_ref:
                    (chan, start, end) = an.split(":")
                    if an not in ans_cmp:
                        fn.add(chan+":"+start)
                        fn_ends.add(chan+":"+end)
                              
                item = ""
                
                #
                # Anotacje pomocnicze
                #
                aux_channels = []
                for channel in asent.all_channels():
                    if channel.startswith("aux_"):
                        aux_channels.append(channel)
                
                if len(aux_channels)>0:                    
                    aux_start = set()
                    aux_end = set()
    
                    for channel_name in aux_channels: 
                        for ann in asent.get_channel(channel_name).make_annotation_vector():
                            aux_start.add("%s:%d" % (channel_name, ann.indices[0]) )
                            aux_end.add("%s:%d" % (channel_name, ann.indices[-1]) )                    
                                        
                    item += "<div class='aux'><div>Auxiliary annotations:</div>"                    
                    for i in range(0, len(asent.tokens())):
                        lexem = asent.tokens()[i].get_preferred_lexeme(ctagset)
                        # tagi rozpoczynajace
                        for channel_name in aux_channels: 
                            key = "%s:%d" % (channel_name, i)
                            if key in aux_start:
                                item += "<span class='%s' title='%s'>" % (channel_name, channel_name)
                        # tekst
                        ctag = ctagset.tag_to_string(lexem.tag()).split(":")
                        item += "<tok title='%s'>" % (lexem_to_title(lexem, ctagset))
                        item += str(asent.tokens()[i].orth())                        
                        item += "</tok>"
                        # tagi zamykajace
                        for channel_name in aux_channels: 
                            if channel_name+":"+str(i) in aux_end:
                                item += "</span>"
                        # separator
                        item += " "
                    item += "</div>"                
                
                #
                # Rozpoznane anotacje
                #
                if len(ans_cmp)>0 or len(ans_cmp)>0:
                    item += "<div class='recognized'><div>Recognized annotations:</div>"                    
                    for i in range(0, len(asent.tokens())):
                        lexem = asent.tokens()[i].get_preferred_lexeme(ctagset)
                        # tagi rozpoczynajace
                        for channel_name in channels: 
                            key = "%s:%d" % (channel_name, i)
                            if key in tp:
                                item += "<span class='%s tp' title='%s'>" % (channel_name, channel_name)
                            if key in fp:
                                item += "<span class='%s fp' title='%s'>" % (channel_name, channel_name)
                        # tekst
                        ctag = ctagset.tag_to_string(lexem.tag()).split(":")
                        item += "<tok title='%s'>" % (lexem_to_title(lexem, ctagset))
                        item += str(asent.tokens()[i].orth())                        
                        item += "</tok>"
                        # tagi zamykajace
                        for channel_name in channels: 
                            if channel_name+":"+str(i) in ends:
                                item += "</span>"
                        # separator
                        item += " "
                    item += "</div>"
                    
                #
                # Anotacje nierozpoznane
                #
                if len(fn) > 0:
                    item += "<div class='reference'><div>Missing annotations:</div>"
                    for i in range(0, len(asent.tokens())):
                        lexem = asent.tokens()[i].get_preferred_lexeme(ctagset)
                        # tagi rozpoczynajace
                        for channel_name in channels: 
                            key = "%s:%d" % (channel_name, i)
                            if key in fn:
                                item += "<span class='%s fn' title='%s'>" % (channel_name, channel_name)
                        # tekst
                        ctag = ctagset.tag_to_string(lexem.tag()).split(":")
                        item += "<tok title='%s'>" % (lexem_to_title(lexem, ctagset))
                        item += str(asent.tokens()[i].orth())                        
                        item += "</tok>"
                        # tagi zamykajace
                        for channel_name in channels: 
                            if channel_name+":"+str(i) in fn_ends:
                                item += "</span>"
                        # separator
                        item += " "
                    item += "</div>"
                    
                if len(item)>0:
                    id = os.path.basename(filename.encode("utf-8")).split(".")[0]
                    href = '<a href="http://www.nlp.pwr.wroc.pl/inforex?page=report&id=%s" target="_blank">%s</a>' % (id, filename.encode("utf-8"))
                    items.append("<b>" + href  + "</b>" + item)

            else:
                break
        del tok_reader

    args = {}
    args["processed"] = processed
    args["items"] = items
    args["total"] = len(filenames)
    return args


def error(msg):
    args = {}
    args["error"] = [msg]
    print json.dumps(args)
    sys.exit()


def main():
    try:
        from optparse import OptionParser
        optparser = OptionParser(usage=u"""usage: python %prog -s START -o OFFSET""")
    
        optparser.add_option("-s", "--start", action="store", dest="start", metavar="FILE",
                             help=u"indeks pierwszego dokumentu")
        optparser.add_option("-o", "--offset", action="store", dest="offset", metavar="FILE",
                             help=u"liczba dokumentów do przetworzenia")
        optparser.add_option("-r", "--rules", action="store", dest="rules", metavar="FILE",
                             help=u"reguły")
        optparser.add_option("-c", "--corpus", action="store", dest="corpus", metavar="FILE",
                             help=u"ścieżka do pliku z nazwami plików")
    
        (options, _) = optparser.parse_args()
    
        if len(options.rules.strip()) == 0:
            error("Błąd systemu: reguły wccl nie zostały przekazane do skryptu wccl-gateway.py")
    
        r = process_files(int(options.start), int(options.offset), options.rules, options.corpus)
        print json.dumps(r)
    
    except IndexError, err:
        error("<em>Błąd w regule WCCL:</em> " + str(err) )
    except IOError, err:
        error("<em>Błąd w wccl-gateway.py:</em> " + err.strerror + ":" + err.filename)
    except Exception, err:
        error("<em>Błąd w wccl-gateway.py:</em> " + traceback.format_exc())

#------------------------------------------------------------------------------
# Entry
#------------------------------------------------------------------------------
if __name__ == '__main__':
    main()
