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

if ( ( $p = array_search("--dsn", $argv)) !== false )
	$config->dsn = $argv[$p+1];

/******************** check configuration *********************************************/

// none

/******************** functions           *********************************************/

function process($table_reports, $simulate){
	$reports = $table_reports->getRows(100000)->fetchAll(MDB2_FETCHMODE_ASSOC);
	
	echo "\n";
	
	foreach ($reports as $report){
		$content_org = $content = $report['content'];
		$id = $report['id'];

		echo "id = $id";
		
		$content = preg_replace('/(\s*?\r?\n\s*\r?\n)/', '</p>\1<p>', $content);
		$content = "<p>$content</p>";

		$content_reverse = $content;
		$content_reverse = str_replace('<p>', "", $content_reverse);
		$content_reverse = str_replace('</p>', "", $content_reverse);

		if ($content_reverse == $content_org)
			echo " ok\n";
		else
			die("\n error!\n\n$content");
			
		if (!$simulate){
			$report['content'] = $content;
			$table_reports->updateRow($id, $report);
		}
	}
	echo "\n";
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
	
	$table_reports = $mdb2->tableBrowserFactory("reports", "id");
	$table_reports->addFilter('corpora', 'corpora', '=', 5);
	
	echo "Simulation\n";
	if (process($table_reports, true)){ // simulate processing
		echo "Simulation is OK\n";
		echo "Processing";
		process($table_reports, false); // if everything is ok then run in normal mode
	}else{
	}
}

/******************** main invoke         *********************************************/
main($config);
?>
