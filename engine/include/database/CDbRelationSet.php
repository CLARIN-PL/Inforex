<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class DbRelationSet{

	/**
	 * Returns a list of relation sets assigned to a corpus with $corpusId id.
	 * @param int $corpusId
	 * @return An array of annotation schemas.
	 */
	static function getRelationSetsAssignedToCorpus($corpusId){
		global $db;		
		$sql = "SELECT * FROM relation_sets rs 
                JOIN corpora_relations cr ON cr.relation_set_id = rs.relation_set_id AND cr.corpus_id = ? ";
		$relation_sets = $db->fetch_rows($sql, $corpusId);

		return $relation_sets;
	}

    /**
     * Returns a list of relation types attached to a given relation set.
     * @param $relation_set_id
     * @param $report_id
     */
	static function getRelationTypesOfSet($relation_set_id, $report_id = null){
        global $db;
        $sql = "SELECT rt.* FROM relation_types rt
                WHERE rt.relation_set_id = ?";
        $params = array($relation_set_id);

        $relation_types = $db->fetch_rows($sql, $params);
        if($report_id != null){
            $report_sql = "AND rao.report_id = ?";
        }

        $sql = "SELECT r.*, COUNT(r.id) AS 'number_of_types' FROM relation_types rt
                LEFT JOIN relations r ON r.relation_type_id = rt.id 
                LEFT JOIN reports_annotations_optimized rao ON (rao.id = r.source_id OR rao.id = r.target_id)
                WHERE (rt.relation_set_id = ? ".$report_sql." AND r.stage = 'agreement' AND rao.stage = 'final')
                GROUP BY r.id";
        $params = array($relation_set_id);
        if($report_id != null){
            $params[] = $report_id;
        }
        $relation_types_counted = $db->fetch_rows($sql, $params);

        $relation_types_used = 0;
        foreach($relation_types_counted as $relation_type){
            $relation_types_used += $relation_type['number_of_types'];
        }

        $relation_types['number_of_uses'] = $relation_types_used/2;
        return $relation_types;
    }

    /**
     * Returns a relation tree consisting of relation sets and their relation types.
     * @param $corpus_id
     */
	static function getRelationTree($corpus_id, $report_id = null){
        $annotation_tree = array();
        $relation_sets = self::getRelationSetsAssignedToCorpus($corpus_id);

        foreach($relation_sets as $relation_set){
            $relation_set_id = $relation_set['relation_set_id'];
            $relation_set_name = $relation_set['name'];


            $annotation_tree[$relation_set_id] = self::getRelationTypesOfSet($relation_set_id, $report_id);
            $annotation_tree[$relation_set_id]['relation_set_name'] = $relation_set_name;
            $annotation_tree[$relation_set_id]['relation_set_id'] = $relation_set_id;
        }
        return $annotation_tree;
    }

    /**
     * Finds all relations with "final" status in the given report.
     * @param $report_id
     * @param $relation_types
     * @return array
     */

    static function getFinalRelations($report_id, $relation_types, $annotation_types){
        global $db;

        $sql = "SELECT r.*, rao.text AS 'source_text', rao.from AS 'source_from', rao.to AS 'source_to', rao.id AS 'annotation_source_id', 
                rao2.from AS 'target_from', rao2.to AS 'target_to', rao2.id AS 'annotation_target_id', rao2.text AS 'target_text',
                rt.name AS 'relation_name'
                FROM relations r 
                JOIN reports_annotations_optimized rao ON r.source_id = rao.id 
                JOIN reports_annotations_optimized rao2 ON r.target_id = rao2.id 
                LEFT JOIN relation_types rt ON rt.id = r.relation_type_id
                WHERE (r.relation_type_id IN (" . implode(",", array_fill(0, count($relation_types), "?")) . ")
                AND rao.type_id IN (" . implode(",", array_fill(0, count($annotation_types), "?")) . ")
                AND rao2.type_id IN (" . implode(",", array_fill(0, count($annotation_types), "?")) . ")
                AND rao.report_id = ? AND r.stage = 'final')";

        $params_constant = array(
            $report_id
        );
        $params = array_merge($relation_types, $annotation_types, $annotation_types, $params_constant);

        $relations = $db->fetch_rows($sql, $params);


        $final_relations = array();
        foreach($relations as $relation){

            $source_type_id = $relation['annotation_source_id'];
            $source_from = $relation['source_from'];
            $source_to = $relation['source_to'];

            $target_type_id = $relation['annotation_target_id'];
            $target_from = $relation['target_from'];
            $target_to = $relation['target_to'];

            $array_relation_id = $source_from . ":" . $source_to . "_" . $source_type_id . "/" . $target_from . ":" . $target_to . "_" . $target_type_id;

            //If final relations exists, add to the array of relations.
            $relation_details = array(
                'relation_type_id' => $relation['relation_type_id'],
                'relation_name' => $relation['relation_name']
            );

            $final_relations[$array_relation_id]['source_text'] = $relation['source_text'];
            $final_relations[$array_relation_id]['source_text'] = $relation['source_text'];
            $final_relations[$array_relation_id]['relation_id'] = $relation['id'];
            $final_relations[$array_relation_id]['relation_types'] = $relation_types;
            $final_relations[$array_relation_id]['target_text'] = $relation['target_text'];
            $final_relations[$array_relation_id]['source_from'] = $relation['source_from'];
            $final_relations[$array_relation_id]['source_to'] = $relation['source_to'];
            $final_relations[$array_relation_id]['annotation_source_id'] = $relation['annotation_source_id'];
            $final_relations[$array_relation_id]['target_from'] = $relation['target_from'];
            $final_relations[$array_relation_id]['target_to'] = $relation['target_to'];
            $final_relations[$array_relation_id]['annotation_target_id'] = $relation['annotation_target_id'];
            $final_relations[$array_relation_id]['user_id'] = $relation['user_id'];
            $final_relations[$array_relation_id]['stage'] = $relation['stage'];

            $final_relations[$array_relation_id]['final_relations'][] = $relation_details;
        }

        return $final_relations;
    }

    /**
     *
     * Finds relations made by the given user (in agreement mode). Compares the relations with the list of
     * all final relations for given document. If a final relation exists, it is added to the 'final' element of the array.
     * @param $report_id
     * @param $relation_types
     * @param $user_id
     * @param $annotation_types
     * @param $final_relations
     * @return array
     */
    private function getUserRelations($report_id, $relation_types, $user_id, $final_relations, $annotation_types){
        global $db;

        $sql = "SELECT r.*, rao.text AS 'source_text', rao.from AS 'source_from', rao.to AS 'source_to', rao.id AS 'annotation_source_id', rao.type_id AS 'annotation_source_type_id', at1.name AS 'annotation_source_name',
                rao2.from AS 'target_from', rao2.type_id AS 'annotation_target_type_id', at2.name AS 'annotation_target_name', rao2.to AS 'target_to', rao2.id AS 'annotation_target_id', rao2.text AS 'target_text',
                rt.name AS 'relation_name'
                FROM relations r 
                JOIN reports_annotations_optimized rao ON r.source_id = rao.id 
                JOIN reports_annotations_optimized rao2 ON r.target_id = rao2.id 
                LEFT JOIN annotation_types at1 ON at1.annotation_type_id = rao.type_id
                LEFT JOIN annotation_types at2 ON at2.annotation_type_id = rao2.type_id
                LEFT JOIN relation_types rt ON rt.id = r.relation_type_id
                WHERE (r.relation_type_id IN (" . implode(",", array_fill(0, count($relation_types), "?")) . ")
                AND rao.type_id IN (" . implode(",", array_fill(0, count($annotation_types), "?")) . ")
                AND rao.stage = 'final'
                AND rao2.type_id IN (" . implode(",", array_fill(0, count($annotation_types), "?")) . ")
                AND rao2.stage = 'final'
                AND r.user_id = ?  AND rao.report_id = ? AND r.stage = 'agreement')";

        $params_constant = array(
            $user_id,
            $report_id
        );
        $params = array_merge($relation_types, $annotation_types, $annotation_types, $params_constant);

        $relations = $db->fetch_rows($sql, $params);

        $user_relations = array();
        foreach($relations as $relation){
            $source_type_id = $relation['annotation_source_id'];
            $annotation_source_type_id = $relation['annotation_source_type_id'];
            $annotation_target_type_id = $relation['annotation_target_type_id'];
            $source_from = $relation['source_from'];
            $source_to = $relation['source_to'];

            $target_type_id = $relation['annotation_target_id'];
            $target_from = $relation['target_from'];
            $target_to = $relation['target_to'];

            $possible_relation_types = self::getRelationsBetweenAnnotations($annotation_source_type_id, $annotation_target_type_id, $relation_types);

            $array_relation_id = $source_from . ":" . $source_to . "_" . $source_type_id . "/" . $target_from . ":" . $target_to . "_" . $target_type_id;

            //Add each relation to an array with a special key format.
            //key format: sourceFrom:sourceTo_sourceRelationTypeID/targetFrom:targetTo_targetRelationTypeID
            //e.g. 49:65_13/241:65_17
            if(isset($user_relations[$array_relation_id])){
                $user_relations[$array_relation_id]['user_relations'][]['relation_type_id'] = $relation['relation_type_id'];
                $new_key = max(array_keys($user_relations[$array_relation_id]['user_relations']));
                $user_relations[$array_relation_id]['user_relations'][$new_key]['relation_name'] = $relation['relation_name'];
            } else{
                $user_relations[$array_relation_id]['relation_id'] = $relation['id'];
                $user_relations[$array_relation_id]['source_text'] = $relation['source_text'];
                $user_relations[$array_relation_id]['relation_types'] = $possible_relation_types;
                $user_relations[$array_relation_id]['target_text'] = $relation['target_text'];
                $user_relations[$array_relation_id]['source_from'] = $relation['source_from'];
                $user_relations[$array_relation_id]['source_to'] = $relation['source_to'];
                $user_relations[$array_relation_id]['target_from'] = $relation['target_from'];
                $user_relations[$array_relation_id]['target_to'] = $relation['target_to'];
                $user_relations[$array_relation_id]['user_id'] = $relation['user_id'];
                $user_relations[$array_relation_id]['annotation_source_id'] = $relation['annotation_source_id'];
                $user_relations[$array_relation_id]['annotation_source_name'] = $relation['annotation_source_name'];
                $user_relations[$array_relation_id]['annotation_target_name'] = $relation['annotation_target_name'];
                $user_relations[$array_relation_id]['annotation_target_id'] = $relation['annotation_target_id'];

                $user_relations[$array_relation_id]['user_relations'][]['relation_type_id'] = $relation['relation_type_id'];
                $new_key = max(array_keys($user_relations[$array_relation_id]['user_relations']));
                $user_relations[$array_relation_id]['user_relations'][$new_key]['relation_name'] = $relation['relation_name'];
            }

            //Check if final relation exists.
            if(isset($final_relations[$array_relation_id])){
                $user_relations[$array_relation_id]['final'] = $final_relations[$array_relation_id];
            } else{
                $user_relations[$array_relation_id]['final'] = null;
            }
        }

        return $user_relations;
    }

    /**
     * Compares relations of two users
     * @param $user_a_relations
     * @param $user_b_relations
     */
    private function compareUserRelations($user_a_relations, $user_b_relations){
        //Check which relations exist for both users
        $annotations_a_and_b= self::relation_intersection($user_a_relations, $user_b_relations);
        $annotations_compared = $annotations_a_and_b;

        //Check which relations exist only for user A
        $annotations_user_a = array_diff_key($user_a_relations, $user_b_relations);
        foreach($annotations_user_a as $key => $annotation_a){
            $annotations_user_a[$key]['user_agreement'] = 'only_a';
        }
        $annotations_compared = array_merge($annotations_compared, $annotations_user_a);

        //Check which relations exist only for user B
        $annotations_user_b = array_diff_key($user_b_relations, $user_a_relations);
        foreach($annotations_user_b as $key => $annotation_b){
            $annotations_user_b[$key]['user_agreement'] = 'only_b';
        }
        $annotations_compared = array_merge($annotations_compared, $annotations_user_b);

        return $annotations_compared;
    }

    private function relation_intersection($user_a_relations, $user_b_relations){
        $intersections = array_intersect_key($user_a_relations, $user_b_relations);
        $annotations_compared = array();

        foreach($intersections as $key => $intersection){
            $annotations_compared[$key]['source_text'] = $intersection['source_text'];
            $annotations_compared[$key]['relation_id'] = $intersection['id'];
            $annotations_compared[$key]['relation_types'] = $intersection['relation_types'];
            $annotations_compared[$key]['target_text'] = $intersection['target_text'];
            $annotations_compared[$key]['source_from'] = $intersection['source_from'];
            $annotations_compared[$key]['source_to'] = $intersection['source_to'];
            $annotations_compared[$key]['annotation_source_id'] = $intersection['annotation_source_id'];
            $annotations_compared[$key]['annotation_source_name'] = $intersection['annotation_source_name'];
            $annotations_compared[$key]['annotation_target_id'] = $intersection['annotation_target_id'];
            $annotations_compared[$key]['annotation_target_name'] = $intersection['annotation_target_name'];
            $annotations_compared[$key]['target_from'] = $intersection['target_from'];
            $annotations_compared[$key]['target_to'] = $intersection['target_to'];
            $annotations_compared[$key]['user_a_id'] = $user_a_relations[$key]['user_id'];
            $annotations_compared[$key]['user_b_id'] = $user_a_relations[$key]['user_id'];
            $annotations_compared[$key]['user_agreement'] = 'a_and_b';
            if($user_a_relations[$key]['final'] != null){
                $annotations_compared[$key]['final'] = $user_a_relations[$key]['final'];
            }

            foreach($user_a_relations[$key]['user_relations'] as $index => $user_a_relation){
                $annotations_compared[$key]['user_a_relations'][$index]['relation_type_id'] = $user_a_relation['relation_type_id'];
                $annotations_compared[$key]['user_a_relations'][$index]['relation_name'] = $user_a_relation['relation_name'];
            }

            foreach($user_b_relations[$key]['user_relations'] as $index => $user_b_relation){
                $annotations_compared[$key]['user_b_relations'][$index]['relation_type_id'] = $user_b_relation['relation_type_id'];
                $annotations_compared[$key]['user_b_relations'][$index]['relation_name'] = $user_b_relation['relation_name'];
            }

            $annotations_compared[$key]['all_relations'] = array_map("unserialize", array_unique(array_map("serialize", array_merge($user_a_relations[$key]['user_relations'], $user_b_relations[$key]['user_relations']))));



            //Check which relations out of possible relations between two annotations exist for both annotators.
            foreach( $annotations_compared[$key]['all_relations'] as $arg => $relation_type){
                $in_a = false;
                foreach($user_a_relations[$key]['user_relations'] as $index => $user_a_relation){
                    if($user_a_relation['relation_type_id'] == $relation_type['relation_type_id']){
                        $in_a = true;
                        break;
                    }
                }
                $in_b = false;
                foreach($user_b_relations[$key]['user_relations'] as $index =>$user_b_relation){
                    if($user_b_relation['relation_type_id'] == $relation_type['relation_type_id']){
                        $in_b = true;
                        break;
                    }
                }

                if($in_a && $in_b){
                    $annotations_compared[$key]['all_relations'][$arg]['agreement'] = 'a_and_b';
                    $annotations_compared[$key]['a_and_b_relations'][] = array('name' => $relation_type['relation_name'],
                                                                               'relation_type_id' => $relation_type['relation_type_id']);
                } else if($in_a){
                    $annotations_compared[$key]['all_relations'][$arg]['agreement'] = 'only_a';
                } else if($in_b){
                    $annotations_compared[$key]['all_relations'][$arg]['agreement'] = 'only_b';
                }

                $number_of_final_rels = 0;
                if(isset($annotations_compared[$key]['final'])){
                    foreach($annotations_compared[$key]['final']['final_relations'] as $final_rel){
                        if(isset($annotations_compared[$key]['a_and_b_relations'])){
                            foreach($annotations_compared[$key]['a_and_b_relations'] as $index => $a_b){
                                if($a_b['relation_type_id'] == $final_rel['relation_type_id']){
                                    $annotations_compared[$key]['a_and_b_relations'][$index]['final'] = true;
                                    $number_of_final_rels += 1;
                                }
                            }
                        }
                    }
                }

                if($number_of_final_rels == count($annotations_compared[$key]['a_and_b_relations'])){
                    $annotations_compared[$key]['only_finals'] = true;
                } else{
                    $annotations_compared[$key]['only_finals'] = false;
                }

            }

            if($annotations_compared[$key]['a_and_b_relations'] != null){
                foreach($annotations_compared[$key]['relation_types'] as $arg => $relation_type){
                    if(in_array($relation_type, $annotations_compared[$key]['a_and_b_relations'])){
                        $annotations_compared[$key]['relation_types'][$arg]['agreement'] = true;
                    }
                }
            }
        }

        return $annotations_compared;
    }

    /**
     * Returns the list of relations made by two users.
     * @param $report_id
     * @param $relation_types
     * @param $user_a
     * @param $user_b
     * @param $final_relations
     * @return array
     */

    static function getRelationAgreement($report_id, $relation_types, $user_a, $user_b, $final_relations, $annotation_types){
        $user_a_relations = self::getUserRelations($report_id, $relation_types, $user_a, $final_relations, $annotation_types);
        $user_b_relations = self::getUserRelations($report_id, $relation_types, $user_b, $final_relations, $annotation_types);
        $annotations_compared = self::compareUserRelations($user_a_relations, $user_b_relations);
        self::array_sort_by_column($annotations_compared, 'source_from');
        return $annotations_compared;
    }

    /**
     * Finds possible relations between two annotation types.
     * @param $source_annotation
     * @param $target_annotation
     * @param $relation_types
     * @return mixed
     */

    private function getRelationsBetweenAnnotations($source_annotation, $target_annotation, $relation_types){
        global $db;

        $source_structure = DbAnnotation::getAnnotationSetSubsetOfType($source_annotation);
        $target_structure = DbAnnotation::getAnnotationSetSubsetOfType($target_annotation);

        $params_constant = array(
            $source_structure['annotation_set_id'],
            $source_structure['annotation_subset_id'],
            $source_structure['annotation_type_id'],
            $target_structure['annotation_set_id'],
            $target_structure['annotation_set_id'],
            $target_structure['annotation_set_id'],
        );

        $sql = "SELECT DISTINCT rg.relation_type_id, rt.name FROM relations_groups rg 
                JOIN relation_types rt ON rg.relation_type_id = rt.id 
                WHERE 
                (
                    (rg.relation_type_id IN (" . implode(",", array_fill(0, count($relation_types), "?")) . "))
                    AND (
                        (part = 'source' AND (rg.annotation_set_id = ? OR rg.annotation_subset_id = ? OR rg.annotation_type_id = ?))
                        OR 
                        (part = 'target' AND (rg.annotation_set_id = ? OR rg.annotation_subset_id = ? OR rg.annotation_type_id = ?))
                    )
                )
                ORDER BY rt.name ASC
                ";

        $params = array_merge($relation_types, $params_constant);

        $relations = $db->fetch_rows($sql, $params);

        return $relations;
    }

    /**
     * Deletes relation with given id.
     * @param $attributes
     */
    static function deleteRelation($attributes){
        global $db;
        $sql = "DELETE FROM `relations` WHERE (`target_id` = ? AND `source_id` = ? AND `relation_type_id` = ? AND `stage` = ?)";
        $params = array(
            $attributes['target_id'],
            $attributes['source_id'],
            $attributes['relation_type_id'],
            $attributes['stage'],
        );

        $db->execute($sql, $params);
    }

    /**
     * Updates the relation, changing the relation type_id to a new one.
     * @param $relation_id
     * @param $new_relation_type_id
     */
    static function updateRelation($relation_id, $new_relation_type_id){
        global $db;

        $sql = "UPDATE relations SET relation_type_id = ? WHERE id = ?";
        $db->execute($sql, array($new_relation_type_id, $relation_id));
    }

    /**
     * Gets the annotation sets associated with the list of relations types.
     * @param $relation_types (comma separated ids)
     * @return mixed
     */
    static function getAnnotationsOfRelations($relation_types, $report_id){
        global $db;

        $sql = "SELECT DISTINCT at.group_id AS 'annotation_set_id', r.id FROM relations r 
                JOIN reports_annotations_optimized rao ON (rao.id = r.target_id OR rao.id = r.source_id)
                JOIN annotation_types at ON (at.annotation_type_id = rao.type_id)
                WHERE (r.relation_type_id IN (" . implode(",", array_fill(0, count($relation_types), "?")) . ")
                AND rao.report_id = ? AND rao.stage = 'final' AND r.stage = 'agreement')
                ";

        $params_constant = array($report_id);
        $params = array_merge($relation_types, $params_constant);
        $annotation_sets = $db->fetch_rows($sql, $params);
        return $annotation_sets;
    }

    static function getAnnotationTypesOfAnnotationSets($annotation_sets){
        $sql = "SELECT * FROM annotation_types at ";
    }

    private function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
        $sort_col = array();
        foreach ($arr as $key=> $row) {
            $sort_col[$key] = $row[$col];
        }

        array_multisort($sort_col, $dir, $arr);
    }

    static function getUserRelationCount($report_id){
        global $db;

        $sql = "SELECT u.user_id, u.screename, COUNT(r.id) AS 'annotation_count' FROM users u 
                JOIN relations r ON r.user_id = u.user_id
                JOIN reports_annotations_optimized rao ON r.source_id = rao.id 
                JOIN reports_annotations_optimized rao2 ON r.target_id = rao2.id 
                WHERE (rao.report_id = ? AND rao2.report_id = ? AND r.stage = 'agreement')
                GROUP BY u.screename";

        $params = array(
            $report_id, $report_id
        );
        $relation_count = $db->fetch_rows($sql, $params);

        return $relation_count;
    }

    static function insertFinalRelation($attributes){
        global $db;
        $sql = "SELECT * FROM relations r
                WHERE (r.relation_type_id = ? AND r.source_id = ? AND r.target_id = ? AND r.stage = ?)";
        $params = array($attributes['relation_type_id'], $attributes['source_id'], $attributes['target_id'], $attributes['stage']);
        $result = $db->fetch_rows($sql, $params);

        if(empty($result)){
            $sql_insert = "INSERT INTO  relations(`relation_type_id`, `source_id`, `target_id`, `date`, `user_id`, `stage`) VALUES(?,?,?,?,?,?)";
            $params = array(
                $attributes['relation_type_id'],
                $attributes['source_id'],
                $attributes['target_id'],
                $attributes['date'],
                $attributes['user_id'],
                $attributes['stage']
            );
            $db->execute($sql_insert, $params);
        }
    }




    static function getUsersAndRelationCount($corpus_id = null, $subcorpus_ids = null, $report_ids= null, $relation_set_id = null, $relation_type_ids = null, $flags = null, $stage = null){
        global $db;

        $params = array();
        $params_where = array();
        $sql_where = array();

        $sql = "SELECT u.*, COUNT(DISTINCT r.id) as relation_count,  COUNT(DISTINCT rao.report_id) FROM users u
                JOIN relations r ON r.user_id = u.user_id
                JOIN reports_annotations_optimized rao ON (r.source_id = rao.id OR r.target_id = rao.id) ";

        if ( $corpus_id || ($subcorpus_ids !==null && count($subcorpus_ids) > 0) ){
            $sql .= " JOIN reports rep ON rao.report_id = rep.id ";
        }

        if ( $corpus_id ){
            $params_where[] = $corpus_id;
            $sql_where[] = " rep.corpora = ? ";
        }

        if ( $subcorpus_ids !==null && count($subcorpus_ids) > 0 ){
            $params_where = array_merge($params_where, $subcorpus_ids);
            $sql_where[] = " rep.subcorpus_id IN (" . implode(",", array_fill(0, count($subcorpus_ids), "?")) . ") ";
        }

        if ( $report_ids !==null && count($report_ids) > 0 ){
            $params_where = array_merge($params_where, $report_ids);
            $sql_where[] = " rao.report_id IN (" . implode(",", array_fill(0, count($report_ids), "?")) . ") ";
        }

        if ( $relation_set_id && count($relation_set_id) > 0 ){
            $params_where[] = $relation_set_id;
            $sql .= " JOIN relation_types rt ON (rt.id = r.relation_type_id) ";
            $sql_where[] = " rt.relation_set_id = ? ";
        }

        if ( $relation_type_ids !== null ){
            $relation_type_ids = array_map(intval, $relation_type_ids);
            if ( count($relation_type_ids) > 0 ){
                $params_where = array_merge($params_where, $relation_type_ids);
                $sql_where[] = " r.relation_type_id IN (" . implode(",", array_fill(0, count($relation_type_ids), "?")) .") ";
            }
            else{
                /* Jeżeli tablica z identyfikatorami typów anotacji jest pusta, to nie zostanie zwrócona żadna anotacje */
                return array();
            }
        }

        if ( $stage ){
            $params_where[] = $stage;
            $sql_where[] = "r.stage = ?";
        }

        if ( $flags !== null && is_array($flags) && count($flags) > 0 ){
            $sql .= " LEFT JOIN reports_flags rf ON (rf.report_id = rep.id AND rf.corpora_flag_id = ?)";
            $sql_where[] = "rf.flag_id = ?";
            $keys = array_keys($flags);
            $params[] = $keys[0];
            $params_where[] = $flags[$keys[0]];
        }

        if ( count($sql_where) > 0 ){
            $sql .= " WHERE (" . implode(" AND ", $sql_where);
            $sql .= ") ";
        }

        $sql .= " GROUP BY u.user_id";

        ChromePhp::log($sql);
        ChromePhp::log($params_where);
        return $db->fetch_rows($sql, array_merge($params, $params_where));
    }
}
