<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class DocumentConverter{
	
	/**
	 * Konwertuje obiekt WcclDocument do AnnotationDocument.
	 */
	static function wcclDocument2AnnotatedDocument($wccl){

		$doc = &new AnnotatedDocument($wccl->name);
		
		$sentence_id = 1;
		$sentencecs = array();
		
		$annotation_index = array();
		
		foreach($wccl->chunks as $c){
			
			$chunk = &new AnnotatedDocumentChunk($c->id);
			$doc->addChunk($chunk);
						
			foreach($c->sentences as $s){

				$sentene = &new AnnotatedDocumentSentence($s->id);
				$chunk->addSentence($sentene);	
				
				$token_id = 1;
				$annotation_id = 1;
								
				foreach($s->tokens as &$t){
					$token = &new AnnotatedDocumentToken($token_id++, $t->orth, $t->ns);
					$sentene->addToken($token);
									
					if (!is_array($t->lexemes) ){
						print_r($t);
						throw new Exception("`lexems` attribute is not an array for '$t->orth'.");
					}
						
					
					foreach ($t->lexemes as $l){
						$lexem = &new AnnotatedDocumentLexem($l->base, $l->ctag, $l->disamb); 
						$token->addLexem($lexem);
					}
				}
				
				if (count($s->tokens)>0)
					foreach ($s->tokens[0]->channels as $name=>$val){
						$last_num = 0;
						$text = "";
						$first_index = null;
						for ($i=0; $i<count($s->tokens); $i++){
							$t = $s->tokens[$i];
							if ($t->channels[$name] != $last_num && $first_index !== null){
								// dodaj anotacje
								$ad = &new AnnotatedDocumentAnnotation($annotation_id++, $sentene, $first_index, $i-1, $name, trim($text));
								$sentene->addAnnotation($ad);
								
								$hash = sprintf("%s_%s_%s", $s->id, $name, $last_num);
								$annotation_index[$hash] = &$ad;
								
								$last_num = 0;
								$first_index = null;
							}
							if ($t->channels[$name] != 0 ){
																						
								// rozpocznij nową anotację
								if ($last_num == 0){
									$text = "";
									$first_index = $i;
									$last_num = $t->channels[$name]; 
								}
								
								$text .= ($t->ns ? "" : " ") . $t->orth;
							}
						}
						if ($last_num != 0){
							$ad = &new AnnotatedDocumentAnnotation($annotation_id++, $sentene, $first_index, $i-1, $name, trim($text));
							$sentene->addAnnotation($ad);

							$hash = sprintf("%s_%s_%s", $s->id, $name, $last_num);
							$annotation_index[$hash] = &$ad;
						}
					}									
			}
		}
		
		$relation_id = 1;
		$relations = array();
		
		foreach ($wccl->relations as $r){
			$source_hash = sprintf("%s_%s_%s", $r->source_sentence_id, $r->source_channal_name, $r->source_id);
			$target_hash = sprintf("%s_%s_%s", $r->target_sentence_id, $r->target_channal_name, $r->target_id);
			
			if (!isset($annotation_index[$source_hash])){
				throw new Exception("Annotation $source_hash not found");
			}

			if (!isset($annotation_index[$target_hash])){
				throw new Exception ("Annotation $target_hash not found");
			}
			
			$source = $annotation_index[$source_hash];
			$target = $annotation_index[$target_hash];
			
			$doc->addRelation(new AnnotatedDocumentRelation($relation_id++, $source, $target, $r->type));
		}
		
		return $doc;
	}
	
}

?>