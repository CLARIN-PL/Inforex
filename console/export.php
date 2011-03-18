<?
/**
 * Export corpus annotations to files.
 */
require_once("PEAR.php");
require_once("MDB2.php");

$config = null;

$config->dsn = array(
    			'phptype'  => 'mysql',
    			'username' => 'gpw',
    			'password' => 'gpw',
    			'hostspec' => 'nlp.pwr.wroc.pl:3308',
    			'database' => 'gpw',
				);

include("../engine/database.php");

//$rows = db_fetch_rows("SELECT *" .
//		" FROM reports_annotations a" .
//		" JOIN reports r ON (r.id=a.report_id)" .
//		" WHERE r.corpora = 7");
$reports = db_fetch_rows("SELECT * FROM reports r WHERE r.corpora = 7");

$folder = "corpus_synat";

foreach ($reports as $r){
	$filename = "$folder/".$r['link'];
	$anns = db_fetch_rows("SELECT *" .
			" FROM reports_annotations a" .
			" JOIN annotation_types t ON (a.type = t.name)" .
			" WHERE a.report_id = ?" .
			"   AND t.group_id = 3" .
			" ORDER BY a.`from`, a.`to`, a.type", array($r['id']));
	
	$text = "position;length;label";
	foreach ($anns as $a){
		$type = $a['type'];
		$text .= sprintf("\n%s;%s;%s\t%s", $a['from'], $a['to']-$a['from']+1, $a['type'], $a['text']);
	}
		
	echo $filename . "\n";
	file_put_contents($filename, $r['content']);
	file_put_contents( str_replace(".xml", ".ne.txt", $filename), $text);
}

?>