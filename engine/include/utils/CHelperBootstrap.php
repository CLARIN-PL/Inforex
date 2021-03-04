<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class HelperBootstrap{
	
	static function transformCclToAnnotations($ccl){
		$reader = new TakipiReader();
		$reader->loadText($ccl);

		$offset = 0;
		$annotations = array();
        while ($reader->nextChunk()){
            $chunk = $reader->readChunk();
			foreach ($chunk->sentences as $sentence){
			    $tokenStart = array($offset);
			    /* Index global offsets for each token in the sentence */
                foreach ($sentence->tokens as $token){
                    $offset += mb_strlen(custom_html_entity_decode($token->orth));
                    $tokenStart[] = $offset;
                }
                foreach ($sentence->tokens[0]->channels as $channel=>$x){
                    $lastTag = 0;
                    $tokenIndex = 0;
                    $tokenFrom = null;
                    $annotationText = "";
                    foreach ($sentence->tokens as $token){
                        $tag = $token->channels[$channel];
                        if ( $tokenFrom !== null && ($tag == "0" || $tag != $lastTag)){
                            /* Dodajemy nową anotację */
                            $an = new TableReportAnnotation();
                            $an->setFrom($tokenStart[$tokenFrom]);
                            $an->setTo($tokenStart[$tokenIndex]-1);
                            $an->setType($channel);
                            $an->setText(trim($annotationText));
                            $annotations[] = $an;

                            $annotationText = null;
                            $tokenFrom = null;
                        }
                        if ( $tag != "0" ){
                            if ( $tokenFrom === null ){
                                $annotationText = $token->orth;
                                $tokenFrom = $tokenIndex;
                            } else {
                                $annotationText .= " " . $token->orth;
                            }
                        }
                        $tokenIndex++;
                        $lastTag = $tag;
                    }
                    /* Dodaj anotacje kończącą się razem ze zdaniem */
                    if ( $tokenFrom !== null ){
                        $an = new TableReportAnnotation();
                        $an->setFrom($tokenStart[$tokenFrom]);
                        $an->setTo($tokenStart[$tokenIndex]-1);
                        $an->setType($channel);
                        $an->setText(trim($annotationText));
                        $annotations[] = $an;
                    }
                }
			}
		}
		return $annotations;
	}

	// TODO: to remove
	static function chunkWithLiner2($text, $model){
		$liner2 = Config::Config()->get_path_liner2()."/liner2.sh";
		$liner2 = "liner2";
		
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
		global $db;
		
		$count = 0;
		
		$content = $db->fetch_one("SELECT content FROM reports WHERE id = ?", array($report_id));
		$corpus_id = $db->fetch_one("SELECT corpora FROM reports WHERE id = ?", array($report_id));

		$paragraphs[] = html_entity_decode($content);
				
		$tagged = HelperTokenize::tagPlainWithWcrft($content);
		
		$chunked = HelperBootstrap::chunkWithLiner2($tagged, $model_ini);		

		$annotations = HelperBootstrap::transformCclToAnnotations($chunked);
		
		$hs = new HtmlStr($content);
		foreach ($annotations as $n=>$an){
			$annotations[$n]->text = $hs->getText($an->from, $an->to);			
		}
		
		$sql = "SELECT name" .
				" FROM annotation_types t" .
				" JOIN annotation_sets_corpora c ON (c.annotation_set_id=t.group_id)" .
				" WHERE c.corpus_id=?";
		
		$typesDB = $db->fetch_rows($sql, array($corpus_id));
		$typesArray = array();
		foreach ($typesDB as $t){
			array_push($typesArray, $t['name']);
		}
		
		foreach ($annotations as $an){
			if (in_array($an->type, $typesArray)){
				$sql = "SELECT `id` FROM `reports_annotations` " .
						"WHERE `report_id`=? AND `type`=? AND `from`=? AND `to`=?";
				$ids = $db->fetch_rows($sql, array($report_id, $an->type, $an->from, $an->to));
				if ( count($ids)==0 ){					
					$sql = "INSERT INTO `reports_annotations_optimized` " .
							"(`report_id`, `type_id`, `from`, `to`, `text`, `user_id`, `creation_time`, `stage`,`source`) VALUES " .
							'(?, (SELECT annotation_type_id FROM annotation_types WHERE name=?), ?, ?, ?, ?, now(), "new", "bootstrapping")';
					$db->execute($sql, array($report_id, $an->type, $an->from, $an->to, $an->text, $user_id));
					$count++;
				}
			}
		}
					
		return array("recognized"=>count($annotations), "added"=>$count);
	}
		
}

?>
