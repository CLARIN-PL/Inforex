<?php

$engine = "../engine/";
include($engine."cliopt.php");
include($engine."include/anntakipi/ixtTakipiStruct.php"); 
include($engine."include/anntakipi/ixtTakipiDocument.php"); 
include($engine."database.php");
require_once("PEAR.php");
require_once("MDB2.php");
require_once($engine."include/class/a_table.php");
require_once($engine."include/class/c_report.php");
require_once($engine."include/database/CDbReportAnnotationLemma.php");
require_once($engine."include/database/CDbAnnotation.php");
require_once($engine."include/database/CDbToken.php");
require_once($engine."include/database/CDbCtag.php");
require_once($engine."include/database/CDbBase.php");
require_once($engine."include/database/CDbTag.php");
mb_internal_encoding("UTF-8");

// Identyfikator korpusu CORE
$core_corpus_id = 21;
global $core_corpus_id;

class TeiReport{
	var $date;
	var $title;
	var $source;
	var $raw_text;
	var $paragraphs;
	// Składowe dokumentu tei
	var $text;
	// Ścieżka do plików
	var $path;
	// Czy ma koreferencję
	var $coreference;
	// Annotacje
	var $annotations;

	function __construct($path, $reader, $writer, $coreference){
		$this->path = $path;
		$this->reader = $reader;
		$this->writer = $writer;
		$this->coreference = $coreference;
	}

	function write(){
		$this->writeText();
		$this->writeSegmentation();
		$this->writeMentions();
		$this->writeCoreference();
	}

	function writeText(){
		$this->report_id = $this->writer->writeText($this->raw_text);
	}

	function writeSegmentation(){
		$this->writer->writeSegmentationAndMorphosyntax($this->report_id, $this->tokens);
	}

	function writeMentions(){
		$this->annotations = $this->writer->writeMentions($this->report_id, $this->tokens, $this->annotations);
	}

	function writeCoreference(){
		$this->writer->writeCoreference($this->report_id, $this->relations, $this->annotations);
	}

	function read(){
		$this->readText();
		$this->readSegmentation();
		$this->readMorphosyntax();
		$this->readMentions();
		$this->readCoreference();		
	}

	function readText(){
		$this->reader->loadTextFile($this->path);
		$raw_para = $this->reader->readAllRaw();
		$this->raw_text = $raw_para[0];
		$this->paragraphs = $raw_para[1];
	}

	function readSegmentation(){
		$this->reader->loadSegmentationFile($this->path);
		$this->tokens = $this->reader->readTokens($this->raw_text, $this->paragraphs);
	}

	function readMorphosyntax(){
		$this->reader->loadMorphosyntaxFile($this->path);
		$this->tokens = $this->reader->readMorphosyntax($this->tokens);
	}

	function readMentions(){
		#if(!$this->coreference) return;
		$this->reader->loadMentionsFile($this->path);
		$tok_anno = $this->reader->readMentions($this->tokens);
		$this->tokens = $tok_anno[0];
		$this->annotations = $tok_anno[1];
	}

	function readCoreference(){
		#if(!$this->coreference) return;	
		$this->reader->loadCoreferenceFile($this->path);
		$this->relations = $this->reader->readCoreference();
	}
}

class TeiTextReader{
	var $reader = null;
	var $token_index = 0;

	function __construct(){
		$this->reader = new XMLReader();
	}
	
	function loadFile($filename){
		$xml = file_get_contents($filename);
		$this->reader->xml($xml);
		$this->reader->read();
	}

	function loadTextFile($folder){
		$this->loadFile($folder."/text.xml");
	}

	function loadSegmentationFile($folder){
		$this->loadFile($folder."/ann_segmentation.xml");
	}

	function loadMorphosyntaxFile($folder){
		$this->loadFile($folder."/ann_morphosyntax.xml");	
	}
	
	function loadMentionsFile($folder){
		$this->loadFile($folder."/ann_mentions.xml");	
	}

	function loadCoreferenceFile($folder){
		$this->loadFile($folder."/ann_coreference.xml");	
	}

	function readAllRaw(){
		// Wczytuje sam tekst z text_structure.xml
		$paragraphs = array();
		$paragraphs[0] = 0;
		$raw_text = "";
		while($this->goToNextParagraph()){
			$raw_text .= $this->reader->readInnerXml();
			$paragraphs[] = mb_strlen($raw_text);
		}
		return array($raw_text, $paragraphs);
	}

