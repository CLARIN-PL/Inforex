<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

/**
 * Nowa wersja skryptu do eksportu korpusu, która pozwala na definicję eksportu
 * wskazanych elementów w oparciu o statusy flag dla dokumentów.
 * 
 * I. Wskazanie dokumentów do eksportu
 * 
 *   document_selector: corpus_id=7&flag:clean=3,4
 * 
 * 
 * II. Wskazanie anotacji do eksportu:
 * 
 *   element: flag_name=statusy => definicja elementów
 * 
 *   np.
 *     names=3,4 => annotation_set_id=1
 *     names=3,4 => annotation_subset_id=3,4; relation_set_id=2
 * 
 * III. Wygenerowanie indeksów:
 * 
 *   index:nazwa => flag_name=statusy
 * 
 *   np. 
 *     index:
 */ 

$engine = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), "..", "engine")));
include($engine . DIRECTORY_SEPARATOR . "config.php");
include($engine . DIRECTORY_SEPARATOR . "config.local.php");
include($engine . DIRECTORY_SEPARATOR . "include.php");
include($engine . DIRECTORY_SEPARATOR . "cliopt.php");

mb_internal_encoding("utf-8");
ob_end_clean();

//--------------------------------------------------------
//configure parameters
$opt = new Cliopt();
$opt->addExecute("php export-corpus.php ...",null);
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));
$opt->addParameter(new ClioptParameter("selector", "s", "description", "opis selektora, np. corpus_id=7&name=3,4"));
$opt->addParameter(new ClioptParameter("extractor", "e", "description", "opis esktraktora anotacji i relacji w zależności od wartości flagi, np. names=3,4:annotation_set_id=1"));
$opt->addParameter(new ClioptParameter("list", "l", "description", "generator listy dokumentów"));
$opt->addParameter(new ClioptParameter("output", "o", "path", "ścieżka do katalogu, w którym ma być zapisany korpus"));


//--------------------------------------------------------
// Parse parameters
$config = new stdClass();
$dns = null;
$config->selectors = array();
try {
	$opt->parseCli($argv);

	// Parsowanie db-uri 
	$uri = $opt->getRequired("db-uri");
	if ( preg_match("/(.+):(.+)@(.*)\/(.*)/", $uri, $m)){
		$dbUser = $m[1];
		$dbPass = $m[2];
		$dbHost = $m[3];
		$dbName = $m[4];
		$config->dsn = array('phptype'  => 'mysql', 'username' => $dbUser, 'password' => $dbPass,
    							'hostspec' => $dbHost, 'database' => $dbName);		    			
		
	}else{
		throw new Exception("DB URI is incorrect. Given '$uri', but exptected 'user:pass@host:port/name'");
	}
	
	$config->output = $opt->getRequired("output");
	$config->selectors = $opt->getParameters("selector");
	$config->extractors = $opt->getParameters("extractor");
	$config->lists = $opt->getParameters("list");
}
catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

/**
 * Funckja parsuje opis ekstraktora danych
 */
