<?php
/*
 * Skrypt do wygenerowania korpusu na potrzeby eksperymenty twyd09
 */

include("../engine/include/CHtmlStr.php");
include("../engine/include/report_reformat.php");

mb_internal_encoding("UTF-8");

/******************** set configuration   *********************************************/

$config = null;
$config->option = $argv[2];

/**
 * Prepare a list of annotation types to keep and a list of types mapping.
 */
if ( ($p = array_search("--dont-ignore", $argv)) !==false && $p+1 < count($argv) ){
	$config->dontignore = explode(",", $argv[$p+1]);
	foreach ($config->dontignore as $k=>&$v)
		echo $v."\n";
		if ($l=strpos($v, ":")){
			$parts = explode(":", $v);
			$v = $parts[0];
			$config->map[$parts[0]] = $parts[1];
		}	
}

$db_host = "localhost";
$db_user = "root";
$db_pass = "krasnal";
$db_name = "gpw";

$corpus_path = "/home/czuk/nlp/corpora/gpw2004/";

/******************** check configuration *********************************************/

if (false) 
	die ("Incorrect argument.\n" .
			"\n" .
			"Execute:\n" .
			"  php fetch.php [options]                  // process all files in a folder\n" .
			"\n" .
			"Options:\n" .
			"  --dont-ignore <annotations>              // remove any other annotations that given\n" .
			"\n" .
			"  <annotations> = 'person,company'         // select the PERSON and COMPANY annotation\n"  
			);  

/******************** functions           *********************************************/

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
	$where_type = "";
	if ( count($config->dontignore)>0 ){
		foreach ($config->dontignore as $ann_name)
			$where_type[] = "type='$ann_name'";
		$where_type = " AND (".implode(" OR ", $where_type).") ";
	}
	//die($where_type);
	
	$result_ann = mysql_query("SELECT * FROM reports_annotations WHERE report_id={$row['id']} $where_type");
	$count_ann = 0;
	while ($ann = mysql_fetch_array($result_ann)){
		$htmlStr->insert($ann['from'], sprintf("<hr><an#%d:%s>", $ann['id'], $ann['type']));
		$htmlStr->insert($ann['to']+1, "</an><hr>", false);
		$count_ann++;
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
	echo "Saved: ".$name.".txt with $count_ann annotation(s)\n";
}

?>
