<?
/**
 * 
 */

$engine = "../engine/";
include($engine."cliopt.php");
include($engine."include/anntakipi/ixtTakipiStruct.php"); 
include($engine."include/anntakipi/ixtTakipiDocument.php"); 
include($engine."database.php");
require_once("PEAR.php");
require_once("MDB2.php");
require_once($engine."include/database/CDbReportAnnotationLemma.php");
require_once($engine."include/database/CDbAnnotation.php");
require_once($engine."include/database/CDbToken.php");

$debug = false;
class Printer{
	
	static function println($text){
		global $debug;
		if($debug){
			echo "[".date("Y-m-d H:i:s")."] ".$text."\n";
		}
	}

	static function printDiffSummary($annotationDiffSummary){
		$totalRemFile = 0;
		$totalRemDb = 0;
		$totalLemFile = 0;
		$totalLemDb = 0;
		echo "\t\t ------ ANNOTATIONS ------ \n";
		foreach($annotationDiffSummary as $channelId => $counts){
			echo "CHANNEL: \t".$channelId." \tTO ADD: ".$counts['file_only']." \tTO REMOVE: ".$counts['db_only'] ."\n";
			$totalRemFile += $counts['file_only'];
			$totalRemDb += $counts['db_only'];
		}
		echo " ---------------------------------------------------------------- \n";
		echo "TOTAL: \t\t\t\tTO ADD: ".$totalRemFile." \tTO REMOVE: ".$totalRemDb ."\n";
		echo " ---------------------------------------------------------------- \n";
		echo "\t\t ------ LEMMAS ------ \n";
		foreach($annotationDiffSummary as $channelId => $counts){
			echo "CHANNEL: \t".$channelId." \tTO ADD: ".$counts['file_lemma']." \tTO REMOVE: ".$counts['db_lemma'] ."\n";
			$totalLemFile += $counts['file_lemma'];
			$totalLemDb += $counts['db_lemma'];
		}
		echo " ---------------------------------------------------------------- \n";
		echo "TOTAL: \t\t\t\tTO ADD: ".$totalLemFile." \tTO REMOVE: ".$totalLemDb ."\n";
		echo " ---------------------------------------------------------------- \n";
		
	}

	static function printDiff($annotationDiff){
		$hline = " ---------------------------------------------- \n";
		echo " ---------------------- ANNOTATION DIFFERENCES ------------------------ \n";
		echo $hline;
		foreach( $annotationDiff as $diff ){
			echo "Annotation channel: ". $diff['channel'] ."\n";
			if(array_key_exists('from', $diff)){
				if($diff['from']['da'] < 0) $diff['from']['da'] = "<MISSING>";
				if($diff['from']['fa'] < 0) $diff['from']['fa'] = "<MISSING>";
				echo "Position in database: ". $diff['from']['da'] .", found: ".$diff['from']['fa']."\n";
			}
			if(array_key_exists('text', $diff)){
				if($diff['text']['da'] == "") $diff['text']['da'] = "<MISSING>";
				if($diff['text']['fa'] == "") $diff['text']['fa'] = "<MISSING>";
				echo "Text in database: \"". $diff['text']['da'] ."\", found: \"".$diff['text']['fa']."\"\n";
			}
			if(array_key_exists('lemma', $diff)){
				if($diff['lemma']['da'] == "") $diff['lemma']['da'] = "<MISSING>";
				if($diff['lemma']['fa'] == "") $diff['lemma']['fa'] = "<MISSING>";
				echo "Lemma in database: \"". $diff['lemma']['da'] ."\", found: \"".$diff['lemma']['fa']."\"\n";
			}
			echo $hline;
		}
	}