	function readTokens($raw_text, $paragraphs){
		// Wczytuje tokenizachę z ann_segmentation.xml
		$tokens = array();
		$spaces = 0;
		$current_p = 0;
		while($this->goToNextSeg()){
			$seg = new SimpleXMLElement($this->reader->readOuterXML());
			$seg->attributes("nkjp", true);
			// $seg->corresp = "text.xml#string-range(p-X,start,len)"
			$range = split('#', (string)$seg["corresp"])[1];
			$rangeArr = preg_split("/[(),]/", $range);
			$paragraph = split("-",$rangeArr[1])[1] - 1;
			$begin = $rangeArr[2] + $paragraphs[$paragraph];
			$len = $rangeArr[3];
			$token = new TakipiToken(mb_substr($raw_text, $begin, $len));
			
			$xml = $seg->attributes("xml", true);
			$nkjp = $seg->attributes("nkjp", true);
			
			if($nkjp["nps"]){
				$token->setNS(true);
			}
			else{
				$spaces++;
			}
			
			if($current_p < $paragraph){
				$current_p = $paragraph;
				$spaces--;
			}
			$tokens[(string)$xml['id']] = array('from' => $begin - $spaces + 1, 'to' => $begin + $len - $spaces, 'token' => $token);
		}

		return $tokens;
	}

	function readMorphosyntax($tokens){
		$new_tokens = array();
		while($this->gotoNextSeg()){
			$seg = new SimpleXMLElement($this->reader->readOuterXML());
			$fs_morph = $seg->fs;

			$xml = $seg->attributes("xml", true);
			$new_token_index = (string)$xml['id'];

			$token_index = split('#', (string)$seg["corresp"])[1];
			$current_token = $tokens[$token_index];

			foreach($fs_morph->children() as $f){
				
				switch($f["name"]){
					case "orth":
						$current_token["token"]->orth = (string)$f->string;
						break;
					case "interps":
						foreach($f->fs->children() as $ff){
							switch($ff["name"]){
								case "base":
									$base = (string)$ff->string;
									break;
								case "ctag":
									$pos = $ff->symbol["value"];
									break;
								case "msd":
									$msd = $ff->symbol["value"];
									break;
							}
						}
						$ctag = $pos.":".$msd;
						$current_token["token"]->addLex($base, $ctag, true);
						break;
					case "disamb":
						break;
				}
			}
			$new_tokens[$new_token_index] = $current_token;
		}
		return $new_tokens;
	}

	function readMentions($tokens){
		$annotations = array();
		while($this->gotoNextSeg()){
			$seg = new SimpleXMLElement($this->reader->readOuterXML());
			$xml = $seg->attributes("xml", true);
			$mention_channel = (string)$xml["id"];
			$annotations[$mention_channel] = array();

			$tokens_in_mention = array();

			foreach($seg->children() as $ptr){
				if($ptr->getName() != 'ptr') continue;
				$target_token = split('#', (string)$ptr["target"])[1];
				$annotations[$mention_channel][] = $target_token;
			}


			foreach($annotations[$mention_channel] as $token_index){
				$tokens[$token_index]["token"]->channels[] = $mention_channel;
			}



		}
		return array($tokens, $annotations);
	}

	
	function rerouteToParent($mentions){
		$parent_mention = $mentions[0];
		$total_mentions = count($mentions);
		$relations = array();
		for($i = 1; $i < $total_mentions; $i++){
			$relations[] = array('from' => $mentions[$i], 'to' => $parent_mention);
		}
		return $relations;
	}

	function readCoreference(){
		$relations = array();
		while($this->goToNextSeg()){
			$seg = new SimpleXMLElement($this->reader->readOuterXML());
			$target_mentions = array();
			foreach($seg->children() as $ptr){
				if($ptr->getName() != 'ptr') continue;
				$tgt_mention = (string)$ptr["target"];
				if(strpos($tgt_mention,'#') !== false) $tgt_mention = split("#", $tgt_mention)[1];
				$target_mentions[] = $tgt_mention;
			}
			$relations[] = $this->rerouteToParent($target_mentions);
		}
		return $relations;
	}

