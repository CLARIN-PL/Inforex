<?php

ini_set("xdebug.profiler_enable", 1);
ini_set("xdebug.profiler_output_dir", "/home/czuk");

/*
 * Skrypt do wygenerowania korpusu na potrzeby eksperymenty twyd09
 */

include("../engine/include/CHtmlStr.php");
include("../engine/include/report_reformat.php");
include("cliopt.php");

mb_internal_encoding("UTF-8");

/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addArgument(new ClioptArgument("action", "type of action", array("all", "resume", "DECIMAL")));
$opt->addExecute("php fetch.php all --where \"YEAR(date)=2004 AND status=2 AND corpora=1\"", "get all GPW reports from 2004");
$opt->addParameter(new ClioptParameter("corpus-location", null, "path", "path to a folder where the data will be save"));
$opt->addParameter(new ClioptParameter("dont-ignore", null, "annotation", "remove any other annotations that given"));
$opt->addParameter(new ClioptParameter("db-host", null, "host", "database address"));
$opt->addParameter(new ClioptParameter("db-user", null, "user", "database user name"));
$opt->addParameter(new ClioptParameter("db-pass", null, "password", "database user password"));
$opt->addParameter(new ClioptParameter("db-name", null, "name", "database name"));
$opt->addParameter(new ClioptParameter("dry-run", null, null, "process the data without saving anything to the disk"));
$opt->addParameter(new ClioptParameter("where", null, null, "SQL where condition"));

$config = null;
$error = null;
$config->dontignore = array();

//"/home/czuk/nlp/corpora/gpw2004/";
try{
	$opt->parseCli($argv);
	
	$db_host = $opt->getOptional("db-host", "localhost");
	$db_user = $opt->getOptional("db-user", "root");
	$db_pass = $opt->getOptional("db-pass", "krasnal");
	$db_name = $opt->getOptional("db-name", "gpw");

	$config->action = $opt->getArgument();
	$config->dontignore = $opt->getParameters("dont-ignore");
	$config->dryrun = $opt->exists("dry-run");
	
	$config->where = $opt->getRequired("where");
	$corpus_path = $opt->getRequired("corpus-location");
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

/******************** MAIN           *********************************************/

if ($config->dryrun){
	print "!! This is only a dry-run !!\n";
}

if (!$config->dryrun){
	if (!file_exists($corpus_path)) mkdir($corpus_path, true);
	if (!file_exists($corpus_path_text)) mkdir($corpus_path_text, true);
	if (!file_exists($corpus_path_ann)) mkdir($corpus_path_ann, true);

	chmod($corpus_path_text, 0777);
	chmod($corpus_path_ann, 0777);
}
		
mysql_connect($db_host, $db_user, $db_pass);
mysql_select_db($db_name);
mysql_query("SET CHARACTER SET utf8;");

$sql = "SELECT * FROM reports WHERE {$config->where}";
if (is_numeric($config->action)) $sql .= " AND id={$config->action}";
$sql .= " ORDER BY id";
$result = mysql_query($sql) or die (mysql_error());

$where_type = "";
if ( count($config->dontignore)>0 ){
	foreach ($config->dontignore as $ann_name)
		$where_type[] = "a.type='$ann_name'";
	$where_type = " AND (".implode(" OR ", $where_type).") ";
}

$annotations = array();
$sql = "SELECT a.* FROM reports_annotations a JOIN reports r ON (a.report_id = r.id) WHERE $config->where $where_type ORDER BY a.`from`";
$result_ann = mysql_query($sql) or die(mysql_error()."\n$sql");
while ($ann = mysql_fetch_array($result_ann)){
	$annotations[$ann['report_id']][] = $ann;
}

while ($row = mysql_fetch_array($result)){
	$name = str_pad($row['id'], 7, "0", STR_PAD_LEFT);
	$content = $row['content'];
	$content = normalize_content($row['content']);
	$htmlStr = new HtmlStr(html_entity_decode($content, ENT_COMPAT, "UTF-8"));
	
	// Wstaw anotacje do treści dokumentu	
	if (isset($annotations[$row['id']]))
		foreach ($annotations[$row['id']] as $ann){
			//print sprintf("[%s,%s] %s diff=%s size=%s\n", $ann['from'], $ann['to'], $ann['text'], $ann['to']-$ann['from']+1, strlen($ann['text']));
			$htmlStr->insertBuffered($ann['from'], sprintf("<an#%d:%s>", $ann['id'], $ann['type']));
			$htmlStr->insertBuffered($ann['to']+1, "</an>", false);
		}
	$content_ann = $htmlStr->getContent();

	// Change </p></an> to </an></p>
	$content_ann = preg_replace("/(<[^>]*>)<\/an>/s", '</an>\1', $content_ann);
	
	$content_ann = preg_replace('/(<\/an>)(\S)/s', '\1 \2', $content_ann);
	$content_ann = preg_replace('/(\S)(<an#)/s', '\1 \2', $content_ann);
	$content_ann = preg_replace('/<(\/)?[pP]>/s', ' ', $content_ann);
    $content_ann = preg_replace('/<br(\/)?><\/an>/s', '</an>', $content_ann);
    $content_ann = preg_replace('/<br(\/)?>/s', ' ', $content_ann);
 	
	$content_ann = trim($content_ann);

		
	
	$content_clean = trim(strip_tags($content_ann));
	
	$content_ann = html_entity_decode($content_ann, ENT_COMPAT, "utf-8");
	$content_clean = html_entity_decode($content_clean, ENT_COMPAT, "utf-8");
	if (!$config->dryrun){
		file_put_contents($corpus_path_text.$name.".txt", $content_clean);
		file_put_contents($corpus_path_ann.$name.".txt", $content_ann);
	}
	
	$count = isset($annotations[$row['id']]) ? count($annotations[$row['id']]) : 0;
	echo "Saved: ".$name.".txt with ".$count." annotation(s)\n";
}

?>