	static function printLemmaSummary($lemmas){
		echo " \t ------- LEMMAS TO BE UPDATED ------ \t\n";
		echo "+ -------------  + ------ + -------------------------------- \n";
		echo "| ANNOTATION ID  | SAFE\t | LEMMA \n";
		echo "+ -------------  + ------ + -------------------------------- \n";
		foreach($lemmas as $channel_id => $channel){
			foreach($channel as $from => $lemma){
				if($lemma['diff']){
					echo "| ".($lemma['id']?:"\t")." \t | ".($lemma['safe']?"YES":"NO")."\t | ".($lemma['old']?:"<NULL>") ." -> ".($lemma['text']?:"<NULL>")." \n";
				}
			}
		}
		echo "+ -------------  + ------ + -------------------------------- \n";
	}

	static function printLemma($lemmas){

	}
}

class Report{

	var $path;
	var $reader;
	var $document_id;
	var $document; 
	var $annotationMap;
	var $annotationRegex;
	var $lemmas;

	var $stage = "new";
	var $source = "user";
	var $user_id = "1";

	function __construct($path, $reader, $regex){
		$this->document = array();
		$this->path = $path;
		$this->reader = $reader;
		$this->annotationRegex = $regex;

		$pathParts = explode("/", $path);
		$nameExt = $pathParts[count($pathParts)-1];
		$nameParts = explode(".", $nameExt);

		$this->document_id = $nameParts[0];
		Printer::println("Processing document with id: ".$this->document_id);
	}

	function read(){
		$this->reader->loadFile($this->path);

		$s = 0;
		$t = 0;
		$chunks = array();
		while ($this->reader->nextChunk()){
			$chunks[] = $this->reader->readChunk();
		}
		$chunkNum = 0;
		foreach ($chunks as $chunk){
			$s += count($chunk->sentences);
			foreach ($chunk->sentences as $sentence){
				$tokensNum = count($sentence->tokens); 
				$t += $tokensNum;
			}
		}

		Printer::println("Liczba chunków: " . count($chunks));
		Printer::println("Liczba zdań   : " . $s);
		Printer::println("Liczba tokenów: " . $t);


		/* Poskładaj dokument */
		foreach ($chunks as $chunk){
			$document_part = $chunk->id;
			$this->document[$document_part] = $chunk;
		}

		/* Sprawdź kolejność części */
		$i=1;
		foreach ($this->document as $no=>$part){
			if ( $no != "ch".$i++)
				throw new Exception("Missing part for document $this->document_id");
		}
		Printer::println("Liczba części: " . ($i-1));
		

	}

