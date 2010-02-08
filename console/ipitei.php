<?php
/* 
 * ---
 * 
 * ---
 * Created on 2010-02-08
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */
 
include("../engine/include/report_reformat.php");
include("../engine/include/CTeiFormater.php");
 
/* Konfiguracja */ 

$db_host = "localhost";
$db_user = "root";
$db_pass = "krasnal";
$db_name = "gpw";

$corpus_path = "/home/czuk/nlp/corpora/gpw2004-tei/";
$corpus_header_name = "GPW2004_header.xml";

if (!is_dir($corpus_path)) 
	mkdir($corpus_path);
else{
	//die("\nUsuń ręcznie katalog: $corpus_path\n\n");
}

/* Konfiguracja - koniec */
	
mysql_connect($db_host, $db_user, $db_pass);
mysql_select_db($db_name);
mysql_query("SET CHARACTER SET utf8;");

$sql = "SELECT * FROM reports WHERE YEAR(date)=2004 AND status=2";
$result = mysql_query($sql);

file_put_contents($corpus_path . $corpus_header_name, TeiFormater::corpus_header());

while ($row = mysql_fetch_array($result)){

	$dir = $corpus_path . "raport_nr_" . str_pad($row['id'], 7, "0", STR_PAD_LEFT);
	$dir = rtrim($dir, "_");

	if (!is_dir($dir))
		mkdir($dir);
	echo $dir."\n";

	echo $row['id'];
	file_put_contents($dir . "/header.xml", TeiFormater::report_to_header($row));
	echo ' ok';
	try{
		file_put_contents($dir . "/text.xml", TeiFormater::report_to_text($row, $corpus_header_name));
	}catch(Exception $ex){
		echo "\n".$row['content']."\n";
		die($ex);
	}
	echo ' ok';
	echo "\n";

}

// tar -zcf gpw2004-tei-r1.tar gpw2004-tei/
?>
