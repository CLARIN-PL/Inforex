<?

class DocumentConverter{
	
	/**
	 * Konwertuje obiekt WcclDocument do AnnotationDocument.
	 */
	static function wcclDocument2AnnotatedDocument($wccl){
		
		$sentence_id = 1;
		$sentencecs = array();
		
		$annotation_index = array();
		
		foreach($wccl->chunks as $c){
			foreach($c->sentences as $s){

				$sentene = &new AnnotatedDocumentSentence($s->id, array(), array());
				$sentencecs[] = $sentene;	
				
				$token_id = 1;
				$annotation_id = 1;
								
				foreach($s->tokens as $t){
					$sentene->tokens[] = new AnnotatedDocumentToken($token_id++, $t->orth, $t->ns);
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
								$sentene->annotations[] = $ad;
								
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
							$sentene->annotations[] = $ad;

							$hash = sprintf("%s_%s_%s", $s->id, $name, $last_num);
							$annotation_index[$hash] = &$ad;
						}
					}									
			}
		}
		
		$relation_id = 1;
		foreach ($wccl->relations as $r){
			$source_hash = sprintf("%s_%s_%s", $r->source_sentence_id, $r->source_channal_name, $r->source_id);
			$target_hash = sprintf("%s_%s_%s", $r->target_sentence_id, $r->target_channal_name, $r->target_id);
			
			if (!isset($annotation_index[$source_hash]))
				throw new Exception("Annotation $source_hash not found");

			if (!isset($annotation_index[$target_hash]))
				throw new Exception ("Annotation $target_hash not found");
			
			$source = $annotation_index[$source_hash];
			$target = $annotation_index[$target_hash];
			
			$relations[] = new AnnotatedDocumentRelation($relation_id++, $source, $target, $r->type);
		}
		
		return new AnnotatedDocument($name, $sentencecs, $relations);
		
	}
	
}

?>