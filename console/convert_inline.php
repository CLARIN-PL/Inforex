<?php
/* 
 * ---
 * Converts documents to IOB represenation in a simple format.
 * Wywołanie:
 *   php convert_inline.php
 * ---
 * Created on 2010-03-08
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */ 
mb_internal_encoding("UTF-8");

ini_set("include_path", ini_get("include_path").":".'../engine/pear');
include("MDB2.php");
include("../engine/include/report_reformat.php");
 
/******************** set configuration   *********************************************/

$config = null;
$config->dsn = "mysql://root:krasnal@localhost/gpw";

/******************** check configuration *********************************************/

// none

/******************** functions           *********************************************/

function process($table_annotations, $table_reprts, $anns, $simulate){

	foreach ($anns as $ann){
		$report = $table_reprts->getRow($ann['report_id']);
		$content = normalize_content($report['content']);
		
		if (mb_strpos($content, chr(11))!==false || mb_strpos($content, chr(12))!==false)
			die("chr(11) or chr(12) found in document");
			
		$content = preg_replace(sprintf("/<an#%d:.*?>(.*?)<\/an>/",$ann['id'],$ann['type']), chr(11)."$1".chr(12), $content);
		$content = preg_replace("/<an#[0-9]*:[a-z_]*>(.*?)<\/an>/", "$1", $content);
		$content = preg_replace("/<br\/?>/", "", $content);
		$content = preg_replace("/<\/?p>/", "", $content);
		$content_marked = $content;
				
		$from = mb_strpos($content, chr(11));
		$to = mb_strpos($content, chr(12)) - 1;
		$content = str_replace(chr(11), "", $content);
		$content = str_replace(chr(12), "", $content);
		$text = mb_substr($content, $from, $to-$from);
		if ($text == $ann['text']){
			if ($simulate!==null && $simulate==false){
				$ann['from'] = $from;
				$ann['to'] = $to;
				$table_annotations->updateRow($ann['id'], $ann);
				
				$report['content'] = preg_replace(sprintf("/<an#%d:.*?>(.*?)<\/an>/",$ann['id']), "$1", $report['content']);
				$table_reprts->updateRow($ann['report_id'], $report);
			}
			echo ".";
		}else{
			echo "\n--------------------\n";
			echo "$content";
			echo "\n--------------------\n";
			echo "    Span from: $from \n";	
			echo "      Span to: $to \n";
			echo "    Report id: {$ann['report_id']} \n";	
			echo "Annotation id: {$ann['id']} \n";
			echo "         Text: |$text| \n";
			echo "  != Database: |{$ann['text']}|\n";
			die();
		}
	}
	return true;	
}

// 

/******************** main function       *********************************************/
// Process all files in a folder
function main ($config){
	
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
	$table_annotations->addFilter('from', 'from', '=', 0);
	$table_annotations->addFilter('to', 'to', '=', 0);
	$anns = $table_annotations->getRows(100000)->fetchAll(MDB2_FETCHMODE_ASSOC);
	
	echo "Simulation";
	if (process($table_annotations, $table_reprts, $anns, true)){		// simulate processing
		echo "Simulation is OK\n";
		echo "Processing";
		process($table_annotations, $table_reprts, $anns, false); // if everything is ok then run in normal mode
	}else{
		
	}
}

/******************** main invoke         *********************************************/
main($config);
?>