	function goToNextTag($tag){
		// Ustawia wskaźnik na najbliższy <$tag>
		do {
			$read = $this->reader->read();
		}while ( $read && !($this->reader->localName == $tag && $this->reader->nodeType == XMLReader::ELEMENT));

		if (!$read)
			return false;

		return true;
	}

	function goToNextSeg(){
		return $this->goToNextTag("seg");
	}

	function goToNextParagraph(){
		return $this->goToNextTag("p");
	}

	function close(){
		$this->reader->close();

	}
}

class TeiDatabaseWriter{
	
	function writeText($raw_text){
		global $core_corpus_id;
		$report = new CReport();
		$report->corpora = $core_corpus_id;
		$report->date = date("Y-m-d");
		$report->title = "Tei Test";
		$report->source = "IPI PAN";
		$report->author = "Adam"; 	
		$report->content = $raw_text; 	
		$report->type = 1; 	
		$report->status = 2; 	
		$report->user_id = 1; 	
		$report->format_id = 2;

		$report->save();

		return $report->id;
	}

	function writeSegmentationAndMorphosyntax($report_id, $tokens){
		foreach($tokens as $token){
			$t_disamb = $token['token']->getDisamb();
			$token_id = DbToken::saveToken($report_id, $token['from'], $token['to'], $token['token']);
			$ctag_id = DbCtag::saveIfNotExists($t_disamb->ctag);
			$base_id = DbBase::saveIfNotExists($t_disamb->base);
			$pos = $t_disamb->getPos();
			DbTag::saveTag($token_id, $base_id, $ctag_id, true,  $pos);
		}
	}

	function writeMentions($report_id, $tokens, $annotations){
		$channels = array("anafora_wyznacznik", "chunk_np", "chunk_agp");
		$annotation_ids = array();
		$annot = array();
		$token_channels = array();
		foreach($annotations as $mention_id => $annotation){
			$annot[$mention_id] = $this->getContinuousAnnotations($annotation);
		}
		
		$annId = array();
		foreach ($annot as $mention_id => $annotation){
			$max_token_channels = 0;
			foreach($annotation as $part){
				$text = "";
				$from = -1;
				foreach($part as $morph){
					$token_wrap = $tokens[$morph];
					$token = $token_wrap["token"];

					if(!array_key_exists($morph, $token_channels)) $token_channels[$morph] = 0;
					
					$current_channels = $token_channels[$morph];
					if($current_channels > $max_token_channels) $max_token_channels = $current_channels;
					$token_channels[$morph]++;

					if($from < 0) $from = $token_wrap["from"];

					if ($token->ns) {
						$text .= $token->orth;
					}
					else {
						$text .= " ".$token->orth;
					}
				}
			}
			$ann_name = $channels[0];
			$anaIndex = DbAnnotation::saveAnnotation($report_id, $ann_name, $from, $text, 1, "final", "user");
			//$agpIndex = DbAnnotation::saveAnnotation($report_id, "chunk_agp", $from, $text, 1, "final", "user");
			//$agpHeadIndex = 0;
			//array_push($annId, $raoIndex);
			$annotation_ids[$mention_id] = $anaIndex;

			// Rozbita annotacja - TODO: SPRAWDZIĆ
			//if (count($annId)==2){
			//	DbAnnotation::addRelation($annId[0], $annId[1], 1);
			//	$annId = array($annId[1]);		
			//}						
		}
		return $annotation_ids;
	}

	function getContinuousAnnotations($mention){
		$continuous = array();
		$last_id = -1;
		foreach($mention as $annotation){
			$curr_id = (int)$this->getMorphLastPart($annotation);
			if($last_id + 1 == $curr_id){
				$continuous[count($continuous) - 1][] = $annotation;
			}
			else{
				$continuous[] = array($annotation);
			}
			$last_id = $curr_id;
		}
		return $continuous;
	}

	function getMorphLastPart($annotation){
		$s_seg = split("\.", $annotation);
		$n_seg = $s_seg[count($s_seg) - 1];
		$last_part = split("-", $n_seg)[0];
		return $last_part;
	}

