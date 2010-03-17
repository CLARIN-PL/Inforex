<?php
class Page_annmap extends CPage{

	var $isSecure = false;
	
	function execute(){		
		global $mdb2;
		
		$sql = "SELECT count(*) FROM reports r JOIN reports_annotations a ON (r.id = a.report_id) WHERE status=2";
		$annotation_count = $mdb2->query($sql)->fetchOne();
		
		$sql = "SELECT type, COUNT(*) AS count, COUNT(DISTINCT(text)) AS `unique` FROM reports_annotations GROUP BY type ORDER BY count desc;";
		if (PEAR::isError($r = $mdb2->query($sql)))
			die("<pre>{$r->getUserInfo()}</pre>");
		$annotations_count = $r->fetchAll(MDB2_FETCHMODE_ASSOC);

		$sql = "SELECT type, text, COUNT(*) AS count, COUNT(DISTINCT(text)) AS `unique` FROM reports_annotations GROUP BY type, text ORDER BY type, count DESC";
		if (PEAR::isError($r = $mdb2->query($sql)))
			die("<pre>{$r->getUserInfo()}</pre>");
		$annotations = $r->fetchAll(MDB2_FETCHMODE_ASSOC);
		$annotation_map = array();
		$annotation_type = "";
		$annotation_list = array();
		foreach ($annotations as $an){			
			if ($an['type'] != $annotation_type){
				if ($annotation_type!=""){
					$annotation_map[$annotation_type] = $annotation_list;
					$annotation_list = array();
				}
				$annotation_type = $an['type'];
			}
			$annotation_list[] = $an;
		}

		// scal listę anotacji z listą szczegółową anotacji
		foreach ($annotations_count as &$an){
			$an['details'] = $annotation_map[$an['type']];
		}
		
		$this->set('annotation_count', number_format($annotation_count, 0, "", "."));
		$this->set('tags', $annotations_count);				
	}
}


?>
