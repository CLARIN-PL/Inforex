<?php
/* 
 * ---
 * Uploads parts of InfiKorp into database
 * ---
 * Created on 2010-01-14
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
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
$opt->addParameter(new ClioptParameter("document", "d", "id", "id of the document"));
$opt->addParameter(new ClioptParameter("db-host", null, "host", "database address"));
$opt->addParameter(new ClioptParameter("db-port", null, "port", "database port"));
$opt->addParameter(new ClioptParameter("db-user", null, "user", "database user name"));
$opt->addParameter(new ClioptParameter("db-pass", null, "password", "database user password"));
$opt->addParameter(new ClioptParameter("db-name", null, "name", "database name"));

/******************** parse cli *********************************************/

try{
	$opt->parseCli($argv);
	
	$db_host = $opt->getOptional("db-host", "localhost");
	$db_user = $opt->getOptional("db-user", "root");
	$db_pass = $opt->getOptional("db-pass", "krasnal");
	$db_name = $opt->getOptional("db-name", "gpw");
	$db_port = $opt->getOptional("db-port", "3306");

	$config->dsn = array(
    			'phptype'  => 'mysql',
    			'username' => $db_user,
    			'password' => $db_pass,
    			'hostspec' => "$db_host:$db_port",
    			'database' => $db_name,
				);	
	$mdb2 =& MDB2::singleton($config->dsn, $options);
		
	$config->corpus = $opt->getParameters("corpus");
	$config->subcorpus = $opt->getParameters("subcorpus");
	$config->documents = $opt->getParameters("documents");
	
	mysql_connect("$db_host:$db_port", $db_user, $db_pass);
	mysql_select_db($db_name);
	mysql_query("SET CHARACTER SET utf8;");
	
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

/******************** main function       *********************************************/
// Process all files in a folder
function main ($config){

	$ids = array();
	
	foreach ($config->corpus as $c){
		$sql = sprintf("SELECT * FROM reports WHERE corpora = %d", $c);
		foreach ( db_fetch_rows($sql) as $r ){
			$ids[$r['id']] = 1;			
		}		
	}

	foreach ($config->subcorpus as $s){
		$sql = sprintf("SELECT * FROM reports WHERE subcorpus_id = %d", $s);
		foreach ( db_fetch_rows($sql) as $r ){
			$ids[$r['id']] = 1;			
		}		
	}
	
	foreach ($config->documents as $d){
		$ids[$d] = 1;
	}

	$n = 0;
	foreach ( array_keys($ids) as $report_id){
		echo "\r " . (++$n) . " z " . count($ids) . " :  id=$report_id     ";

		$tagger = new WSTagger($config->takipi_wsdl);
		$doc = db_fetch("SELECT * FROM reports WHERE id=?",array($report_id));
		$text = $doc['content'];

  		db_execute("DELETE FROM tokens WHERE report_id=?", array($report_id));
  		$takipiText="";
  		$tokensTags="INSERT INTO `tokens_tags` (`token_id`,`base`,`ctag`,`disamb`) VALUES ";
		$reader = new XMLReader();
		$reader->xml($text);
		do {
			$read = $reader->read();
			if ($reader->localName == "chunk" && $reader->nodeType == XMLReader::ELEMENT){
				$text = $reader->readString();	
				$text = strip_tags($text);
				$text = html_entity_decode($text);
				$tagger->tag($text);
			  	try {
			  		$takipiDoc = TakipiReader::createDocumentFromText("<doc>".$tagger->tagged."</doc>");
			  	}
			  	catch (Exception $e){
					echo json_encode(array("error"=>"TakipiReader error", "exception"=>$e->getMessage()));
					die("Exception");
			  	}		
		  		foreach ($takipiDoc->sentences as $sentence){
  					$lastId = count($sentence->tokens)-1;
		  			foreach ($sentence->tokens as $index=>$token){
				  		$from =  mb_strlen($takipiText);
				  		$takipiText = $takipiText . html_entity_decode($token->orth);
				  		$to = mb_strlen($takipiText)-1;
				  		$lastToken = $index==$lastId ? 1 : 0;
				  		db_execute("INSERT INTO `tokens` (`report_id`, `from`, `to`, `eos`) VALUES (?, ?, ?, ?)", array($report_id, $from, $to, $lastToken));
				  		$token_id = mysql_insert_id();
				  		foreach ($token->lex as $lex){
				  			$base = addslashes(strval($lex->base));
				  			$ctag = addslashes(strval($lex->ctag));
				  			$disamb = $lex->disamb ? "true" : "false";
				  			$tokensTags .= "($token_id, \"$base\", \"$ctag\", $disamb),";
				  		}
		  			}
		  		}
								
			}				
		}
		while ( $read );
		db_execute(substr($tokensTags,0,-1));
		
		$sql = "SELECT corpora_flag_id FROM corpora_flags WHERE corpora_id = ? AND short = 'Tokens'";
		$corpora_flag_id = db_fetch_one($sql, array($doc['corpora']));
		
		if ($corpora_flag_id){
			db_execute("REPLACE reports_flags (corpora_flag_id, report_id, flag_id) VALUES(?, ?, 3)",
				array($corpora_flag_id, $report_id));	
		}	
	}
} 

/******************** main invoke         *********************************************/
main($config);
?>
