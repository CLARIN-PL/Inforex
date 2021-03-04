<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_validate extends CPage{
	
	function execute(){

		$errors = array();

		$records_limit = 100000;
		$sql="SELECT a.id, a.report_id, a.from, a.to, a.text, r.content FROM reports_annotations_optimized AS a LEFT JOIN reports AS r ON a.report_id = r.id ORDER BY a.id ASC LIMIT ".$records_limit;
		$anns=$this->getDb()->fetch_rows($sql);

		foreach ($anns as $ann){
			$content = normalize_content($ann['content']);
			$from = $ann['from'];
			$to = $ann['to'];
			
			if (mb_strpos($content, chr(11))!==false || mb_strpos($content, chr(12))!==false)
				die("chr(11) or chr(12) found in document");
				
			$content = preg_replace("/<an#[0-9]*:[a-z_]*>(.*?)<\/an>/", "$1", $content);
			$content = preg_replace("/<br\/?>/", "", $content);
			$content = preg_replace("/<\/?p>/", "", $content);
					
			$text = mb_substr($content, $from, $to-$from+1);
			if ($text == $ann['text']){
				// nop
			}else{
				ob_start();
				echo "\n--------------------\n";
				echo "$content";
				echo "\n--------------------\n";
				echo "    Span from: $from \n";	
				echo "      Span to: $to \n";
				echo "    Report id: {$ann['report_id']} \n";	
				echo "Annotation id: {$ann['id']} \n";
				echo "         Text: |$text| \n";
				echo "  != Database: |{$ann['text']}|\n";
				$errors[] = array("msg"=>ob_get_clean(), "report_id"=>$ann['report_id']);
			}
		} // foreach $ann
		$this->set('errors', $errors);
	} // execute()
}


?>
