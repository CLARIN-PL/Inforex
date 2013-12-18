<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
require_once("{$config->path_engine}/pages/ner.php");

/**
 */
class Ajax_report_autoextension_ner_process extends CPage {
	
	var $isSecure = false;
	
	/**
	 * Generate AJAX output.
	 */
	function execute(){
		global $mdb2, $user, $corpus, $config;
		
		$text = strval($_POST['text']);
		$model = strval($_POST['model']);
		$report_id = intval($_POST['report_id']);
		$corpus_id = intval($_POST['corpus_id']);
		$user_id = $user['user_id'];
		
		$models = PerspectiveAutoExtension::getModels();

		$tagger = new WSTagger($config->takipi_wsdl);
		$tagger->tag($text);
		$sentences = $tagger->getIOB();
		
		$takipiText = "";
	  	foreach ($sentences as $sentence){
	  		foreach ($sentence as $elem){
	  			$takipiText = $takipiText . $elem[0] . " ";
	  		}  
	  	}	  	
		$text = $takipiText;
		
		$chunker = new Liner($config->path_python, $config->path_liner, $config->path_liner."/models/" . $models[$model]['file']);

		$htmlStr = new HtmlStr($text, true);
		$offset = 0;
		$annotations = array();
		
		$chunker->chunkSentences($sentences);
		
		$annotations = array();
		$chunkings = $chunker->getChunkingChars();
		$i = 0;
		
		$sql = "SELECT name" .
				" FROM annotation_types t" .
				" JOIN annotation_sets_corpora c ON (c.annotation_set_id=t.group_id)" .
				" WHERE c.corpus_id=?";
		
		$typesDB = db_fetch_rows($sql, array($corpus_id));
		$typesArray = array();
		foreach ($typesDB as $t){
			array_push($typesArray, $t['name']);
		}
		fb($typesArray);
		
		// Zdanie po zdaniu
		foreach ($chunkings as $chunking){
			// Treść zdania
			$text = $chunker->cseq[$i];
			
			foreach ($chunking as $c){
				fb($c);
				$annType = strtolower($c[2]);
				$from = $offset+$c[0];
				$to = $offset + $c[1];				
				if (in_array($annType, $typesArray)){
					$sql = "SELECT `id` " .
							"FROM `reports_annotations` " .
							"WHERE `report_id`=$report_id " .
							"AND `type`=\"$annType\" " .
							"AND `from`=$from " .
							"AND `to`=$to";
					if (count(db_fetch_rows($sql))==0){					
						$sql = "INSERT INTO `reports_annotations_optimized` " .
								"(`report_id`, `type_id`, `from`, `to`, `text`, `user_id`, `creation_time`, `stage`,`source`) VALUES " .
								sprintf('(%d, (SELECT annotation_type_id FROM annotation_types WHERE name="%s"), %d, %d, "%s", %d, now(), "new", "bootstrapping")',
										$report_id, $annType, $from, $to, $htmlStr->getText($from, $to), $user_id  );
						db_execute($sql);
					}
				}
			}
				
			foreach ($sentences[$i] as $token)
				$offset += mb_strlen($token[0]);
				
			$i++;			
		}
		$json = array( "success"=>1);
		return $json;
	}
		
}
?>
