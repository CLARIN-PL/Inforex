<?php
/* 
 * ---
 * Uruchamia proces przetwarzania dokumentów, od wyciągnięcia z bazy danych, po tagowanie i urównoleglanie.
 * ---
 * Created on 2010-01-14
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */
 
$corpora_location = "/home/czuk/nlp/corpora/";
$corpora_name = $argv[1];
$document_id = $argv[2];

if ($corpora_name=="") die("! Podaj nazwę korpusu");

mysql_connect("localhost", "root", "krasnal");
mysql_select_db("gpw");
mysql_query("SET CHARACTER SET utf8;");

$sql = "SELECT * FROM reports WHERE YEAR(date)=2004 AND status=2 AND id=$document_id";
$result = mysql_query($sql);

while ($row = mysql_fetch_array($result)){
	$name = str_pad($row['id'], 7, "0", STR_PAD_LEFT);
	$content_ann = $row['content'];
	
	$content = $row['content'];
	$content = preg_replace('/<(\/)?[pP]>/s', ' ', $content);
    $content = preg_replace('/<br(\/)?>/s', ' ', $content);	
	$content_clean = trim(strip_tags($content));
	
	$content_ann = html_entity_decode($content_ann, ENT_COMPAT, "utf-8");
	$content_clean = html_entity_decode($content_clean, ENT_COMPAT, "utf-8");
	file_put_contents($corpus_path_text.$name.".txt", $content_clean);
	file_put_contents($corpus_path_ann.$name.".txt", $content_ann);
	echo $name.".txt extracted";
}

?>