function parse_extractor($description){
	$extractors = array();
	$parts = explode(":", $description);
	if ( count($parts) !== 2 ){
		throw new Exception("Niepoprawny opis ekstraktora " . $description);
	}
	$flag = $parts[0];
	$elements = $parts[1];
	
	$flag = split("=", $flag);
	if ( count($flag) !== 2 ){
		throw new Exception("Niepoprawny opis ekstraktora " . $description .": definicja flagi");
	}

	$flag_name = strtolower($flag[0]);
	$flag_ids = explode(",", $flag[1]);
	
	foreach ( explode("&", $elements) as $element ){
		$parts = explode("=", $element);
		$element_name = $parts[0];
		$extractor_name = $flag_name."=".implode(",", $flag_ids).":".$element;
		$extractor = array("flag_name"=>$flag_name, "flag_ids"=>$flag_ids, "name"=>$extractor_name);		
		
		/* Esktraktor anotacji po identyfikatorze zbioru anotacji */
		if ( $element_name === "annotation_set_id" ){			
			$extractor["params"] = explode(",", $parts[1]);			
			$extractor["extractor"] = function($report_id, $params, &$elements){
				// $params -- set of annotation_set_id
				$annotations = DbAnnotation::getAnnotationsBySets(array($report_id), $params);
				if ( is_array($annotations) ) {
					$elements['annotations'] = array_merge($elements['annotations'], $annotations);
				}
			};
			$extractors[] = $extractor;
		}
		/* Esktraktor anotacji po identyfikatorze podzbioru anotacji */
		elseif ( $element_name === "annotation_subset_id" ){			
			$extractor["params"] = explode(",", $parts[1]);			
			$extractor["extractor"] = function($report_id, $params, &$elements){
				// $params -- set of annotation_set_id
				$annotations = DbAnnotation::getAnnotationsBySubsets(array($report_id), $params);
				if ( is_array($annotations) ) {
					$elements['annotations'] = array_merge($elements['annotations'], $annotations);
				}
			};
			$extractors[] = $extractor;
		}
		/* Esktraktor relacji po identyfikatorze zbioru */
		elseif ( $element_name === "relation_set_id" ){
			$extractor["params"] = explode(",", $parts[1]);
			$extractor["extractor"] = function($report_id, $params, &$elements){
				// $params -- set of annotation_set_id
				$relations = DbCorpusRelation::getRelationsBySets2(array($report_id), $params);
				if ( is_array($relations) ) {
					$elements['relations'] = array_merge($elements['relations'], $relations);
				}
			};
			$extractors[] = $extractor;
		}		
		/* Ekstraktor lematów dla zbioru anotacji*/
		elseif ( $element_name === "lemma_annotation_set_id" ){
			$extractor["params"] = explode(",", $parts[1]);
			$extractor["extractor"] = function($report_id, $params, &$elements){
				// $params -- set of annotation_set_id
				$lemmas = DbReportAnnotationLemma::getLemmasBySets(array($report_id), $params);
				if ( is_array($lemmas) ) {
					$elements['lemmas'] = array_merge($elements['lemmas'], $lemmas);
				}
			};
			$extractors[] = $extractor;
		}		
		/* Ekstraktor lematów dla podzbioru anotacji*/
		elseif ( $element_name === "lemma_annotation_subset_id" ){
			$extractor["params"] = explode(",", $parts[1]);
			$extractor["extractor"] = function($report_id, $params, &$elements){
				// $params -- set of annotation_set_id
				$lemmas = DbReportAnnotationLemma::getLemmasBySubsets(array($report_id), $params);
				if ( is_array($lemmas) ) {
					$elements['lemmas'] = array_merge($elements['lemmas'], $lemmas);
				}
			};
			$extractors[] = $extractor;
		}		
		else{
			throw new Exception("Niepoprawny opis ekstraktora " . $description . ": nieznany ektraktor " . $element_name);
		}
	}
	return $extractors;
}

/**
 * 
 */
function parse_list($description){
	$cols = explode(":", $description);
	if ( count($cols) != 2 ){
		throw new Exception("Niepoprawny opis listy: $description");
	}	
	$list_name = $cols[0];
	$list_flags = array();
	foreach ( explode("&", $cols[1]) as $flags ){
		$fc = explode("=", $flags);
		if ( count($fc) != 2 ){
			throw new Exception("Niepoprawny warunek dla flagi '$flags' w $description");
		}
		$flag_name = strtolower($fc[0]);
		$flag_ids = explode(",", $fc[1]);
		$list_flags[] = array("flag_name"=>$flag_name, "flag_ids"=>$flag_ids);
	}
	return array("name"=>$list_name, "flags"=>$list_flags, "report_ids" => array());
}

/**
 * 
 */
function log_error($file_name, $line_no, $report_id, $message){
	$file_name = basename($file_name);
	echo "[$file_name:$line_no] Błąd dla dokumentu id=$report_id: $message\n";
}

/**
 * Eksport dokumentu o wskazanym identyfikatorze
 * @param $report_id Identyfikator dokumentu do eksportu
 * @param $extractors Lista extraktorów danych
 * @param $disamb_only Jeżeli true, to eksportowany są tylko tagi oznaczone jako disamb
 * @param $extractors_stats Tablica ze statystykami ekstraktorów
 */