	function writeCoreference($report_id, $coreference, $annotations){
		foreach($coreference as $relation){
			foreach($relation as $part){
				$from = $annotations[$part["from"]];
				$to = $annotations[$part["to"]];
				DbAnnotation::addCoreference($from, $to, 1); // Tymczasowo 6 - coreference_agp
			}
		}
	}

}

class Importer {
	var $db;
	var $read_mode;
	var $import_mode;
	var $path;
	var $regex;

	var $reader;
	var $documents;

	function __construct($db, $path){
		$this->db = $db;
		$this->path = $path;
		
		$this->reader = new TeiTextReader();
		$this->writer = new TeiDatabaseWriter();
		$this->documents = array();
	}

	function importFile($folder){
		// Create document object
		$document = new TeiReport($folder, $this->reader, $this->writer, false);
		// Read document contents
		$document->read();
		$document->write();
	}

	function import(){
		$this->importFile($this->path);
	}
}


class ImportTEI{
	
	var $opt;
	var $config;

	function __construct(){
		$this->opt = new Cliopt();
		$this->opt->addExecute("php import-ccl.php ",null);
		//$this->opt->addParameter(new ClioptParameter("read_mode", "r", "read_mode", "Input file read mode: single file (default) | (d)irectory | (f)ile containing list of files"));
		//$this->opt->addParameter(new ClioptParameter("import_mode", "m", "import_mode", "Import mode: only analyse, import nothing (default) | (l)emmas only | (a)nnotations and lemmas"));
		$this->opt->addParameter(new ClioptParameter("input", "i", "input", "Input file or directory path"));
		//$this->opt->addParameter(new ClioptParameter("annotation", "a", "annotation", "Regex describing annotation types to be considered by script"));
		$this->opt->addParameter(new ClioptParameter("debug_level", "d", "debug_level", "Debug level - for analyse display: 1 - short analyse, 2 - long analyse"));
		$this->opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
		$this->opt->addParameter(new ClioptParameter("db-host", null, "host", "database address"));
		$this->opt->addParameter(new ClioptParameter("db-port", null, "port", "database port"));
		$this->opt->addParameter(new ClioptParameter("db-user", null, "user", "database user name"));
		$this->opt->addParameter(new ClioptParameter("db-pass", null, "password", "database user password"));
		$this->opt->addParameter(new ClioptParameter("db-name", null, "name", "database name"));
	}

	function parseArgs($argv){
		try {
			$this->opt->parseCli($argv);
			
			$this->dbUser = $this->opt->getOptional("db-user", "root");
			$this->dbPass = $this->opt->getOptional("db-pass", "sql");
			$this->dbHost = $this->opt->getOptional("db-host", "localhost") . ":" . $this->opt->getOptional("db-port", "3306");
			$this->dbName = $this->opt->getOptional("db-name", "gpw");
			
			if ( $this->opt->exists("db-uri")){
				$uri = $this->opt->getRequired("db-uri");
				if ( preg_match("/(.+):(.+)@(.*)\/(.*)/", $uri, $m)){
					$this->dbUser = $m[1];
					$this->dbPass = $m[2];
					$this->dbHost = $m[3];
					$this->dbName = $m[4];
				}else{
					throw new Exception("DB URI is incorrect. Given '$uri', but exptected 'user:pass@host:port/name'");
				}
			}
				
			$this->config = array(
			    			'phptype'  => 'mysql',
			    			'username' => $this->dbUser,
			    			'password' => $this->dbPass,
			    			'hostspec' => $this->dbHost,
			    			'database' => $this->dbName);		 

			//$this->importMode = $this->opt->getOptional("import_mode","analyse");
			//$this->readMode = $this->opt->getOptional("read_mode", "single");
			$this->path = $this->opt->getRequired("input");
			//$this->annotationRegex = $this->opt->getOptional("annotation","*");
			$this->debugLevel = $this->opt->getOptional("debug_level","*");
		}
		catch(Exception $ex){
			print "!! ". $ex->getMessage() . " !!\n\n";
			$this->opt->printHelp();
			die("\n");
		}
	}

	function getDbConfig(){
		return $this->config;
	}

	function import($db){
		$importer = new Importer($db, $this->path);
		$importer->import();
	}
}

$tei_importer = new ImportTEI();
$tei_importer->parseArgs($argv);

$db = new Database($tei_importer->getDbConfig());
$tei_importer->import($db);

?>

