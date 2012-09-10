<?php
class Page_annmap extends CPage{

	var $isSecure = true;
	var $roles = array("loggedin");
	
	function checkPermission(){
		return hasCorpusRole("read");
	}
	
	function execute(){		
		global $mdb2, $corpus, $db;
		
		$corpus_id = $corpus['id'];
		$subcorpus = $_GET['subcorpus'];
		
		$params = array($corpus_id);
		if ($subcorpus)
			$params[] = $subcorpus;
				
		$sql = "SELECT a.type, COUNT(*) AS count, COUNT(DISTINCT(a.text)) AS `unique`" .
				" FROM reports_annotations a" .
				" JOIN reports r ON (r.id = a.report_id)" .
				" WHERE status=2 AND r.corpora=?" . ($subcorpus ? " AND r.subcorpus_id = ?" : "") .
				" GROUP BY a.type" .
				" ORDER BY a.type;";
		$annotations_count = $db->fetch_rows($sql, $params); 

		$sql = "SELECT a.type, a.text, COUNT(*) AS count" .
				" FROM reports_annotations a" .
				" JOIN reports r ON (r.id = a.report_id)" .
				" WHERE status=2 AND r.corpora=? AND a.stage='final'" . ($subcorpus ? " AND r.subcorpus_id = ?" : "") .
				" GROUP BY a.type, a.text" .
				" ORDER BY a.type, count DESC";
		$annotations = $db->fetch_rows($sql, $params);
		$annotation_map = array();
		$annotation_type = "";
		$annotation_list = array();


		foreach ($annotations as $an){
			$annotation_map[$an['type']][] = $an;			
		}
		foreach ($annotations_count as $k=>$an){
			$annotations_count[$k]['details'] = $annotation_map[$an['type']];
		}
		
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
		}		
				
		$this->set("sets", $annotation_set_map);
		$this->set("subcorpus", $subcorpus);
		$this->set("subcorpora", DbCorpus::getCorpusSubcorpora($corpus_id));			
	}
}


?>
