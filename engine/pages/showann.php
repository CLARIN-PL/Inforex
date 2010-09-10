<?php
class Page_showann extends CPage{

	var $isSecure = false;
	
	function execute(){		
		global $mdb2, $corpus;

		$cid = $corpus['id'];

		$sql = "SELECT a.*, r.content" .
				" FROM reports_annotations a" .
				" JOIN reports r ON (a.report_id = r.id)" .
				" WHERE a.type = 'PERSON' AND r.corpora = $cid" .
				" ";

		$rows = db_fetch_rows($sql);		

		$sentences = array();
		foreach ($rows as $row){
			$content = $row['content'];
			$content = normalize_content($row['content']);
			$htmlStr = new HtmlStr(html_entity_decode($content, ENT_COMPAT, "UTF-8"));
			
			$htmlStr->insert($row['from'], sprintf("<span style='color: red'>", $row['id'], $row['type']));
			$htmlStr->insert($row['to']+1, "</span>", false);
			
			$content = $htmlStr->getContent();
			
			preg_match("/\n?.*?<\/span>[^\n]*/", $content, $match);
			
			$sentences[] = array( 'html' => $match[0], 'report_id' => $row['report_id']);
		}
		
		$this->set('sentences', $sentences);		
	}
	
}

?>
