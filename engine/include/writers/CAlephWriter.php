<?
/**
 * Converts a set of CclDocument into a Aleph format.
 */

class AlephWriter{
	
	static function transformOrth($orth){

		$orth = str_replace("\"", "s_PAR", $orth);	
		$orth = str_replace(".", "s_DOT", $orth);
		$orth = str_replace(",", "s_COMMA", $orth);
		$orth = str_replace("(", "s_BRACKET_L", $orth);
		$orth = str_replace(")", "s_BRACKET_R", $orth);
		$orth = str_replace("[", "s_SQBRACKET_L", $orth);
		$orth = str_replace("]", "s_SQBRACKET_R", $orth);
		$orth = str_replace("–", "s_DASH", $orth);
		$orth = str_replace("-", "s_DASH", $orth);
		$orth = str_replace(":", "s_DOTS", $orth);
		$orth = str_replace("+", "s_PLUS", $orth);
		$orth = str_replace("%", "s_PERCENT", $orth);
		$orth = str_replace("=", "s_EQUAL", $orth);
		$orth = str_replace("°", "s_OOO", $orth);
		$orth = str_replace("®", "s_RESERVED", $orth);
		$orth = str_replace(";", "s_SEMICOLON", $orth);
		$orth = str_replace("/", "s_SLASH", $orth);
		$orth = str_replace("&", "s_AMP", $orth);
		$orth = mb_strtolower($orth, 'UTF-8');
		//$orth = utf8_decode($orth);
		$orth = str_replace("'", "_", $orth);
		$orth = str_replace("?", "_", $orth);
//		$orth = str_replace("ł", "l", $orth);
//		$orth = str_replace("ę", "e", $orth);
//		$orth = str_replace("ż", "z", $orth);
//		$orth = str_replace("ą", "a", $orth);
//		$orth = str_replace("ń", "n", $orth);
//		$orth = str_replace("ź", "z", $orth);
		
		return "w_" . $orth;
	}
	
	static function write($filename, $cclDocuments=array()){
		assert('is_array($cclDocuments)');
		
		$negativeCount = array();
		$positiveCount = array();
		$words = array();
		$annotation_types = array();
		
		$fb = fopen("$filename.b", "w");
		$ff = fopen("$filename.f", "w");
		$fn = fopen("$filename.n", "w");
		
		fwrite($fb, file_get_contents("ilp_header.txt"));
		fwrite($fb, "\n");
		
		$document_id = 1;
		foreach ($cclDocuments as $d){
			echo "... " . $d->name . "\n"; 
			try{
				$ad = DocumentConverter::wcclDocument2AnnotatedDocument($d);
			}catch(Exception $ex){
				echo $ex->getMessage();				
			}
			
			$annotationsBySentence = array();
			
			foreach ($ad->getChunks() as $c){
			
				foreach ($c->getSentences() as $s){
					$prev = null;
					foreach ($s->getTokens() as $t){
						$token_global_id = sprintf("d%d_%s_t%s", $document_id, $s->id, $t->id);
						fwrite($fb, sprintf("token(%s). ",$token_global_id ));
						if ($prev != null){
							fwrite($fb, sprintf("token_after_token(%s, %s). ", $prev, $token_global_id));
						}
						fwrite($fb, sprintf("token_attributes(%s, '%s').\n", $token_global_id, AlephWriter::transformOrth($t->orth)));
						$words[AlephWriter::transformOrth($t->orth)] = 1;
						$prev = $token_global_id;
					}

					$annotationsInSentence = array();
					foreach ($s->getAnnotations() as $a){
						$annotation_id = sprintf("d%s_%s_a%s", $document_id, $s->id, $a->id);
						$token_source_id = sprintf("d%d_%s_t%s", $document_id, $s->id, $a->getFirstToken()->id);
						$token_target_id = sprintf("d%d_%s_t%s", $document_id, $s->id, $a->getLastToken()->id);
						fwrite($fb, sprintf("annotation(%s). ", $annotation_id));
						fwrite($fb, sprintf("annotation_attributes(%s, %s, %s, %s).\n", 
								$annotation_id, $token_source_id, $token_target_id, $a->type));
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
				fwrite($ff, sprintf("relation_%s(%s, %s).\n", $type, $annotation_source_id, $annotation_target_id));
		
				$relations[$type][$annotation_source_id][$annotation_target_id] = 1;
				$relations[$type][$annotation_target_id][$annotation_source_id] = 1;		
				
				$positiveCount[$type]++;
			}
			
			/** Wygeneruj negatywne relacje */
			foreach ($annotationsBySentence as $annotationsInSentence){
				if (count($annotationsInSentence) < 2)
					continue;
					
				foreach ($annotationsInSentence as $a)
					foreach ($annotationsInSentence as $b)
						if ($a <> $b){
							foreach (array_keys($relations) as $rel){
								if (!isset($relations[$rel][$a][$b])){
									fwrite($fn, sprintf("relation_%s(%s, %s).\n", $rel, $a, $b));
									$negativeCount[$rel]++;
								}
							}
						}
				
			}
					
			/** Następny dokument */			
			$document_id++;
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
		fclose($ff);
		fclose($fn);
		
		echo "# Positive \n";
		echo "-----------\n";
		print_r($positiveCount);
		echo "\n";
		echo "# Negative \n";
		echo "-----------\n";
		print_r($negativeCount);				
		echo "\n";
	}
	
}

?>