<?php

class ConllAndJsonFactory {

    const FORMAT_LEGACY = 'legacy';
    const FORMAT_TEXT = 'text';
    const FORMAT_CONLLU_CLARIN = 'conllu';
    const FORMAT_CONLLU_STANDARD = 'conllu_standard';
    const FORMAT_CLARIN_JSON = 'clarin_json';
    const FORMAT_CLARIN_PARQUET_ZST = 'clarin_parquet_zst';
    const FORMAT_DIALOG_PARQUET_ZST = 'dialog_parquet_zst';
    protected $annotationSetNameCache = array();

    protected function makeLemmaCache(array $lemma) {

        $lemmaCache = array();
        for($i=0;$i<count($lemma);$i++) {
            if(isset($lemma[$i]['report_annotation_id'])) {
                $lemmaCache[$lemma[$i]['report_annotation_id']] =
                    array(
                        'idx' => $i,
                        'lemma' => isset($lemma[$i]['lemma']) ? $lemma[$i]['lemma'] : null
                    );
            }
        }
        return $lemmaCache;

    } // makeLemmaCache()

    protected function makeConllAndJsonExportData($ccl, $tokens, $relations, $annotations, $tokens_ids, $annotations_by_id, $lemmas) {

        $lemmas_by_annotation_id = $this->makeLemmaCache($lemmas);
        foreach($annotations as &$ann) {
            if(array_key_exists($ann['id'],$lemmas_by_annotation_id)) {
                $ann['lemma']=$lemmas_by_annotation_id[$ann['id']]['lemma'];
            }
        }

        $cache_tokens_from = [];
        foreach ($tokens as $token) {
            $cache_tokens_from += [$token['from'] => [
                'to' => $token['to'],
                'id' => $token['token_id']
            ]];
        }

        $annotations_token_id = [];
        foreach ($annotations as $annotation) {
            $annotation_from = $annotation['from'];
            $annotation_to = $annotation['to'];
            $start = true;
            foreach ($cache_tokens_from as $from => $value) {
                $to = $value['to'];
                $id = $value['id'];
                if ($from >= $annotation_from and $to <= $annotation_to) {
                    $iob = $start ? 'B-' : 'I-';
                    $start = false;
                    if (array_key_exists($id, $annotations_token_id)) {
                        $annotations_token_id[$id][] = array(
                            'ann_id' => $annotation['id'],
                            'iob' => $iob
                        );
                    } else {
                        $annotations_token_id += [
                            $id => [array(
                                'ann_id' => $annotation['id'],
                                'iob' => $iob
                            )]
                        ];
                    }
                }
            }
        }

        $relations_cache = [];
        foreach ($relations as $relation) {
            $ids = array_unique([$relation['source_id'], $relation['target_id']]);
            foreach ($ids as $id) {
                if (array_key_exists($id, $relations_cache)) {
                    $relations_cache[$id][] = $relation;
                } else {
                    $relations_cache += [
                        $id => [$relation]
                    ];
                }
            }
        }

        $conll = '';
        $conll .= "ORDER_ID\tTOKEN_ID\tORTH\tCTAG\tFROM\tTO\tANN_TAGS\tANN_IDS\tREL_IDS\tREL_TARGET_ANN_IDS\n";
        $json_builder = [
            'chunks' => [],
            'relations' => array_values($relations),
            'annotations' => $annotations,
        ];
        $it = 0;
        foreach ($ccl->chunks as $chunk) {
            $json_sentences = [];
            foreach ($chunk->sentences as $sentence) {
                $json_sentence = [];
                $id = 0;
                foreach ($sentence->tokens as $token) {
                    $original_id = isset($tokens_ids[$it]) ? $tokens_ids[$it++] : null;
                    $ann_tag = [];
                    $ann_id = [];
                    $rel_id = [];
                    $rel_target_id = [];
                    if (array_key_exists($original_id, $annotations_token_id)) {
                        $annotations_for_id = $annotations_token_id[$original_id];
                        foreach ($annotations_for_id as $annotations_from_cache) {
                            $annotations_from_cache_id = $annotations_from_cache['ann_id'];
                            $iob = $annotations_from_cache['iob'];

                            $annotation = $annotations_by_id[$annotations_from_cache_id];
                            $ann_tag[] = $iob . $annotation['type'];
                            $ann_id[] = $annotation['id'];

                            if (array_key_exists($annotation['id'], $relations_cache)) {
                                $relations_for_token = $relations_cache[$annotation['id']];
                                foreach ($relations_for_token as $relation) {
                                    $rel_id[] = $relation['id'];
                                    if ($relation['source_id'] == $annotation['id']) {
                                        $rel_target_id[] = $relation['target_id'];
                                    }
                                }
                            }
                        }
                    }
                    $token_id = $id++;
                    $ctag = isset($token->lexemes[0]->ctag) ? $token->lexemes[0]->ctag :'';
                    $json_sentence[] = array(
                        'order_id' => $token->id,
                        'token_id' => $token_id,
                        'orth' => $token->orth,
                        'ctag' => $ctag,
                        'from' => $token->from,
                        'to'  => $token->to,
                        'annotations' => $ann_id,
                        'relations' => $rel_id
                    );

                    if (empty($ann_tag)) {
                        $ann_tag += ['O'];
                    }

                    $arrays_to_check = [&$ann_id, &$rel_id, &$rel_target_id];
                    foreach ($arrays_to_check as &$array_to_check) {
                        if (empty($array_to_check)) {
                            $array_to_check += ['_'];
                        }
                    }
                    $conll .= $token->id . "\t" . $token_id . "\t" . $token->orth . "\t" . $ctag . "\t" . $token->from . "\t" .
                        $token->to . "\t" . join(':', $ann_tag) . "\t" . join(':', $ann_id) . "\t" .
                        join(':', $rel_id) . "\t" . join(':', $rel_target_id) . "\n";

                }
                $conll .= "\n";
                $json_sentences[] = $json_sentence;
            }
            $json_builder['chunks'][] = $json_sentences;
        }

        return array($conll,$json_builder);

    } // makeConllAndJsonExportData()

    protected function mapXposToUpos($xpos) {

        $xpos = strtolower((string)$xpos);
        if ($xpos === '') {
            return 'X';
        }

        $prefix = explode(':', $xpos, 2)[0];
        $map = array(
            'subst' => 'NOUN',
            'depr' => 'NOUN',
            'ger' => 'NOUN',
            'brev' => 'NOUN',
            'adj' => 'ADJ',
            'adja' => 'ADJ',
            'adjp' => 'ADJ',
            'adjc' => 'ADJ',
            'adv' => 'ADV',
            'qub' => 'PART',
            'prep' => 'ADP',
            'conj' => 'CCONJ',
            'comp' => 'SCONJ',
            'interj' => 'INTJ',
            'interp' => 'PUNCT',
            'num' => 'NUM',
            'ppron12' => 'PRON',
            'ppron3' => 'PRON',
            'siebie' => 'PRON',
            'fin' => 'VERB',
            'bedzie' => 'AUX',
            'aglt' => 'AUX',
            'praet' => 'VERB',
            'impt' => 'VERB',
            'imps' => 'VERB',
            'inf' => 'VERB',
            'pcon' => 'VERB',
            'pant' => 'VERB',
            'imps' => 'VERB',
            'winien' => 'VERB',
            'pred' => 'VERB',
            'ppas' => 'ADJ',
            'pact' => 'ADJ',
            'xxx' => 'X',
            'ign' => 'X'
        );

        return isset($map[$prefix]) ? $map[$prefix] : 'X';

    } // mapXposToUpos()

