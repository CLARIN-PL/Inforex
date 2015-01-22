<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class Ajax_report_autoextension_proper_names extends CPage {
	
	var $isSecure = false;
	
	/**
	 * Generate AJAX output.
	 */
	function execute(){
		$count = 0;
		$count = $this->runModel();
								
		$json = array( "success"=>1, "count"=>$count);
		return $json;
	}

	function runModel(){
		global $mdb2, $user, $corpus, $config;
		
		$count = 0;
		$report_id = intval($_POST['report_id']);
		$user_id = $user['user_id'];
		
		$content = db_fetch_one("SELECT content FROM reports WHERE id = ?", array($report_id));
		$corpus_id = db_fetch_one("SELECT corpora FROM reports WHERE id = ?", array($report_id));
		$content = strip_tags($content);
			
		$liner2 = new WSLiner2("http://kotu88.ddns.net/nerws/ws/nerws.wsdl");
		$tuples = $liner2->chunk($content, "PLAIN:WCRFT", "TUPLES", "ner-names");
		
		if (preg_match_all("/\((.*),(.*),\"(.*)\"\)/", $tuples, $matches, PREG_SET_ORDER)){
			foreach ($matches as $m){
				$annotation_type = strtolower($m[2]);
				list($from, $to) = split(',', $m[1]);
				$ann_text = trim($m[3], '"');
					
				// Todo: kwerendy do przepisania przy użyciu mdb2.
				$sql = "SELECT `id` FROM `reports_annotations` " .
						"WHERE `report_id`=? AND `type`=? AND `from`=? AND `to`=?";
				if (count(db_fetch_rows($sql, array($report_id, $annotation_type, $from, $to)))==0){					
					$sql = "INSERT INTO `reports_annotations_optimized` " .
							"(`report_id`, `type_id`, `from`, `to`, `text`, `user_id`, `creation_time`, `stage`,`source`) VALUES " .
							sprintf('(%d, (SELECT annotation_type_id FROM annotation_types WHERE name="%s"), %d, %d, "%s", %d, now(), "new", "bootstrapping")',
									$report_id, $annotation_type, $from, $to, $ann_text, $user_id  );
					db_execute($sql);
					$count++;
				}
			}
		}
		
		return $count;
	}		
}
?>