	function processAnnotationns(){
		$annotationMap = array();
		$sentenceNum = 0;
		$takipiText = "";
			
		// Iteruj po częściach dokumentu
		foreach ($this->document as $chunk){
			// Iteruj po zdaniach w każdej części
			foreach ($chunk->sentences as $sentence){
				// Utwórz tablicę annotacji dla bieżacego zdania
				$annotationMap[$sentenceNum]=array();
				// Iteruj po tokenach w zdaniu
				foreach ($sentence->tokens as $token){
					// Iteruj po typach annotacji dla tokena
					foreach ($token->channels as $channel=>$value){
						if(strpos($channel, "head") > 0)
							var_dump($channel);

						// Sprawdź czy annotacja odpowiada wyrażeniu regularnemu, jeśli nie to pomiń
						if(!preg_match("/$this->annotationRegex/", $channel)) continue;

						// Lemat bieżącej annotacji
						$lemma = array_key_exists($channel,$token->lemmas)?$token->lemmas[$channel]:"";
						// Identyfikator annotacji dla kanału(typu) w zdaniu
						$intvalue = intval($value);	

						// Jeśli identyfikator jest dodatni - przetwarzamy annotację				
						if ($intvalue>0){

							// Jeśli jest to pierwsza annotacja danego typu w zdaniu - zainicjuj tablicę annotacji
							// danego typu dla bieżącego zdania
							if (!array_key_exists($channel, $annotationMap[$sentenceNum])){
								$annotationMap[$sentenceNum][$channel] = array();
								// Ostatnio odwiedzona annotacja
								$annotationMap[$sentenceNum][$channel]['lastval'] = $intvalue;
								// Informacje o annotacji
								$annotationMap[$sentenceNum][$channel][$intvalue][] = array("from"=>mb_strlen($takipiText, 'utf-8'), "text"=>$token->orth, "lemma" => $lemma);
							}							
							// Jeśli jest to pierwszy token z danym identyfikatorem annotacji w kanale(typie) w zdaniu
							else if (!array_key_exists($intvalue, $annotationMap[$sentenceNum][$channel])){
								// Ostatnio odwiedzona annotacja
								$annotationMap[$sentenceNum][$channel]['lastval']=$intvalue;
								// Informacje o annotacji
								$annotationMap[$sentenceNum][$channel][$intvalue][] = array("from"=>mb_strlen($takipiText, 'utf-8'), "text"=>$token->orth, "lemma" => $lemma);
							}							
							// Jeśli jest to annotacja o identyfikatorze spotkanym wcześniej dla danego kanały(typu) w bieżącym zdaniu - część większej annotacji
							else if (array_key_exists($channel, $annotationMap[$sentenceNum]) && array_key_exists($intvalue, $annotationMap[$sentenceNum][$channel])){
								// Ostatnio odwiedzona annotacja w bieżącym kanale
								$lastVal = $annotationMap[$sentenceNum][$channel]['lastval'];
								// Jeśli ostatnio odwiedzona annotacja jest taka sama - mamy ciągłą annotację na kilku kolejnych tokenach
								if ($intvalue == $lastVal){
									// Ostatnia annotacja
								 	$lastElem = array_pop($annotationMap[$sentenceNum][$channel][$lastVal]);
									// Dołącz tekst bieżącego tokena do tekstu całej annotacji
									if ($token->ns) {
										$lastElem["text"].=$token->orth;
									}
									else {
										$lastElem["text"].= " ".$token->orth;
									}
									array_push($annotationMap[$sentenceNum][$channel][$lastVal], $lastElem);
								}
								// Jeśli ostatnio odwiedzona annotacja jest inna - dołącz jako osobny fragment
								else{
									array_push($annotationMap[$sentenceNum][$channel][$intvalue], array("from"=>mb_strlen($takipiText, 'utf-8'), "text"=>$token->orth, "lemma" => $lemma));
								}
								$annotationMap[$sentenceNum][$channel]['lastval']=$intvalue;
							}
						}
						// Jeśli identyfikator nie jest dodatni - dla danego tokena w bieżącym kanale(typie)
						// nie ma annotacji - zaznaczamy, że w ostatnim tokenie nie było annotacji w tym kanale(typie)
						else {
							if (array_key_exists($channel, $annotationMap[$sentenceNum])){
								$annotationMap[$sentenceNum][$channel]['lastval']=0;
							}
						}
					} 	
					$takipiText .= $token->orth;	
				}
	 			$sentenceNum++;
			}
		}
		$this->annotationMap = $annotationMap;
	}