function export_document($report_id, &$extractors, $disamb_only, &$extrators_stats, &$lists, $output_folder, $subcorpora){
	$flags = DbReportFlag::getReportFlags($report_id);
	$elements = array("annotations"=>array(), "relations"=>array(), "lemmas"=>array());
	
	// Wykonaj esktraktor w zależności od ustalonej flagi
	foreach ( $extractors as $extractor ){
		$func = $extractor["extractor"];
		$params = $extractor["params"]; 
		$flag_name = $extractor["flag_name"];
		$flag_ids = $extractor["flag_ids"];
		if ( isset($flags[$flag_name]) && in_array($flags[$flag_name], $flag_ids) ){
			$extractor_elements = array();
			foreach (array_keys($elements) as $key){
				$extractor_elements[$key] = array();
			}
			$func($report_id, $params, $extractor_elements);
			foreach (array_keys($extractor_elements) as $key){
				$elements[$key] = array_merge($elements[$key], $extractor_elements[$key]);
			}
			// Zapisz statystyki
			$name = $extractor["name"];
			if ( !isset($extrators_stats[$name]) ){
				$extrators_stats[$name] = array();
			}
			foreach ( $extractor_elements as $type=>$items ){
				if ( !isset($extrators_stats[$name][$type]) ){
					$extrators_stats[$name][$type] = count($items);
				}
				else{
					$extrators_stats[$name][$type] += count($items);
				}
			}
		}
	}
		
	//
	$tokens = DbToken::getTokenByReportId($report_id);
	$tags = DbTag::getTagsByReportId($report_id);
	
	$tags_by_tokens = array();
	foreach ($tags as $tag){
		$token_id = $tag['token_id'];
		if ( !isset($tags_by_tokens[$token_id]) ){
			$tags_by_tokens[$token_id] = array();
		}
		if ( $disamb_only == false || $tag['disamb'] ){
			$tags_by_tokens[$token_id][] = $tag;
		} 
	}
		
	$report = DbReport::getReportById($report_id);
	try{
		$ccl = CclFactory::createFromReportAndTokens($report, $tokens, $tags_by_tokens);
	}
	catch(Exception $ex){
		log_error(__FILE__, __LINE__, $report_id, "Problem z utworzeniem ccl: " . $ex->getMessage());
		return;
	}
	$annotations = array();
	$relations = array();
	$lemmas = array();
	if ( isset($elements["annotations"]) && count($elements["annotations"]) ){
		$annotations = $elements["annotations"];
	}
	if ( isset($elements["relations"]) && count($elements["relations"]) ){
		$relations = $elements["relations"];
	}
	if ( isset($elements["lemmas"]) && count($elements["lemmas"]) ){
		$lemmas = $elements["lemmas"];
	}
	
	/* Usunięcie zduplikowanych anotacji */
	$annotations_by_id = array();
	foreach ($annotations as $an){
		$anid = intval($an['id']);
		if ( $anid > 0 ){
			$annotations_by_id[$anid] = $an;
		}
		else{
			log_error(__FILE__, __LINE__, $report_id, "brak identyfikatora anotacji");
		}
	}
	$annotations = array_values($annotations_by_id);
	
	/* Sprawdzenie, anotacji źródłowych i docelowych dla relacji */
	foreach ( $relations as $rel ){
		$source_id = $rel["source_id"];
		$target_id = $rel["target_id"];
		if ( !isset($annotations_by_id[$source_id]) ){			
			log_error(__FILE__, __LINE__, $report_id, "brak anotacji źródłowej o identyfikatorze $source_id ({$rel["name"]}) -- brakuje warsty anotacji?");
		}
		if ( !isset($annotations_by_id[$target_id]) ){
			log_error(__FILE__, __LINE__, $report_id, "brak anotacji źródłowej o identyfikatorze $target_id ({$rel["name"]}) -- brakuje warsty anotacji?");
		}
	}
	
	/* Sprawdzenie lematów */
	foreach ($lemmas as $an){
		$anid = intval($an['id']);
		if ( !isset($annotations_by_id[$anid]) ){			
			//print_r($an);
			log_error(__FILE__, __LINE__, $report_id, "brak anotacji $anid dla lematu ({$an["name"]}) -- brakuje warsty anotacji?");
		}
	}
	
	/* Wygeneruj xml i rel.xml */
	CclFactory::setAnnotationsAndRelations($ccl, $annotations, $relations);
	CclFactory::setAnnotationLemmas($ccl, $lemmas);
	CclWriter::write($ccl, $output_folder . "/" . $ccl->getFileName() . ".xml", CclWriter::$CCL);
	CclWriter::write($ccl, $output_folder . "/" . $ccl->getFileName() . ".rel.xml", CclWriter::$REL);
	
	/* Eksport metadanych */
	$report = DbReport::getReportById($report_id);
	$ext = DbReport::getReportExtById($report_id);
	
	$basic = array("id", "date", "title", "source", "author", "tokenization", "subcorpus");			
	$lines = array();
	$lines[] = "[document]";
	$report["subcorpus"] = $subcorpora[$report['subcorpus_id']];
	
	foreach ($basic as $name){
		$lines[] = sprintf("%s = %s", $name, $report[$name]);
	}	
	if ( count($ext) > 0 ){
		$lines[] = "";
		$lines[] = "[metadata]";
		foreach ($ext as $key=>$val){
			if ($key != "id"){
				$key = preg_replace("/[^\p{L}|\p{N}]+/u", "_", $key);
				$lines[] = sprintf("%s = %s", $key, $val);
			}		
		}
	}
	file_put_contents($output_folder . "/" . $ccl->getFileName() . ".ini", implode("\n", $lines));
	
	/* Przypisanie dokumentu do list */
	foreach ( $lists as $ix=>$list){
		foreach ( $list['flags'] as $flag){
			$flag_name = $flag["flag_name"];
			$flag_ids = $flag["flag_ids"];
			if ( isset($flags[$flag_name]) && in_array($flags[$flag_name], $flag_ids) ){
				$lists[$ix]["document_names"][$ccl->getFileName() . ".xml"] = 1;
			}
		}		
	}
		
}

