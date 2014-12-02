<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class HelperBootstrap{
	
	static function transformCclToChunkSet($ccl){
		$reader = new TakipiReader();
		$reader->loadText($ccl);

		$chunks = array();

		while ($reader->nextChunk()){
			$chunks[] = $reader->readChunk();
		}
		
		$offset = 0;

		$annotations = array();
		
		foreach ($chunks as $chunk){
			foreach ($chunk->sentences as $sentence){
				if (count($sentence->tokens) > 0){ 
					foreach ($sentence->tokens[0]->channels as $channel=>$x){
						$stoken = null;
						$sbegin = null;
						$sentence_offset = $offset;
						$annotation_text = "";
						foreach ($sentence->tokens as $token){
							$tag = $token->channels[$channel];

							if ( $stoken != null && ($tag == "0" || $tag != $stoken->channels[$channel])){
								/* Dodajemy nową anotację */
																			
								$annotations[] = new WcclAnnotation($sbegin, $sentence_offset-1, $channel, trim($annotation_text));
												
								$stoken = null;
								$annotation_text = null;				
							}
							
							if ( $tag != "0" ){
								if ( $stoken == null ){
									$stoken = $token;
									$annotation_text = $token->orth;
									$sbegin = $sentence_offset;
								}
								else{
									$annotation_text .= " " . $token->orth;
								}																	
							}						
														
							//$sentence_offset += mb_strlen(html_entity_decode($token->orth, ENT_COMPAT, "utf-8"));
							$sentence_offset += mb_strlen(custom_html_entity_decode($token->orth));
						}
						
						/* Dodaj anotacje kończącą się razem ze zdaniem */
						if ( $stoken != null ){
							$annotations[] = new WcclAnnotation($sbegin, $sentence_offset-1, $channel, trim($annotation_text));
						}
					}
				}
				foreach ($sentence->tokens as $token)
					$offset += mb_strlen(custom_html_entity_decode($token->orth));
				//$offset += mb_strlen(html_entity_decode($token->orth, ENT_COMPAT, "utf-8"));
			}
		}		
		
		return $annotations;
	}

	static function chunkWithLiner2($text, $model){
		global $config;
		$liner2 = "{$config->path_liner2}/liner2.sh";
		$liner2 = "liner2";
		
//		if ( !file_exists($liner2) )
//			throw new Exception("File '$liner2' not found");

		$tmp_in = "/tmp/inforex_liner2_input.txt";
		$tmp_out = "/tmp/inforex_liner2_output.txt";

		file_put_contents($tmp_in, $text);		
		$cmd = sprintf("%s pipe -i ccl -ini %s -f %s -t %s", $liner2, $model, $tmp_in, $tmp_out);
		
		$output = array();
		$error = 0;
		
		exec($cmd, $output, $error);
		return file_get_contents($tmp_out);
	}

	/**
	 * Funkcja opakowuje logikę rozpoznania nowych chunków w dokumencie
	 * i warunkowego dodania ich do tabeli z anotacjami.
	 * @return liczba nowych anotacji
	 */
	static function bootstrapPremorphFromLinerModel($report_id, $user_id, $model_ini){			
		global $mdb2, $config;
		
		$count = 0;
		
		$content = db_fetch_one("SELECT content FROM reports WHERE id = ?", array($report_id));
		$corpus_id = db_fetch_one("SELECT corpora FROM reports WHERE id = ?", array($report_id));
		
//		$paragraphs = array();
		
//		$reader = new XMLReader();
//		$reader->xml($content);
//		do {
//			$read = $reader->read();
//			if ($reader->localName == "chunk" && $reader->nodeType == XMLReader::ELEMENT){
//				$text = trim($reader->readString());
//				if ($text == "" || $reader->getAttribute("type") == "s")
//					continue;
//					
//				$text = strip_tags($text);
//				//$text = html_entity_decode($text);
//				$text = custom_html_entity_decode($text);
//				$text = preg_replace("/(\n|[ ]+)/m", " ", $text);
//				$paragraphs[] = $text;				
//			}	
//		}
//		while ( $read );

		$paragraphs[] = html_entity_decode($content);
				
		$tagged = HelperTokenize::tagPlainWithWcrft($content);
		
		$chunked = HelperBootstrap::chunkWithLiner2($tagged, $model_ini);		

		$annotations = HelperBootstrap::transformCclToChunkSet($chunked);
		
		$hs = new HtmlStr($content);
		foreach ($annotations as $n=>$an){
			$annotations[$n]->text = $hs->getText($an->from, $an->to);			
		}
		
		$sql = "SELECT name" .
				" FROM annotation_types t" .
				" JOIN annotation_sets_corpora c ON (c.annotation_set_id=t.group_id)" .
				" WHERE c.corpus_id=?";
		
		$typesDB = db_fetch_rows($sql, array($corpus_id));
		$typesArray = array();
		foreach ($typesDB as $t){
			array_push($typesArray, $t['name']);
		}
		
		foreach ($annotations as $an){
			if (in_array($an->type, $typesArray)){
				$sql = "SELECT `id` FROM `reports_annotations` " .
						"WHERE `report_id`=? AND `type`=? AND `from`=? AND `to`=?";
				$ids = db_fetch_rows($sql, array($report_id, $an->type, $an->from, $an->to));
				if ( count($ids)==0 ){					
					$sql = "INSERT INTO `reports_annotations_optimized` " .
							"(`report_id`, `type_id`, `from`, `to`, `text`, `user_id`, `creation_time`, `stage`,`source`) VALUES " .
							'(?, (SELECT annotation_type_id FROM annotation_types WHERE name=?), ?, ?, ?, ?, now(), "new", "bootstrapping")';
					db_execute($sql, array($report_id, $an->type, $an->from, $an->to, $an->text, $user_id));
					$count++;
				}
			}
		}
					
		return array("recognized"=>count($annotations), "added"=>$count);
	}
		
}

?>