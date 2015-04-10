#!/bin/python
# -*- coding: utf-8 -*-

import corpus2
import wccl
import codecs
import os
import json
import sys
import traceback
import re
import operator

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


def contains_all(collection, elements):
	"""
	Checks if collection contains all elements.
	@param collection: {set([])} collection to check in
	@param elements: {[]} set of elements to find
	@return: {Boolean}
	"""
	for element in elements:
		if element not in collection:
			return False
	return True


def process_file(wccl_rules, file_path, annotations):
	tagset = "nkjp"
	ctagset = corpus2.get_named_tagset(tagset)
	channels = ['t3_range', 't3_date', 't3_time', 't3_set', 't3_duration']

	p = wccl.Parser(ctagset)
	wc = p.parseWcclFile(wccl_rules)

	required = set()
	display = set()
	for (name, val) in annotations.items():
		if val['required'] == True:
			required.add(name)
		display.add(name)

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
			#aux_channels = []
			#for channel in asent.all_channels():
			#	if channel.startswith("aux_"):
			#		aux_channels.append(channel)
								
			
            
			#if len(aux_channels)>0:
			if contains_all(asent.all_channels(), required):                    
				aux_start = {}
				aux_end = set()

				for channel_name in asent.all_channels(): 
					if channel_name in display:
						for ann in asent.get_channel(channel_name).make_annotation_vector():
							# Starting tags
							start_index = ann.indices[0]
							if start_index not in aux_start:
								aux_start[start_index] = []
							aux_start[start_index].append((channel_name, ann.indices[-1]-ann.indices[0]))
							# Ending tags
							aux_end.add("%s:%d" % (channel_name, ann.indices[-1]) )                    
            
				if len(aux_start)>0:
					item += "<div class='aux'>"                    
					for i in range(0, len(asent.tokens())):
						lexem = asent.tokens()[i].get_preferred_lexeme(ctagset)
						# tagi rozpoczynajace
						if i in aux_start:
							sorted_x = reversed(sorted(aux_start[i], key=operator.itemgetter(1)))
							for (channel_name, length) in sorted_x:
								color = "black" if channel_name not in annotations else annotations[channel_name]['color']
								item += "<span class='%s aux' title='%s' style='border-color: %s'>" % (channel_name, channel_name, color)
						# tekst
						ctag = ctagset.tag_to_string(lexem.tag()).split(":")
						item += "<tok title='%s'>" % (lexem_to_title(lexem, ctagset))
						item += str(asent.tokens()[i].orth())                        
						item += "</tok>"
	                    # tagi zamykajace
						for channel_name in display: 
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


def parse_annotations(text):
	"""
	Parse text describing annotation configuration. The description has a form of text:
	  // lines starting with "//" are comments
	  // name color (yes|no)
	  aux_nam red yes
	@return: Dict
	"""
	lines = text.split("\n")
	annotations = {}
	for line in lines:
		line = line.strip()
		if line.startswith("//"):
			pass
		else:
			cols = re.split("[ \t]+", line)
			#print cols
			if len(cols)>1:
				name = cols[0]
				color = cols[1]
				required = len(cols)>2 and cols[2] == "yes"
				annotations[name] = {'color': color, 'required': required}
	return annotations				


def main():
	try:
		from optparse import OptionParser
		optparser = OptionParser(usage=u"""usage: python %prog -s START -o OFFSET""")
		optparser.add_option("-f", "--file", action="store", dest="file", metavar="FILE",
                             help=u"ścieżka do pliku ccl")
		optparser.add_option("-r", "--rules", action="store", dest="rules", metavar="FILE",
                             help=u"reguły")
		optparser.add_option("-a", "--annotations", action="store", dest="annotations", metavar="FILE",
                             help=u"anotacje")
    
		(options, _) = optparser.parse_args()
    
		if len(options.rules.strip()) == 0:
			error("Błąd systemu: reguły wccl nie zostały przekazane do skryptu wccl-gateway.py")
    
		r = process_file(options.rules, options.file, parse_annotations(options.annotations))
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
