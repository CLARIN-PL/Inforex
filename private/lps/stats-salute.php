<?
include("../../engine/config.php");
include("../../engine/config.local.php");
include("../../engine/include.php");
include("../cliopt.php");
include("mb_printf.php");
mb_internal_encoding("utf-8");
libxml_use_internal_errors(true);

$config->dsn = array(
    			'phptype'  => 'mysql',
    			'username' => 'gpw',
    			'password' => 'gpw',
    			'hostspec' => 'nlp.pwr.wroc.pl:3308',
    			'database' => 'gpw'
				);

$db = new Database($config->dsn);

$rows = DbReport::getExtReports(3);

$i = 1;
$c = count($rows);
$salute_opener = 0;
$salute_closer = 0;
$filename = tempnam("/tmp", "inforex_lps_");
$stats = array();
$subcorpora = array(48=>"prawdziwe", 49=>"symulowane", 
		60=>"sfałszowane", 65=>"porównawcze");

foreach ($rows as $row){
	echo sprintf("\r$i z $c");
	$docid = $row['id'];
	$i++;
	
	$content = trim($row['content']);	
	if ( strlen($content) == 0 ){
		echo sprintf("\n--{id=%d} ignored: empty document\n", $docid);
		continue;
	}
		
	
	$content = custom_html_entity_decode($row['content']);
	file_put_contents($filename, $content);

	$cmd = sprintf("xsltproc %s/resources/lps/lps-count.xsl %s", $config->path_engine, $filename);	
	$result = shell_exec($cmd);
			
	$xml = simplexml_load_string($result);
	if($elem !== false)
	{
		$gender = ( trim($row['deceased_gender']) ? $row['deceased_gender'] : "none" );
		$stats['general']['docs_count'][$row['subcorpus_id']] +=1;
		$stats[$gender]['docs_count'][$row['subcorpus_id']] +=1;
		$stats[$gender]['salute_in_opener'][$row['subcorpus_id']] 
				+= intval($xml->opener);
		$stats[$gender]['salute_in_opener_docs'][$row['subcorpus_id']]
				+= intval($xml->opener) > 0 ? 1 : 0;
		$stats[$gender]['salute_in_closer'][$row['subcorpus_id']] 
				+= intval($xml->closer);
		$stats[$gender]['salute_in_closer_docs'][$row['subcorpus_id']]
				+= intval($xml->closer) > 0 ? 1 : 0;
	}
	else
	{
	    foreach(libxml_get_errors() as $error)
	    {
	        echo sprintf('\n--{id=%d} Error parsing XML file: %s\n', $docid, $error->message);
	    }
	}
}
unlink($filename);

echo "\n";
echo mb_sprintf("%-30s", "Atrybut");
foreach ( array_values($subcorpora) as $subcorpus)
	echo mb_sprintf("%13s", $subcorpus);
echo "\n";
echo str_repeat("-", 80) . "\n";
foreach ( $stats as $group_name=>$groups ){
	echo mb_sprintf("%s\n", $group_name);
	foreach ( $groups as $stat=>$values){
		echo mb_sprintf(" - %-27s", $stat);
		foreach ( array_keys($subcorpora) as $subcorpus)
			echo mb_sprintf("%13d", intval($values[$subcorpus]));
		echo "\n";
	}
}
echo str_repeat("-", 80) . "\n";
?>