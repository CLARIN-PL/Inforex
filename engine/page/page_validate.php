<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Page_validate extends CPage{
	
	function execute(){
		global $mdb2;

		$errors = array();

		$options = array(
		    'debug' => 2,
		    'result_buffering' => false,
		);
		
		$mdb2 =& MDB2::singleton($config->dsn);
		if (PEAR::isError($mdb2)) {
		    die($mdb2->getMessage());
		}
		$mdb2->query("SET CHARACTER SET 'utf8'");	
		$mdb2->loadModule('Extended');
		$mdb2->loadModule('TableBrowser');
		
		$table_annotations = $mdb2->tableBrowserFactory("reports_annotations", "id");
		$table_reprts = $mdb2->tableBrowserFactory("reports", "id");
		$anns = $table_annotations->getRows(100000)->fetchAll(MDB2_FETCHMODE_ASSOC);

		foreach ($anns as $ann){
			$report = $table_reprts->getRow($ann['report_id']);
			$content = normalize_content($report['content']);
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
		}
		$this->set('errors', $errors);
	}
}


?>
