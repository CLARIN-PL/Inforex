<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class DbRelationAgreement{

    static function getUserRelations($corpus_id, $subcorpus_ids, $annotation_types, $relation_types, $flag, $user){
        global $db;
        ChromePhp::log(func_get_args());

        if($relation_types == null || $annotation_types == null){
            return null;
        }

        $where_sql = array();
        $params = array();
        $where_params = array();

        $sql = "SELECT DISTINCT r.id, a_source.text AS 'source_text', a_target.text AS 'target_text', 
                a_source.report_id, at_source.name AS 'source_name', at_target.name AS 'target_name', 
                a_source.from AS 'source_from', a_source.to AS 'source_to',
                a_target.from AS 'target_from', a_target.to AS 'target_to',
                a_source.id AS 'source_id', a_target.id AS 'target_id',
                r.relation_type_id, u.screename, rt.name
                FROM users u
                JOIN relations r ON r.user_id = u.user_id
                JOIN relation_types rt ON rt.id = r.relation_type_id
                JOIN reports_annotations_optimized a_source ON a_source.id = r.source_id
                JOIN reports_annotations_optimized a_target ON a_target.id = r.target_id
                JOIN annotation_types at_source ON at_source.annotation_type_id = a_source.type_id
                JOIN annotation_types at_target ON at_target.annotation_type_id = a_target.type_id
                JOIN reports rps ON rps.id = a_source.report_id";

        if($user != null){
            $where_sql[] = "u.user_id = ?";
            $where_params[] = $user;
        }

        if($annotation_types != null){
            $anns_imploded = implode(",", array_fill(0, count($annotation_types), "?"));
            $where_sql[] = "a_source.type_id IN (".$anns_imploded.") AND a_target.type_id IN (".$anns_imploded.")";
            $where_params = array_merge($where_params, $annotation_types, $annotation_types);
        }

        if($relation_types != null){
            $rels_imploded = implode(",", array_fill(0, count($relation_types), "?"));
            $where_sql[] = "r.relation_type_id IN (".$rels_imploded.")";
            $where_params = array_merge($where_params, $relation_types);
        }

        if($corpus_id != null){
            ChromePhp::log($corpus_id);
            $where_sql[] = "rps.corpora = ?";
            $where_params[] = $corpus_id;
        }


        if(!empty($subcorpus_ids) && $corpus_id != null){
            $subcorpora_ids_str = implode(",", array_fill(0, count($subcorpus_ids), "?"));
            $where_sql[] = "rps.subcorpus_id IN (".$subcorpora_ids_str.")";
            $where_params = array_merge($where_params, $subcorpus_ids);
        }

        if($flag != null){
            $sql .= " LEFT JOIN reports_flags rf ON (rf.report_id = rps.id AND rf.corpora_flag_id = ?)";
            $where_sql[] = " rf.flag_id = ? ";
            $params[] = intval($flag['corpus_flag_id']);
            $where_params[] = intval($flag['flag_id']);
        }

        if ( count($where_sql) > 0 ){
            $sql .= " WHERE (" . implode(" AND ", $where_sql) . ")";
        }

        $sql .= "";


        $params = array_merge($params, $where_params);

        $relations = $db->fetch_rows($sql, $params);
        $relations = self::reportIdAsKey($relations);

        return $relations;
    }

    static function getRelationsAgreement($corpus_id, $subcorpus_ids, $annotation_types, $relation_types, $flag, $users){
        ChromePhp::log("Arguments");
        ChromePhp::log(func_get_args());
        $user_a_relations = self::getUserRelations($corpus_id, $subcorpus_ids, $annotation_types, $relation_types, $flag, $users['a']);
        $user_b_relations = self::getUserRelations($corpus_id, $subcorpus_ids, $annotation_types, $relation_types, $flag, $users['b']);
        $relations_compared = self::compareUserRelations($user_a_relations, $user_b_relations);

        $agreement = self::calculateAgreement($relations_compared);

        ChromePhp::log($relations_compared);

        $relation_agreement = array(
            'relations_compared' => $relations_compared,
            'pcs' => $agreement
        );
        return $relation_agreement;
    }

    static function getUserRelationCount($corpus_id, $subcorpus_ids, $annotation_types, $relation_types, $flag){
        global $db;
        $where_sql = array();
        $where_params = array();
        $params = array();

        if($annotation_types == null || $rels_imploded = null){
            return array();
        }

        $sql = "SELECT u.*, COUNT(DISTINCT r.id) AS 'relation_count', COUNT(DISTINCT rps.id) AS 'document_count'
                FROM users u
                JOIN relations r ON r.user_id = u.user_id
                JOIN relation_types rt ON rt.id = r.relation_type_id
                JOIN reports_annotations_optimized a_source ON a_source.id = r.source_id
                JOIN reports_annotations_optimized a_target ON a_target.id = r.target_id
                JOIN annotation_types at_source ON at_source.annotation_type_id = a_source.type_id
                JOIN annotation_types at_target ON at_target.annotation_type_id = a_target.type_id
                JOIN reports rps ON rps.id = a_source.report_id";

        if($annotation_types != null){
            $anns_imploded = implode(",", array_fill(0, count($annotation_types), "?"));
            $where_sql[] = "a_source.type_id IN (".$anns_imploded.") AND a_target.type_id IN (".$anns_imploded.")";
            $where_params = array_merge($where_params, $annotation_types, $annotation_types);
        }

        if(!empty($subcorpus_ids) && $corpus_id != null){
            $subcorpora_ids_str = implode(",", array_fill(0, count($subcorpus_ids), "?"));
            $where_sql[] = "rps.subcorpus_id IN (".$subcorpora_ids_str.")";
            $where_params = array_merge($where_params, $subcorpus_ids);
        }

        if($flag != null){
            $sql .= " LEFT JOIN reports_flags rf ON (rf.report_id = rps.id AND rf.corpora_flag_id = ?)";
            $where_sql[] = " rf.flag_id = ? ";
            $params[] = intval($flag['corpus_flag_id']);
            $where_params[] = intval($flag['flag_id']);
        }


        if($relation_types != null){
            $rels_imploded = implode(",", array_fill(0, count($relation_types), "?"));
            $where_sql[] = "r.relation_type_id IN (".$rels_imploded.")";
            $where_params = array_merge($where_params, $relation_types);
        }

        if($corpus_id != null){
            $where_sql[] = "rps.corpora = ?";
            $where_params[] = $corpus_id;
        }


        if ( count($where_sql) > 0 ){
            $sql .= " WHERE (" . implode(" AND ", $where_sql) . ")";
        }

        $sql.= " GROUP BY u.user_id ";
        $sql.= " ";
        $params = array_merge($params, $where_params);
        $relations = $db->fetch_rows($sql, $params);

        return $relations;
    }


    static function reportIdAsKey($user_relations){
        $formatted_relations = array();
        foreach($user_relations as $user_relation){
            if($user_relation['report_id'] == 100506){
                ChromePhp::log($user_relation);
            }
            $key = $user_relation['source_from'] . '_' . $user_relation['source_to'] . '/' .$user_relation['target_from'] . '_' . $user_relation['target_to'];
            $formatted_relations[$user_relation['report_id']][$key][$user_relation['relation_type_id']] = $user_relation;
            $formatted_relations[$user_relation['report_id']][$key]['data']['source_bounds'] = $user_relation['source_from'] . "," . $user_relation['source_to'];
            $formatted_relations[$user_relation['report_id']][$key]['data']['target_bounds'] = $user_relation['target_from'] . "," . $user_relation['target_to'];
            $formatted_relations[$user_relation['report_id']][$key]['data']['source_name'] = $user_relation['source_name'];
            $formatted_relations[$user_relation['report_id']][$key]['data']['source_text'] = $user_relation['source_text'];
            $formatted_relations[$user_relation['report_id']][$key]['data']['target_name'] = $user_relation['target_name'];
            $formatted_relations[$user_relation['report_id']][$key]['data']['target_text'] = $user_relation['target_text'];

        }

        return $formatted_relations;
    }

    static function compareUserRelations($user_a_relations, $user_b_relations){
        $all_relations = array();
        if($user_a_relations != null){
            foreach($user_a_relations as $report_id => $a_relation){
                foreach($a_relation as $source_from => $a){
                    if(!isset($all_relations[$report_id][$source_from]['data'])){
                        $all_relations[$report_id][$source_from]['data'] = $a['data'];
                    }
                    unset($a['data']);

                    $all_relations[$report_id][$source_from]['a'] = $a;


                }
            }
        }

        if($user_b_relations != null){
            foreach($user_b_relations as $report_id => $b_relation){
                foreach($b_relation as $source_from => $b){
                    if(!isset($all_relations[$report_id][$source_from]['data'])){
                        $all_relations[$report_id][$source_from]['data'] = $b['data'];
                    }
                    unset($b['data']);

                    $all_relations[$report_id][$source_from]['b'] = $b;
                }
            }
        }

        foreach($all_relations as $report_id => $report){
            foreach($report as $source_from => $relation){
                if(isset($relation['a']) && isset($relation['b'])){
                    $all_relations[$report_id][$source_from]['a_and_b'] = array_intersect_key($all_relations[$report_id][$source_from]['a'], $all_relations[$report_id][$source_from]['b']);
                    $only_a = array_diff_key($all_relations[$report_id][$source_from]['a'], $all_relations[$report_id][$source_from]['b']);
                    $only_b = array_diff_key($all_relations[$report_id][$source_from]['b'], $all_relations[$report_id][$source_from]['a']);

                    if($only_a != null){
                        $all_relations[$report_id][$source_from]['a'] = $only_a;
                    } else{
                        unset($all_relations[$report_id][$source_from]['a']);
                    }

                    if($only_b != null){
                        $all_relations[$report_id][$source_from]['b'] = $only_b;
                    } else{
                        unset($all_relations[$report_id][$source_from]['b']);
                    }
                }
            }
        }


        ksort($all_relations);
        return $all_relations;
    }

    static function calculateAgreement($all_relations){
        $agreement = array();
        $a_count = 0;
        $b_count = 0;
        $a_b_count = 0;

        //Calculate how many times each relation is set by one user or both.
        foreach($all_relations as $report_id){
            foreach($report_id as $text){
                if(isset($text['a'])){
                    foreach($text['a'] as $type_id => $relation_a){
                        if(isset($agreement[$type_id]['only_a'])){
                            $agreement[$type_id]['only_a']+= 1;
                        } else{
                            $agreement[$type_id]['only_a'] = 1;
                            $agreement[$type_id]['name'] = $relation_a['name'];
                        }
                        $a_count++;
                    }
                }

                if(isset($text['b'])){
                    foreach($text['b'] as $type_id => $relation_b){
                        if(isset($agreement[$type_id]['only_b'])){
                            $agreement[$type_id]['only_b']+= 1;
                        } else{
                            $agreement[$type_id]['only_b'] = 1;
                            $agreement[$type_id]['name'] = $relation_b['name'];
                        }
                        $b_count++;
                    }
                }

                if(isset($text['a_and_b'])){
                    foreach($text['a_and_b'] as $type_id => $relation_a_b){
                        if(isset($agreement[$type_id]['a_and_b'])){
                            $agreement[$type_id]['a_and_b']+= 1;
                        } else{
                            $agreement[$type_id]['a_and_b'] = 1;
                            $agreement[$type_id]['name'] = $relation_a_b['name'];

                        }
                        $a_b_count++;
                    }
                }
            }
        }

        //Calculate PCS
        foreach($agreement as $key => $relation_type_id){
            if($relation_type_id['only_a'] != null){
                $only_a = $relation_type_id['only_a'];
            } else{
                $only_a = 0;
            }

            if($relation_type_id['only_b'] != null){
                $only_b = $relation_type_id['only_b'];
            } else{
                $only_b = 0;
            }

            if($relation_type_id['a_and_b'] != null){
                $a_and_b = $relation_type_id['a_and_b'];
            } else{
                $a_and_b = 0;
            }

            $agreement[$key]['pcs'] = $a_and_b *200.0/(2.0*$a_and_b+$only_a+$only_b);
        }

        if(count($agreement)){
            //Add row for ALL statistics
            $agreement['all']['only_a'] = $a_count;
            $agreement['all']['only_b'] = $b_count;
            $agreement['all']['a_and_b'] = $a_b_count;
            $agreement['all']['pcs'] = $a_b_count*200.0/(2.0*$a_b_count+$a_count+$b_count);

        }

        return $agreement;
    }
}
