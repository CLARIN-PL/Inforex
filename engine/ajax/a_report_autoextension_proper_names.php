<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class Ajax_report_autoextension_proper_names extends CPage {
	
	var $isSecure = false;
	
	/**
	 * Generate AJAX output.
	 */
	function execute(){
		$count = 0;
		$count += $this->runModel("crf_model_gpw-all-nam_orth-base-ctag.ini");
		$count += $this->runModel("crf_model_4corpora-5nam_7x24-feat-dict.ini");		
		//$count += $this->runModel("crf_model_wikinews-all-nam_7x24-feat-dict-gen.ini");
		$count += $this->runModel("crf_4corpora_person_country_city_road_w1_feat_dict.ini");
								
		$json = array( "success"=>1, "count"=>$count);
		return $json;
	}

	function runModel($model){
		global $mdb2, $user, $corpus, $config;
		
		$count = 0;
		$report_id = intval($_POST['report_id']);
		$user_id = $user['user_id'];
		
		$content = db_fetch_one("SELECT content FROM reports WHERE id = ?", array($report_id));
		$corpus_id = db_fetch_one("SELECT corpora FROM reports WHERE id = ?", array($report_id));
		$content = strip_tags($content);
	
		
		$tagger = new WSTagger($config->takipi_wsdl);
		$tagger->tag($content);
		$sentences = $tagger->getIOB();
		
		$takipiText = "";
	  	foreach ($sentences as $sentence){
	  		foreach ($sentence as $elem){
	  			$takipiText .= $elem[0] . " ";
	  		}  
	  		$takipiText .= " ";
	  	}	  	
		$text = $takipiText;
		
		$chunker = new Liner($config->path_python, $config->path_liner, $config->path_liner."/models/".$model );

		$htmlStr = new HtmlStr($text, true);
		$offset = 0;
		$annotations = array();
		
		// TODO: zmieniło się API
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
		
		// Zdanie po zdaniu
		foreach ($chunkings as $chunking){
			// Treść zdania
			$text = $chunker->cseq[$i];
			
			foreach ($chunking as $c){
				$annType = strtolower($c[2]);
				$from = $offset+$c[0];
				$to = $offset + $c[1];				
				if (in_array($annType, $typesArray)){
					$sql = "SELECT `id` FROM `reports_annotations` " .
							"WHERE `report_id`=? AND `type`=? AND `from`=? AND `to`=?";
					if (count(db_fetch_rows($sql, array($report_id, $annType, $from, $to)))==0){					
						$sql = "INSERT INTO `reports_annotations_optimized` " .
								"(`report_id`, `type_id`, `from`, `to`, `text`, `user_id`, `creation_time`, `stage`,`source`) VALUES " .
								sprintf('(%d, (SELECT annotation_type_id FROM annotation_types WHERE name="%s"), %d, %d, "%s", %d, now(), "new", "bootstrapping")',
										$report_id, $annType, $from, $to, $htmlStr->getText($from, $to), $user_id  );
						db_execute($sql);
						$count++;
					}
				}
			}
				
			foreach ($sentences[$i] as $token)
				$offset += mb_strlen($token[0]);
				
			$i++;			
		}
		
		return $count;
	}
		
}
?>
