<?php

class DbReportAnnotationLemma{
	
	/**
	 * Returns lemma of an annotation.
	 * @param unknown $annotation_id
	 */
	static function getAnnotationLemma($annotation_id){
		global $db;
		$sql = "SELECT lemma FROM reports_annotations_lemma WHERE report_annotation_id = ?";
		return $db->fetch_one($sql, array($annotation_id));
	}
	
	static function saveAnnotationLemma($id, $lemma){
		global $db;
		$sql = "INSERT INTO reports_annotations_lemma (report_annotation_id,lemma) ".
				"VALUES (?,?) ON DUPLICATE KEY UPDATE lemma=?;";
		
		$db->execute($sql, array($id,$lemma,$lemma));
	}
	
	static function deleteAnnotationLemma($id){
		global $db;
		$sql = "DELETE FROM reports_annotations_lemma WHERE report_annotation_id=?;";
		$db->execute($sql,array($id));
	}
	
	static function getLemmasByReportsIds($reports_ids){
		global $db;
		$sql = "SELECT * FROM reports_annotations_lemma ral ".
				"JOIN reports_annotations rao ON(ral.report_annotation_id = rao.id) ".
				"WHERE rao.report_id IN(".implode(",",$reports_ids).");";

		$lemmas = $db->fetch_rows($sql);
		$lemmasByReports = array();
		foreach($lemmas as $lemma){
			$report_id = $lemma['report_id'];
			if(!array_key_exists($report_id, $lemmasByReports)){
				$lemmasByReports[$report_id] = array();
			}
			$lemmasByReports[$report_id][] = $lemma;
		}
		
		return $lemmasByReports;
	}

    static function getPropertiesBySets($report_ids=null, $annotation_layers=null, $annotation_names=null){
        global $db;
        // "if(ra.type like 'wsd%', 'sense', ra.type) as" wsd_* traktujemy osobno
        $sql = "SELECT ra.id, ra.type, ra.report_id, sa.name, rasa.value, ra.from, ra.to " .
            " FROM reports_annotations_shared_attributes rasa " .
            " JOIN shared_attributes sa " .
            " ON (rasa.shared_attribute_id=sa.id) " .
            " JOIN reports_annotations ra " .
            " ON (rasa.annotation_id = ra.id) ".
            " LEFT JOIN annotation_types at ON (ra.type=at.name) ";
        $andwhere = array();
        $orwhere = array();
        $andwhere[] = " stage='final' ";
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
        $sql .= "  ORDER BY `from`";

        $properties = $db->fetch_rows($sql);

        $propertiesByReports = array();
        foreach($properties as $property){
            $report_id = $property['report_id'];
            if(!array_key_exists($report_id, $propertiesByReports)){
                $propertiesByReports[$report_id] = array();
            }
            $propertiesByReports[$report_id][] = $property;
        }

        return $propertiesByReports;
    }

	/**
	 * 
	 */
	static function getLemmasBySets2($report_ids=null, $annotation_layers=null, $annotation_names=null){
		global $db;
		// "if(ra.type like 'wsd%', 'sense', ra.type) as" wsd_* traktujemy osobno 
		$sql = "SELECT * " .
				" FROM reports_annotations_lemma ral ".
				" JOIN reports_annotations ra ON(ral.report_annotation_id = ra.id)".
				" LEFT JOIN annotation_types at ON (ra.type=at.name) " .
				" LEFT JOIN reports_annotations_attributes raa ON (ra.id=raa.annotation_id) ";
		$andwhere = array();
		$orwhere = array();		
		$andwhere[] = " stage='final' ";
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
		$lemmas = $db->fetch_rows($sql);

		$lemmasByReports = array();
		foreach($lemmas as $lemma){
			$report_id = $lemma['report_id'];
			if(!array_key_exists($report_id, $lemmasByReports)){
				$lemmasByReports[$report_id] = array();
			}
			$lemmasByReports[$report_id][] = $lemma;
		}
		
		return $lemmasByReports;
	}

	static function getLemmasBySets($report_ids=null, $annotation_layers=null, $annotation_names=null){
		global $db;
		// "if(ra.type like 'wsd%', 'sense', ra.type) as" wsd_* traktujemy osobno
		$sql = "SELECT * " .
				" FROM reports_annotations_lemma ral ".
				" JOIN reports_annotations ra ON(ral.report_annotation_id = ra.id)".
				" LEFT JOIN annotation_types at ON (ra.type=at.name) " .
				" LEFT JOIN reports_annotations_attributes raa ON (ra.id=raa.annotation_id) ";
		$andwhere = array();
		$orwhere = array();
		$andwhere[] = " stage='final' ";
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
	static function getLemmasBySubsets($report_ids=null, $annotation_subset_id=null){
		global $db;
		$sql = "SELECT * " .
				" FROM reports_annotations_lemma ral ".
				" JOIN reports_annotations ra ON(ral.report_annotation_id = ra.id)".
				" LEFT JOIN annotation_types at ON (ra.type=at.name) " .
				" LEFT JOIN reports_annotations_attributes raa ON (ra.id=raa.annotation_id) ";
		$andwhere = array();
		$orwhere = array();		
		$andwhere[] = " stage='final' ";
		if ($report_ids <> null && count($report_ids) > 0)
			$andwhere[] = "report_id IN (" . implode(",",$report_ids) . ")";
		if ($annotation_subset_id <> null && count($annotation_subset_id) > 0)
			$orwhere[] = "at.annotation_subset_id IN (" . implode(",",$annotation_subset_id) . ")";
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

	
	static function deleteAnnotationLemmaByAnnotationRegex($report_id, $regex){
		global $db;
		$sql = "DELETE ral.* FROM reports_annotations_lemma ral 
				LEFT JOIN reports_annotations_optimized rao ON(ral.report_annotation_id = rao.id)
				LEFT JOIN annotation_types at ON(at.annotation_type_id = rao.type_id) 
				WHERE at.name REGEXP ? AND report_id = ?";
		$db->execute($sql, array($regex, $report_id));
	}

}