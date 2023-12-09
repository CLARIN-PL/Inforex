<?php

class ConllAndJsonFactory {

    protected function makeConllAndJsonExportData($ccl, $tokens, $relations, $annotations, $tokens_ids, $annotations_by_id) {

        /**
         * Create a cache for 'token from' to boost processing
         */
        $cache_tokens_from = [];
        foreach ($tokens as $token) {
            $cache_tokens_from += [$token['from'] => [
                "to" => $token['to'],
                "id" => $token['token_id']
            ]];
        }

        /**
         * Cache TOEKN_ID => [ANNOTATIONS_IDS]
         */
        $annotations_token_id = [];
        foreach ($annotations as $annotation) {
            $annotation_from = $annotation['from'];
            $annotation_to = $annotation['to'];
            $start = true;
            foreach ($cache_tokens_from as $from => $value) {
                $to = $value['to'];
                $id = $value['id'];
                if ($from >= $annotation_from and $to <= $annotation_to) {
                    $iob = $start ? "B-" : "I-";
                    $start = false;
                    if (array_key_exists($id, $annotations_token_id)) {
                        $annotations_token_id[$id][] = array(
                            "ann_id" => $annotation['id'],
                             "iob" => $iob
                        );
                    } else {
                        $annotations_token_id += [
                            $id => [array(
                                "ann_id" => $annotation['id'],
                                "iob" => $iob
                            )]
                        ];
                    }
                }
            }
        }

        /**
         * Cache ANNOTATION_ID => [RELATIONS]
         */
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

        /**
         * ORDER_ID - order of the token in the document
         * TOKEN_ID - order of the token in the sentence
         * ORHT - orth of the token
         * CTAG - first ctag from token
         * FROM - offset start in text for the token
         * TO - offset end in text for the token
         * ANN_TAGS - Array of Annotations given for the token, joined by ":"
         * ANN_IDS - Array of Annotation IDs for the token, preserving order ANN_TAGS, joined by ":"
         * REL_IDS - Array of Relations IDs for the token, joined by ":"
         * REL_TARGET_ANN_IDS - Array of references for target Annotation ID for relation, "_" otherwise if an annotation is a target, preserving order REL_IDS, joined by ":"
         */
        $conll = "";
        $conll .= "ORDER_ID\tTOKEN_ID\tORTH\tCTAG\tFROM\tTO\tANN_TAGS\tANN_IDS\tREL_IDS\tREL_TARGET_ANN_IDS\n";
        $json_builder = [
            "chunks" => [],
            "relations" => array_values($relations),
            "annotations" => $annotations,
        ];
        $it = 0;
        foreach ($ccl->chunks as $chunk) {
            $json_sentences = [];
            foreach ($chunk->sentences as $sentence) {
                $json_sentence = [];
                $id = 0;
                foreach ($sentence->tokens as $token) {
                    $original_id = $tokens_ids[$it++];
                    $ann_tag = [];
                    $ann_id = [];
                    $rel_id = [];
                    $rel_target_id = [];
                    if (array_key_exists($original_id, $annotations_token_id)) {
                        $annotations_for_id = $annotations_token_id[$original_id];
                        foreach ($annotations_for_id as $annotations_from_cache) {
                            $annotations_from_cache_id = $annotations_from_cache["ann_id"];
                            $iob = $annotations_from_cache["iob"];

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
                    $json_sentence[] = array(
                        "order_id" => $token->id,
                        "token_id" => $token_id,
                        "orth" => $token->orth,
                        "ctag" => $token->lexemes[0]->ctag,
                        "from" => $token->from,
                        "to"  => $token->to,
                        "annotations" => $ann_id,
                        "relations" => $rel_id
                    );

                    if (empty($ann_tag)) {
                        $ann_tag += ["O"];
                    }

                    $arrays_to_check = [&$ann_id, &$rel_id, &$rel_target_id];
                    foreach ($arrays_to_check as &$array_to_check) {
                        if (empty($array_to_check)) {
                            $array_to_check += ["_"];
                        }
                    }
                    $conll .= $token->id . "\t" . $token_id . "\t" . $token->orth . "\t" . $token->lexemes[0]->ctag . "\t" . $token->from . "\t" .
                        $token->to . "\t" . join(":", $ann_tag) . "\t" . join(":", $ann_id) . "\t" .
                        join(":", $rel_id) . "\t" . join(":", $rel_target_id) . "\n";

                }
                $conll .= "\n";
                $json_sentences[] = $json_sentence;
            }
            $json_builder["chunks"][] = $json_sentences;
        }

        return array($conll,$json_builder);

    } // makeConllAndJsonExportData()

    public function exportToConllAndJson($file_path_without_ext, $ccl, $tokens, $relations, $annotations, $tokens_ids, $annotations_by_id)
    {
        list($conll,$json_builder) = $this->makeConllAndJsonExportData($ccl, $tokens, $relations, $annotations, $tokens_ids, $annotations_by_id);
        $fw = new FileWriter();
        $fw->writeTextToFile($file_path_without_ext . ".conll",$conll);
        $fw->writeJSONToFile($file_path_without_ext . ".json",$json_builder);

    } // exportToConllAndJson()

} // ConllAndJsonFactory class
