<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */


//DbTagset::getTagsetId
class WCclImport {

    function __construct(){
        // setting tagset_id to 'nkjp'
        $this->defaultTagsetId = DbTagset::getTagsetId('nkjp');
    }


	function importCcl($report, $file, $stage="new"){
		$content = "";
		$result = true;
		try {
			$document = CclReader::readCclFromFile($file);
			foreach($document->chunks as $chunk){
				foreach($chunk->sentences as $sentence){
					if ($chunk->type === NULL){
						$content = $content . "<chunk>\n";
					} else{
						$content = $content . "<chunk type=\"".$chunk->type."\">\n";
					}

					foreach($sentence->tokens as $token){
						if ($token->ns)
							$content = $content . custom_html_entity_decode($token->orth);
						else
							$content = $content . " " . custom_html_entity_decode($token->orth);
					}
					$content = $content . "\n</chunk>";
				}
				$content = $content . "\n";
			}
			$content = custom_html_entity_decode($content);
		}
		catch (Exception $ex){
			$result = false;
			echo "Exception: " . $ex->getMessage() . "\n";
		}
		
		$report->content = $content;
		$parse = $report->validateSchema();
		$report->save();
		$this->tag_document($document, $report);
		$annotationMap = $this->processAnnotations($document);
		$this->importAnnotations($annotationMap, $report, $stage);
		return $result;
	}
	
	
	function tag_document($ccl, $r){
		global $db;
		$useSentencer = true;
		$reportFormat = "premorph";
		
		try{
			$takipiText="";
			$new_bases = array();
			$new_ctags = array();
			$tokens = array();
			$tokens_tags = array();
			$report_id = $r->id;
	
			foreach ($ccl->chunks as $chunk){
				foreach ($chunk->sentences as $sentence){
					$lastId = count($sentence->tokens)-1;
					foreach ($sentence->tokens as $index=>$token){
						$from =  mb_strlen($takipiText);
						$takipiText = $takipiText . custom_html_entity_decode($token->orth);
						$takipiText = custom_html_entity_decode($takipiText);
						$to = mb_strlen($takipiText)-1;
						$lastToken = $index==$lastId ? 1 : 0;
	
						$args = array($report_id, $from, $to, $lastToken);
						$tokens[] = $args;
	
						$tags = $token->lexemes;
	
						/** W przypadku ignów zostaw tylko ign i disamb */
						$ign = null;
						$tags_ign_disamb = array();
							
						foreach ($tags as $i_tag=>$tag){
							if ($tag->ctag == "ign")
								$ign = $tag;
							if ($tag->ctag == "ign" || $tag->disamb)
								$tags_ign_disamb[] = $tag;
						}
						/** Jeżeli jedną z interpretacji jest ign, to podmień na ign i disamb */
						if ($ign){
							$tags = $tags_ign_disamb;
						}
	
						$tags_args = array();
						foreach ($tags as $lex){
							$base = addslashes(strval($lex->base));
							$ctag = addslashes(strval($lex->ctag));
							$cts = explode(":",$ctag);
							$pos = $cts[0];
							$disamb = $lex->disamb ? "true" : "false";
							if (isset($index_bases[$base]))
								$base_sql = $index_bases[$base];
							else{
								if ( !isset($new_bases[$base]) ) $new_bases[$base] = 1;
								$base_sql = '(SELECT id FROM bases WHERE text="' . $base . '")';
							}
							if (isset($index_ctags[$ctag]))
								$ctag_sql = $index_ctags[$ctag];
							else{
								if ( !isset($new_ctags[$ctag]) ) $new_ctags[$ctag] = 1;
								$ctag_sql = '(SELECT id FROM tokens_tags_ctags
								              WHERE ctag="' . $ctag . '" 
								              AND tagset_id='. $this->defaultTagsetId . ')';
							}
							$tags_args[] = array($base_sql, $ctag_sql, $disamb, $pos);
						}
						$tokens_tags[] = $tags_args;
					}
				}
			}
			
			/* Wstawienie tagów morflogicznych */
			if ( count ($new_bases) > 0 ){
				$sql_new_bases = 'INSERT IGNORE INTO `bases` (`text`) VALUES ("';
				$sql_new_bases .= implode('"),("', array_keys($new_bases)) . '");';
				$db->execute($sql_new_bases);
			}
			if ( count ($new_ctags) > 0 ){
				$sql_new_ctags = 'INSERT IGNORE INTO `tokens_tags_ctags` (`ctag`, `tagset_id`) VALUES ("';
				$sql_new_ctags .= implode('",'. $this->defaultTagsetId .'),("', array_keys($new_ctags)) . '",'.
                    $this->defaultTagsetId .');';
				$db->execute($sql_new_ctags);
			}
				
			$sql_tokens = "INSERT INTO `tokens` (`report_id`, `from`, `to`, `eos`) VALUES";
			$sql_tokens_values = array();
			foreach ($tokens as $t){
				$sql_tokens_values[] ="({$t[0]}, {$t[1]}, {$t[2]}, {$t[3]})";
			}
			$sql_tokens .= implode(",", $sql_tokens_values);
			$db->execute($sql_tokens);
				
			$tokens_id = array();
			foreach ($db->fetch_rows("SELECT token_id FROM tokens WHERE report_id = ? ORDER BY token_id ASC", array($report_id)) as $t){
				$tokens_id[] = $t['token_id'];
			}
			//echo "Tokens: " . count($tokens_id) . "\n";
	
			$sql_tokens_tags = "INSERT INTO `tokens_tags_optimized` (`token_id`,`base_id`,`ctag_id`,`disamb`,`pos`) VALUES ";
			$sql_tokens_tags_values = array();
			for ($i=0; $i<count($tokens_id); $i++){
				$token_id = $tokens_id[$i];
				if ( !isset($tokens_tags[$i]) || count($tokens_tags[$i]) == 0 ){
					die("Bład spójności danych: brak tagów dla $i");
				}
				foreach ($tokens_tags[$i] as $t)
					$sql_tokens_tags_values[] ="($token_id, {$t[0]}, {$t[1]}, {$t[2]}, \"{$t[3]}\")";
			}
			$sql_tokens_tags .= implode(",", $sql_tokens_tags_values);
			$db->execute($sql_tokens_tags);
				
			// Aktualizacja flag i znaczników
			$sql = "UPDATE reports SET tokenization = ? WHERE id = ?";
			$db->execute($sql, array($tokenization, $report_id));
				
			/** Tokens */
			$sql = "SELECT corpora_flag_id FROM corpora_flags WHERE corpora_id = ? AND short = 'Tokens'";
			$corpora_flag_id = $db->fetch_one($sql, array($doc['corpora']));
	
			if ($corpora_flag_id){
				$db->execute("REPLACE reports_flags (corpora_flag_id, report_id, flag_id) VALUES(?, ?, 3)",
						array($corpora_flag_id, $report_id));
			}
	
			/** Sentences */
			if( Config::Config()->get_insertSentenceTags() && $useSentencer )
				Premorph::set_sentence_tag($report_id,Config::Config()->get_user());
				
			$db->execute("COMMIT");
				
		}
		catch(Exception $ex){
			$db->execute("ROLLBACK");
			echo "\n";
			echo "-------------------------------------------------------------\n";
			echo "!! Exception @ id = {$doc['id']}\n";
			echo "   " . $ex->getMessage() . "\n";
			echo "-------------------------------------------------------------\n";
		}
	}

	function processAnnotations($ccl){
		$annotationMap = array();
		$sentenceNum = 0;
		$takipiText = "";
	
		// Iteruj po częściach dokumentu
		foreach ($ccl->chunks as $chunk){
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
						//if(!preg_match("/$this->annotationRegex/", $channel)) continue;
	
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
								$annotationMap[$sentenceNum][$channel][$intvalue][] = array("from"=>mb_strlen($takipiText, 'utf-8'), "text"=>$token->orth, "lemma" => $token->getBase());
							}
							// Jeśli jest to pierwszy token z danym identyfikatorem annotacji w kanale(typie) w zdaniu
							else if (!array_key_exists($intvalue, $annotationMap[$sentenceNum][$channel])){
								// Ostatnio odwiedzona annotacja
								$annotationMap[$sentenceNum][$channel]['lastval']=$intvalue;
								// Informacje o annotacji
								$annotationMap[$sentenceNum][$channel][$intvalue][] = array("from"=>mb_strlen($takipiText, 'utf-8'), "text"=>$token->orth, "lemma" => $token->getBase());
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
										$lastElem["lemma"].=$token->getBase();
									}
									else {
										$lastElem["text"].= " ".$token->orth;
										$lastElem["lemma"].= " ".$token->getBase();
									}
									array_push($annotationMap[$sentenceNum][$channel][$lastVal], $lastElem);
								}
								// Jeśli ostatnio odwiedzona annotacja jest inna - dołącz jako osobny fragment
								else{
									array_push($annotationMap[$sentenceNum][$channel][$intvalue], array("from"=>mb_strlen($takipiText, 'utf-8'), "text"=>$token->orth, "lemma" => $token->getBase()));
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
					$takipiText .= custom_html_entity_decode($token->orth);
					$takipiText = custom_html_entity_decode($takipiText);
				}
				$sentenceNum++;
			}
		}
		return $annotationMap;
	}
	
	function importAnnotations($annotationMap, $r, $stage="new"){
		global $db;
        $sql = "INSERT INTO `reports_annotations_optimized` (`report_id`,`type_id`,`from`,`to`,`text`,`user_id`,`creation_time`,`stage`,`source`) ";

		$sql_values = array();
		$annotation_map_lemma = array();
		foreach ($annotationMap as $sentence){
			foreach ($sentence as $channelId=>$channel){
				foreach ($channel as $annotations){
					if (is_array($annotations)){
						foreach ($annotations as $annotation){	
							$text = $annotation['text'];
							$from = $annotation['from'];				
							$to = $from + mb_strlen(preg_replace("/\n+|\r+|\s+/","",$text), 'utf-8') -1;
							$text = addslashes($text);

                            $sql_values[] = " SELECT {$r->id}, `annotation_type_id`, {$from}, {$to}, '{$text}', {$r->user_id}, now(), '{$stage}', 'bootstrapping' FROM `annotation_types` WHERE `name`={$channelId} ";
							$annotation_key = "{$from},{$to},{$annotation_type_id}";
							$annotation_map_lemma[$annotation_key] = addslashes($annotation["lemma"]);
							/*$raoIndex = DbAnnotation::saveAnnotation($r->id, $channelId, $annotation['from'], $annotation['text'], $r->user_id, "new", "bootstrapping");
							DbAnnotation::setAnnotationLemma($db, $raoIndex, $annotation["lemma"]);*/
						}
					}
				}
			}
		}
        $sql .= implode(" UNION ", $sql_values);
		if (!empty($sql_values)){
			$db->execute($sql);
			$sql = "SELECT concat(`from`, ',', `to`, ',', `type_id`) as `key`, id " . 
					"FROM `reports_annotations_optimized` " .
					"WHERE report_id = ? ";
			$sql2 = "INSERT INTO reports_annotations_lemma (report_annotation_id, lemma) VALUES ";
			$lemma_values = array();
			foreach ($db->fetch_rows($sql, array($r->id)) as $t)
				$lemma_values[] = "({$t['id']},'{$annotation_map_lemma[$t['key']]}')";
			$sql2 .= implode(",", $lemma_values);
			$db->execute($sql2);
		}
			
	}	
	
	
}
?>
