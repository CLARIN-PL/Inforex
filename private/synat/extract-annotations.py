#!/usr/bin/python
# -*- coding: utf-8 -*-
# Copyright ©2014 Jan Kocoń, Wrocław University of Technology
# jan.kocon@pwr.wroc.pl

import sys
import csv
from optparse import OptionParser
from collections import defaultdict as dd
from itertools import repeat, izip
import corpus2
import pprint
import os.path
import codecs



def chunks(rdr):
    """Yields subsequent sentences from a reader."""
    while True:
        chunk = rdr.get_next_chunk()
        if not chunk:
            break
        yield chunk		


def go2():
    parser = OptionParser()
    parser.add_option('-t', '--tagset', type='string', action='store',
        dest='tagset', default='nkjp',
        help='set the tagset used in input; default: nkjp')
    parser.add_option('-i', '--input-format', type='string', action='store',
        dest='input_format', default='ccl',
        help='set the input format; default: ccl')
    (options, args) = parser.parse_args()
    
    if len(args) < 1:
        print 'You need to provide an input corpus.'
        print 'See %s --help' % sys.argv[0]
        sys.exit(1)
      
    
    inpath = args[0]
    tagset = corpus2.get_named_tagset(options.tagset)
    reader = corpus2.TokenReader.create_path_reader(options.input_format, tagset, inpath)
    for chunk in chunks(reader):
        for sentence in chunk.sentences():
            annotated_sentence = corpus2.AnnotatedSentence.wrap_sentence(sentence.clone_shared())
            tokens = sentence.tokens()
            for channel in annotated_sentence.all_channels():
                for annotation in annotated_sentence.get_channel(channel).make_annotation_vector():
                    annotation_orth = None
                    annotation_lemma = None
                    for annotation_token_id in annotation.indices:
                        token = tokens[annotation_token_id]
                        if not annotation_orth:
                            annotation_orth = token.orth_utf8()
                        elif token.after_space():
                            annotation_orth += " " + token.orth_utf8()
                        else:
                            annotation_orth += token.orth_utf8()
                    first_annotation_token = tokens[annotation.indices[0]]
                    if first_annotation_token.has_metadata():
                        token_metadata = first_annotation_token.get_metadata()
                        if token_metadata.has_attribute("%s:lemma" % channel):
                            annotation_lemma = token_metadata.get_attribute("%s:lemma" % channel)
                    #print "%s\t%s\t%s" % (channel, annotation_orth, annotation_lemma)
                    print "%s\t%s" % (channel, annotation_orth)
if __name__ == '__main__':
    go2()
