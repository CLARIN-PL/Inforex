<?php
/* 
 * ---
 * Skrypt do robienia dumpu zdań zawierających wskazane anotacje
 * ---
 * Created on 2012-03-23 
 * 
 */
include("../../engine/config.php");
include("../../engine/config.local.php");
include("../../engine/include.php");
include("../cliopt.php");

mb_internal_encoding("utf-8");
ob_end_clean();
 
/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("corpus", "c", "id", "id of the corpus"));
$opt->addParameter(new ClioptParameter("subcorpus", "s", "id", "id of the subcorpus"));
$opt->addParameter(new ClioptParameter("report", "r", "id", "id of the report"));
$opt->addParameter(new ClioptParameter("file_name", "f", "name", "output file name"));
$opt->addParameter(new ClioptParameter("db-uri", "u", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("db-host", null, "host", "database address"));
$opt->addParameter(new ClioptParameter("db-port", null, "port", "database port"));
$opt->addParameter(new ClioptParameter("db-user", null, "user", "database user name"));
$opt->addParameter(new ClioptParameter("db-pass", null, "password", "database user password"));
$opt->addParameter(new ClioptParameter("db-name", null, "name", "database name"));
$opt->addParameter(new ClioptParameter("annotations", null, "annotation1:annotation2", "annotation pair names"));
$opt->addParameter(new ClioptParameter("relation", null, "name", "relation name"));

/******************** parse cli *********************************************/
$config = null;
try{
	$opt->parseCli($argv);
	
	if ( $opt->exists("db-uri")){
		$uri = $opt->getRequired("db-uri");
		if ( preg_match("/(.+):(.+)@(.*)\/(.*)/", $uri, $m)){
			$dbUser = $m[1];
			$dbPass = $m[2];
			$dbHost = $m[3];
			$dbName = $m[4];
		}else{
			throw new Exception("DB URI is incorrect. Given '$uri', but exptected 'user:pass@host:port/name'");
		}
	}
	
	$config->dsn = array(
	    			'phptype'  => 'mysql',
	    			'username' => $dbUser,
	    			'password' => $dbPass,
	    			'hostspec' => $dbHost,
	    			'database' => $dbName);	
	
	$config->corpus = $opt->getOptionalParameters("corpus");
	$config->subcorpus = $opt->getOptionalParameters("subcorpus");
	$config->report = $opt->getOptionalParameters("report");
	$config->file_name = $opt->getRequired("file_name");
	$config->annotations = $opt->getParameters("annotations");
	$config->relation = $opt->getRequired("relation");
	if (!$config->corpus && !$config->subcorpus && !$config->report)
		throw new Exception("No corpus, subcorpus or report id set");
	
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

/******************** main function       *********************************************/
function main ($config){
	$ids = array();
	$GLOBALS['db'] = new Database($config->dsn);
	
	foreach(DbReport::getReports($config->corpus,$config->subcorpus,$config->report, null) as $row){
		$ids[$row['id']] = $row;
	}
	echo "\n Start dump-sentence on " . $config->dsn['hostspec'] . "." . $config->dsn['database'] . " -> " . count($ids) . " documents\n";
	$f = fopen($config->file_name, "w");
	fwrite($f, "<html>\n");
	fwrite($f, "<head>\n");
	fwrite($f, '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />' . "\n");
	fwrite($f, '<style type="text/css">' . "\n");
	fwrite($f, 'body { font-size: 12px; }' . "\n");
	fwrite($f, 'sub { color: #555; }' . "\n");
	fwrite($f, 'span.source { border: 1px solid #FF4848; background: #FFDFDF } ' . "\n");
	fwrite($f, 'span.target { border: 1px solid #1FCB4A; background: #BDF4CB } ' . "\n");
	fwrite($f, 'li { line-height: 25px } ' . "\n");
	fwrite($f, '</style>' . "\n");
	fwrite($f, "</head>\n");
	fwrite($f, "<body>\n");
				
	$n = 0;
	$sql = "SELECT id FROM relation_types WHERE name=?";
	$relation_id = $GLOBALS['db']->fetch_one($sql, array($config->relation));
	$annotations_list = array();
	$annotation_type = array();
	foreach($config->annotations as $annotations){
		$annotation = explode(':', $annotations);
		$annotations_list[$annotation[0]] = 1;
		$annotations_list[$annotation[1]] = 1;
		if(!isset($annotation_type[$annotation[0]]))
			$annotation_type[$annotation[0]] = array();
		array_push($annotation_type[$annotation[0]], $annotation[1]); 
	}
	
	fwrite($f, "<h1>Relation: ". $config->relation ."</h1>\n");
	fwrite($f, "<h2>Annotation types:<br/>"); 
	foreach($annotation_type as $type_from => $elements)
		foreach($elements as $type_to)
			fwrite($f, "<span class='source'>". $type_from ."</span> => <span class='target'>". $type_to ."</span><br/>");
	fwrite($f, "</h2>\n");
		
	foreach ( array_keys($ids) as $report_id){
		echo "\r " . (++$n) . " z " . count($ids) . " :  id=$report_id    ";
		ob_flush();
		$relstr = "<h3>Document name: <a target='_blank' href='http://nlp.pwr.wroc.pl/inforex/index.php?page=report&corpus=". $ids[$report_id]['corpora'] ."&subpage=annotator&id=". $report_id ."'>" . $ids[$report_id]['title'] . "</a></h3><br/>\n";
		$sentence_dump = array();
		$report = new CReport($report_id);
		$htmlStr =  new HtmlStr($report->content, true);
		$sql = "SELECT * FROM tokens WHERE report_id=$report_id AND eos=1 ORDER BY `from`";
		$token_in_document = $GLOBALS['db']->fetch_rows($sql);
		$relation_in_document = DbCorpusRelation::getRelationsBySets2(array($report_id), '', array($relation_id));
		$annotations_in_document = DbAnnotation::getAnnotationsBySets(array($report_id),'',array_keys($annotations_list));
		$reference_data = array();

		foreach($relation_in_document as $relation){
			if(!isset($reference_data[$relation['source_id']]))
				$reference_data[$relation['source_id']] = array();
			array_push($reference_data[$relation['source_id']], $relation['target_id']);
		}
		$sentence_from = 0;

		foreach($token_in_document as $token){
			$annotation_in_sentence = array();
			foreach($annotations_in_document as $annotation){
				if($annotation['from'] >= $sentence_from && $annotation['to'] <= $token['to'])
					array_push($annotation_in_sentence, $annotation); 
			}
			if(count($annotation_in_sentence)>1){
				foreach($annotation_in_sentence as $sent1){
					if(in_array($sent1['type'], array_keys($annotation_type))){
						foreach($annotation_in_sentence as $sent2){
							if(in_array($sent2['type'], $annotation_type[$sent1['type']]) && (!isset($reference_data[$sent1['id']]) || !in_array($sent2['id'], $reference_data[$sent1['id']]))){
								$htmlStr2 =  new HtmlStr($htmlStr->getText($sentence_from, $token['to']), true);
								$htmlStr2->insertTag($sent1['from']-$sentence_from,'<span class=\'source\' title=\''.$sent1['type'].'\'>',$sent1['to']-$sentence_from+1,'</span>');
								$htmlStr2->insertTag($sent2['from']-$sentence_from,'<span class=\'target\' title=\''.$sent2['type'].'\'>',$sent2['to']-$sentence_from+1,'</span>');
								if(!isset($sentence_dump[$sent1['type']]))
									$sentence_dump[$sent1['type']] = array();
								if(!isset($sentence_dump[$sent1['type']][$sent2['type']]))
									$sentence_dump[$sent1['type']][$sent2['type']] = array();
								array_push($sentence_dump[$sent1['type']][$sent2['type']], $htmlStr2->getContent());
							}
						}
					}
				}
			}
			$sentence_from = $token['to'] + 1;
		}

		if(count($sentence_dump)){
			fwrite($f, "<hr />\n");
			fwrite($f, $relstr);
			foreach($sentence_dump as $type1=>$data_type2){
				foreach($data_type2 as $type2=>$dump){
					fwrite($f, "<small><span class='source'>".$type1."</span> -> <span class='target'>".$type2."</span></small>");
					fwrite($f, "<ol>\n");
					foreach($dump as $elements)
						fwrite($f, "<li>$elements</li>\n");
					fwrite($f, "</ol>\n");
				}				
			}			
		}
	}
	
	fwrite($f, "</body>");
	fwrite($f, "</html>");		
	fclose($f);
	echo "\r End set-sentence: " . ($n) . " z " . count($ids) . "\n";
} 

/******************** main invoke         *********************************************/
main($config);
?>