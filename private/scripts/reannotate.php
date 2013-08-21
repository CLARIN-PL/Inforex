
<?php

mysql_connect("localhost", "root", "krasnal");
//mysql_connect("nlp.pwr.wroc.pl:3308", "", "");
mysql_select_db("gpw");
mysql_query("SET CHARACTER SET utf8");

mb_internal_encoding("utf8");

//function fb($s){ }

require_once("PEAR.php");
require_once("MDB2.php");
include ("../../engine/config.php");
include ("../../engine/config.local.php");
include ("../../engine/database.php");
include("../../engine/include.php");
require_once ("../../engine/include/lib_htmlstr.php");
require_once ("../../engine/include/lib_htmlparser.php");

$sql = "select r.* from reports r join reports_annotations an ON (r.id=an.report_id) GROUP BY r.id";
$sql_annotations = "SELECT an.* FROM reports_annotations AS an LEFT JOIN annotation_types t ON (an.type=t.name) WHERE report_id = ? ORDER BY `from` ASC, `level` DESC";

ob_end_clean();
$n = 1;
$reports = db_fetch_rows($sql);
foreach ($reports as $r){
	$annotations = db_fetch_rows($sql_annotations, array($r['id']));
	$annotations_old = array();
	if (count($annotations)==0) die("0 anotacji dla ".$r['id']);
	echo ($n++) . ") " . $r['id']." ".count($annotations)."\n";
	
	try{
		//$htmlStr = new HtmlStr(html_entity_decode(stripslashes($r['content']), ENT_COMPAT, "UTF-8"), true);
		// custom_html_entity_decode nie jest wymagane, bo w HtmlParser::readInlineAnnotations już jest 
		$htmlStr = new HtmlStr(stripslashes($r['content']), true);
		foreach ($annotations as $ann){
			$htmlStr->insertTag($ann['from'], sprintf("<an#%d:%s>", $ann['id'], $ann['type']), $ann['to']+1, "</an>");
			$annotations_old[$ann['id']] = $ann;
		}
	}catch (Exception $ex){
		//custom_exception_handler($ex);
		print_r($ex);
		die($htmlStr->getContent());
	}

	$annotations_new = HtmlParser::readInlineAnnotations($htmlStr->getContent());
	foreach ($annotations_new as $a){
		$from = $a[0]; 
		$to = $a[1];
		$type = $a[2];
		$id = $a[3];
		$text = $a[4];
		
		if ($text != $annotations_old[$id]['text']){
			print "New:\n";
			print_r($a);
			print "Database:\n";
			print_r($annotations_old[$id]);
			die();
		}
		
		$sql_update = sprintf("UPDATE reports_annotations_optimized SET `from` = %d, `to` = %d WHERE id = %d", $from, $to, $id);
		//db_execute($sql_update);
		print "SQL UPDATE is commented\n";
	}
	
}
?>