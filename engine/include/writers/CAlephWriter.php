<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/**
 * Converts a set of CclDocument into a Aleph format.
 */

class AlephWriter{
	
	var $docs = array();
	
	// [typ_relacji][id_anotacji][id_anotacji] = 1
	var $relations = array();
	var $relationTypes = array();
	var	$positiveCount = array();
	var $annotationsBySentence = array();
	
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
	
	static function orthPattern($orth){
		if ( preg_match('/^\p{N}+$/u', $orth) )
			return 'PATTERN_NUM';
		elseif ( preg_match('/^\p{Lu}+$/u', $orth) )
			return 'PATTERN_UPPERCASE';
		elseif ( preg_match('/^\p{Ll}+$/u', $orth) )
			return 'PATTERN_LOWERCASE';
		elseif ( preg_match('/^\p{Lu}\p{Ll}+$/u', $orth) )
			return 'PATTERN_UPPERFIRST';
		elseif ( preg_match('/^\p{P}+$/u', $orth) )
			return 'PATTERN_PUNCT';
		else
			return 'PATTERN_MIX';
	}
	
	static function getOrthPatterns(){
		$patterns = array();
		$patterns[] = 'PATTERN_NUM';
		$patterns[] = 'PATTERN_UPPERCASE';
		$patterns[] = 'PATTERN_LOWERCASE';
		$patterns[] = 'PATTERN_MIX';
		return $patterns;
	}
	
	static function write_train_script($package){

		$package_top = basename($package);
		
		$template = "['aleph'].
read_all('$package_top/aleph').
induce_max.
write_rules('$package_top/rules.txt').";

		file_put_contents("$package/train", $template);
	}
	
	var $wn = null;
	
	function __construct(){
		$dsn = array(
		    			'phptype'  => 'mysql',
		    			'username' => 'root',
		    			'password' => 'krasnal',
		    			'hostspec' => 'localhost',
		    			'database' => 'wordnet_test',
						);
		$db = new Database($dsn);
		$this->wn = new PlWordnet();
		$this->wn->loadFromDb($db);		
	}
	
	/**
	 * Wczytuje dokumenty w formacie CCL i zamienia na format AnnotatedDocument.
	 */
	function loadCorpus($cclDocuments){
		assert('is_array($cclDocuments)');

		$document_id = 1;
		foreach ($cclDocuments as $d){
			try{
				$ad = DocumentConverter::wcclDocument2AnnotatedDocument($d);
				$ad->setId($document_id++);
				$this->docs[] = $ad;
			}catch(Exception $ex){
				echo $ex->getMessage();				
			}
		}		
	}
	
	/**
	 * 
	 */
	function writeAlephConfiguration($filename){
		file_put_contents($filename, file_get_contents("ilp_header.txt"));
	}
	
