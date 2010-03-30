<?php
/*
 * Created on 2009-08-26
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

// Konfiguracja 
chdir("../engine");
include("config.php");
require_once($conf_global_path . '/include.php');

$mdb2 =& MDB2::singleton($dsn);
if (PEAR::isError($mdb2)) {
    die($mdb2->getMessage());
}
$mdb2->query("SET CHARACTER SET 'utf8'");
// ----------------------------------------------------

$token_separators = " \n\t\r()[]\".,:;"; 

$sql = "SELECT content FROM reports";
$result = mysql_query($sql);
$unique_words = array();

$n = 0;
while($row = mysql_fetch_array($result)){
	$document_unique_words = array();
	$content = strip_tags($row['content']);

	$tok = strtok($content, $token_separators);
	while ($tok !== false) {
		$tok = strtolower($tok);
		if (!isset($unique_words[$tok]))
		{
			$unique_words[$tok] = 1;
			$document_unique_words[$tok] = 1;
		}
		else
		{
			if (!isset($document_unique_words[$tok]))
				$unique_words[$tok]++;
			$document_unique_words[$tok] = 1;
		}
		
	   	$tok = strtok($token_separators);
	}
	echo "" . ($n++) ."\n";
}
$unique_words_2 = array();
foreach ($unique_words as $word=>$count){
	if ($count>7 && !is_numeric($word) && strlen($word)>1 && strlen($word)<20)
		$unique_words_2[$word] = 0;
}
ob_start();
print_r($unique_words_2);
file_put_contents("../scripts/dump.txt", ob_get_clean());

echo count($unique_words)."\n";
echo count($unique_words_2)."\n";

$f = fopen("../scripts/gpw_reports", "w");
$fl = fopen("../scripts/gpw_reports.rlabels", "w");
fwrite($f, "$n ".count($unique_words_2));

$sql = "SELECT id, title, content FROM reports";
$result = mysql_query($sql);
$unique_words = array();
while($row = mysql_fetch_array($result)){
	$vector = $unique_words_2;

	$tok = strtok($content, $token_separators);
	while ($tok !== false) {
		$tok = strtolower($tok);
		if (isset($vector[$tok]))
			$vector[$tok]++;
	   	$tok = strtok($token_separators);
	}
	
	fwrite($f, "\n" . implode(" ", array_values($vector)));
	fwrite($fl, $row['id'].":".$row['title']."\n");
	echo "" . ($n--) . "\n";
}

fclose($f);
fclose($fl);

?>