	/**
	 * Sprawdza zgodność anotacji w bazie danych z anotacjami w pliku wejściowym
	 */
	function checkAnnotations($debug){
		$dbAnnotations = DbAnnotation::getReportAnnotationsChannelsByReportId($this->document_id, $this->annotationRegex);
		$fileAnnotations = array();

		$dbAnnotationRelations = array();
		$fileAnnotationRelations = array();

		$annotationDiff = array();
		$annotationDiffSummary = array();
		// Tablica lematów, które mozna bezpiecznie zaktualizować - ich annotacje się nie zmieniły
		$this->lemmas = array();
		// Przekształć mapę annotacji na indeksowaną typem annotacji	
		foreach($this->annotationMap as $sentenceNum => $channels){
			foreach($channels as $channelId => $channel){
				if(!array_key_exists($channelId,$fileAnnotations)) $fileAnnotations[$channelId] = array();
				foreach ($channel as $annotations){
					if (is_array($annotations)){
						foreach ($annotations as $annotation){
							$fileAnnotations[$channelId][] = $annotation;
						}
					}
				}
			}
		}

		// Posortuj tablice typów annotacji wg. pola 'from'
		foreach($fileAnnotations as $channel => $annotations){
			$from = array();
			foreach ($annotations as $key => $row){
			    $from[$key] = $row['from'];
			}
			array_multisort($from, SORT_ASC, $fileAnnotations[$channel]);
		}
		
		// Dla każdej annotacji w pliku porównaj ją z annotatcją w bazie
		foreach($fileAnnotations as $channel => $fileAnn){
			if(!array_key_exists($channel, $annotationDiffSummary)){
				$annotationDiffSummary[$channel] = array(
					'file_only' => 0, 
					'db_only' => 0,
					'file_lemma' => 0,
					'db_lemma' => 0
				);
			}
			$dbAnn = $dbAnnotations[$channel]; 
			foreach($fileAnn as $fa){
				$da = $dbAnn[$fa['from']];
				if(!$da){
					$da = array('from' => -1, 'to' => -1, 'text' => "", "lemma" => "");
					$annotationDiffSummary[$channel]['file_only'] += 1;
				}
				else{
					$dbAnnotations[$channel][$fa['from']]['visited'] = true;
				}
				
				// Compare
				$fromEq = $fa['from'] == $da['from'];
				$textEq = $fa['text'] == $da['text'];
				$lemmaEq = $fa['lemma'] == $da['lemma'];
			
				$safe = false;
				if(!($fromEq && $textEq && $lemmaEq)){
					$diffArray = array("channel" => $channel);
					$diffArray['from']['fa'] = $fa['from'];
					$diffArray['from']['da'] = $da['from'];
					
					$diffArray['text']['fa'] = $fa['text']."(".strlen($fa['text']).")";
					$diffArray['text']['da'] = $da['text']."(".strlen($da['text']).")";
			
					$diffArray['lemma']['fa'] = $fa['lemma']."(".strlen($fa['lemma']).")";
					$diffArray['lemma']['da'] = $da['lemma']."(".strlen($da['lemma']).")";
					
					$annotationDiff[] = $diffArray;


					if(!$lemmaEq){
						if($fa['lemma'] != ""){
							$annotationDiffSummary[$channel]['file_lemma'] += 1;
						}else{
							$annotationDiffSummary[$channel]['db_lemma'] += 1;
						}
						$safe = $fromEq && $textEq;
						
					}
				}
				if(!array_key_exists($channel, $this->lemmas)){
					$this->lemmas[$channel] = array();
				}
				$this->lemmas[$channel][$fa['from']] = array("id" => $da['id'], "text" => $fa['lemma'], "old" => $da['lemma'], 'safe' => $safe, 'diff' => !$lemmaEq);


			}
		}

		// Znajdź annotacje w bazie, które nie mają odpowiedników w pliku
		foreach( $dbAnnotations as $channelId => $channel){
			foreach($channel as $da){
				if(!array_key_exists('visited', $da) || !$da['visited']){
					$annotationDiff[] = array(
						'channel' => $channelId, 
						'from' => array(
							'fa' => -1, 
							'da' => $da['from']
						),
						'text' => array(
							'fa' => "",
							'da' => $da['text']
						)
					);
					if(!array_key_exists($channelId, $annotationDiffSummary)){
						$annotationDiffSummary[$channelId] = array(
							'file_only' => 0, 
							'db_only' => 0,
							'file_lemma' => 0,
							'db_lemma' => 0
						);
					}
					$annotationDiffSummary[$channelId]['db_only'] += 1;
				}
			}
		}

		switch($debug){
			case 1:
				Printer::printDiffSummary($annotationDiffSummary);
				Printer::printLemmaSummary($this->lemmas);
				break;
			case 2:
				Printer::printDiff($annotationDiff);
				Printer::printLemma($this->lemmas);	
				break;
			default:
				break;
		}
		
		
	}

