<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class DbAnnotation{

	/**
	 * Return a list of annotations with specified criteria
	 * @param $report_id ReportContent identifier
	 * @param $annotation_set_id Set of annotation set ids, if null the filter is not applied
	 * @param $stages Set of annotation stages, if null the filter is not applied
	 */
	static function getReportAnnotations($report_id,
                                         $user_ids=null,
                                         $annotation_set_ids=null,
                                         $annotation_subset_ids=null,
                                         $annotation_type_ids=null,
                                         $stages=null,
			                             $fetch_user_data=false){
		global $db;

		/* Sprawdź poprawność parametrów */
		$annotation_set_ids = $annotation_set_ids !== null && !is_array($annotation_set_ids) ? null : $annotation_set_ids;
		$annotation_subset_ids = $annotation_subset_ids !== null && !is_array($annotation_subset_ids) ? null : $annotation_subset_ids;
		$annotation_type_ids = $annotation_type_ids !== null && !is_array($annotation_type_ids) ? null : $annotation_type_ids;
		/* EOB */

		$sql = "SELECT a.*, at.name as type, at.group_id, at.annotation_subset_id, l.lemma, u.login, u.screename";
		$sql .= " FROM reports_annotations_optimized a";
		$sql .= " LEFT JOIN reports_annotations_lemma l ON (a.id = l.report_annotation_id)";
		$sql .= " JOIN annotation_types at ON (a.type_id = at.annotation_type_id)";
        $sql .= " LEFT JOIN users u ON (u.user_id = a.user_id)";

		$where = array("a.report_id = ?");
		$params = array($report_id);

		if ( $annotation_set_ids !== null ){
			$where[] = "at.group_id IN (" . implode(", ", $annotation_set_ids) . ")";
		}

		if ( $annotation_subset_ids !== null ){
			$where[] = "at.annotation_subset_id IN (" . implode(", ", $annotation_subset_ids) . ")";
		}

		if ( $annotation_type_ids !== null ){
			$annotation_type_ids[] = -1;
			$where[] = "a.type_id IN (" . implode(", ", $annotation_type_ids) . ")";
		}

		if ( $user_ids != null ){
			$where[] = "a.user_id IN (" . implode(",", $user_ids) . ")";
		}

		if ( $stages != null ){
			$where[] = "a.stage IN ('" . implode("','", $stages) . "')";
		}

		$sql = $sql . " WHERE " . implode(" AND ", $where);
		$annotations = $db->fetch_rows($sql, $params);
		return $annotations;
	}

    static function get($annotationId){
        global $db;
        $sql = "SELECT a.*, at.name as type, at.group_id, at.annotation_subset_id, l.lemma";
        $sql .= " FROM reports_annotations_optimized a";
        $sql .= " LEFT JOIN reports_annotations_lemma l ON (a.id = l.report_annotation_id)";
        $sql .= " JOIN annotation_types at ON (a.type_id = at.annotation_type_id)";
        $sql .= " WHERE a.id = ?";
        return $db->fetch($sql, array($annotationId));
    }

	/**
	 * Return a list of annotations for a givent document.
	 */
	static function getAnnotationByReportId($report_id,$fields=null){
		global $db;
		$sql = " SELECT " .
				($fields ? $fields : " * " ) .
				" FROM reports_annotations " .
				" WHERE report_id = ?";
		return $db->fetch_rows($sql, array($report_id));
	}

	static function getReportAnnotationsBySubsetId($report_id, $subset_id){
		global $db;
		$sql = "SELECT * FROM reports_annotations an" .
				" JOIN annotation_types at ON (an.type = at.name)" .
				" WHERE an.report_id = ? AND at.annotation_subset_id = ?";
		return $db->fetch_rows($sql, array($report_id, $subset_id));
	}

	/**
	 * Return list of annotations types.
	 */
	static function getAnnotationTypes($fields=null){
		global $db;

		$sql = " SELECT " .
				($fields ? $fields : " * " ) .
				" FROM annotation_types ";

		return $db->fetch_rows($sql);
	}

	/**
	 * Return list of annotations types.
	 */
	static function getAnnotationTypesByIds($ids){
		global $db;
		$ids[] = -1;
		$sql = " SELECT * FROM annotation_types ".
				" WHERE annotation_type_id IN (". implode(",", $ids) .")".
				" ORDER BY group_id, annotation_subset_id";
		return $db->fetch_rows($sql);
	}

	/**
	 *
	 * @param unknown $corpus_id
	 * @param unknown $fields
	 * @return {Array}
	 */
	static function getAnnotationTypesByCorpora($corpus_id,$fields=null){
		global $db;

		$sql = " SELECT " .
				($fields ? $fields : " * " ) .
				" FROM annotation_sets a_s " .
				" LEFT JOIN annotation_sets_corpora a_s_c ON (a_s.annotation_set_id=a_s_c.annotation_set_id) " .
				" WHERE a_s_c.corpus_id=? ";

		return $db->fetch_rows($sql,array($corpus_id));
	}

	static function getAnnotationTypesByGroupId($group_id){
		global $db;
	    $sql = "SELECT name FROM annotation_types WHERE group_id = ?";
		return $db->fetch_rows($sql, array($group_id));
	}

	static function getAnnotationTypesBySets($report_ids, $relation_ids){
		global $db;
	    $sql = "SELECT DISTINCT type, report_id " .
	            "FROM reports_annotations " .
	            "WHERE report_id IN('" . implode("','",$report_ids) . "') " .
	            "AND " .
	                "(id IN " .
	                    "(SELECT source_id " .
	                    "FROM relations " .
	                    "WHERE relation_type_id " .
	                    "IN " .
	                        "(".implode(",",$relation_ids).") ) " .
	                "OR id " .
	                "IN " .
	                    "(SELECT target_id " .
	                    "FROM relations " .
	                    "WHERE relation_type_id " .
	                    "IN " .
	                        "(".implode(",",$relation_ids).") ) )";
		return $db->fetch_rows($sql);
	}

	static function getAnnotationsBySets($report_ids=null, $annotation_layers=null, $annotation_names=null, $stage = null){
		global $db;
		$sql = "SELECT *, ra.type, raa.`value` AS `prop` " .
				" FROM reports_annotations ra" .
				" LEFT JOIN annotation_types at ON (ra.type=at.name) " .
				" LEFT JOIN reports_annotations_attributes raa ON (ra.id=raa.annotation_id) ";
		$andwhere = array();
		$orwhere = array();
		if($stage == null){
            $andwhere[] = " ra.stage='final' ";
        } else{
		    $andwhere[] = " ra.stage = '" . $stage . "' ";
        }

		if ($report_ids <> null && count($report_ids) > 0)
			$andwhere[] = "report_id IN (" . implode(",",$report_ids) . ")";
		if ($annotation_layers <> null && count($annotation_layers) > 0)
			$orwhere[] = "at.group_id IN (" . implode(",",$annotation_layers) . ")";
		if ($annotation_names <> null && count($annotation_names) > 0)
			$orwhere[] = "ra.type IN ('" . implode("','",$annotation_names) . "')";
		if (count($andwhere) > 0)
			$sql .= " WHERE (" . implode(" AND ", $andwhere) . ") ";
		if (count($orwhere) > 0)
			if (count($andwhere)==0)
				$sql .= " WHERE ";
			else
				$sql .= " AND ( " . implode(" OR ",$orwhere) . " ) ";
		$sql .= "  GROUP BY ra.id ORDER BY `from`";

		$rows = $db->fetch_rows($sql);

		return $rows;
	}

	/**
	 *
	 */
	static function getAnnotationsBySubsets($report_ids=null, $annotation_subset_ids=null){
		global $db;
		$sql = "SELECT *, ra.type, raa.`value` AS `prop` " .
				" FROM reports_annotations ra" .
				" LEFT JOIN annotation_types at ON (ra.type=at.name) " .
				" LEFT JOIN reports_annotations_attributes raa ON (ra.id=raa.annotation_id) ";
		$andwhere = array();
		$orwhere = array();
		$andwhere[] = " stage='final' ";
		if ($report_ids <> null && count($report_ids) > 0)
			$andwhere[] = "report_id IN (" . implode(",",$report_ids) . ")";
		if ($annotation_subset_ids <> null && count($annotation_subset_ids) > 0)
			$orwhere[] = "at.annotation_subset_id IN (" . implode(",",$annotation_subset_ids) . ")";
		if (count($andwhere) > 0)
			$sql .= " WHERE (" . implode(" AND ", $andwhere) . ") ";
		if (count($orwhere) > 0)
			if (count($andwhere)==0)
				$sql .= " WHERE ";
			else
				$sql .= " AND ( " . implode(" OR ",$orwhere) . " ) ";
		$sql .= " ORDER BY ra.id, `from`";
		$rows = $db->fetch_rows($sql);

		return $rows;
	}

	static function getAnnotationSharedAttributes($annotationId){
	    global $db;
	    $sql = "SELECT * FROM reports_annotations_shared_attributes WHERE annotation_id = ?";
	    return $db->fetch_rows($sql, array($annotationId));
    }

	static function deleteReportAnnotationsByType($report_id, $types){
		global $db;
		if (!is_array($types)) $types = array($types);

		$sql = "DELETE FROM reports_annotations_optimized WHERE report_id = ? ".
				" AND type IN (". implode(",", array_fill(0, count($types), "?")) .")";

		$params = array_merge(array($report_id), array_values($types));
		$db->execute($sql, $params);
	}

	/**
	 * Usuwa anotację o wskazanym ID z jednoczesnym sprawdzeniem, czy anotacja należy do określonego dokumentu.
	 * @param unknown $report_id
	 * @param unknown $annotation_id
	 */
	static function deleteReportAnnotation($report_id, $annotation_id){
		global $db;

		$sql = "DELETE FROM reports_annotations_optimized WHERE id=? AND report_id=?";
		$db->execute($sql, array($annotation_id, $report_id));
	}

	static function deleteReportAnnotationsByRegexp($report_id, $regex){
		global $db;

		$sql = "DELETE rao.* FROM reports_annotations_optimized rao
				LEFT JOIN annotation_types at ON(at.annotation_type_id = rao.type_id) 
				WHERE at.name REGEXP ? AND report_id = ?";

		$db->execute($sql, array($regex, $report_id));
	}

	static function getSubsetsBySetAndCorpus($set_id, $corpus_id){
		global $db;
		$sql = "SELECT ansub.annotation_subset_id as id, ans.name setname, ansub.description subsetname"
		." FROM annotation_subsets ansub"
		." JOIN annotation_sets ans ON ( ansub.annotation_set_id = ans.annotation_set_id )"
		." LEFT JOIN annotation_sets_corpora ac ON ( ac.annotation_set_id = ans.annotation_set_id )"
		." WHERE ac.corpus_id = ? "
		." AND ans.annotation_set_id = ?";

		$rows = $db->fetch_rows($sql, array($corpus_id, $set_id));

		return $rows;
	}

    static function getAnnotationSetsWithCount_old($corpus_id, $subcorpus, $status){
        global $db;
        $params = array($corpus_id);

        $setsById = array();

        $sql = "SELECT DISTINCT ans.annotation_set_id AS id, ans.name AS name FROM annotation_types at ".
            "LEFT JOIN annotation_subsets ansub ON(at.annotation_subset_id = ansub.annotation_subset_id) ".
            "JOIN annotation_sets ans ON(at.group_id = ans.annotation_set_id) ".
            "LEFT JOIN annotation_sets_corpora ac ON (ac.annotation_set_id = ans.annotation_set_id) ".
            "WHERE ac.corpus_id = ?";

        $sets = $db->fetch_rows($sql, $params);

        foreach($sets as $set){
            $setsById[$set['id']] = array('name' => $set['name'], 'unique' => 0, 'count' => 0);
        }

        if ($subcorpus)
            $params[] = $subcorpus;

        if ( $status > 0 )
            $params[] = $status;

        $sql = "SELECT b.setname AS name, b.id, b.group, SUM( b.count ) AS count, SUM( b.unique ) AS `unique` ".
            "FROM ( ".

            "		SELECT a.type AS type , ans.name AS setname, at.group_id AS `group` , ".
            "		COUNT( * ) AS count, ".
            "		COUNT( DISTINCT (a.text) ) AS `unique` , ".
            "		COUNT( DISTINCT (r.id) ) AS docs, at.group_id AS id ".

            "		FROM annotation_sets ans ".
            "			JOIN annotation_types at ON (at.group_id = ans.annotation_set_id) ".
            "			JOIN reports_annotations a ON (a.type = at.name) ".
            "			JOIN reports r ON (r.id = a.report_id) ".
            "		WHERE r.corpora = ?".
            ( $subcorpus ? " AND r.subcorpus_id = ? " : "") .
            ( $status ? " AND r.status = ? " : "") .
            "		GROUP BY a.type ".
            "		ORDER BY a.type ".

            ") AS b ".
            "GROUP BY b.group";


        $annotation_sets = $db->fetch_rows($sql, $params);

        foreach($annotation_sets as $set){
            $setsById[$set['id']]['unique'] = $set['unique'];
            $setsById[$set['id']]['count'] = $set['count'];
            if($setsById[$set['id']]['name'] == ''){
                $setsById[$set['id']]['inc_name'] = $set['name'];
            }
        }

        return $setsById;
    }

	static function getAnnotationSetsWithCount($corpus_id, $session){
		global $db;
		$params = array($corpus_id);
		$setsById = array();

		$filters = $session;
		$ext_table = DbCorpus::getCorpusExtTable($corpus_id);

		$sql = "SELECT DISTINCT ans.annotation_set_id AS id, ans.name AS name FROM annotation_types at ".
				"LEFT JOIN annotation_subsets ansub ON(at.annotation_subset_id = ansub.annotation_subset_id) ".
				"JOIN annotation_sets ans ON(at.group_id = ans.annotation_set_id) ".
				"LEFT JOIN annotation_sets_corpora ac ON (ac.annotation_set_id = ans.annotation_set_id) ".
				"WHERE ac.corpus_id = ?";

		$sets = $db->fetch_rows($sql, $params);

		foreach($sets as $set){
			$setsById[$set['id']] = array('name' => $set['name'], 'unique' => 0, 'count' => 0);
		}

        if ($filters['flags'] != null && $filters['flags']['flag'] != "-" && $filters['flags']['flag_status'] != "-"){
		    $flag_active = true;
		    $params = array();
            $params[] = intval($filters['flags']['flag']);
            $params[] = $corpus_id;
            $params[] = intval($filters['flags']['flag_status']);
        } else{
            $flag_active = false;
        }

        $where_metadata = "";
        $sql_metadata = "";
        if(isset($filters['metadata'])){
            $ext_columns = DbCorpus::getCorpusExtColumns($ext_table);
            $metadata_columns = array();
            foreach($ext_columns as $column){
                $metadata_columns[] = $column['field'];
            }

            foreach($filters['metadata'] as $column => $metadata){
                if($metadata != "0"){
                    if(in_array($column, $metadata_columns)){
                        $where_metadata .=  " AND ext." . $column . " = '" . $metadata ."'";
                        if($sql_metadata == ""){
                            $sql_metadata = " JOIN " . $ext_table . " ext ON ext.id = r.id ";
                        }
                    } else{
                        unset($_SESSION['annmap']['metadata'][$column]);
                    }
                }
            }
        }

		if ( $filters['status'] && $filters['status'] != '0'){
            $params[] = intval($filters['status']);
            $status = true;
        } else{
		    $status = false;
        }

        $sql = "SELECT ans.name AS `name`,
                      at.group_id AS `group`,
                      COUNT( * ) AS `count`,".
                      //COUNT( DISTINCT (a.text) ) AS `unique` ,
                      //COUNT( DISTINCT (r.id) ) AS docs,
                      "at.group_id AS id
                 FROM annotation_sets ans
                        JOIN annotation_types at ON (at.group_id = ans.annotation_set_id)
                        JOIN reports_annotations_optimized a ON (a.type_id = at.annotation_type_id)
                        JOIN reports r ON (r.id = a.report_id)";
		$sql .= $sql_metadata;
        $sql .= $flag_active ? " JOIN reports_flags rf ON (rf.report_id = r.id AND rf.corpora_flag_id = ?) " : "";
        $sql .= " WHERE r.corpora = ?";
        $sql .= $flag_active ? " AND rf.flag_id = ? " : "";
        $sql .= $status ? " AND r.status = ? " : "";
        $sql .= $where_metadata;
        $sql .= " GROUP BY ans.annotation_set_id
                 ORDER BY ans.name";
        $annotation_sets = $db->fetch_rows($sql, $params);

		foreach($annotation_sets as $set){
			$setsById[$set['id']]['unique'] = $set['unique'];
			$setsById[$set['id']]['count'] = $set['count'];
			if($setsById[$set['id']]['name'] == ''){
				$setsById[$set['id']]['inc_name'] = $set['name'];
			}
		}

		return $setsById;
	}

	static function getAnnotationSubsetsWithCount($corpus_id, $set_id, $session){
		global $db;

        $params = array($corpus_id, $set_id);
        $filters = $session;
        $ext_table = DbCorpus::getCorpusExtTable($corpus_id);

		$subsetsById = array();
		$subsetsByName = array();

		$sql = "SELECT ansub.annotation_subset_id AS id, ansub.name AS name FROM annotation_types at ".
				"LEFT JOIN annotation_subsets ansub ON(at.annotation_subset_id = ansub.annotation_subset_id) ".
				"JOIN annotation_sets ans ON(at.group_id = ans.annotation_set_id) ".
				"JOIN reports_annotations a ON ( at.name = a.type ) ".
				"JOIN reports r ON ( r.id = a.report_id ) ".
				//"LEFT JOIN annotation_sets_corpora anc ON 1(anc.annotation_set_id = ans.annotation_set_id) ".
				"WHERE r.corpora = ? AND ans.annotation_set_id = ? ".
				"GROUP BY id ORDER BY name";

		$subsets = $db->fetch_rows($sql, $params);

        if ($filters['flags'] != null && $filters['flags']['flag'] != "-" && $filters['flags']['flag_status'] != "-"){
            $flag_active = true;
            $params = array(intval($filters['flags']['flag']), intval($corpus_id), intval($set_id), intval($filters['flags']['flag_status']));
        } else{
            $flag_active = false;
        }

        if(isset($filters['metadata'])){
            $where_metadata = "";
            $sql_metadata = "";
            foreach($filters['metadata'] as $column => $metadata){
                if($metadata != "0"){
                    $where_metadata .=  " AND ext." . $column . " = '" . $metadata ."'";
                    if($sql_metadata == ""){
                        $sql_metadata = " JOIN " . $ext_table . " ext ON ext.id = r.id ";
                    }
                }
            }
        }

		foreach($subsets as $subset){
			$subsetsById[$subset['id']] = array('name' => $subset['name'], 'unique' => 0, 'count' => 0);
			$subsetsByName[$subset['name']] = array('id' => $subset['id'], 'unique' => 0, 'count' => 0);
		}

        if ($filters['subcorpus'] && $filters['subcorpus'] != '0'){
            $params[] = intval($filters['subcorpus']);
            $subcorpus = true;
        } else{
            $subcorpus = false;
        }

        if ( $filters['status'] && $filters['status'] != '0'){
            $params[] = intval($filters['status']);
            $status = true;
        } else{
            $status = false;
        }

		$sql =  "SELECT a.type AS type, ".
                "  ansub.name AS name, ".
                "  at.group_id AS `group` , ".
				"  COUNT( * ) AS count, ".
				// "  COUNT( DISTINCT (a.text) ) AS `unique` , ".
				"  COUNT( DISTINCT (r.id) ) AS docs, ".
                "  at.annotation_subset_id AS id ".
				"FROM reports_annotations a ".
				"  JOIN reports r ON ( r.id = a.report_id ) ".
                $sql_metadata.
                "  JOIN annotation_types at ON ( at.name = a.type ) ".
				"  JOIN annotation_subsets ansub ON ( at.annotation_subset_id = ansub.annotation_subset_id ) ".
                ($flag_active ? " JOIN reports_flags rf ON (rf.report_id = r.id AND rf.corpora_flag_id = ?) " : "") .
                "WHERE ".
                "  r.corpora = ? ".
				"  AND at.group_id = ? ".
                ( $flag_active ? " AND rf.flag_id = ? " : "") .
				( $subcorpus ? " AND r.subcorpus_id = ? " : "") .
				( $status ? " AND r.status = ? " : "") .
                $where_metadata.
				" GROUP BY at.annotation_subset_id ";

		$annotation_subsets = $db->fetch_rows($sql, $params);

		foreach($annotation_subsets as $subset){
			$subsetsById[$subset['id']]['unique'] = strval($subset['unique']);
			$subsetsById[$subset['id']]['count'] = $subset['count'];
			$subsetsByName[$subset['name']]['unique'] = strval($subset['unique']);
			$subsetsByName[$subset['name']]['count'] = $subset['count'];
		}
		return $subsetsByName;
	}

	static function getAnnotationTypesWithCount($corpus_id, $subset_id, $session){
		global $db;
		$params = array($corpus_id, $subset_id);

		$typesById = array();
        $filters = $session;
        $ext_table = DbCorpus::getCorpusExtTable($corpus_id);

		$sql = "SELECT at.name AS name, at.name AS id" .
				" FROM annotation_types at ".
				" JOIN annotation_subsets ansub ON(at.annotation_subset_id = ansub.annotation_subset_id) ".
				" JOIN annotation_sets ans ON(at.group_id = ans.annotation_set_id) ".
				" LEFT JOIN reports_annotations a ON ( at.name = a.type ) ".
				" LEFT JOIN reports r ON ( r.id = a.report_id ) ".
				" WHERE (r.corpora = ? OR r.corpora IS NULL)" .
				"   AND ansub.annotation_subset_id = ? ".//AND ans.annotation_set_id = ?
				"ORDER BY name";

		$types = $db->fetch_rows($sql, $params);

		foreach($types as $type){
			$typesById[$type['id']] = array('name' => $type['name'], 'unique' => 0, 'count' => 0, 'docs' => 0);
		}

        if ($filters['flags'] != null && $filters['flags']['flag'] != "-" && $filters['flags']['flag_status'] != "-"){
            $flag_active = true;
            $params = array(intval($filters['flags']['flag']), intval($corpus_id), intval($subset_id), intval($filters['flags']['flag_status']));
        } else{
            $flag_active = false;
        }

        if(isset($filters['metadata'])){
            $where_metadata = "";
            $sql_metadata = "";
            foreach($filters['metadata'] as $column => $metadata){
                if($metadata != "0"){
                    $where_metadata .=  " AND ext." . $column . " = '" . $metadata ."'";
                    if($sql_metadata == ""){
                        $sql_metadata = " JOIN " . $ext_table . " ext ON ext.id = r.id ";
                    }
                }
            }
        }

        if ($filters['subcorpus'] && $filters['subcorpus'] != '0'){
            $params[] = intval($filters['subcorpus']);
            $subcorpus = true;
        } else{
            $subcorpus = false;
        }

        if ( $filters['status'] && $filters['status'] != '0'){
            $params[] = intval($filters['status']);
            $status = true;
        } else{
            $status = false;
        }

		$sql = "SELECT at.name AS name, at.name AS id, ".
				"COUNT( a.id ) AS count, ".
				"COUNT( DISTINCT (a.text) ) AS `unique` , ".
				"COUNT( DISTINCT (r.id) ) AS docs ".
				"FROM annotation_types at ".
				"JOIN annotation_subsets ansub ON ( at.annotation_subset_id = ansub.annotation_subset_id ) ".
				"LEFT JOIN reports_annotations a ON ( at.name = a.type )".
				"LEFT JOIN reports r ON ( r.id = a.report_id ) ".
                ($flag_active ? " JOIN reports_flags rf ON (rf.report_id = r.id AND rf.corpora_flag_id = ?) " : "") .
                $sql_metadata .
				"WHERE (r.corpora = ? OR r.corpora IS NULL)".
				"AND at.annotation_subset_id = ? ".
                $where_metadata.
                ($flag_active ? " AND rf.flag_id = ? " : "") .
				( $subcorpus ? " AND (r.subcorpus_id = ? OR r.subcorpus_id IS NULL) " : "") .
				( $status ? " AND (r.status = ? OR r.status IS NULL) " : "") .
				"GROUP BY a.type ".
				"ORDER BY a.type ";

		$annotation_subsets = $db->fetch_rows($sql, $params);

		foreach($annotation_subsets as $type){
			$typesById[$type['id']]['unique'] = $type['unique'];
			$typesById[$type['id']]['count'] = $type['count'];
			$typesById[$type['id']]['docs'] = $type['docs'];
		}

		return $typesById;
	}

    public static function getAnnotationReportLinks($corpusId, $annotationType, $annotationText, $filters)
    {
        global $db;

        $params = array($corpusId, $annotationType, $annotationText);
        $ext_table = DbCorpus::getCorpusExtTable($corpusId);

        $flag_active = false;

        if ($filters['flags'] != null && $filters['flags']['flag'] != "-" && $filters['flags']['flag_status'] != "-"){
            $flag_active = true;
            $params = array(intval($filters['flags']['flag']), $corpusId, $annotationType, $annotationText, intval($filters['flags']['flag_status']));
        }

        if(isset($filters['metadata'])){
            $where_metadata = "";
            $sql_metadata = "";
            foreach($filters['metadata'] as $column => $metadata){
                if($metadata != "0"){
                    $where_metadata .=  " AND ext." . $column . " = '" . $metadata ."'";
                    if($sql_metadata == ""){
                        $sql_metadata = " JOIN " . $ext_table . " ext ON ext.id = r.id ";
                    }
                }
            }
        }

        $sql = "SELECT DISTINCT r.id, r.title" .
            " FROM reports_annotations ra" .
            " JOIN reports r ON ra.report_id=r.id" .
            ($flag_active ? " JOIN reports_flags rf ON (rf.report_id = r.id AND rf.corpora_flag_id = ?) " : "") .
            $sql_metadata .
            " WHERE r.corpora= ? AND ra.type= ? AND ra.text = ? " .
            $where_metadata .
            ($flag_active ? " AND rf.flag_id = ? " : "") .
            " ORDER BY r.title, r.id";
        return  $db->fetch_rows($sql, $params);
    }

	static function getAnnotationTags($corpus_id, $annotation_type, $session){
		global $db;
		$params = array($corpus_id, $annotation_type);
        $filters = $session;
        $ext_table = DbCorpus::getCorpusExtTable($corpus_id);

        if ($filters['flags'] != null && $filters['flags']['flag'] != "-" && $filters['flags']['flag_status'] != "-"){
            $flag_active = true;
            $params = array(intval($filters['flags']['flag']), intval($corpus_id), $annotation_type, intval($filters['flags']['flag_status']));
        } else{
            $flag_active = false;
        }

        $where_metadata = "";
        $sql_metadata = "";

        if(isset($filters['metadata'])){
            foreach($filters['metadata'] as $column => $metadata){
                if($metadata != "0"){
                    $where_metadata .=  " AND ext." . $column . " = '" . $metadata ."'";
                    if($sql_metadata == ""){
                        $sql_metadata = " JOIN " . $ext_table . " ext ON ext.id = r.id ";
                    }
                }
            }
        }

        if ($filters['subcorpus'] && $filters['subcorpus'] != '0'){
            $params[] = intval($filters['subcorpus']);
            $subcorpus = true;
        } else{
            $subcorpus = false;
        }

        if ( $filters['status'] && $filters['status'] != '0'){
            $params[] = intval($filters['status']);
            $status = true;
        } else{
            $status = false;
        }

		$sql = "SELECT a.text, COUNT(*) AS count ".
				"FROM reports_annotations a ".
				"JOIN reports r ON ( r.id = a.report_id ) ".
				"JOIN annotation_types at ON ( at.name = a.type ) ".
				"JOIN annotation_subsets ansub ON ( at.annotation_subset_id = ansub.annotation_subset_id ) ".
                ($flag_active ? " JOIN reports_flags rf ON (rf.report_id = r.id AND rf.corpora_flag_id = ?) " : "") .
                $sql_metadata .
				"WHERE r.corpora = ? ".
				"AND at.name = ? ".
                $where_metadata .
                ($flag_active ? " AND rf.flag_id = ? " : "") .
				( $subcorpus ? " AND r.subcorpus_id = ? " : "") .
				( $status ? " AND r.status = ? " : "") .
				"GROUP BY a.type, a.text ".
				"ORDER BY a.type, count desc";

		return  $db->fetch_rows($sql, $params);
	}

    static private function annotationStructureFromDBToArrayTree($dbResult,$limited = false) {

        if($limited){
            // key for this array is [set_id,subset_id]
            $typesCountForSetSubset = array();
            $maxTypesLimitThreshold = Config::Config()->get_max_types_limit_threshold();
        }

        $annotation_sets = array();
        foreach($dbResult as $at){
            $set_id = $at['set_id'];
            $subset_id = $at['subset_id'];
            if (!isset($annotation_sets[$set_id])){
                $annotation_sets[$set_id] = array('name' => $at['set_name']);
            }
            if (!isset($annotation_sets[$set_id][$subset_id])){
                $annotation_sets[$set_id][$subset_id] = array('name' => $at['subset_name']);
            }

            if($limited){
                // counts types for set,subset
                $typesCountForSetSubset[$set_id][$subset_id] = 1 +
                    ( isset($typesCountForSetSubset[$set_id][$subset_id])
                        ? $typesCountForSetSubset[$set_id][$subset_id] : 0 );
                // test threshold
                if($typesCountForSetSubset[$set_id][$subset_id]
                    == $maxTypesLimitThreshold ) {
                    $annotation_sets[$set_id][$subset_id][MAX_TYPES_LABEL_INDEX] = MAX_TYPES_NAME_LABEL;
                }
                if($typesCountForSetSubset[$set_id][$subset_id]
                    < $maxTypesLimitThreshold ) {
                    $annotation_sets[$set_id][$subset_id][$at['type_id']] = $at['type_name'];
                }
            } else {
                // old method generating very big html structure
                $annotation_sets[$set_id][$subset_id][$at['type_id']] = $at['type_name'];
            } // if !limited

        } // foreach()

        return $annotation_sets;

    } // annotationStructureFromDBToArrayTree

	static function getAnnotationStructureByCorpora($corpus_id,$limited = false){
		global $db;

		$sql = "SELECT ans.annotation_set_id AS set_id, ans.name AS set_name, ansub.annotation_subset_id AS subset_id, ".
				"ansub.name AS subset_name, at.name AS type_name, at.annotation_type_id AS type_id FROM annotation_types at ".
				"JOIN annotation_subsets ansub USING(annotation_subset_id) ".
				"JOIN annotation_sets ans USING(annotation_set_id) ".
				"LEFT JOIN annotation_sets_corpora ac USING(annotation_set_id) ".
				"WHERE ac.corpus_id = ?";

		$annotation_types = $db->fetch_rows($sql,array($corpus_id));

		return self::annotationStructureFromDBToArrayTree($annotation_types,$limited);

	} // getAnnotationStructureByCorpora()

    /*
     *  for one integer $corpus_id returns list of all annotation sets
     *  id used in this corpus. No deep annotation structure data.
     *
     *  @param      $corpus_id  int corpus ID
     *
     *  @return     array ( may be empty ) of annotation id's
     */
    static function getAnnotationSetsForCorpora($corpus_id) {

        global $db;

        $sql = "SELECT annotation_set_id FROM annotation_sets_corpora WHERE corpus_id=?";
        $annotation_sets = $db->fetch_rows($sql,array($corpus_id));
        $result = array();
        foreach($annotation_sets as $row){
            if(isset($row['annotation_set_id'])) {
                $result[] = $row['annotation_set_id'];
            }
        } 
        return $result;           

    } // getAnnotationSetsForCorpora()

	static function getReportAnnotationsByTypes($report_id, $types){
		global $db;

		$sql = "SELECT rao.*, at.annotation_type_id AS atid, at.css, ral.lemma AS lemma FROM `reports_annotations_optimized` rao ".
				"JOIN `annotation_types` at ON(rao.type_id = at.annotation_type_id) ".
				"LEFT JOIN  `reports_annotations_lemma` ral ON ( rao.id = ral.report_annotation_id ) ".
				"WHERE rao.report_id = ".$report_id." AND at.annotation_type_id IN(".implode(",",$types).") ".
				" ORDER BY `from` ASC, `to` ASC";

		//$typesList = implode(",",$types);

		$annotations = $db->fetch_rows($sql);//, array($report_id, $typesList));
		//echo $sql;die;
		return $annotations;
	}

	static function getIdByName($name){
		global $db;
		$sql = "SELECT annotation_type_id FROM annotation_types WHERE name=?";
		return $db->fetch_one($sql,array($name));
	}

	static function getReportAnnotationsChannelsByReportId($report_id, $regex){
		global $db;

		$sql = "SELECT rao.id as `id`, text,`from`,`to`,name, lemma FROM reports_annotations_optimized rao
				LEFT JOIN annotation_types at ON(at.annotation_type_id = rao.type_id)
				LEFT JOIN reports_annotations_lemma ral ON(rao.id = ral.report_annotation_id) 
				WHERE report_id = ? AND at.name REGEXP ? ORDER BY `from` ASC";

		$annotations = $db->fetch_rows($sql, array($report_id, $regex));
		// Group by channels
		$annotationsChannels = array();
		foreach($annotations as $annotation){
			$channelId = $annotation['name'];
			if(!array_key_exists($channelId, $annotationsChannels)) $annotationsChannels[$channelId] = array();
			$annotationsChannels[$channelId][(int)$annotation['from']] = $annotation;
		}

		return $annotationsChannels;
	}

	static function saveAnnotation($document_id, $channel, $from, $text, $user, $stage, $source, $annotation_set_id, $ignore_duplicates = false, $ignore_unknown = false){
		global $db;

		$sql = 'SELECT annotation_type_id FROM annotation_types WHERE name=? AND group_id = ?';
		$annotation_type_id = $db->fetch_one($sql, array($channel, $annotation_set_id));
        $unknown_annotations = array();

        //Do not insert unknown annotation types
		if($annotation_type_id == null){
            $unknown_annotations[] = $channel;
            if($ignore_unknown){
                return null;
            } else{
                return array('error' => $channel);
            }
        }


        $to = $from + mb_strlen(preg_replace("/\n+|\r+|\s+/","",$text), 'utf-8') -1;
        $text = addslashes($text);

		$sql  = "SELECT id FROM reports_annotations_optimized WHERE report_id = ? AND type_id = ? AND `from` = ? and `to` = ? AND `text` = ?";
        $existing_annotation = $db->fetch_rows($sql, array($document_id, $annotation_type_id, $from, $to, $text));
        if($existing_annotation != null && $ignore_duplicates){
            return null;
        } else{
            $sql = "INSERT INTO `reports_annotations_optimized` (`report_id`,`type_id`,`from`,`to`,`text`,`user_id`,`creation_time`,`stage`,`source`) " .
                "VALUES (?, ?, ?, ?, ?, ?, now(), ?, ? )";

            $db->execute($sql, array($document_id, $annotation_type_id, $from, $to, $text, $user, $stage, $source));
            $id = $db->last_id();
            return $id;
        }
	}

	/**
	 * Set annotation lemma.
	 * @param $db {Database} Database obejct.
	 * @param $annotation_id {int} Annotation identifier.
	 * @param $lemma {string} Annotation lemma
	 */
	static function setAnnotationLemma($db, $annotation_id, $lemma){
		$db->replace("reports_annotations_lemma", array("report_annotation_id"=>$annotation_id, "lemma"=>$lemma));
	}

	static function addRelation($rel1, $rel2, $user){
		global $db;
		$sql = "INSERT INTO `relations` (`relation_type_id`,`source_id`,`target_id`,`date`,`user_id`) VALUES (1,?,?,now(), ?)";
		$db->execute($sql, array($rel1, $rel2, $user));
	}

	static function addCoreference($rel1, $rel2, $user){
		global $db;
		$sql = "INSERT INTO `relations` (`relation_type_id`,`source_id`,`target_id`,`date`,`user_id`) VALUES (6,?,?,now(), ?)";
		$db->execute($sql, array($rel1, $rel2, $user));
	}

	/**
	 * Zwraca listę użytkowników z liczbą anotacji o określonych parametrach.
	 *
	 * @param unknown $corpus_id
	 * @param unknown $subcorpus_ids
	 * @param unknown $report_ids
	 * @param unknown $annotation_set_id
	 * @param unknown $annotation_type_ids
	 * @param unknown $flags
	 * @param unknown $stage
	 * @return {Array}
	 */
	static function getUserAnnotationCount($corpus_id=null, $subcorpus_ids=null, $report_ids=null,
                                   $annotation_set_id=null, $annotation_type_ids=null, $flags=null, $stage=null){

		global $db;

		$params = array();
		$params_where = array();
		$sql_where = array();

		$sql = "SELECT u.*, COUNT(DISTINCT a.id) AS annotation_count, COUNT(DISTINCT a.report_id) AS document_count"
				." FROM users u JOIN `reports_annotations_optimized` a ON (u.user_id=a.user_id)";

		if ( $corpus_id || ($subcorpus_ids !==null && count($subcorpus_ids) > 0) ){
			$sql .= " JOIN reports r ON a.report_id = r.id";
		}

		if ( $corpus_id ){
			$params_where[] = $corpus_id;
			$sql_where[] = "r.corpora = ?";
		}

		if ( $subcorpus_ids !==null && count($subcorpus_ids) > 0 ){
			$params_where = array_merge($params_where, $subcorpus_ids);
			$sql_where[] = "r.subcorpus_id IN (" . implode(",", array_fill(0, count($subcorpus_ids), "?")) . ")";
		}

		if ( $report_ids !==null && count($report_ids) > 0 ){
			$params_where = array_merge($params_where, $report_ids);
			$sql_where[] = "a.report_id IN (" . implode(",", array_fill(0, count($report_ids), "?")) . ")";
		}

		if ( $annotation_set_id && count($annotation_set_id) > 0 ){
			$params_where[] = $annotation_set_id;
			$sql .= " JOIN annotation_types t ON (a.type_id = t.annotation_type_id)";
			$sql_where[] = "t.group_id = ?";
		}

		if ( $annotation_type_ids !== null ){
			$annotation_type_ids = array_map(intval, $annotation_type_ids);
			if ( count($annotation_type_ids) > 0 ){
				$params_where = array_merge($params_where, $annotation_type_ids);
				$sql_where[] = "a.type_id IN (" . implode(",", array_fill(0, count($annotation_type_ids), "?")) .")";
			}
			else{
				/* Jeżeli tablica z identyfikatorami typów anotacji jest pusta, to nie zostanie zwrócona żadna anotacje */
				return array();
			}
		}

		if ( $stage ){
			$params_where[] = $stage;
			$sql_where[] = "a.stage = ?";
		}

		if ( $flags !== null && is_array($flags) && count($flags) > 0 ){
			$sql .= " LEFT JOIN reports_flags rf ON (rf.report_id = r.id AND rf.corpora_flag_id = ?)";
			$sql_where[] = "rf.flag_id = ?";
			$keys = array_keys($flags);
			$params[] = $keys[0];
			$params_where[] = $flags[$keys[0]];
		}

		if ( count($sql_where) > 0 ){
			$sql .= " WHERE " . implode(" AND ", $sql_where);
		}

		$sql .= " GROUP BY u.user_id";
		return $db->fetch_rows($sql, array_merge($params, $params_where));
	}

	/**
	 *
	 * @param unknown $user_id
	 * @param unknown $corpus_id
	 * @param unknown $annotation_set_id
	 * @param unknown $stage
	 * @return {Array}
	 */
	static function getUserAnnotations($user_id=null, $corpus_id=null, $subcorpus_ids=null,
			$annotation_set_id=null, $annotation_type_ids=null, $flags=null, $stage=null){
		global $db;

		$params = array();
		$params_where = array();
		$sql_where = array();

		$sql = "SELECT a.*, t.name AS annotation_name, l.lemma FROM users u"
				." JOIN `reports_annotations_optimized` a ON (u.user_id=a.user_id)"
				." LEFT JOIN `reports_annotations_lemma` l ON (a.id=l.report_annotation_id)"
				." JOIN `annotation_types` t ON (a.type_id = t.annotation_type_id)";

		if ( $user_id !== null ){
			$params_where[] = $user_id;
			$sql_where[] = "a.user_id = ?";
		}

		if ( $corpus_id || ($subcorpus_ids !==null && count($subcorpus_ids) > 0) ){
			$sql .= " JOIN reports r ON a.report_id = r.id";
		}

		if ( $corpus_id ){
			$params_where[] = $corpus_id;
			$sql_where[] = "r.corpora = ?";
		}

		if ( $subcorpus_ids !==null && count($subcorpus_ids) > 0 ){
			$params_where = array_merge($params_where, $subcorpus_ids);
			$sql_where[] = "r.subcorpus_id IN (" . implode(",", array_fill(0, count($subcorpus_ids), "?")) . ")";
		}

		if ( $annotation_set_id ){
			$params_where[] = $annotation_set_id;
			$sql_where[] = "t.group_id = ?";
		}

		if ( $annotation_type_ids !== null ){
			$annotation_type_ids = array_map(intval, $annotation_type_ids);
			if ( count($annotation_type_ids) > 0 ){
				$params_where = array_merge($params_where, $annotation_type_ids);
				$sql_where[] = "a.type_id IN (" . implode(",", array_fill(0, count($annotation_type_ids), "?")) .")";
			}
			else{
				/* Jeżeli tablica z identyfikatorami typów anotacji jest pusta, to nie zostanie zwrócona żadna anotacje */
				return array();
			}
		}

		if ( $stage ){
			$params_where[] = $stage;
			$sql_where[] = "a.stage = ?";
		}

		if ( $flags !== null && is_array($flags) && count($flags) > 0 ){
			$sql .= " LEFT JOIN reports_flags rf ON (rf.report_id = r.id AND rf.corpora_flag_id = ?)";
			$sql_where[] = "rf.flag_id = ?";
			$keys = array_keys($flags);
			$params[] = $keys[0];
			$params_where[] = $flags[$keys[0]];
		}

		if ( count($sql_where) > 0 ){
			$sql .= " WHERE " . implode(" AND ", $sql_where);
		}

		return $db->fetch_rows($sql, array_merge($params, $params_where));
	}

	/**
	 * Zwraca liczbę anotacji spełniających określone warunki.
	 * @param unknown $user_id
	 * @param unknown $corpus_id
	 * @param unknown $annotation_set_id
	 * @param unknown $stage
	 * @return {Array}
	 */
	static function getAnnotationCount($user_id=null, $corpus_id=null, $subcorpus_ids=null,
			$annotation_set_id=null, $annotation_type_ids=null, $flags=null, $stage=null){
		global $db;

		$params = array();
		$params_where = array();
		$sql_where = array();

		$sql = "SELECT COUNT(DISTINCT a.id) as `count` FROM users u"
				." JOIN `reports_annotations_optimized` a ON (u.user_id=a.user_id)"
				." LEFT JOIN `reports_annotations_lemma` l ON (a.id=l.report_annotation_id)"
				." JOIN `annotation_types` t ON (a.type_id = t.annotation_type_id)";

		if ( $user_id !== null ){
			$params_where[] = $user_id;
			$sql_where[] = "a.user_id = ?";
		}

		if ( $corpus_id || ($subcorpus_ids !==null && count($subcorpus_ids) > 0) ){
			$sql .= " JOIN reports r ON a.report_id = r.id";
		}

		if ( $corpus_id ){
			$params_where[] = $corpus_id;
			$sql_where[] = "r.corpora = ?";
		}

		if ( $subcorpus_ids !==null && count($subcorpus_ids) > 0 ){
			$params_where = array_merge($params_where, $subcorpus_ids);
			$sql_where[] = "r.subcorpus_id IN (" . implode(",", array_fill(0, count($subcorpus_ids), "?")) . ")";
		}

		if ( $annotation_set_id ){
			$params_where[] = $annotation_set_id;
			$sql_where[] = "t.group_id = ?";
		}

		if ( $annotation_type_ids !== null ){
			$annotation_type_ids = array_map(intval, $annotation_type_ids);
			if ( count($annotation_type_ids) > 0 ){
				$params_where = array_merge($params_where, $annotation_type_ids);
				$sql_where[] = "a.type_id IN (" . implode(",", array_fill(0, count($annotation_type_ids), "?")) .")";
			}
			else{
				/* Jeżeli tablica z identyfikatorami typów anotacji jest pusta, to nie zostanie zwrócona żadna anotacje */
				return array();
			}
		}

		if ( $stage ){
			$params_where[] = $stage;
			$sql_where[] = "a.stage = ?";
		}

		if ( $flags !== null && is_array($flags) && count($flags) > 0 ){
			$sql .= " LEFT JOIN reports_flags rf ON (rf.report_id = r.id AND rf.corpora_flag_id = ?)";
			$sql_where[] = "rf.flag_id = ?";
			$keys = array_keys($flags);
			$params[] = $keys[0];
			$params_where[] = $flags[$keys[0]];
		}

		if ( count($sql_where) > 0 ){
			$sql .= " WHERE " . implode(" AND ", $sql_where);
		}

		return $db->fetch_one($sql, array_merge($params, $params_where));
	}



    /**
	 * Zwraca liczbę anotacji dla każdego typu anotacji dla danego korpusu.
	 * @param unknown $corpus_id
	 */
	function getAnnotationByTypeCount($corpus_id){
		global $db;
		$sql = "SELECT at.annotation_type_id, at.name, COUNT(an.id) AS c".
				" FROM annotation_types at".
				" JOIN reports_annotations_optimized an ON (an.type_id = at.annotation_type_id)".
				" JOIN reports r ON (r.id = an.report_id)".
				" WHERE r.corpora = ?".
				" GROUP BY at.annotation_type_id".
				" ORDER BY at.name ASC";
		$params = array($corpus_id);
		return $db->fetch_rows($sql, $params);
	}

	/**
	 * Zwraca liczbę anotacji z podziałem na stage anotacji.
	 * @param unknown $corpus_id
	 * @return {Array}
	 */
	function getAnnotationByStageCount($corpus_id){
		global $db;
		$sql = "SELECT an.stage, COUNT(*) AS c" .
				" FROM reports_annotations_optimized an ".
				" JOIN reports r ON (r.id = an.report_id)" .
				" WHERE r.corpora = ?" .
				" GROUP BY an.stage";
		$params = array($corpus_id);
		return $db->fetch_rows($sql, $params);
	}

	/**
	 * Wraca liczbę anotacji z podziałem na stage anotacji.
	 * @param unknown $corpus_id
	 * @return {Array}
	 */
	function getAnnotationBySetCount($corpus_id){
		global $db;
		$sql = "SELECT s.annotation_set_id, s.name AS name, COUNT(*) AS c" .
				" FROM reports_annotations_optimized an ".
				" JOIN reports r ON (r.id = an.report_id)" .
				" JOIN annotation_types at ON (at.annotation_type_id = an.type_id)" .
				" JOIN annotation_sets s ON (at.group_id = s.annotation_set_id)".
				" WHERE r.corpora = ?" .
				" GROUP BY s.annotation_set_id".
				" ORDER BY name ASC";
		$params = array($corpus_id);
		return $db->fetch_rows($sql, $params);
	}


	/**
	 * Zwraca liczbę dokumentów zawierających anotacje spełniające określone warunki.
	 * @param unknown $user_id
	 * @param unknown $corpus_id
	 * @param unknown $annotation_set_id
	 * @param unknown $stage
	 * @return {Array}
	 */
	static function getAnnotationDocCount($user_id=null, $corpus_id=null, $subcorpus_ids=null,
			$annotation_set_id=null, $annotation_type_ids=null, $flags=null, $stage=null){
		global $db;

		$params = array();
		$params_where = array();
		$sql_where = array();

		$sql = "SELECT COUNT(DISTINCT a.report_id) as `count` FROM users u"
				." JOIN `reports_annotations_optimized` a ON (u.user_id=a.user_id)"
				." LEFT JOIN `reports_annotations_lemma` l ON (a.id=l.report_annotation_id)"
				." JOIN `annotation_types` t ON (a.type_id = t.annotation_type_id)";

		if ( $user_id !== null ){
			$params_where[] = $user_id;
			$sql_where[] = "a.user_id = ?";
		}

		if ( $corpus_id || ($subcorpus_ids !==null && count($subcorpus_ids) > 0) )
		{
			$sql .= " JOIN reports r ON a.report_id = r.id";
		}

		if ( $corpus_id ){
			$params_where[] = $corpus_id;
			$sql_where[] = "r.corpora = ?";
		}

		if ( $subcorpus_ids !==null && count($subcorpus_ids) > 0 ){
			$params_where = array_merge($params_where, $subcorpus_ids);
			$sql_where[] = "r.subcorpus_id IN (" . implode(",", array_fill(0, count($subcorpus_ids), "?")) . ")";
		}

		if ( $annotation_set_id ){
			$params_where[] = $annotation_set_id;
			$sql_where[] = "t.group_id = ?";
		}

		if ( $annotation_type_ids !== null ){
			$annotation_type_ids = array_map(intval, $annotation_type_ids);
			if ( count($annotation_type_ids) > 0 ){
				$params_where = array_merge($params_where, $annotation_type_ids);
				$sql_where[] = "a.type_id IN (" . implode(",", array_fill(0, count($annotation_type_ids), "?")) .")";
			}
			else{
				/* Jeżeli tablica z identyfikatorami typów anotacji jest pusta, to nie zostanie zwrócona żadna anotacje */
				return array();
			}
		}

		if ( $stage ){
			$params_where[] = $stage;
			$sql_where[] = "a.stage = ?";
		}

		if ( $flags !== null && is_array($flags) && count($flags) > 0 ){
			$sql .= " LEFT JOIN reports_flags rf ON (rf.report_id = r.id AND rf.corpora_flag_id = ?)";
			$sql_where[] = "rf.flag_id = ?";
			$keys = array_keys($flags);
			$params[] = $keys[0];
			$params_where[] = $flags[$keys[0]];
		}

		if ( count($sql_where) > 0 ){
			$sql .= " WHERE " . implode(" AND ", $sql_where);
		}

		return $db->fetch_one($sql, array_merge($params, $params_where));
	}

    /**
     * Returns the default visibility status of an annotation from `annotation_types`.
     * @param $id - id anotacji
     * @return {Array}
     */
    static function getAnnotationVisibility($id){
        global $db;

        $sql = "SELECT `shortlist` FROM `annotation_types` WHERE annotation_type_id = ?";
        return $db->fetch_rows($sql, array($id));
    }

    /**
     * Deletes user annotation status from table `annotation_types_shortlist`.
     * @param $user_id
     * @param $id - annotation id
     */
    static function deleteUserAnnotationStatus($user_id, $id){
        global $db;

        $sql_delete = "DELETE FROM `annotation_types_shortlist` WHERE (user_id = ? AND annotation_type_id = ?)";
        $db->execute($sql_delete, array($user_id, $id));
    }


    /**
     * Sets user annotation status in table `annotation_types_shortlist`.
     * @param $annotation - {Array} containing:
     *      user_id
     *      annotation_type_id - annotation id
     *      shortlist - 1 if hiding, 0 if showing
     */
    static function setUserAnnotationStatus($annotation){
        global $db;
        $db->replace("annotation_types_shortlist", $annotation);
    }


    static function getAnnotationSetSubsetOfType($annotation_type_id){
        global $db;

        $sql = "SELECT annotation_subset_id, group_id AS 'annotation_set_id', annotation_type_id FROM annotation_types at WHERE at.annotation_type_id = ?";
        $annotation_structure = $db->fetch_rows($sql, array($annotation_type_id));

        return $annotation_structure[0];
    }

    static function groupAnnotationsByRangesOld($annotations, $user_id1, $user_id2){

        $groups = array();
        $last_range = "";
        foreach ($annotations as $an){
            if ( $an[DB_COLUMN_REPORTS_ANNOTATIONS__USER_ID] == $user_id1
                || $an[DB_COLUMN_REPORTS_ANNOTATIONS__USER_ID] == $user_id2
                || $an[DB_COLUMN_REPORTS_ANNOTATIONS__STAGE] == "final"){
                $range = sprintf("%d:%d", $an[DB_COLUMN_REPORTS_ANNOTATIONS__FROM], $an[DB_COLUMN_REPORTS_ANNOTATIONS__TO]);
                if ( $range != $last_range ){
                    $group = array();
                    $group[DB_COLUMN_REPORTS_ANNOTATIONS__FROM] = $an[DB_COLUMN_REPORTS_ANNOTATIONS__FROM];
                    $group[DB_COLUMN_REPORTS_ANNOTATIONS__TO] = $an[DB_COLUMN_REPORTS_ANNOTATIONS__TO];
                    $group[DB_COLUMN_REPORTS_ANNOTATIONS__TEXT] = $an[DB_COLUMN_REPORTS_ANNOTATIONS__TEXT];
                    $group["user1"] = null;
                    $group["user2"] = null;
                    $group["final"] = null;
                    $groups[] = $group;
                }
                $last_range = $range;
                $type = array();
                $type[DB_COLUMN_REPORTS_ANNOTATIONS__REPORT_ANNOTATION_ID] = $an[DB_COLUMN_REPORTS_ANNOTATIONS__REPORT_ANNOTATION_ID];
                $type[DB_COLUMN_REPORTS_ANNOTATIONS__ANNOTATION_TYPE_ID] = $an[DB_COLUMN_REPORTS_ANNOTATIONS__ANNOTATION_TYPE_ID];
                $type["type"] = $an['type'];

                if ( $an[DB_COLUMN_REPORTS_ANNOTATIONS__STAGE] == "agreement" ) {
                    if ($an[DB_COLUMN_REPORTS_ANNOTATIONS__USER_ID] == $user_id1) {
                        $groups[count($groups) - 1]["user1"] = $type;
                    } else if ($an[DB_COLUMN_REPORTS_ANNOTATIONS__USER_ID] == $user_id2) {
                        $groups[count($groups) - 1]["user2"] = $type;
                    }
                }
                else if ($an[DB_COLUMN_REPORTS_ANNOTATIONS__STAGE] == "final"){
                    $groups[count($groups)-1]["final"] = $type;
                }
            }
        }

        return $groups;
    }

    /**
     * Grupowanie anotacji po zakresie
     * @param unknown $annotations
     */
    static function groupAnnotationsByRanges($annotations, $user_id1, $user_id2, $available_annotation_types){
        $groups = array();
        foreach ($annotations as $an){
            if ( $an[DB_COLUMN_REPORTS_ANNOTATIONS__USER_ID] == $user_id1
                || $an[DB_COLUMN_REPORTS_ANNOTATIONS__USER_ID] == $user_id2
                || $an[DB_COLUMN_REPORTS_ANNOTATIONS__STAGE] == "final"){
                $range = sprintf("%d:%d", $an[DB_COLUMN_REPORTS_ANNOTATIONS__FROM], $an[DB_COLUMN_REPORTS_ANNOTATIONS__TO]);
                if(!isset($groups[$range])){
                    $group = array();
                    $group[DB_COLUMN_REPORTS_ANNOTATIONS__FROM] = $an[DB_COLUMN_REPORTS_ANNOTATIONS__FROM];
                    $group[DB_COLUMN_REPORTS_ANNOTATIONS__TO] = $an[DB_COLUMN_REPORTS_ANNOTATIONS__TO];
                    $group[DB_COLUMN_REPORTS_ANNOTATIONS__TEXT] = $an[DB_COLUMN_REPORTS_ANNOTATIONS__TEXT];
                    $group["user1"] = null;
                    $group["user2"] = null;
                    $group["final"] = null;
                    $group["available_annotation_types"] = array();
                    $group["available_annotation_types"] = $available_annotation_types;
                    $groups[$range] = $group;
                }


                $type = array();
                //$type[DB_COLUMN_REPORTS_ANNOTATIONS__REPORT_ANNOTATION_ID] = $an[DB_COLUMN_REPORTS_ANNOTATIONS__REPORT_ANNOTATION_ID];
                $type[DB_COLUMN_REPORTS_ANNOTATIONS__ANNOTATION_TYPE_ID] = $an[DB_COLUMN_REPORTS_ANNOTATIONS__ANNOTATION_TYPE_ID];
                $type["type"] = $an['type'];

                if($an[DB_COLUMN_REPORTS_ANNOTATIONS__STAGE] == "agreement"){
                    if ($an[DB_COLUMN_REPORTS_ANNOTATIONS__USER_ID] == $user_id1) {
                        $groups[$range]["user1"][] = $type;
                    } else if ($an[DB_COLUMN_REPORTS_ANNOTATIONS__USER_ID] == $user_id2) {
                        $groups[$range]["user2"][] = $type;
                    }
                }
                else if ($an[DB_COLUMN_REPORTS_ANNOTATIONS__STAGE] == "final"){
                    $type["annotation_id"] = $an['id'];
                    $groups[$range]["final"][] = $type;
                }
            }
        }

        $groups = self::handleFinalAnnotations($groups);
        $groups = self::handleAnnotationAgreement($groups);
        return $groups;
    }

    private function handleAnnotationAgreement($groups){
        foreach($groups as $range => $group){
            if($group['user1'] == null){
                $group['user1'] = array();
            }

            if($group['user2'] == null){
                $group['user2'] = array();
            }

            //Check which annotations are agreed upon and which not.
            $groups[$range]['all_annotations'] = array_map("unserialize", array_unique(array_map("serialize", array_merge($group['user1'], $group['user2']))));
            foreach($groups[$range]['all_annotations'] as $key=>$annotation){

                //Check user1's picked annotations
                $user1_pick = false;
                foreach($group['user1'] as $user1){
                    if($user1['type_id'] == $annotation['type_id']){
                        $user1_pick = true;
                        break;
                    }
                }

                //Check user2's picked annotations
                $user2_pick = false;
                foreach($group['user2'] as $user2){
                    if($user2['type_id'] == $annotation['type_id']){
                        $user2_pick = true;
                        break;
                    }
                }

                //If both users agree on an annotation, add it to a 'a_and_b' array.
                if($user1_pick && $user2_pick){
                    $groups[$range]['all_annotations'][$key]['agreement'] = 'a_and_b';
                    $groups[$range]['a_and_b'][] = $annotation;
                    //Mark annotation type as checked if both users agree
                    foreach($groups[$range]['available_annotation_types'] as $an_key => $annotation_type){
                       if($annotation_type['annotation_type_id'] == $groups[$range]['all_annotations'][$key]['type_id'] && !$groups[$range]['all_annotations'][$key]['final']){
                            $groups[$range]['available_annotation_types'][$an_key]['checked'] = true;
                        }
                    }

                }//If only user1 agrees, add to 'only_a' array.
                else if($user1_pick){
                    $groups[$range]['all_annotations'][$key]['agreement'] = 'only_a';
                }//If only user2 agrees, add to 'only_b' array.
                else if($user2_pick){
                    $groups[$range]['all_annotations'][$key]['agreement'] = 'only_b';
                }
            }

            $all_final = true;
            if(isset($groups[$range]['a_and_b'])){
                foreach($groups[$range]['a_and_b'] as $index => $a_b){
                    if(!$a_b['final']){
                        $all_final = false;
                    } else{
                        unset($groups[$range]['a_and_b'][$index]);
                    }
                }
            }

            if($all_final){
                $groups[$range]['all_final'] = true;
            }

        }
        return $groups;
    }


    private function handleFinalAnnotations($groups){
        foreach($groups as $range => $group){
            //Check which annotations exist as final annotation
            if($group['final'] != null){
                //Add 'final' parameter to annotations which exist in DB as final
                foreach($group['final'] as $group_final){
                    if($group['user1'] != null){
                        //Check User1's annotations
                        foreach($group['user1'] as $user_1_key => $user_1_an){
                            if($user_1_an['type_id'] == $group_final['type_id']){
                                $groups[$range]['user1'][$user_1_key]['final'] = true;
                            }
                        }
                    }

                    if($group['user2'] != null){
                        //Check User2's annotations
                        foreach($group['user2'] as $user_2_key => $user_2_an) {
                            if ($user_2_an['type_id'] == $group_final['type_id']) {
                                $groups[$range]['user2'][$user_2_key]['final'] = true;
                            }
                        }
                    }
                }

                //Check if all annotations exist in DB as final
                $all_final = true;

                //User1's annotations are all final
                if($group['user1'] != null){
                    foreach($group['user1'] as $user_key => $user_1){
                        if(!isset($groups[$range]['user1'][$user_key]['final'])){
                            $all_final = false;
                            break;
                        }
                    }
                }
                //User2's annotations are all final
                if($group['user2'] != null){
                    foreach($group['user2'] as $user_key => $user_2){
                        if(!isset($groups[$range]['user2'][$user_key]['final'])){
                            $all_final = false;
                            break;
                        }
                    }
                }

                if($group['user1'] == null && $group['user2'] == null){
                    $all_final = false;
                }

                //If all annotations already exist as 'final', add 'all_final' parameter to the groups array.
                if($all_final){
                    $groups[$range]['all_final'] = true;
                }

            }
        }

        return $groups;
    }

    /**
     * @param $report_id
     */

    static function getUsersWithAnnotations($report_id){
        global $db;
        $sql = "SELECT u.screename, u.user_id, COUNT(rao.id) AS 'ann_count' 
                FROM users u 
                JOIN reports_annotations_optimized rao ON (rao.user_id = u.user_id AND rao.report_id = ?)
                GROUP BY u.user_id
                ORDER BY u.screename ASC";
        $params = array($report_id);
        $result = $db->fetch_rows($sql, $params);

        return $result;
    }

    static function getBootstrappedAnnotationsSummary($report_id){
        global $db;
        $report = new TableReport($report_id);

        $builder = new SqlBuilder("annotation_sets", "s");
        $builder->addSelectColumn(
            new SqlBuilderSelect("s.name", "annotation_set_name"));
        $builder->addSelectColumn(
            new SqlBuilderSelect("s.annotation_set_id"));
        $builder->addSelectColumn(
            new SqlBuilderSelect("SUM(IF(an.stage='new',1,0))", "count_new"));
        $builder->addSelectColumn(
            new SqlBuilderSelect("SUM(IF(an.stage='final',1,0))", "count_final"));
        $builder->addSelectColumn(
            new SqlBuilderSelect("SUM(IF(an.stage='discarded',1,0))", "count_discarded"));
        $builder->addJoinTable(
            new SqlBuilderJoin(DB_TABLE_ANNOTATION_SETS_CORPORA, "sc",
                "sc.annotation_set_id = s.annotation_set_id AND sc.corpus_id = ?", array($report->getCorpusId())));
        $builder->addJoinTable(
            new SqlBuilderJoin(DB_TABLE_ANNOTATION_TYPES, "at", "at.group_id = s.annotation_set_id"));
        $builder->addJoinTable(
            new SqlBuilderJoin(DB_TABLE_REPORTS_ANNOTATIONS, "an",
                "an.type_id = at.annotation_type_id AND an.report_id = ?", array($report_id)));
        $builder->addWhere(new SqlBuilderWhere("sc.annotation_set_id IS NOT NULL"));
        $builder->addGroupBy("s.annotation_set_id");

        list($sql, $params) = $builder->getSql();
        return $db->fetch_rows($sql, $params);
    }

    /**
     * Loads new annotations marked as source=bootstrapping.
     */
    static function getNewBootstrappedAnnotations($db, $report_id, $annotation_set_id){
        $sql = "SELECT an.*, t.name AS type, t.group_id" .
            " FROM reports_annotations an" .
            " JOIN annotation_types t ON (an.type_id = t.annotation_type_id)" .
            " WHERE an.stage='new'" .
            " 	AND an.source='bootstrapping' " .
            "	AND an.report_id = ?" .
            "	AND t.group_id = ?" .
            " ORDER BY an.from, an.to, an.text";
        $annotations =	$db->fetch_rows($sql, array($report_id, $annotation_set_id));
        return $annotations;
    }

    /**
     * Loads bootstrapped annotations that are not marked as new
     */
    static function getOtherBootstrappedAnnotations($db, $report_id, $annotation_set_id){
        $sql = "SELECT * FROM reports_annotations an" .
            " JOIN annotation_types t ON (an.type_id = t.annotation_type_id)" .
            " WHERE an.stage='final' " .
            "	AND an.source='bootstrapping' " .
            "	AND an.report_id = ?" .
            "	AND t.group_id = ?";
        $annotations =$db->fetch_rows($sql, array($report_id, $annotation_set_id));
        return $annotations;
    }

    static function getAnnotationTypesForChangeList($db, $annotation_set_id){
        $sql = "SELECT * FROM annotation_types WHERE group_id=? ORDER BY name";
        return $db->fetch_rows($sql, array($annotation_set_id));
    }

    /**
     * @param $annotationId
     * @param $sharedAttributeId
     * @param $value
     * @param $userId
     */
    static function setSharedAttributeValue($annotationId, $sharedAttributeId, $value, $userId){
        global $db;
        $sql = "REPLACE INTO reports_annotations_shared_attributes (annotation_id, shared_attribute_id, `value`, user_id) VALUES (?,?,?,?)";
        $params = array($annotationId, $sharedAttributeId, $value, $userId);
        $db->execute($sql, $params);
    }

    /**
     * @param $annotationId
     * @throws Exception
     */
    static function removeUnusedAnnotationSharedAttributes($annotationId){
        global $db;
        $sql = "DELETE a
  FROM reports_annotations_shared_attributes a
  JOIN reports_annotations_optimized rao ON a.annotation_id = rao.id
  LEFT JOIN annotation_types_shared_attributes sa ON (a.shared_attribute_id = sa.shared_attribute_id AND rao.type_id = sa.annotation_type_id)
 WHERE a.annotation_id = ? AND sa.annotation_type_id IS NULL;";
        $db->execute($sql, array($annotationId));
    }
}