//--------------------------------------------------------
/**
 * 
 */
function run($config){
	$db = new Database($config->dsn);
	$GLOBALS['db'] = $db;
	
	/* Przygotuje katalog docelowy */
	$output_folder = $config->output;	
	if ( !file_exists("$output_folder/documents") ){
		mkdir("$output_folder/documents", 0777, true);
	}
	
	/* Przygotuj listę podkorpusów w postaci tablicy id=>nazwa*/
	$subcorpora_assoc = DbCorpus::getSubcorpora();
	$subcorpora = array();
	foreach ( $subcorpora_assoc as $sub ){
		$subcorpora[$sub['subcorpus_id']] = $sub['name'];
	}
	
	$extractors = array();
	foreach ( $config->extractors as $extractor ){
		$extractors = array_merge($extractors, parse_extractor($extractor));
	}
	
	$lists = array();
	foreach ( $config->lists as $list){
		$lists[] = parse_list($list);
	}
	
	$document_ids = array();
	foreach ( $config->selectors as $selector ){
		foreach ( DbReport::getReportsBySelector($selector, "id") as $d ){
			$document_ids[$d['id']] = 1;
		}
	}	
	
	$document_ids = array_keys($document_ids);
	echo "Liczba dokumentów do eksportu: " . count($document_ids) . "\n";
	
	$extrators_stats = array();
	
	foreach ($document_ids as $id){
		export_document($id, $extractors, true, $extrators_stats, $lists, "$output_folder/documents", $subcorpora);
	}
	foreach ($lists as $list){
		echo sprintf("%4d %s\n", count(array_keys($list["document_names"])), $list["name"]);
		$lines = array();
		foreach ( array_keys($list["document_names"]) as $document_name ){
			$lines[] = "./documents/" . $document_name;
		}
		sort($lines);	
		file_put_contents("$output_folder/{$list['name']}", implode("\n", $lines));
	}	
	
	$types = array();
	$max_len_name = 0;
	foreach ($extrators_stats as $name=>$items){
		$max_len_name = max(strlen($name), $max_len_name);
		foreach (array_keys($items) as $type){
			$types[$type] = 1;
		}
	}
	
	echo "\n";
	echo str_repeat(" ", $max_len_name);
	foreach ( array_keys($types) as $type ){
		echo " $type";
	}
	echo "\n";
	foreach ($extrators_stats as $name=>$items){
		echo sprintf("%-".$max_len_name."s", $name);
		foreach ( array_keys($types) as $type ){
			$val = "-";
			if ( isset($items[$type]) && intval($items[$type]) > 0 ){
				$val = "" . $items[$type];
			}
			echo sprintf(" %".strlen($type)."s", $val);
		}	
		echo "\n";
	}
}
 
//--------------------------------------------------------
 try {
 	run($config);
 }
 catch(Exception $ex){
	print "\n!! ". $ex->getMessage() . " !!\n";
	die("\n");
}


?>