	function analyse($debug){
		$this->checkAnnotations($debug);
	}

	private function deleteAnnotations(){
		Printer::println("Deleting annotations");
		
		DbAnnotation::deleteReportAnnotationsByRegexp($this->document_id, $this->annotationRegex);
		
		Printer::println("Annotations deleted");
	}

	private function importAnnotations(){
		Printer::println("Importing annotations");

		foreach ($this->annotationMap as $sentence){
			foreach ($sentence as $channelId=>$channel){
				foreach ($channel as $annotations){				
					if (is_array($annotations)){
						$annId = array();
						foreach ($annotations as $annotation){
							$raoIndex = DbAnnotation::saveAnnotation($this->document_id, $channelId, $annotation['from'], $annotation['text'], $this->user_id, $this->stage, $this->source);
							array_push($annId, $raoIndex);
							// Przekaż lematowi identyfikator
							$this->lemmas[$channelId][$annotation['from']]['id'] = $raoIndex;
							// Rozbita annotacja
							if (count($annId)==2){
								DbAnnotation::addRelation($annId[0], $annId[1], $this->user_id);
								$annId = array($annId[1]);		
							}						
						}
					}
				}
			}
		}



		Printer::println("Annotations imported");
	}


	/**
	 * Usuwa lematy z bazy
	 * @return void
	 */
	private function deleteLemmas(){
		Printer::println("Deleting lemmas");

		DbReportAnnotationLemma::deleteAnnotationLemmaByAnnotationRegex($this->document_id, $this->annotationRegex);
		
		Printer::println("Lemmas deleted");
	}

	/**
	 * Importuje lematy do bazy danych zgodnie z treścią pliku wejściowego
	 * @param  boolean $onlyWithConsistentAnnotations informacja o tym, czy importować tylko lematy, które są zgodne z annotacjami
	 * istniejącymi w bazie przed importem. Używane przy imporcie samych lematów, bez annotacji
	 * @return void
	 */
	private function importLemmas($onlyWithConsistentAnnotations){
		Printer::println("Importing lemmas");
		
		foreach ($this->lemmas as $channel_id => $channel){
			foreach($channel as $from => $lemma){
				//var_dump(array($channel, $from, $lemma));
				// Sprawdź czy można dodać lemat, jeśli nie są aktualizowane anotacje
				if($lemma['safe'] || !$onlyWithConsistentAnnotations){
					// Jeśli lemat jest pusty - usuń z bazy (Przypadek: w bazie istnieje lemat, którego nie ma w pliku)
					if($lemma['text'] != ""){
						DbReportAnnotationLemma::saveAnnotationLemma($lemma['id'], $lemma['text']);
					}else{
						DbReportAnnotationLemma::deleteAnnotationLemma($lemma['id']);
					}
				}
			}
		}

		Printer::println("Lemmas imported");	
	}

