<?php
class Page_annmap extends CPage{

	var $isSecure = false;
	
	function execute(){		
		global $mdb2, $corpus;
		
		$sql = "SELECT count(*)" .
				" FROM reports r JOIN reports_annotations a ON (r.id = a.report_id)" .
				" WHERE status=2 AND corpora={$corpus['id']}";
		$annotation_count = $mdb2->query($sql)->fetchOne();
		$annotation_count = db_fetch_one($sql);
		
		$sql = "SELECT a.type, COUNT(*) AS count, COUNT(DISTINCT(a.text)) AS `unique`" .
				" FROM reports_annotations a" .
				" JOIN reports r ON (r.id = a.report_id)" .
				" WHERE r.corpora={$corpus['id']}" .
				" GROUP BY a.type" .
				" ORDER BY a.type;";
//		if (PEAR::isError($r = $mdb2->query($sql)))
//			die("<pre>{$r->getUserInfo()}</pre>");
//		$annotations_count = $r->fetchAll(MDB2_FETCHMODE_ASSOC);
		$annotations_count = db_fetch_rows($sql); 

		$sql = "SELECT a.type, a.text, COUNT(*) AS count, COUNT(DISTINCT(text)) AS `unique`" .
				" FROM reports_annotations a" .
				" JOIN reports r ON (r.id = a.report_id)" .
				" WHERE r.corpora={$corpus['id']}" .
				" GROUP BY a.type, a.text" .
				" ORDER BY a.type, count DESC";
		if (PEAR::isError($r = $mdb2->query($sql)))
			die("<pre>{$r->getUserInfo()}</pre>");
		$annotations = $r->fetchAll(MDB2_FETCHMODE_ASSOC);
		$annotation_map = array();
		$annotation_type = "";
		$annotation_list = array();

		foreach ($annotations as $an){
			$annotation_map[$an['type']][] = $an;			
		}

		// scal listę anotacji z listą szczegółową anotacji
		foreach ($annotations_count as $k=>$an){
			$annotations_count[$k]['details'] = $annotation_map[$an['type']];
		}
		$this->set('annotation_count', number_format($annotation_count, 0, "", "."));
		$this->set('tags', $annotations_count);				
	}
}


?>