    protected function selectSentenceRootIndex(array $sentenceTokens) {

        foreach ($sentenceTokens as $index => $token) {
            $xpos = isset($token['xpos']) ? strtolower($token['xpos']) : '';
            foreach (array('fin', 'praet', 'bedzie', 'impt', 'imps', 'inf', 'winien', 'pred') as $verbPrefix) {
                if (strpos($xpos, $verbPrefix) === 0) {
                    return $index;
                }
            }
        }

        return 0;

    } // selectSentenceRootIndex()

    protected function buildTokenAnnotationsMap(array $tokens, array $annotations, array $annotationsById) {

        $cacheTokensFrom = array();
        foreach ($tokens as $token) {
            $cacheTokensFrom[$token['from']] = array(
                'to' => $token['to'],
                'id' => $token['token_id']
            );
        }

        $annotationsTokenId = array();
        foreach ($annotations as $annotation) {
            $annotationFrom = $annotation['from'];
            $annotationTo = $annotation['to'];
            $start = true;
            foreach ($cacheTokensFrom as $from => $value) {
                $to = $value['to'];
                $id = $value['id'];
                if ($from >= $annotationFrom && $to <= $annotationTo) {
                    $iob = $start ? 'B-' : 'I-';
                    $start = false;
                    if (!isset($annotationsTokenId[$id])) {
                        $annotationsTokenId[$id] = array();
                    }
                    $annotationRecord = isset($annotationsById[$annotation['id']]) ? $annotationsById[$annotation['id']] : $annotation;
                    $annotationsTokenId[$id][] = $iob . $annotationRecord['type'];
                }
            }
        }

        return $annotationsTokenId;

    } // buildTokenAnnotationsMap()

    protected function buildTokenRelationsMap(array $relations) {

        $relationsBySource = array();
        foreach ($relations as $relation) {
            $sourceId = intval($relation['source_id']);
            if (!isset($relationsBySource[$sourceId])) {
                $relationsBySource[$sourceId] = array();
            }
            $relationsBySource[$sourceId][] = intval($relation['target_id']);
        }

        return $relationsBySource;

    } // buildTokenRelationsMap()

    protected function buildStandardConllu($ccl, array $tokens, array $relations, array $annotations, array $tokensIds, array $annotationsById, $plainText) {

        $offsetMap = $this->buildCompactToPlainOffsetMap($plainText);
        $tokenAnnotationMap = $this->buildTokenAnnotationsMap($tokens, $annotations, $annotationsById);
        $relationsBySource = $this->buildTokenRelationsMap($relations);
        $lines = array();
        $tokenIterator = 0;
        $sentenceIndex = 1;

        foreach ($ccl->chunks as $chunk) {
            foreach ($chunk->sentences as $sentence) {
                $sentenceTokens = array();
                foreach ($sentence->tokens as $token) {
                    $originalId = isset($tokensIds[$tokenIterator]) ? intval($tokensIds[$tokenIterator]) : ($tokenIterator + 1);
                    $tokenIterator++;
                    $xpos = isset($token->lexemes[0]->ctag) ? $token->lexemes[0]->ctag : '_';
                    $lemma = isset($token->lexemes[0]->base) && $token->lexemes[0]->base !== '' ? $token->lexemes[0]->base : $token->orth;
                    $sentenceTokens[] = array(
                        'original_id' => $originalId,
                        'form' => $token->orth,
                        'lemma' => $lemma,
                        'xpos' => $xpos,
                        'upos' => $this->mapXposToUpos($xpos),
                        'from' => $token->from,
                        'to' => $token->to
                    );
                }

                if (empty($sentenceTokens)) {
                    continue;
                }

                $rootIndex = $this->selectSentenceRootIndex($sentenceTokens);
                $sentenceTextParts = array();
                foreach ($sentenceTokens as $index => $tokenData) {
                    $sentenceTextParts[] = $tokenData['form'];
                }
                $lines[] = '# sent_id = ' . $sentenceIndex;
                $lines[] = '# text = ' . implode(' ', $sentenceTextParts);

                foreach ($sentenceTokens as $index => $tokenData) {
                    $id = $index + 1;
                    $head = $index === $rootIndex ? 0 : ($rootIndex + 1);
                    $deprel = $index === $rootIndex ? 'root' : 'dep';
                    $misc = array();

                    list($start, $stop) = $this->convertOffsetsToPlainText($tokenData['from'], $tokenData['to'], $offsetMap);
                    $misc[] = 'StartChar=' . intval($start);
                    $misc[] = 'EndChar=' . intval($stop);

                    if (isset($tokenAnnotationMap[$tokenData['original_id']]) && count($tokenAnnotationMap[$tokenData['original_id']]) > 0) {
                        $misc[] = 'Entity=' . implode(',', array_unique($tokenAnnotationMap[$tokenData['original_id']]));
                    }

                    if (isset($relationsBySource[$tokenData['original_id']]) && count($relationsBySource[$tokenData['original_id']]) > 0) {
                        $misc[] = 'Relations=' . implode(',', array_unique($relationsBySource[$tokenData['original_id']]));
                    }

                    if ($index < count($sentenceTokens) - 1) {
                        list($currentStart, $currentStop) = $this->convertOffsetsToPlainText($tokenData['from'], $tokenData['to'], $offsetMap);
                        list($nextStart, $nextStop) = $this->convertOffsetsToPlainText($sentenceTokens[$index + 1]['from'], $sentenceTokens[$index + 1]['to'], $offsetMap);
                        if ($nextStart === $currentStop) {
                            $misc[] = 'SpaceAfter=No';
                        }
                    }

                    $lines[] = implode("\t", array(
                        $id,
                        $tokenData['form'],
                        $tokenData['lemma'],
                        $tokenData['upos'],
                        $tokenData['xpos'] !== '' ? $tokenData['xpos'] : '_',
                        '_',
                        $head,
                        $deprel,
                        '_',
                        empty($misc) ? '_' : implode('|', $misc)
                    ));
                }

                $lines[] = '';
                $sentenceIndex++;
            }
        }

        return implode("\n", $lines);

    } // buildStandardConllu()

    protected function buildAttributesByAnnotationId(array $attributes) {

        $attributesByAnnotationId = array();
        foreach ($attributes as $attribute) {
            if (!isset($attribute['id']) || !isset($attribute['name'])) {
                continue;
            }
            $annotationId = $attribute['id'];
            if (!isset($attributesByAnnotationId[$annotationId])) {
                $attributesByAnnotationId[$annotationId] = array();
            }
            $attributesByAnnotationId[$annotationId][$attribute['name']] = isset($attribute['value']) ? $attribute['value'] : null;
        }

        return $attributesByAnnotationId;

    } // buildAttributesByAnnotationId()

    protected function detectTagsetName(array $tagsByTokens) {

        global $db;

        foreach ($tagsByTokens as $tokenTags) {
            if (!is_array($tokenTags)) {
                continue;
            }
            foreach ($tokenTags as $tag) {
                if (!isset($tag['tagset_id'])) {
                    continue;
                }
                $tagsetName = $db->fetch_one(
                    'SELECT name FROM tagsets WHERE tagset_id = ?',
                    array(intval($tag['tagset_id']))
                );
                if ($tagsetName) {
                    return $tagsetName;
                }
            }
        }

        return 'nkjp';

    } // detectTagsetName()