	/**
	 * Reload annotations - przeładowyje annotacje w dwóch trybach:
	 * 1. Przeładowuje annotacje razem z lematami
	 * 2. Przeładowuje same lematy dla annotacji
	 * @param  boolean $onlyLemmas Wskazuje, czy przeładowywać jedynie lematy, czy całe annotacje
	 * @return void
	 */
	function reloadAnnotations($onlyLemmas){
		if(!$onlyLemmas) {
			// Przy przeładowaniu samych lematów, te które istnieją tylko w bazie 
			// i tak zostaną usunięte przy imporcie 
			// (zastępowanie pustym lematem powoduje usunięcie)
			$this->deleteLemmas($onlyLemmas);
			// Usuwanie i import annotacji
			$this->deleteAnnotations();
			$this->importAnnotations();
		}
		// Import lematów 
		$this->importLemmas($onlyLemmas);
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

	function __construct($db, $path, $read_mode, $import_mode, $regex, $debug_level){
		$this->db = $db;
		$this->path = $path;
		$this->read_mode = $read_mode;
		$this->import_mode = $import_mode;
		$this->regex = $regex;
		$this->debug = $debug_level;

		$this->reader = new TakipiReader();
		$this->documents = array();
	}

	/**
	 * The main import function allowing to import single file contents
	 * @param  string $file path to imported filed
	 * @return void
	 */
	function importFile($file){
		// Create document object
		$document = new Report($file, $this->reader, $this->regex);
		// Read document contents
		$document->read();
		// Read document annotations
		$document->processAnnotationns();
		// Make analyse
		$document->analyse($this->debug);
		// Reload annotations
		if($this->import_mode == "annotation" || $this->import_mode == "lemma"){
			$document->reloadAnnotations($this->import_mode == "lemma");
		}

	}
	
	function importFiles($files){
		foreach($files as $file){
			Importer::importFile($file);
		}
	}
	
	function getFileList($file){
		$files = array();
		$lines = file($file);
		foreach($lines as $line){
			$files[] = $line;
		}

		return $files;
	}

	function importFromListFile($file){
		$files = $this->getFileList($file);
		Importer::importFiles($files);
	}
	
	function rglob($pattern='*', $flags = 0, $path='')
	{
		$paths=glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);
		$files=glob($path.$pattern, $flags);
		foreach ($paths as $path) { 
			$files=array_merge($files,$this->rglob($pattern, $flags, $path)); 
		}
		return $files;
	}

	function getDirectoryFiles($dir, $ext){
		$files = $this->rglob("*.".$ext, 0, $dir);
		return $files;
	}

	function importDirectory($directoryPath){
		$files = $this->getDirectoryFiles($directoryPath, "xml");
		Importer::importFiles($files);
	}
	
	
	function import(){
		switch($this->read_mode){
			case "list_file":
				Importer::importFromListFile($this->path);
				break;
			case "directory":
				Importer::importDirectory($this->path);
				break;
			default:
				Importer::importFile($this->path);
		}
	}
	
}


class ImportCCL{
	
	var $opt;
	var $config;

	function __construct(){
		$this->opt = new Cliopt();
		$this->opt->addExecute("php import-ccl.php ",null);
		$this->opt->addParameter(new ClioptParameter("read_mode", "r", "read_mode", "Input file read mode: single file (default) | (d)irectory | (f)ile containing list of files"));
		$this->opt->addParameter(new ClioptParameter("import_mode", "m", "import_mode", "Import mode: only analyse, import nothing (default) | (l)emmas only | (a)nnotations and lemmas"));
		$this->opt->addParameter(new ClioptParameter("input", "i", "input", "Input file or directory path"));
		$this->opt->addParameter(new ClioptParameter("annotation", "a", "annotation", "Regex describing annotation types to be considered by script"));
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
				
			$this->config->dsn = array(
			    			'phptype'  => 'mysql',
			    			'username' => $this->dbUser,
			    			'password' => $this->dbPass,
			    			'hostspec' => $this->dbHost,
			    			'database' => $this->dbName);		 

			$this->importMode = $this->opt->getOptional("import_mode","analyse");
			$this->readMode = $this->opt->getOptional("read_mode", "single");
			$this->path = $this->opt->getRequired("input");
			$this->annotationRegex = $this->opt->getOptional("annotation","*");
			$this->debugLevel = $this->opt->getOptional("debug_level","*");
		}
		catch(Exception $ex){
			print "!! ". $ex->getMessage() . " !!\n\n";
			$this->opt->printHelp();
			die("\n");
		}
	}

	function getDbConfig(){
		return $this->config->dsn;
	}

	function import($db){
		$importer = new Importer($db, $this->path, $this->readMode, $this->importMode, $this->annotationRegex, $this->debugLevel);
		$importer->import();
	}
}

$ccl_importer = new ImportCCL();
$ccl_importer->parseArgs($argv);

$db = new Database($ccl_importer->getDbConfig());
$ccl_importer->import($db);

?>