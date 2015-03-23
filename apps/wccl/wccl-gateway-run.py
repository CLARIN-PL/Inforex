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
	title = "base: %s" % lexem.lemma_utf8()
	title += "\nclass: %s" % ctag[0]
	for val in ctag[1:]:
		if val in _nkjp_val_to_attr:
			title += "\n%s: %s" % (_nkjp_val_to_attr[val], val)
		else:
			title += "\n?: %s" % (val)        
	return title


def process_file(wccl_rules, file_path):
	tagset = "nkjp"
	ctagset = corpus2.get_named_tagset(tagset)
	channels = ['t3_range', 't3_date', 't3_time', 't3_set', 't3_duration']

	p = wccl.Parser(ctagset)
	wc = p.parseWcclFile(wccl_rules)

	items = []
	tok_reader = corpus2.TokenReader.create_path_reader('ccl', corpus2.get_named_tagset(tagset), file_path)
	while True:
		sent = tok_reader.get_next_sentence()
		if sent:
			item = ""
			asent = corpus2.AnnotatedSentence.wrap_sentence(sent)
			match_rules = wc.get_match_rules_ptr()
			match_rules.apply_all(asent)
            
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
            
				item += "<div class='aux'>"                    
				for i in range(0, len(asent.tokens())):
					lexem = asent.tokens()[i].get_preferred_lexeme(ctagset)
					# tagi rozpoczynajace
					for channel_name in aux_channels: 
						key = "%s:%d" % (channel_name, i)
						if key in aux_start:
							item += "<span class='%s aux' title='%s'>" % (channel_name, channel_name)
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
                                            
			if len(item)>0:
				items.append(item)

		else:
			break
	del tok_reader

	args = {}
	args["items"] = items
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
		optparser.add_option("-f", "--file", action="store", dest="file", metavar="FILE",
                             help=u"ścieżka do pliku ccl")
		optparser.add_option("-r", "--rules", action="store", dest="rules", metavar="FILE",
                             help=u"reguły")
    
		(options, _) = optparser.parse_args()
    
		if len(options.rules.strip()) == 0:
			error("Błąd systemu: reguły wccl nie zostały przekazane do skryptu wccl-gateway.py")
    
		r = process_file(options.rules, options.file)
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
