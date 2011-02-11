<?php
class Page_annmap extends CPage{

	var $isSecure = false;
	
	function execute(){		
		global $mdb2, $corpus;
		
		/*$sql = "SELECT count(*)" .
				" FROM reports r JOIN reports_annotations a ON (r.id = a.report_id)" .
				" WHERE status=2 AND corpora={$corpus['id']}";*/
		$sql = "SELECT count(*)" .
				" FROM reports r JOIN reports_annotations a ON (r.id = a.report_id)" .
				" WHERE status=2 AND corpora={$corpus['id']}";
		//$annotation_count = $mdb2->query($sql)->fetchOne();
		$annotation_count = db_fetch_one($sql);
		
		$sql = "SELECT a.type, COUNT(*) AS count, COUNT(DISTINCT(a.text)) AS `unique`" .
				" FROM reports_annotations a" .
				" JOIN reports r ON (r.id = a.report_id)" .
				" WHERE status=2 AND r.corpora={$corpus['id']}" .
				" GROUP BY a.type" .
				" ORDER BY a.type;";
//		if (PEAR::isError($r = $mdb2->query($sql)))
//			die("<pre>{$r->getUserInfo()}</pre>");
//		$annotations_count = $r->fetchAll(MDB2_FETCHMODE_ASSOC);
		$annotations_count = db_fetch_rows($sql); 

/*		$sql = "SELECT a.type, a.text, COUNT(*) AS count, COUNT(DISTINCT(text)) AS `unique`" .
				" FROM reports_annotations a" .
				" JOIN reports r ON (r.id = a.report_id)" .
				" WHERE r.corpora={$corpus['id']}" .
				" GROUP BY a.type, a.text" .
				" ORDER BY a.type, count DESC";*/
		$sql = "SELECT a.type, a.text, COUNT(*) AS count" .
				" FROM reports_annotations a" .
				" JOIN reports r ON (r.id = a.report_id)" .
				" WHERE status=2 AND r.corpora={$corpus['id']}" .
				" GROUP BY a.type, a.text" .
				" ORDER BY a.type, count DESC";
		/*if (PEAR::isError($r = $mdb2->query($sql)))
			die("<pre>{$r->getUserInfo()}</pre>");
		$annotations = $r->fetchAll(MDB2_FETCHMODE_ASSOC);*/
		$annotations = db_fetch_rows($sql);
		$annotation_map = array();
		$annotation_type = "";
		$annotation_list = array();


		foreach ($annotations as $an){
			$annotation_map[$an['type']][] = $an;			
		}
		//$tmp = $annotation_map;
		// scal listę anotacji z listą szczegółową anotacji
		foreach ($annotations_count as $k=>$an){
			$annotations_count[$k]['details'] = $annotation_map[$an['type']];
		}
		//$tmp = $annotations_count;
		
		//kotu{
		$sql = "SELECT ans.description setname, ansub.description subsetname, at.name typename FROM annotation_types at" .
				" LEFT JOIN annotation_subsets ansub on (at.annotation_subset_id=ansub.annotation_subset_id)" .
				" JOIN annotation_sets ans on (at.group_id=ans.annotation_set_id)" .
				" ORDER BY setname, subsetname, typename";
		
		$annotation_sets = db_fetch_rows($sql);
		$annotation_set_map = array();
		$annotation_set_map["!uncategorized"]=NULL;
		$i=0;
		$annotationsAmount = count($annotations_count);
		foreach ($annotation_sets as $as){
			$setName = $as['setname'];
			$subsetName = $as['subsetname']==NULL ? "!uncategorized" : $as['subsetname'];
			$anntype = $as['typename'];
			foreach ($annotations_count as $ac_elem){				
				if ($ac_elem && $ac_elem['type']==$anntype){
					$annotation_set_map[$setName][$subsetName][$anntype] = $ac_elem;
					$annotation_set_map[$setName][$subsetName]['count']+=$ac_elem['count'];				
					$annotation_set_map[$setName][$subsetName]['unique']+=$ac_elem['unique'];
					$annotation_set_map[$setName]['count']+=$ac_elem['count'];				
					$annotation_set_map[$setName]['unique']+=$ac_elem['unique'];				
					break;
				}
			}
			//$elem = $annotations_count[$i++];
			//$annotation_set_map[$as['setname']][$as['subsetname']==NULL ? "!uncategorized" : $as['subsetname']][$as['typename']] = $elem;//$annotations_count[$as['typename']];
			//if ($elem){				
			//}
		}

		foreach ($annotations_count as $ac_elem){
			$found = 0;
			$type = $ac_elem['type'];
			foreach ($annotation_set_map as $set){
				if (is_array($set)){
					foreach ($set as $subset){
						if (is_array($subset) && array_key_exists($type,$subset)){
							$found=1;
							break;
						}
					}
				}
				if ($found==1) break;
			}
			if ($found==0){
				$annotation_set_map["!uncategorized"]["!uncategorized"][$type]=$ac_elem;
				$annotation_set_map["!uncategorized"]["!uncategorized"]['count']+=$ac_elem['count'];				
				$annotation_set_map["!uncategorized"]["!uncategorized"]['unique']+=$ac_elem['unique'];
				$annotation_set_map["!uncategorized"]['count']+=$ac_elem['count'];				
				$annotation_set_map["!uncategorized"]['unique']+=$ac_elem['unique'];				
			}
						
			/*if ($ac_elem && $ac_elem['type']==$anntype){
				$elem = $ac_elem;
				$annotation_set_map[$setName][$subsetName][$anntype] = $elem;
				$annotation_set_map[$setName][$subsetName]['count']+=$elem['count'];				
				$annotation_set_map[$setName][$subsetName]['unique']+=$elem['unique'];
				$annotation_set_map[$setName]['count']+=$elem['count'];				
				$annotation_set_map[$setName]['unique']+=$elem['unique'];				
				break;
			}*/
		}

		/*foreach ($annotations_count as $ac_elem){
			
			if ($ac_elem && $ac_elem['type']==$anntype){
				$elem = $ac_elem;
				$annotation_set_map[$setName][$subsetName][$anntype] = $elem;
				$annotation_set_map[$setName][$subsetName]['count']+=$elem['count'];				
				$annotation_set_map[$setName][$subsetName]['unique']+=$elem['unique'];
				$annotation_set_map[$setName]['count']+=$elem['count'];				
				$annotation_set_map[$setName]['unique']+=$elem['unique'];				
				break;
			}
		}*/
		
		//$tmp = $annotations_count;
		//$tmp = $annotation_set_map;
		//$tmp = $annotation_sets;
		
		//}
		
		
		
		$this->set('annotation_count', number_format($annotation_count, 0, "", "."));
		//$this->set('tags', $annotations_count);	
		$this->set('sets', $annotation_set_map);
		$this->set('tmp',$tmp);			
	}
}


?>
