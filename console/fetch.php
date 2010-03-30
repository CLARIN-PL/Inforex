<?php
/*
 * Skrypt do wygenerowania korpusu na potrzeby eksperymenty twyd09
 */

include("../engine/include/CHtmlStr.php");
include("../engine/include/report_reformat.php");

mb_internal_encoding("UTF-8");

/* Konfiguracja */ 

$db_host = "localhost";
$db_user = "root";
$db_pass = "krasnal";
$db_name = "gpw";

$corpus_path = "/home/czuk/nlp/corpora/gpw2004/";

/* Konfiguracja - koniec */

$option = $argv[1];

if ($option!="all" && !is_numeric($option))  die ("\nIncorrect argument. Expected 'all' or raport id.\n\n");

$corpus_path_text = $corpus_path . "text/"; 
$corpus_path_ann = $corpus_path . "annotated/"; 

if (!file_exists($corpus_path)) mkdir($corpus_path, true);
if (!file_exists($corpus_path_text)) mkdir($corpus_path_text, true);
if (!file_exists($corpus_path_ann)) mkdir($corpus_path_ann, true);
	
chmod($corpus_path_text, 0777);
chmod($corpus_path_ann, 0777);
	
mysql_connect($db_host, $db_user, $db_pass);
mysql_select_db($db_name);
mysql_query("SET CHARACTER SET utf8;");

$sql = "SELECT * FROM reports WHERE YEAR(date)=2004 AND status=2";
if (is_numeric($option)) $sql .= " AND id={$option}";
$result = mysql_query($sql);

while ($row = mysql_fetch_array($result)){
	$name = str_pad($row['id'], 7, "0", STR_PAD_LEFT);
	$content = $row['content'];
	$content = normalize_content($row['content']);
	$htmlStr = new HtmlStr(html_entity_decode($content, ENT_COMPAT, "UTF-8"));
	
	// Wstaw anotacje do treści dokumentu
	$result_ann = mysql_query("SELECT * FROM reports_annotations WHERE report_id={$row['id']}");
	while ($ann = mysql_fetch_array($result_ann)){
		$htmlStr->insert($ann['from'], sprintf("<hr><an#%d:%s>", $ann['id'], $ann['type']));
		$htmlStr->insert($ann['to']+1, "</an><hr>", false);
	}
	
	$content_ann = $htmlStr->getContent();
	
	$content_ann = preg_replace('/<hr>/s', ' ', $content_ann);
	$content_ann = preg_replace('/<(\/)?[pP]>/s', ' ', $content_ann);
    $content_ann = preg_replace('/<br(\/)?>/s', ' ', $content_ann);    	
	$content_ann = trim($content_ann);
	
	$content_clean = trim(strip_tags($content_ann));
	
	$content_ann = html_entity_decode($content_ann, ENT_COMPAT, "utf-8");
	$content_clean = html_entity_decode($content_clean, ENT_COMPAT, "utf-8");
	file_put_contents($corpus_path_text.$name.".txt", $content_clean);
	file_put_contents($corpus_path_ann.$name.".txt", $content_ann);
	echo "Saved: ".$name.".txt\n";
}

?>