	/**
	 * Zapisuje plik bazy wiedzy dla wcześniej załadowanego korpusu.
	 */
	function writeBackground($filename){
		
		$negativeCount = array();
		$negativeCountTotal = 0;
		$discardedSentenceCount = 0;
		$sentenceCount = 0;
		$words = array();
		$morph = array();
		$sentenceHashes = array();
		$annotation_types = array();
		$available_chunk_types = array("chunk_adjp", "chunk_agp", "chunk_np", "chunk_vp", "chunk_qp", "chunk_cnp", "chunk_prep","chunk_numord");
		$chunk_types = array();
		$filename .= "";
				
		$fb = fopen("$filename", "w");
		
		foreach ($this->docs as $ad){
			
			foreach ($ad->getChunks() as $c){
			
				foreach ($c->getSentences() as $s){

					if ( count($s->getAnnotations()) < 2) {
						$discardedSentenceCount++;
						continue;
					}
					
					$sentenceCount++;
					
					$prev = null;
					foreach ($s->getTokens() as $t){
						$token_global_id = sprintf("d%d_%s_t%s", $ad->getId(), $s->id, $t->id);
						fwrite($fb, sprintf("token(%s). ",$token_global_id ));
						if ($prev != null){
							fwrite($fb, sprintf("token_after_token(%s, %s). ", $prev, $token_global_id));
						}
						fwrite($fb, sprintf("token_orth(%s, '%s'). ", $token_global_id, AlephWriter::transformOrth($t->orth)));
						fwrite($fb, sprintf("token_pattern(%s, '%s'). ", $token_global_id, AlephWriter::orthPattern($t->orth)));
						$lexems = array();
						$tag_num = 0;
						foreach ($t->getLexems() as $l){ 
							$lexems[$l->getBase()] = 1;
							$tag_id = $token_global_id . sprintf("_%s", $tag_num++);
							fwrite($fb, sprintf("token_tag(%s, %s). ", $token_global_id, $tag_id));
							fwrite($fb, sprintf("tag_base(%s, '%s'). ", $tag_id, AlephWriter::transformOrth($l->getBase())));
							
							foreach ( preg_split("/:/",$l->getCtag()) as $m){
								$morph[$m] = 1;
								fwrite($fb, sprintf("tag_morph(%s, '%s'). ", $tag_id, $m));
							}
						}
						
						$hyperonyms = array();						
						foreach (array_keys($lexems) as $lexem) {
							//fwrite($fb, sprintf("token_base(%s, '%s'). ", $token_global_id, AlephWriter::transformOrth($lexem)));
							$words[AlephWriter::transformOrth($lexem)] = 1;
							foreach ($this->wn->getAllHyperonymSynsets($lexem) as $hyph)
								$hyperonyms[$hyph] = 1;								
						}
						
						foreach (array_keys($hyperonyms) as $hyph){
							fwrite($fb, sprintf("token_hypheronym(%s, '%s'). ", $token_global_id, AlephWriter::transformOrth($hyph)));
							$words[AlephWriter::transformOrth($hyph)] = 1;							
						}
						
						fwrite($fb, "\n");
						$words[AlephWriter::transformOrth($t->orth)] = 1;
						$prev = $token_global_id;
					}

					$annotationsInSentence = array();
					foreach ($s->getAnnotations() as $a){
						
						if ( in_array( $a->type, array("person_first_nam", "person_last_nam") ) )
							continue;
						
						$annotation_id = sprintf("d%s_%s_a%s", $ad->getId(), $s->id, $a->id);
						$token_source_id = sprintf("d%d_%s_t%s", $ad->getId(), $s->id, $a->getFirstToken()->id);
						$token_target_id = sprintf("d%d_%s_t%s", $ad->getId(), $s->id, $a->getLastToken()->id);
						fwrite($fb, sprintf("annotation(%s). ", $annotation_id));
						fwrite($fb, sprintf("annotation_range(%s, %s, %s). ", 
								$annotation_id, $token_source_id, $token_target_id));
						fwrite($fb, sprintf("annotation_of_type(%s, %s).\n",  $annotation_id, $a->type));
						$annotation_types[$a->type] = 1;
						if ( in_array( $a->type, $available_chunk_types ) ){
							fwrite($fb, sprintf("chunk(%s, %s, '%s').\n", $token_source_id, $token_target_id, $a->type));
							$chunk_types[$a->type] = 1;
						}
							
						$annotationsInSentence[] = $annotation_id;
					}
					fwrite($fb, "\n");
					
					if ( count($annotationsInSentence) > 0 )
						$this->annotationsBySentence[] = $annotationsInSentence;
				}
			}	
				
			foreach ($ad->getRelations() as $r){				
				$type = strtolower($r->type);
				$annotation_source_id = sprintf("d%s_%s_a%s", $ad->getId(), $r->source->sentence->id, $r->source->id);		
				$annotation_target_id = sprintf("d%s_%s_a%s", $ad->getId(), $r->target->sentence->id, $r->target->id);
						
				$this->relations[$type][$annotation_source_id][$annotation_target_id] = 1;
				$this->relations[$type][$annotation_target_id][$annotation_source_id] = 1;		
				$this->relationTypes[$type] = 1;
				
				$this->positiveCount[$type]++;				
			}		
			
								
		}
		
		fwrite($fb, "\n");
		foreach (array_keys($this->relationTypes) as $relation){
			fwrite($fb, sprintf("relation_type('%s').\n", $relation));
		}
		
		fwrite($fb, "\n");
		foreach (array_keys($words) as $w){
			fwrite($fb, sprintf("orth('%s'). \n", $w));
		}
		
		fwrite($fb, "\n");
		foreach (array_keys($morph) as $m){
			fwrite($fb, sprintf("morph('%s'). \n", $m));
		}
		
		fwrite($fb, "\n");
		foreach (array_keys($annotation_types) as $t){
			fwrite($fb, sprintf("annotation_type('%s'). \n", $t));
		}
		
		fwrite($fb, "\n");
		foreach (array_keys($chunk_types) as $c){
			fwrite($fb, sprintf("chunk_type('%s'). \n", $c));
		}

		fwrite($fb, "\n");
		foreach (AlephWriter::getOrthPatterns() as $t){
			fwrite($fb, sprintf("pattern('%s'). \n", $t));
		}
		
		fclose($fb);
	}
	
	/**
	 * 
	 */
	function writePositiveRelations($filename, $relations_generate){
		$ff = fopen($filename, "w");
		
		/** Wygeneruj pozytywne relacje */		
		foreach ($this->docs as $ad){
			foreach ($ad->getRelations() as $r){				
				$type = strtolower($r->type);
				$annotation_source_id = sprintf("d%s_%s_a%s", $ad->getId(), $r->source->sentence->id, $r->source->id);		
				$annotation_target_id = sprintf("d%s_%s_a%s", $ad->getId(), $r->target->sentence->id, $r->target->id);
				
				if ( count($relations_generate) == 0 ||  in_array($type, $relations_generate)){
					fwrite($ff, sprintf("relation(%s, %s, %s).\n", $annotation_source_id, $annotation_target_id, $type));	
				}		
			}	
		}	
		
		fclose($ff);		
	}
	
	/**
	 * 
	 */
	function writeNegativeRelations($filename, $relations_generate){
		$fn = fopen($filename, "w");
		$negativeCount = array();

		/** Wygeneruj negatywne relacje */
		foreach ($this->annotationsBySentence as $annotationsInSentence){
			if (count($annotationsInSentence) < 2)
				continue;
				
			foreach ($annotationsInSentence as $a){
				foreach ($annotationsInSentence as $b)
					if ($a <> $b){
						foreach ( array_keys($this->relationTypes) as $rel){								
							if ( count($relations_generate) == 0 || in_array($rel, $relations_generate))								
								if (!isset($this->relations[$rel][$a][$b])){
									fwrite($fn, sprintf("relation(%s, %s, %s).\n", $a, $b, $rel));
									$negativeCount[$rel]++;
								}
						}
					}
			}
			
			}
		fclose($fn);
	}
	
}

?>