    protected function splitUtf8Chars($text) {

        if ($text === null || $text === '') {
            return array();
        }

        return preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);

    } // splitUtf8Chars()

    protected function buildCompactToPlainOffsetMap($plainText) {

        $chars = $this->splitUtf8Chars((string)$plainText);
        $offsetMap = array();
        $compactOffset = 0;

        foreach ($chars as $plainOffset => $char) {
            if (preg_match('/\s/u', $char)) {
                continue;
            }
            $offsetMap[$compactOffset] = $plainOffset;
            $compactOffset++;
        }

        return $offsetMap;

    } // buildCompactToPlainOffsetMap()

    protected function convertOffsetsToPlainText($start, $stopInclusive, array $offsetMap) {

        $start = intval($start);
        $stopInclusive = intval($stopInclusive);

        if (isset($offsetMap[$start])) {
            $start = $offsetMap[$start];
        }
        if (isset($offsetMap[$stopInclusive])) {
            $stopInclusive = $offsetMap[$stopInclusive];
        }

        return array($start, $stopInclusive + 1);

    } // convertOffsetsToPlainText()

    protected function buildClarinTokens($ccl, array $tokensIds, array $offsetMap) {

        $tokenIterator = 0;
        $clarinTokens = array();

        foreach ($ccl->chunks as $chunk) {
            foreach ($chunk->sentences as $sentence) {
                foreach ($sentence->tokens as $token) {
                    $tokenId = isset($tokensIds[$tokenIterator]) ? intval($tokensIds[$tokenIterator]) : ($tokenIterator + 1);
                    $tokenIterator++;

                    $lexemes = array();
                    foreach ($token->lexemes as $lexeme) {
                        $lexemes[] = array(
                            'lemma' => $lexeme->base,
                            'pos' => $lexeme->ctag,
                            'disamb' => (bool)$lexeme->disamb
                        );
                    }

                    list($start, $stop) = $this->convertOffsetsToPlainText($token->from, $token->to, $offsetMap);

                    $clarinTokens[] = array(
                        'lexemes' => $lexemes,
                        'id' => $tokenId,
                        'start' => $start,
                        'stop' => $stop
                    );
                }
            }
        }

        return array('default' => $clarinTokens);

    } // buildClarinTokens()

    protected function buildSentenceSpans($ccl, array $offsetMap) {

        $spans = array();
        $sentenceIndex = 1;

        foreach ($ccl->chunks as $chunk) {
            foreach ($chunk->sentences as $sentence) {
                if (count($sentence->tokens) === 0) {
                    continue;
                }
                $firstToken = $sentence->tokens[0];
                $lastToken = $sentence->tokens[count($sentence->tokens) - 1];
                list($start, $stop) = $this->convertOffsetsToPlainText($firstToken->from, $lastToken->to, $offsetMap);
                $spans[] = array(
                    'id' => 'sentence-' . $sentenceIndex,
                    'start' => $start,
                    'stop' => $stop,
                    'type' => 'sentence'
                );
                $sentenceIndex++;
            }
        }

        return $spans;

    } // buildSentenceSpans()

    protected function normalizeSentenceSpans(array $sentenceSpans, array $annotationSpans) {

        if (empty($sentenceSpans)) {
            return $sentenceSpans;
        }

        usort($sentenceSpans, function($left, $right) {
            if ($left['start'] === $right['start']) {
                return $left['stop'] - $right['stop'];
            }
            return $left['start'] - $right['start'];
        });

        foreach ($annotationSpans as $annotation) {
            $contained = false;
            foreach ($sentenceSpans as $sentence) {
                if ($annotation['start'] >= $sentence['start'] && $annotation['stop'] <= $sentence['stop']) {
                    $contained = true;
                    break;
                }
            }
            if ($contained) {
                continue;
            }

            $firstIndex = null;
            $lastIndex = null;
            foreach ($sentenceSpans as $index => $sentence) {
                if ($annotation['stop'] < $sentence['start']) {
                    break;
                }
                if ($annotation['start'] <= $sentence['stop'] && $annotation['stop'] >= $sentence['start']) {
                    if ($firstIndex === null) {
                        $firstIndex = $index;
                    }
                    $lastIndex = $index;
                }
            }

            if ($firstIndex === null) {
                foreach ($sentenceSpans as $index => $sentence) {
                    if ($annotation['start'] < $sentence['start']) {
                        $firstIndex = max(0, $index - 1);
                        $lastIndex = $index;
                        break;
                    }
                }
            }

            if ($firstIndex === null) {
                $firstIndex = count($sentenceSpans) - 1;
                $lastIndex = count($sentenceSpans) - 1;
            }

            $sentenceSpans[$firstIndex]['start'] = min($sentenceSpans[$firstIndex]['start'], $annotation['start']);
            $sentenceSpans[$firstIndex]['stop'] = max($sentenceSpans[$lastIndex]['stop'], $annotation['stop']);

            if ($lastIndex > $firstIndex) {
                array_splice($sentenceSpans, $firstIndex + 1, $lastIndex - $firstIndex);
            }
        }

        $normalized = array();
        foreach (array_values($sentenceSpans) as $index => $sentence) {
            $normalized[] = array(
                'id' => 'sentence-' . ($index + 1),
                'start' => $sentence['start'],
                'stop' => $sentence['stop'],
                'type' => 'sentence'
            );
        }

        return $normalized;

    } // normalizeSentenceSpans()

    protected function buildMetadata($report, $reportExt, $taggingMethod, $exportFormat) {

        $metadata = array(
            'document' => array(
                'id' => isset($report['id']) ? intval($report['id']) : null,
                'title' => isset($report['title']) ? $report['title'] : null,
                'source' => isset($report['source']) ? $report['source'] : null,
                'author' => isset($report['author']) ? $report['author'] : null,
                'date' => isset($report['date']) ? $report['date'] : null,
                'filename' => isset($report['filename']) ? $report['filename'] : null,
                'language' => isset($report['language']) ? $report['language'] : null,
                'status' => isset($report['status']) ? $report['status'] : null,
                'subcorpus_id' => isset($report['subcorpus_id']) ? intval($report['subcorpus_id']) : null
            ),
            'export' => array(
                'format' => $exportFormat,
                'tagging' => $taggingMethod
            )
        );

        if (is_array($reportExt)) {
            foreach ($reportExt as $key => $value) {
                if ($key === 'id' || $value === null) {
                    continue;
                }
                $metadata['custom'][$key] = $value;
            }
        }

        return $metadata;

    } // buildMetadata()

    protected function buildLayerMeta(array $items, array $fields, $kind) {

        $meta = array(
            'kind' => $kind,
            'count' => count($items)
        );

        foreach ($fields as $field) {
            $values = array();
            foreach ($items as $item) {
                if (isset($item[$field]) && $item[$field] !== '') {
                    $values[] = $item[$field];
                }
            }
            $values = array_values(array_unique($values, SORT_REGULAR));
            if (count($values) === 1) {
                $meta[$field] = $values[0];
            }
        }

        return $meta;

    } // buildLayerMeta()

    protected function getAnnotationSetDisplayName($annotationSetId) {

        $annotationSetId = intval($annotationSetId);
        if ($annotationSetId <= 0) {
            return 'annotations';
        }

        if (isset($this->annotationSetNameCache[$annotationSetId])) {
            return $this->annotationSetNameCache[$annotationSetId];
        }

        global $db;
        $name = $db->fetch_one(
            'SELECT name FROM annotation_sets WHERE annotation_set_id = ?',
            array($annotationSetId)
        );

        if (!$name) {
            $name = 'annotation_set_' . $annotationSetId;
        }

        $this->annotationSetNameCache[$annotationSetId] = $name;
        return $name;

    } // getAnnotationSetDisplayName()

    protected function buildSpanLayerName(array $annotation) {

        if (isset($annotation['group']) && $annotation['group'] !== '') {
            return $this->getAnnotationSetDisplayName($annotation['group']);
        }

        return 'annotations';

    } // buildSpanLayerName()

    protected function buildClarinSpans(array $annotations, array $attributes, array $offsetMap, $plainText) {

        $attributesByAnnotationId = $this->buildAttributesByAnnotationId($attributes);
        $spanLayers = array();

        foreach ($annotations as $annotation) {
            list($start, $stop) = $this->convertOffsetsToPlainText($annotation['from'], $annotation['to'], $offsetMap);
            $layer = $this->buildSpanLayerName($annotation);
            $span = array(
                'id' => intval($annotation['id']),
                'start' => $start,
                'stop' => $stop,
                'type' => $annotation['type']
            );

            if (isset($annotation['lemma'])) {
                $span['lemma'] = $annotation['lemma'];
            }
            if (isset($attributesByAnnotationId[$annotation['id']])) {
                $span['attributes'] = $attributesByAnnotationId[$annotation['id']];
            }

            if (!isset($spanLayers[$layer])) {
                $spanLayers[$layer] = array(
                    'meta' => array(),
                    'items' => array()
                );
            }

            $spanLayers[$layer]['items'][] = $span;
            $spanLayers[$layer]['meta_source'][] = array(
                'group' => isset($annotation['group']) ? $annotation['group'] : null,
                'stage' => isset($annotation['stage']) ? $annotation['stage'] : null,
                'source' => isset($annotation['source']) ? $annotation['source'] : null
            );
        }

        foreach ($spanLayers as $layer => &$definition) {
            $definition['meta'] = $this->buildLayerMeta($definition['meta_source'], array('group', 'stage', 'source'), 'annotation');
            unset($definition['meta_source']);
        }
        unset($definition);

        return $spanLayers;

    } // buildClarinSpans()

    protected function buildClarinRelations(array $relations) {

        $clarinRelations = array();
        foreach ($relations as $relation) {
            $layer = isset($relation['rsname']) && trim($relation['rsname']) !== ''
                ? $relation['rsname']
                : 'relations';
            $clarinRelation = array(
                'id' => intval($relation['id']),
                'type' => $relation['name'],
                'source' => intval($relation['source_id']),
                'target' => intval($relation['target_id'])
            );

            if (isset($relation['relation_type_id'])) {
                $clarinRelation['relation_type_id'] = intval($relation['relation_type_id']);
            }

            if (!isset($clarinRelations[$layer])) {
                $clarinRelations[$layer] = array(
                    'meta' => array(),
                    'items' => array(),
                    'meta_source' => array()
                );
            }
            $clarinRelations[$layer]['items'][] = $clarinRelation;
            $clarinRelations[$layer]['meta_source'][] = array(
                'relation_set_id' => isset($relation['relation_set_id']) ? intval($relation['relation_set_id']) : null,
                'rsname' => isset($relation['rsname']) ? $relation['rsname'] : null
            );
        }

        foreach ($clarinRelations as &$definition) {
            $definition['meta'] = $this->buildLayerMeta($definition['meta_source'], array('relation_set_id', 'rsname'), 'relation');
            unset($definition['meta_source']);
        }
        unset($definition);

        return $clarinRelations;

    } // buildClarinRelations()

    protected function getUtf8TextSlice($text, $start, $stop) {

        $start = max(0, intval($start));
        $stop = max($start, intval($stop));
        return mb_substr((string)$text, $start, $stop - $start, 'UTF-8');

    } // getUtf8TextSlice()

    protected function normalizeDialogText($text) {

        $text = custom_html_entity_decode((string)$text);
        $text = preg_replace('/\s+/u', ' ', trim($text));
        return $text;

    } // normalizeDialogText()

    protected function getCompactTextLength($text) {

        $compact = preg_replace('/\s+/u', '', (string)$text);
        return mb_strlen($compact, 'UTF-8');

    } // getCompactTextLength()

    protected function loadDialogDom($content) {

        $dom = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $loaded = $dom->loadXML((string)$content);
        libxml_clear_errors();
        libxml_use_internal_errors(false);

        if (!$loaded) {
            return null;
        }
        if ($dom->documentElement && strtolower($dom->documentElement->nodeName) === 'body') {
            return $dom;
        }

        $bodies = $dom->getElementsByTagName('body');
        if ($bodies->length > 0) {
            return $dom;
        }

        return null;

    } // loadDialogDom()

    protected function addDialogSegment(array &$segments, $segmentType, $text, $compactCursor, $turnId = null) {

        $text = $this->normalizeDialogText($text);
        $compactLength = $this->getCompactTextLength($text);
        if ($compactLength <= 0) {
            return array($compactCursor, null);
        }

        $segment = array(
            'segment_type' => $segmentType,
            'turn_id' => $turnId,
            'text' => $text,
            'compact_start' => intval($compactCursor),
            'compact_stop' => intval($compactCursor + $compactLength - 1)
        );
        $segments[] = $segment;

        return array($compactCursor + $compactLength, $segment);

    } // addDialogSegment()

    protected function parseDialogStructure($content, $dialogId) {

        $dom = $this->loadDialogDom($content);
        if ($dom === null) {
            $fallbackText = $this->normalizeDialogText(strip_tags((string)$content));
            return array(
                'subtitle' => null,
                'participants' => array(),
                'turns' => array(
                    array(
                        'turn_id' => $dialogId . '-turn-1',
                        'sequence_no' => 1,
                        'speaker' => null,
                        'raw_author' => null,
                        'text' => $fallbackText,
                        'kind' => 'document',
                        'author_compact_start' => null,
                        'author_compact_stop' => null,
                        'content_compact_start' => 0,
                        'content_compact_stop' => max(0, $this->getCompactTextLength($fallbackText) - 1)
                    )
                ),
                'segments' => array(
                    array(
                        'segment_type' => 'content',
                        'turn_id' => $dialogId . '-turn-1',
                        'text' => $fallbackText,
                        'compact_start' => 0,
                        'compact_stop' => max(0, $this->getCompactTextLength($fallbackText) - 1)
                    )
                )
            );
        }

        $body = strtolower($dom->documentElement->nodeName) === 'body'
            ? $dom->documentElement
            : $dom->getElementsByTagName('body')->item(0);

        $subtitle = null;
        $participants = array();
        $turns = array();
        $segments = array();
        $compactCursor = 0;
        $turnNo = 1;

        foreach ($body->childNodes as $child) {
            if (!($child instanceof DOMElement)) {
                continue;
            }
            $nodeName = strtolower($child->nodeName);

            if ($nodeName === 'subtitle') {
                $subtitle = $this->normalizeDialogText($child->textContent);
                list($compactCursor,) = $this->addDialogSegment($segments, 'subtitle', $subtitle, $compactCursor, null);
                continue;
            }

            if ($nodeName === 'out') {
                $participant = $this->normalizeDialogText($child->textContent);
                if ($participant !== '') {
                    $participants[] = $participant;
                    list($compactCursor,) = $this->addDialogSegment($segments, 'participant', $participant, $compactCursor, null);
                }
                continue;
            }

            if ($nodeName !== 'message') {
                continue;
            }

            $authorNode = null;
            $contentNode = null;
            foreach ($child->childNodes as $messageChild) {
                if (!($messageChild instanceof DOMElement)) {
                    continue;
                }
                $messageNodeName = strtolower($messageChild->nodeName);
                if ($messageNodeName === 'author') {
                    $authorNode = $messageChild;
                } elseif ($messageNodeName === 'content') {
                    $contentNode = $messageChild;
                }
            }

            $authorText = $authorNode ? $this->normalizeDialogText($authorNode->textContent) : '';
            $contentText = $contentNode ? $this->normalizeDialogText($contentNode->textContent) : '';
            $turnId = $dialogId . '-turn-' . $turnNo;
            $turnNo++;

            $authorSegment = null;
            if ($authorText !== '') {
                list($compactCursor, $authorSegment) = $this->addDialogSegment($segments, 'author', $authorText, $compactCursor, $turnId);
            }

            $contentSegment = null;
            list($compactCursor, $contentSegment) = $this->addDialogSegment($segments, 'content', $contentText, $compactCursor, $turnId);

            $turns[] = array(
                'turn_id' => $turnId,
                'sequence_no' => count($turns) + 1,
                'speaker' => $authorText !== '' ? $authorText : null,
                'raw_author' => $authorText !== '' ? $authorText : null,
                'text' => $contentText,
                'kind' => $authorText === '' ? 'stage_direction' : 'utterance',
                'author_compact_start' => $authorSegment ? intval($authorSegment['compact_start']) : null,
                'author_compact_stop' => $authorSegment ? intval($authorSegment['compact_stop']) : null,
                'content_compact_start' => $contentSegment ? intval($contentSegment['compact_start']) : null,
                'content_compact_stop' => $contentSegment ? intval($contentSegment['compact_stop']) : null
            );
        }

        return array(
            'subtitle' => $subtitle,
            'participants' => $participants,
            'turns' => $turns,
            'segments' => $segments
        );

    } // parseDialogStructure()

    protected function findDialogSegmentForAnnotation(array $segments, $annotationFrom, $annotationTo) {

        foreach ($segments as $segment) {
            if ($annotationFrom >= $segment['compact_start'] && $annotationTo <= $segment['compact_stop']) {
                return $segment;
            }
        }

        return null;

    } // findDialogSegmentForAnnotation()

    protected function buildDialogAnnotations(array $annotations, array $attributes, array $segments) {

        $attributesByAnnotationId = $this->buildAttributesByAnnotationId($attributes);
        $dialogAnnotations = array();
        $annotationContext = array();

        foreach ($annotations as $annotation) {
            if (!isset($annotation['from']) || !isset($annotation['to']) || intval($annotation['to']) < intval($annotation['from'])) {
                continue;
            }
            $segment = $this->findDialogSegmentForAnnotation($segments, intval($annotation['from']), intval($annotation['to']));
            if ($segment === null) {
                continue;
            }

            $localCompactStart = intval($annotation['from']) - intval($segment['compact_start']);
            $localCompactStop = intval($annotation['to']) - intval($segment['compact_start']);
            $offsetMap = $this->buildCompactToPlainOffsetMap($segment['text']);
            list($localStart, $localStop) = $this->convertOffsetsToPlainText($localCompactStart, $localCompactStop, $offsetMap);
            $annotationId = intval($annotation['id']);

            $dialogAnnotations[] = array(
                'obj_id' => $annotationId,
                'turn_id' => $segment['turn_id'],
                'segment_type' => $segment['segment_type'],
                'compact_start' => intval($annotation['from']),
                'compact_stop' => intval($annotation['to']) + 1,
                'local_start' => intval($localStart),
                'local_stop' => intval($localStop),
                'text' => $this->getUtf8TextSlice($segment['text'], $localStart, $localStop),
                'annotation_set' => isset($annotation['group']) ? $this->getAnnotationSetDisplayName($annotation['group']) : null,
                'annotation_type' => isset($annotation['type']) ? $annotation['type'] : '',
                'lemma' => isset($annotation['lemma']) && $annotation['lemma'] !== '' ? $annotation['lemma'] : null,
                'stage' => isset($annotation['stage']) && $annotation['stage'] !== '' ? $annotation['stage'] : null,
                'source' => isset($annotation['source']) && $annotation['source'] !== '' ? $annotation['source'] : null,
                'attributes_json' => isset($attributesByAnnotationId[$annotationId])
                    ? $this->toNullableJson($attributesByAnnotationId[$annotationId])
                    : null
            );

            $annotationContext[$annotationId] = array(
                'turn_id' => $segment['turn_id'],
                'segment_type' => $segment['segment_type']
            );
        }

        return array($dialogAnnotations, $annotationContext);

    } // buildDialogAnnotations()

    protected function buildDialogRelations(array $relations, array $annotationContext) {

        $dialogRelations = array();
        foreach ($relations as $relation) {
            $sourceId = intval($relation['source_id']);
            $targetId = intval($relation['target_id']);
            if (!isset($annotationContext[$sourceId]) || !isset($annotationContext[$targetId])) {
                continue;
            }
            $sourceContext = isset($annotationContext[$sourceId]) ? $annotationContext[$sourceId] : array();
            $targetContext = isset($annotationContext[$targetId]) ? $annotationContext[$targetId] : array();

            $dialogRelations[] = array(
                'obj_id' => intval($relation['id']),
                'relation_set' => isset($relation['rsname']) && $relation['rsname'] !== '' ? $relation['rsname'] : null,
                'relation_type' => isset($relation['name']) ? $relation['name'] : '',
                'source_obj_id' => $sourceId,
                'target_obj_id' => $targetId,
                'source_turn_id' => isset($sourceContext['turn_id']) ? $sourceContext['turn_id'] : null,
                'target_turn_id' => isset($targetContext['turn_id']) ? $targetContext['turn_id'] : null,
                'source_segment_type' => isset($sourceContext['segment_type']) ? $sourceContext['segment_type'] : null,
                'target_segment_type' => isset($targetContext['segment_type']) ? $targetContext['segment_type'] : null
            );
        }

        return $dialogRelations;

    } // buildDialogRelations()

    protected function rebuildDialogSegmentText(array $items, $textField) {

        if (empty($items)) {
            return null;
        }

        $length = 0;
        foreach ($items as $item) {
            if (!isset($item['local_stop'])) {
                continue;
            }
            $length = max($length, intval($item['local_stop']));
        }

        if ($length <= 0) {
            return '';
        }

        $chars = array_fill(0, $length, ' ');
        foreach ($items as $item) {
            if (!isset($item[$textField])) {
                continue;
            }
            $start = isset($item['local_start']) ? intval($item['local_start']) : 0;
            $stop = isset($item['local_stop']) ? intval($item['local_stop']) : $start;
            if ($stop < $start) {
                continue;
            }
            $itemChars = $this->splitUtf8Chars((string)$item[$textField]);
            $max = min($stop - $start, count($itemChars));
            for ($index = 0; $index < $max; $index++) {
                $chars[$start + $index] = $itemChars[$index];
            }
        }

        return rtrim(implode('', $chars));

    } // rebuildDialogSegmentText()

    protected function hydrateDialogStructureFromTokens(array $structure, array $dialogTokens) {

        if (empty($dialogTokens) || empty($structure['segments'])) {
            return $structure;
        }

        foreach ($structure['segments'] as &$segment) {
            $segmentTokens = array();
            foreach ($dialogTokens as $token) {
                if ($token['segment_type'] !== $segment['segment_type']) {
                    continue;
                }
                if ($token['turn_id'] !== $segment['turn_id']) {
                    continue;
                }
                if ($token['compact_start'] < $segment['compact_start'] || ($token['compact_stop'] - 1) > $segment['compact_stop']) {
                    continue;
                }
                $segmentTokens[] = $token;
            }

            if (empty($segmentTokens)) {
                continue;
            }

            usort($segmentTokens, function($left, $right) {
                return intval($left['local_start']) - intval($right['local_start']);
            });

            $segment['text'] = $this->rebuildDialogSegmentText($segmentTokens, 'orth');
            $segment['compact_start'] = intval($segmentTokens[0]['compact_start']);
            $segment['compact_stop'] = intval($segmentTokens[count($segmentTokens) - 1]['compact_stop'] - 1);
        }
        unset($segment);

        foreach ($structure['turns'] as &$turn) {
            $authorSegment = null;
            $contentSegment = null;
            foreach ($structure['segments'] as $segment) {
                if ($segment['turn_id'] !== $turn['turn_id']) {
                    continue;
                }
                if ($segment['segment_type'] === 'author') {
                    $authorSegment = $segment;
                } elseif ($segment['segment_type'] === 'content') {
                    $contentSegment = $segment;
                }
            }

            if ($authorSegment !== null) {
                $turn['speaker'] = $authorSegment['text'] !== '' ? $authorSegment['text'] : null;
                $turn['raw_author'] = $authorSegment['text'] !== '' ? $authorSegment['text'] : null;
                $turn['author_compact_start'] = intval($authorSegment['compact_start']);
                $turn['author_compact_stop'] = intval($authorSegment['compact_stop']);
            }
            if ($contentSegment !== null) {
                $turn['text'] = $contentSegment['text'];
                $turn['content_compact_start'] = intval($contentSegment['compact_start']);
                $turn['content_compact_stop'] = intval($contentSegment['compact_stop']);
            }
        }
        unset($turn);

        foreach ($structure['segments'] as $segment) {
            if ($segment['segment_type'] === 'subtitle') {
                $structure['subtitle'] = $segment['text'];
                break;
            }
        }

        $subtitleTokens = array();
        foreach ($dialogTokens as $token) {
            if ($token['segment_type'] === 'subtitle') {
                $subtitleTokens[] = $token;
            }
        }
        if (!empty($subtitleTokens)) {
            usort($subtitleTokens, function($left, $right) {
                return intval($left['local_start']) - intval($right['local_start']);
            });
            $structure['subtitle'] = $this->rebuildDialogSegmentText($subtitleTokens, 'orth');
        }

        $participantSegments = array();
        foreach ($structure['segments'] as $segment) {
            if ($segment['segment_type'] === 'participant') {
                $participantSegments[] = $segment['text'];
            }
        }
        if (!empty($participantSegments)) {
            $structure['participants'] = $participantSegments;
        }

        return $structure;

    } // hydrateDialogStructureFromTokens()

    protected function selectPreferredSubtitleTokens(array $dialogTokens) {

        $subtitleGroups = array();
        $currentGroup = array();
        $lastLocalStart = null;

        foreach ($dialogTokens as $token) {
            if ($token['segment_type'] !== 'subtitle') {
                continue;
            }
            $localStart = isset($token['local_start']) ? intval($token['local_start']) : 0;
            if ($lastLocalStart !== null && $localStart <= $lastLocalStart) {
                if (!empty($currentGroup)) {
                    $subtitleGroups[] = $currentGroup;
                }
                $currentGroup = array();
            }
            $currentGroup[] = $token['token_id'];
            $lastLocalStart = $localStart;
        }

        if (!empty($currentGroup)) {
            $subtitleGroups[] = $currentGroup;
        }

        if (count($subtitleGroups) <= 1) {
            return $dialogTokens;
        }

        $preferredIds = array_flip($subtitleGroups[count($subtitleGroups) - 1]);
        $filtered = array();
        foreach ($dialogTokens as $token) {
            if ($token['segment_type'] === 'subtitle' && !isset($preferredIds[$token['token_id']])) {
                continue;
            }
            $filtered[] = $token;
        }

        return $filtered;

    } // selectPreferredSubtitleTokens()

    protected function buildDialogTokens(array $tokens, array $tagsByTokens, array $segments) {

        $dialogTokens = array();
        $tokenSequence = 1;

        foreach ($tokens as $token) {
            $segment = $this->findDialogSegmentForAnnotation($segments, intval($token['from']), intval($token['to']));
            if ($segment === null) {
                continue;
            }

            $localCompactStart = intval($token['from']) - intval($segment['compact_start']);
            $localCompactStop = intval($token['to']) - intval($segment['compact_start']);
            $offsetMap = $this->buildCompactToPlainOffsetMap($segment['text']);
            list($localStart, $localStop) = $this->convertOffsetsToPlainText($localCompactStart, $localCompactStop, $offsetMap);
            $tokenTags = isset($tagsByTokens[$token['token_id']]) && is_array($tagsByTokens[$token['token_id']])
                ? $tagsByTokens[$token['token_id']]
                : array();
            $primaryTag = count($tokenTags) > 0 ? $tokenTags[0] : array();

            $dialogTokens[] = array(
                'token_id' => intval($token['token_id']),
                'turn_id' => $segment['turn_id'],
                'segment_type' => $segment['segment_type'],
                'sequence_no' => $tokenSequence++,
                'orth' => $this->getUtf8TextSlice($segment['text'], $localStart, $localStop),
                'lemma' => isset($primaryTag['base']) ? $primaryTag['base'] : (isset($primaryTag['base_text']) ? $primaryTag['base_text'] : null),
                'pos' => isset($primaryTag['ctag']) ? $primaryTag['ctag'] : null,
                'compact_start' => intval($token['from']),
                'compact_stop' => intval($token['to']) + 1,
                'local_start' => intval($localStart),
                'local_stop' => intval($localStop),
                'eos' => isset($token['eos']) ? boolval($token['eos']) : false
            );
        }

        return $dialogTokens;

    } // buildDialogTokens()

    protected function buildMetadataPayload($report, $reportExt) {

        $metadata = array();
        $metadataTypes = array();
        $baseFields = array(
            'title' => 'STRING',
            'source' => 'STRING',
            'author' => 'STRING',
            'date' => 'DATE',
            'filename' => 'STRING',
            'language' => 'STRING',
            'status' => 'STRING',
            'subcorpus_id' => 'INTEGER'
        );

        foreach ($baseFields as $field => $type) {
            if (isset($report[$field]) && $report[$field] !== null && $report[$field] !== '') {
                $metadata[$field] = $report[$field];
                $metadataTypes[$field] = $type;
            }
        }

        if (!isset($report['corpora'])) {
            return array($metadata, $metadataTypes);
        }

        $extTable = DbCorpus::getCorpusExtTable($report['corpora']);
        if (!$extTable || !is_array($reportExt)) {
            return array($metadata, $metadataTypes);
        }

        global $db;
        $columns = $db->fetch_rows("SHOW FULL COLUMNS FROM $extTable WHERE `key` <> 'PRI'");
        foreach ($columns as $column) {
            if (!isset($column['Field'])) {
                continue;
            }
            $fieldName = $column['Field'];
            if (!array_key_exists($fieldName, $reportExt)) {
                continue;
            }
            $value = $reportExt[$fieldName];
            if ($value === null || $value === '') {
                continue;
            }
            $metadata[$fieldName] = $value;
            $metadataTypes[$fieldName] = $this->mapMysqlTypeToMetadataType(isset($column['Type']) ? $column['Type'] : '');
        }

        return array($metadata, $metadataTypes);

    } // buildMetadataPayload()

    protected function mapMysqlTypeToMetadataType($mysqlType) {

        $type = strtolower((string)$mysqlType);
        if (strpos($type, 'date') === 0 || strpos($type, 'timestamp') === 0 || strpos($type, 'datetime') === 0) {
            return 'DATE';
        }
        if (strpos($type, 'int') !== false || strpos($type, 'bigint') !== false || strpos($type, 'smallint') !== false) {
            return 'INTEGER';
        }
        if (strpos($type, 'float') !== false || strpos($type, 'double') !== false || strpos($type, 'decimal') !== false) {
            return 'DOUBLE';
        }
        if (strpos($type, 'bool') !== false || strpos($type, 'tinyint(1)') !== false) {
            return 'BOOLEAN';
        }
        return 'STRING';

    } // mapMysqlTypeToMetadataType()

    protected function normalizeAttributeMapByAnnotationId(array $attributes) {

        $attributesByAnnotationId = array();
        foreach ($attributes as $attribute) {
            if (!isset($attribute['id']) || !isset($attribute['name'])) {
                continue;
            }
            $annotationId = intval($attribute['id']);
            if (!isset($attributesByAnnotationId[$annotationId])) {
                $attributesByAnnotationId[$annotationId] = array();
            }
            $attributesByAnnotationId[$annotationId][strtolower($attribute['name'])] =
                isset($attribute['value']) ? $attribute['value'] : null;
        }

        return $attributesByAnnotationId;

    } // normalizeAttributeMapByAnnotationId()

    protected function getFirstExistingValue(array $values, array $keys, $default = null) {

        foreach ($keys as $key) {
            if (array_key_exists($key, $values) && $values[$key] !== null && $values[$key] !== '') {
                return $values[$key];
            }
        }

        return $default;

    } // getFirstExistingValue()

    protected function toNullableJson($value) {

        if (!is_array($value) || empty($value)) {
            return null;
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE);

    } // toNullableJson()

    protected function buildSentenceTokenLengths($ccl) {

        $lengths = array();
        foreach ($ccl->chunks as $chunk) {
            foreach ($chunk->sentences as $sentence) {
                $lengths[] = count($sentence->tokens);
            }
        }
        return $lengths;

    } // buildSentenceTokenLengths()

    protected function buildTokenColumns($ccl) {

        $lemmaTokens = array();
        $surfaceTokens = array();
        $posTokens = array();

        foreach ($ccl->chunks as $chunk) {
            foreach ($chunk->sentences as $sentence) {
                foreach ($sentence->tokens as $token) {
                    $surfaceTokens[] = $token->orth;
                    if (isset($token->lexemes[0])) {
                        $lemmaTokens[] = isset($token->lexemes[0]->base) ? $token->lexemes[0]->base : '';
                        $posTokens[] = isset($token->lexemes[0]->ctag) ? $token->lexemes[0]->ctag : '';
                    } else {
                        $lemmaTokens[] = '';
                        $posTokens[] = '';
                    }
                }
            }
        }

        return array($lemmaTokens, $surfaceTokens, $posTokens);

    } // buildTokenColumns()

    protected function buildSchemaNers(array $annotations, $plainText, array $offsetMap) {

        $ners = array();
        foreach ($annotations as $annotation) {
            if (!isset($annotation['type']) || strpos($annotation['type'], 'nam_') !== 0) {
                continue;
            }
            list($start, $stop) = $this->convertOffsetsToPlainText($annotation['from'], $annotation['to'], $offsetMap);
            $ners[] = array(
                'lexem' => $this->getUtf8TextSlice($plainText, $start, $stop),
                'ner_type' => $annotation['type']
            );
        }

        return $ners;

    } // buildSchemaNers()

    protected function buildSchemaSenseLinks() {

        return array();

    } // buildSchemaSenseLinks()

    protected function buildSchemaGeolocations() {

        return array();

    } // buildSchemaGeolocations()

    protected function buildSchemaAnnotations(array $annotations, array $attributesByAnnotationId, $plainText, array $offsetMap) {

        $exportedAnnotations = array();
        foreach ($annotations as $annotation) {
            list($start, $stop) = $this->convertOffsetsToPlainText($annotation['from'], $annotation['to'], $offsetMap);
            $annotationId = intval($annotation['id']);
            $exportedAnnotations[] = array(
                'obj_id' => $annotationId,
                'start' => $start,
                'stop' => $stop,
                'text' => $this->getUtf8TextSlice($plainText, $start, $stop),
                'annotation_set' => isset($annotation['group']) ? $this->getAnnotationSetDisplayName($annotation['group']) : null,
                'annotation_type' => isset($annotation['type']) ? $annotation['type'] : '',
                'lemma' => isset($annotation['lemma']) && $annotation['lemma'] !== '' ? $annotation['lemma'] : null,
                'stage' => isset($annotation['stage']) && $annotation['stage'] !== '' ? $annotation['stage'] : null,
                'source' => isset($annotation['source']) && $annotation['source'] !== '' ? $annotation['source'] : null,
                'attributes_json' => isset($attributesByAnnotationId[$annotationId])
                    ? $this->toNullableJson($attributesByAnnotationId[$annotationId])
                    : null
            );
        }

        return $exportedAnnotations;

    } // buildSchemaAnnotations()

    protected function buildSchemaRelations(array $relations) {

        $exportedRelations = array();
        foreach ($relations as $relation) {
            $exportedRelations[] = array(
                'obj_id' => intval($relation['id']),
                'relation_set' => isset($relation['rsname']) && $relation['rsname'] !== '' ? $relation['rsname'] : null,
                'relation_type' => isset($relation['name']) ? $relation['name'] : '',
                'source_obj_id' => intval($relation['source_id']),
                'target_obj_id' => intval($relation['target_id'])
            );
        }

        return $exportedRelations;

    } // buildSchemaRelations()

    public function buildClarinJsonlZstDocument($ccl, $relations, $annotations, $tokensIds, $report, $reportExt, $plainText, $attributes = array()) {

        $offsetMap = $this->buildCompactToPlainOffsetMap($plainText);
        list($metadata, $metadataTypes) = $this->buildMetadataPayload($report, $reportExt);
        list($lemmaTokens, $surfaceTokens, $posTokens) = $this->buildTokenColumns($ccl);
        $attributesByAnnotationId = $this->normalizeAttributeMapByAnnotationId($attributes);
        $docId = isset($report['filename']) && $report['filename'] !== '' ? $report['filename'] : $ccl->getFileName();

        return array(
            'doc_id' => $docId !== '' ? (string)$docId : null,
            'metadata_json' => empty($metadata) ? null : json_encode($metadata, JSON_UNESCAPED_UNICODE),
            'metadata_types_json' => empty($metadataTypes) ? null : json_encode($metadataTypes, JSON_UNESCAPED_UNICODE),
            'lemma_tokens' => $lemmaTokens,
            'surface_tokens' => $surfaceTokens,
            'pos_tokens' => $posTokens,
            'sentence_token_lens' => $this->buildSentenceTokenLengths($ccl),
            'ners' => $this->buildSchemaNers($annotations, $plainText, $offsetMap),
            'sense_links' => $this->buildSchemaSenseLinks(),
            'geolocations' => $this->buildSchemaGeolocations(),
            'annotations' => $this->buildSchemaAnnotations($annotations, $attributesByAnnotationId, $plainText, $offsetMap),
            'relations' => $this->buildSchemaRelations($relations)
        );

    } // buildClarinJsonlZstDocument()

    public function buildDialogParquetDocument($report, $reportExt, $annotations, $relations, $attributes = array(), $tokens = array(), $tagsByTokens = array()) {

        list($metadata, $metadataTypes) = $this->buildMetadataPayload($report, $reportExt);
        $docId = isset($report['filename']) && $report['filename'] !== '' ? $report['filename'] : str_pad(isset($report['id']) ? $report['id'] : '', 8, '0', STR_PAD_LEFT);
        $dialogId = $docId !== '' ? (string)$docId : ('dialog-' . (isset($report['id']) ? intval($report['id']) : 0));
        $structure = $this->parseDialogStructure(isset($report['content']) ? $report['content'] : '', $dialogId);
        $dialogTokens = $this->buildDialogTokens($tokens, $tagsByTokens, $structure['segments']);
        $dialogTokens = $this->selectPreferredSubtitleTokens($dialogTokens);
        $structure = $this->hydrateDialogStructureFromTokens($structure, $dialogTokens);
        list($dialogAnnotations, $annotationContext) = $this->buildDialogAnnotations($annotations, $attributes, $structure['segments']);

        return array(
            'doc_id' => $docId !== '' ? (string)$docId : null,
            'dialog_id' => $dialogId,
            'subtitle' => $structure['subtitle'],
            'participants' => $structure['participants'],
            'metadata_json' => empty($metadata) ? null : json_encode($metadata, JSON_UNESCAPED_UNICODE),
            'metadata_types_json' => empty($metadataTypes) ? null : json_encode($metadataTypes, JSON_UNESCAPED_UNICODE),
            'turns' => $structure['turns'],
            'tokens' => $dialogTokens,
            'annotations' => $dialogAnnotations,
            'relations' => $this->buildDialogRelations($relations, $annotationContext)
        );

    } // buildDialogParquetDocument()

    protected function wrapSentenceSpans(array $sentences) {

        return array(
            'sentence' => array(
                'meta' => array(
                    'kind' => 'sentence',
                    'count' => count($sentences)
                ),
                'items' => $sentences
            )
        );

    } // wrapSentenceSpans()

    protected function buildClarinJsonDocument($ccl, $relations, $annotations, $tokensIds, $tagsByTokens, $report, $reportExt, $plainText, $taggingMethod, $attributes, $exportFormat) {

        $offsetMap = $this->buildCompactToPlainOffsetMap($plainText);
        $spans = $this->buildClarinSpans($annotations, $attributes, $offsetMap, $plainText);
        $annotationItems = array();
        foreach ($spans as $layerDefinition) {
            $annotationItems = array_merge($annotationItems, $layerDefinition['items']);
        }
        $sentenceItems = $this->normalizeSentenceSpans(
            $this->buildSentenceSpans($ccl, $offsetMap),
            $annotationItems
        );
        $spans = array_merge($spans, $this->wrapSentenceSpans($sentenceItems));
        $relationLayers = $this->buildClarinRelations($relations);

        $fileName = $ccl->getFileName();
        $label = isset($report['title']) && trim($report['title']) !== ''
            ? $report['title']
            : (isset($report['filename']) ? $report['filename'] : $fileName);

        $metadata = $this->buildMetadata($report, $reportExt, $taggingMethod, $exportFormat);
        $metadata['layer_order'] = array(
            'spans' => array_keys($spans),
            'relations' => array_keys($relationLayers)
        );

        return array(
            'id' => (string)$fileName,
            'label' => $label,
            'metadata' => $metadata,
            'text' => (string)$plainText,
            'tagset' => $this->detectTagsetName($tagsByTokens),
            'tokens' => $this->buildClarinTokens($ccl, $tokensIds, $offsetMap),
            'spans' => $spans,
            'relations' => $relationLayers,
            'records' => new stdClass(),
            'filename' => (string)$fileName
        );

    } // buildClarinJsonDocument()

    public function exportToConllAndJson($file_path_without_ext, $ccl, $tokens, $relations, $annotations, $tokens_ids, $annotations_by_id, $lemmas, $attributes = array(), $options = array())
    {
        $fw = new FileWriter();
        $exportFormat = isset($options['format']) ? $options['format'] : self::FORMAT_LEGACY;

        if ($exportFormat === self::FORMAT_CLARIN_JSON) {
            $clarinJson = $this->buildClarinJsonDocument(
                $ccl,
                $relations,
                $annotations,
                $tokens_ids,
                isset($options['tags_by_tokens']) ? $options['tags_by_tokens'] : array(),
                isset($options['report']) ? $options['report'] : array(),
                isset($options['report_ext']) ? $options['report_ext'] : null,
                isset($options['plain_text']) ? $options['plain_text'] : '',
                isset($options['tagging_method']) ? $options['tagging_method'] : 'tagger',
                $attributes,
                $exportFormat
            );
            $fw->writeJSONToFile($file_path_without_ext . '.json', $clarinJson);
            return;
        }

        list($conll,$json_builder) = $this->makeConllAndJsonExportData($ccl, $tokens, $relations, $annotations, $tokens_ids, $annotations_by_id, $lemmas);
        if ($exportFormat === self::FORMAT_CONLLU_STANDARD) {
            $standardConllu = $this->buildStandardConllu(
                $ccl,
                $tokens,
                $relations,
                $annotations,
                $tokens_ids,
                $annotations_by_id,
                isset($options['plain_text']) ? $options['plain_text'] : ''
            );
            $fw->writeTextToFile($file_path_without_ext . '.conllu',$standardConllu);
            return;
        }

        if ($exportFormat === self::FORMAT_CONLLU_CLARIN) {
            $fw->writeTextToFile($file_path_without_ext . '.conllu',$conll);
            return;
        }

        if ($exportFormat !== self::FORMAT_LEGACY) {
            $fw->writeJSONToFile($file_path_without_ext . '.json',$json_builder);
        }

    } // exportToConllAndJson()

} // ConllAndJsonFactory class
