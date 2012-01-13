<?
/**
 * Converts a set of CclDocument into a Aleph format.
 */

class AlephWriter{
	
	static function transformOrth($orth){

		$base = $orth;

		$orth = str_replace("\"", "meta_PAR", $orth);	
		$orth = str_replace(".", "meta_DOT", $orth);
		$orth = str_replace(",", "meta_COMMA", $orth);
		$orth = str_replace("(", "meta_BRACKET_LEFT", $orth);
		$orth = str_replace(")", "meta_BRACKET_RIGHT", $orth);
		$orth = str_replace("[", "meta_SQBRACKET_LEFT", $orth);
		$orth = str_replace("]", "meta_SQBRACKET_RIGHT", $orth);
		$orth = str_replace("–", "meta_DASH", $orth);
		$orth = str_replace("-", "meta_DASH", $orth);
		$orth = str_replace(":", "meta_DOTS", $orth);
		$orth = str_replace("+", "meta_PLUS", $orth);
		$orth = str_replace("%", "meta_PERCENT", $orth);
		$orth = str_replace("=", "meta_EQUAL", $orth);
		$orth = str_replace("°", "meta_DEGREE", $orth);
		$orth = str_replace("®", "meta_RESERVED", $orth);
		$orth = str_replace(";", "meta_SEMICOLON", $orth);
		$orth = str_replace("/", "meta_SLASH", $orth);
		$orth = str_replace("&", "meta_AMP", $orth);
		$orth = str_replace("?", "meta_QUESTION_MARK", $orth);
		$orth = str_replace("'", "meta_APOSTROPHE", $orth);
		
		if (substr($orth, 0, 5) != 'meta_')
			$orth = "word_" . mb_strtolower($orth, 'UTF-8');
				
		return $orth;
	}
	
	static function write_train_script($package){

		$package_top = basename($package);
		
		$template = "['aleph'].
read_all('$package_top/aleph').
induce_max.
write_rules('$package_top/rules.txt').";

		file_put_contents("$package/train", $template);
	}
	
