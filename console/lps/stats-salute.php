<?
include("../../engine/config.php");
include("../../engine/config.local.php");
include("../../engine/include.php");
include("../cliopt.php");
mb_internal_encoding("utf-8");

$db = new Database($config->dsn);

$rows = DbReport::getReports(array(3));

$i = 1;
$c = count($rows);
$salute_opener = 0;
$salute_closer = 0;
$filename = tempnam("/tmp", "inforex_lps_");

foreach ($rows as $row){
	echo sprintf("\r$i z $c");
	$i++;
	$content = custom_html_entity_decode($row['content']);
	file_put_contents($filename, $content);

	$cmd = sprintf("xsltproc %s/resources/lps/lps-count.xsl %s", $config->path_engine, $filename);	
	$result = shell_exec($cmd);
		
	$xml = simplexml_load_string($result);

	$salute_opener += intval($xml->opener);
	$salute_closer += intval($xml->closer); 
}
unlink($filename);

echo "\n";
echo "salute @opener = $salute_opener\n";
echo "salute @closer = $salute_closer\n";
?>