	static function write($filename, $cclDocuments=array(), $relations_generate=array()){
		assert('is_array($cclDocuments)');
		
		$negativeCount = array();
		$negativeCountTotal = 0;
		$positiveCount = array();
		$discardedSentenceCount = 0;
		$sentenceCount = 0;
		$words = array();
		$sentenceHashes = array();
		$annotation_types = array();
		$relationTypes = array();

		if (!file_exists($filename))
			mkdir($filename);
			
		$filename .= "";
				
		$fb = fopen("$filename/background.txt", "w");
		$f = fopen("$filename/aleph.b", "w");
		$ff = fopen("$filename/aleph.f", "w");
		$fn = fopen("$filename/aleph.n", "w");
		$fm = fopen("$filename/sentences_dump.txt", "w");
		
		fwrite($f, file_get_contents("ilp_header.txt"));
		fwrite($fb, "\n");
		
		$document_id = 1;
		foreach ($cclDocuments as $d){
			try{
				$ad = DocumentConverter::wcclDocument2AnnotatedDocument($d);
			}catch(Exception $ex){
				echo $ex->getMessage();				
			}
			
			$annotationsBySentence = array();
			
			foreach ($ad->getChunks() as $c){
			
				foreach ($c->getSentences() as $s){

					if ( count($s->getAnnotations()) < 2) {
						$discardedSentenceCount++;
						continue;
					}
					
					$sentenceCount++;
					
					$parts = array();
					foreach ($s->getTokens() as $t){
						$parts[] = $t->orth;
					}
					
					$prev = null;
					foreach ($s->getTokens() as $t){
						$token_global_id = sprintf("d%d_%s_t%s", $document_id, $s->id, $t->id);
						fwrite($fb, sprintf("token(%s). ",$token_global_id ));
						if ($prev != null){
							fwrite($fb, sprintf("token_after_token(%s, %s). ", $prev, $token_global_id));
						}
						fwrite($fb, sprintf("token_orth(%s, '%s'). ", $token_global_id, AlephWriter::transformOrth($t->orth)));
						$lexems = array();
						foreach ($t->getLexems() as $l) $lexems[AlephWriter::transformOrth($l->getBase())] = 1;
						foreach (array_keys($lexems) as $lexem) {
							fwrite($fb, sprintf("token_base(%s, '%s'). ", $token_global_id, $lexem));
							$words[$lexem] = 1;
						}
						fwrite($fb, "\n");
						$words[AlephWriter::transformOrth($t->orth)] = 1;
						$prev = $token_global_id;
					}

					$annotationsInSentence = array();
					foreach ($s->getAnnotations() as $a){
						
						if ( in_array( $a->type, array("person_first_nam", "person_last_nam") ) )
							continue;
						
						$annotation_id = sprintf("d%s_%s_a%s", $document_id, $s->id, $a->id);
						$token_source_id = sprintf("d%d_%s_t%s", $document_id, $s->id, $a->getFirstToken()->id);
						$token_target_id = sprintf("d%d_%s_t%s", $document_id, $s->id, $a->getLastToken()->id);
						fwrite($fb, sprintf("annotation(%s). ", $annotation_id));
						fwrite($fb, sprintf("annotation_range(%s, %s, %s). ", 
								$annotation_id, $token_source_id, $token_target_id));
						fwrite($fb, sprintf("annotation_of_type(%s, %s).\n",  $annotation_id, $a->type));
						$annotation_types[$a->type] = 1;
						
						$annotationsInSentence[] = $annotation_id;
					}
					fwrite($fb, "\n");
					
					if ( count($annotationsInSentence) > 0 )
						$annotationsBySentence[] = $annotationsInSentence;
				}
			}	
		
			/** Wygeneruj pozytywne relacje */
			
			// [typ_relacji][id_anotacji][id_anotacji] = 1
			$relations = array();
			
			foreach ($ad->getRelations() as $r){				
				$type = strtolower($r->type);
				$annotation_source_id = sprintf("d%s_%s_a%s", $document_id, $r->source->sentence->id, $r->source->id);		
				$annotation_target_id = sprintf("d%s_%s_a%s", $document_id, $r->target->sentence->id, $r->target->id);
				
				if ( count($relations_generate) == 0 ||  in_array($type, $relations_generate)){
					fwrite($ff, sprintf("relation(%s, %s, %s).\n", $annotation_source_id, $annotation_target_id, $type));

					fwrite($fm, sprintf("relation(%s, %s) :-", $annotation_source_id, $annotation_target_id));
					foreach ($r->source->sentence->getTokens() as $t){
						fwrite($fm, " " . trim($t->getOrth()) );
					}
					fwrite($fm, "\n");

				}
		
				$relations[$type][$annotation_source_id][$annotation_target_id] = 1;
				$relations[$type][$annotation_target_id][$annotation_source_id] = 1;		
				$relationTypes[$type] = 1;
				
				$positiveCount[$type]++;

					
			}
			
			/** Wygeneruj negatywne relacje */
			foreach ($annotationsBySentence as $annotationsInSentence){
				if (count($annotationsInSentence) < 2)
					continue;
					
				foreach ($annotationsInSentence as $a)
					foreach ($annotationsInSentence as $b)
						if ($a <> $b){
							foreach ( array_keys($relationTypes) as $rel){								
								if ( count($relations_generate) == 0 || in_array($rel, $relations_generate))								
									if (!isset($relations[$rel][$a][$b])){
										fwrite($fn, sprintf("relation(%s, %s, %s).\n", $a, $b, $rel));
										$negativeCount[$rel]++;


									}
							}
						}
				
			}
								
			/** Następny dokument */			
			$document_id++;
		}
		

		fwrite($fb, "\n");
		foreach (array_keys($relationTypes) as $relation){
			fwrite($fb, sprintf("relation_type('%s').\n", $relation));
		}
		
		fwrite($fb, "\n");
		foreach (array_keys($words) as $w){
			fwrite($fb, sprintf("orth('%s'). \n", $w));
		}
		
		fwrite($fb, "\n");
		foreach (array_keys($annotation_types) as $t){
			fwrite($fb, sprintf("annotation_type(%s). \n", $t));
		}
		
		fclose($fb);
		fwrite($f, file_get_contents("$filename/background.txt"));
		
		fclose($f);
		fclose($ff);
		fclose($fn);
		fclose($fm);
		AlephWriter::write_train_script($filename);
		
		echo "# Generated \n";
		echo "-----------\n";
		print_r($relations_generate);
		echo "\n";
		echo "# Positive \n";
		echo "-----------\n";
		print_r($positiveCount);
		echo "\n";
		echo "# Negative \n";
		echo "-----------\n";
		print_r($negativeCount);				
		echo "# Total negative: $negativeCountTotal\n";
		echo "\n";
		echo "# Generated sentences: $sentenceCount\n";
		echo "# Discarded sentences: $discardedSentenceCount\n";
		echo "\n";
	}
	
}

?>