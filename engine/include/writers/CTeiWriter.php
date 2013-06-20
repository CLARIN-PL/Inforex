<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
 class TeiWriter{
 	
 	// --- Evaluate	
 	public static function teiElements2xml($teiElements, $xmlStart = ""){
 		$xml = $xmlStart;
 		$xml .= "<{$teiElements->getName()}";
 		
 		if($teiElements->getName() == "!--"){
 			$xml .= " {$teiElements->getTeiBody()} -->\n";
 			return $xml;
 		}
 		
 		foreach($teiElements->getAttributes() as $name=>$value){
 			$xml .= " {$name}={$value}";
 		}
 		if(!$teiElements->countTeiBody() && !$teiElements->countTeiElements()){
 			$xml .= "/>\n";
 			return $xml;
 		}
 		
 		if($teiElements->countTeiBody()){
 			$xml .= ">{$teiElements->getTeiBody()}</{$teiElements->getName()}>\n";	
 		}
 		elseif($teiElements->countTeiElements()){
 			$xml .= ">\n";
 			foreach($teiElements->getTeiElements() as $tei)
 				$xml .= TeiWriter::teiElements2xml($tei, $xmlStart." ");
 			$xml .= "{$xmlStart}</{$teiElements->getName()}>\n";	
 		}
 		
 		return $xml;
 	}
 	
 	private static function annotationsBuffer2TeiElements($ann_buf, $p_num, $s_num){
 		$tei_s = new TeiElements("s");
 		$tei_s->addAttribute("xml:id", "\"p-{$p_num}.{$s_num}-s\"");
 		$tei_s->addAttribute("corresp", "\"ann_morphosyntax.xml#p-{$p_num}.{$s_num}-s\"");
		if(count($ann_buf)){
			$ann_num = 1;
			foreach($ann_buf as $type=>$elements){
				foreach($elements as $annotations){
					$tei_seg = new TeiElements("seg");
					$tei_seg->addAttribute("xml:id", "\"p-{$p_num}.{$s_num}-s_n{$ann_num}\"");					
					
					$tei_fs = new TeiElements("fs");
					$tei_fs->addAttribute("type", "\"named\"");					
					
					$tei_f = new TeiElements("f");
					$tei_f->addAttribute("name", "\"type\"");
							
					$tei_symbol = new TeiElements("symbol");
					$tei_symbol->addAttribute("value", "\"{$type}\"");							
							
					$tei_f->addTeiElements($tei_symbol);
					$tei_fs->addTeiElements($tei_f);
							
					$tei_f = new TeiElements("f");
					$tei_f->addAttribute("name", "\"orth\"");
							
					$tei_string = new TeiElements("string");
					$tei_string->setTeiBody(implode(" ", $annotations['orth']));
							
					$tei_f->addTeiElements($tei_string);
					$tei_fs->addTeiElements($tei_f);
							
					$tei_seg->addTeiElements($tei_fs);
					foreach($annotations['ptr'] as $ptr){
						$tei_ptr = new TeiElements("ptr");
						$tei_ptr->addAttribute("target", "\"ann_morphosyntax.xml#morph_{$ptr}\"");
						$tei_seg->addTeiElements($tei_ptr);
					}

					$tei_s->addTeiElements($tei_seg);
					$ann_num++;
				}								
			}				
		} 
		return $tei_s;
 	}
 	
 	/**
 	 * @param $ccl CclDocument — ccl document to write,
 	 * @param $folder String   — root folder where the subfolder for document will be created,
 	 * @param $document_name String — name of subfolder in $folder,
 	 * @param $tagger_name String — name of tagger used to tokenize the document.
 	 */
 	public static function ccl2teiWrite($ccl, $folder, $document_name, $tagger_name){
		$subfolder = $folder . "/" . $document_name;		
		
		$errors = array();
		$chunks = $ccl->getChunks();
		$names_count = 0;
		
		$text_structure = new TeiDocument($document_name, "text_structure.xml");
		$ann_segmentation = new TeiDocument($document_name, "ann_segmentation.xml");
		$ann_named = new TeiDocument($document_name, "ann_named.xml");
		$ann_morphosyntax = new TeiDocument($document_name, "ann_morphosyntax.xml");
				
		foreach ($chunks as &$chunk){
			$div_num = $chunk->getId() + 1;
			
			$text_structure_div = new TeiElements("div");
			$text_structure_div->addAttribute("type", "\"article\"");
			$text_structure_div->addAttribute("xml:id", "\"div-{$div_num}\"");
			$sentences = $chunk->getSentences();
			
			foreach ($sentences as &$sentence){
				$p_num = str_replace("s", "", $sentence->getId());
				$s_num = 1;
				$segm_num = 1;
				$dot_flag = false;
				$tokens = $sentence->getTokens();
				$ann_buf = array(); 
							
				$text_structure_p = new TeiElements("p");
				$text_structure_p->addAttribute("xml:id", "\"p-{$p_num}\"");
				$text_structure_p_orths = "";
				$ann_segmentation_p = new TeiElements("p");
				$ann_segmentation_p->addAttribute("corresp", "\"text_structure.xml#p-{$p_num}\"");
				$ann_segmentation_p->addAttribute("xml:id", "\"segm_p-{$p_num}\"");
							
				$ann_morphosyntax_p = new TeiElements("p");
				$ann_morphosyntax_p->addAttribute("xml:id", "\"p-{$p_num}\"");
				
				$ann_named_p = new TeiElements("p");
				$ann_named_p->addAttribute("xml:id", "\"p-{$p_num}\"");
				$ann_named_p->addAttribute("corresp", "\"ann_morphosyntax.xml#p-{$p_num}\"");

				$ann_segmentation_s = new TeiElements("s");
				$ann_segmentation_s->addAttribute("xml:id", "\"segm_p-{$p_num}.{$s_num}-s\"");
				
				$ann_morphosyntax_s = new TeiElements("s");
				$ann_morphosyntax_s->addAttribute("corresp", "\"ann_segmentation.xml#segm_p-{$p_num}.{$s_num}-s\"");
				$ann_morphosyntax_s->addAttribute("xml:id", "\"p-{$p_num}.{$s_num}-s\"");
				
				$n_space = false;
				$prev_n_space = false;
				foreach ($tokens as &$token){
					$n_space = $token->getNs();
					$text_structure_p_orths .= ($n_space ? "" : " ") . $token->getOrth();
					
					if($dot_flag){
						$ann_named_p->addTeiElements(TeiWriter::annotationsBuffer2TeiElements($ann_buf, $p_num, $s_num));
					
						$ann_buf = array();					
						$s_num++;
						
						$ann_segmentation_p->addTeiElements($ann_segmentation_s);
						$ann_segmentation_s = new TeiElements("s");
						$ann_segmentation_s->addAttribute("xml:id", "\"segm_p-{$p_num}.{$s_num}-s\"");
								
						$ann_morphosyntax_p->addTeiElements($ann_morphosyntax_s);
						$ann_morphosyntax_s = new TeiElements("s");
						$ann_morphosyntax_s->addAttribute("corresp", "\"ann_segmentation.xml#segm_p-{$p_num}.{$s_num}-s\"");
						$ann_morphosyntax_s->addAttribute("xml:id", "\"p-{$p_num}.{$s_num}-s\"");
						
						$dot_flag = false;
					}
					$ann_segmentation_comment = new TeiElements("!--");
					$ann_segmentation_comment->setTeiBody($token->getOrth());
					$ann_segmentation_s->addTeiElements($ann_segmentation_comment);
					$token_length = $token->getTo() - $token->getFrom() + 1;
					$ann_segmentation_seg = new TeiElements("seg");
					$ann_segmentation_seg->addAttribute("corresp", "\"text_structure.xml#string-range(p-{$p_num},{$token->getFrom()},{$token_length})\"");
					
					if( $prev_n_space ){
						$ann_segmentation_seg->addAttribute("nkjp:nps", "\"true\"");
						if($token->getOrth() == '.')
							$dot_flag = true;
					}
		
					$ann_segmentation_seg->addAttribute("xml:id", "\"segm_p-{$p_num}.{$segm_num}-seg\"");
					$ann_segmentation_s->addTeiElements($ann_segmentation_seg);

					$lexemes = $token->getLexemes();
					$channels = $token->getChannels();
					$lex_array = array();
					$msd_dict = array();
					$disamb = NULL;
			
					foreach ($lexemes as &$lexeme){
						$ctag_all = split(':',$lexeme->getCtag(), 2);
						$ctag = $ctag_all[0];
						if(count($ctag_all) == 1)
							$msd =  "";
						else 
							$msd =  $ctag_all[1];
							
						if($lexeme->getDisamb()){
							$disamb = $ctag.":".$msd;
						}
						
						$index = array_search(htmlspecialchars($lexeme->getBase()) . '_' . $ctag, $msd_dict);
						
						if( $index !== false ){
							array_push($lex_array[$index]['msd'], $msd);
						}
						else{
							array_push($lex_array, array('base' => htmlspecialchars($lexeme->getBase()), 'ctag' => $ctag, 'msd' => array($msd)));
							array_push($msd_dict, htmlspecialchars($lexeme->getBase()) . '_' . $ctag);
						}
					}
					
					foreach ($channels as $type=>$number){
						if($number){
							if(array_key_exists($type, $ann_buf)){
								if(array_key_exists($number, $ann_buf[$type])){
									array_push($ann_buf[$type][$number]['orth'], htmlspecialchars($token->getOrth()));
									array_push($ann_buf[$type][$number]['ptr'], "p-{$p_num}.{$segm_num}-seg");
								}
								else{
									$ann_buf[$type][$number] = array('orth' => array(htmlspecialchars($token->getOrth())), 'ptr' => array("p-{$p_num}.{$segm_num}-seg"));
								}
							}
							else{
								$ann_buf[$type] = array();
								$ann_buf[$type][$number] = array('orth' => array(htmlspecialchars($token->getOrth())), 'ptr' => array("p-{$p_num}.{$segm_num}-seg"));
							}
							$names_count++;
						}
					}
			
					if($disamb){
						$token_orth = str_replace("--", "- -", $token->getOrth());
						
						$ann_morphosyntax_seg = new TeiElements("seg");
						$ann_morphosyntax_seg->addAttribute("corresp", "\"ann_segmentation.xml#segm_p-{$p_num}.{$segm_num}-seg\"");
						$ann_morphosyntax_seg->addAttribute("xml:id", "\"morph_p-{$p_num}.{$segm_num}-seg\"");
						
						$ann_morphosyntax_fs = new TeiElements("fs");
						$ann_morphosyntax_fs->addAttribute("type", "\"morph\"");
						$ann_morphosyntax_f = new TeiElements("f");
						$ann_morphosyntax_f->addAttribute("name", "\"orth\"");
						
						$ann_morphosyntax_string = new TeiElements("string");
						$ann_morphosyntax_string->setTeiBody(htmlspecialchars($token->getOrth()));
						
						$ann_morphosyntax_f->addTeiElements($ann_morphosyntax_string);
						$ann_morphosyntax_fs->addTeiElements($ann_morphosyntax_f);
						
						$ann_morphosyntax_comment = new TeiElements("!--");
						$ann_morphosyntax_comment->setTeiBody("{$token_orth} [{$token->getFrom()},{$token_length}]");
						$ann_morphosyntax_fs->addTeiElements($ann_morphosyntax_comment);
						
						$ann_morphosyntax_f_interps = new TeiElements("f");
						$ann_morphosyntax_f_interps->addAttribute("name", "\"interps\"");
						
						if(count($lex_array)>1)
							$ann_morphosyntax_f_vAlt = new TeiElements("vAlt");
						
						$msd_num = 0;
						$disamb_array = array();
			
						foreach($lex_array as $key=>$lex){						
							$ann_morphosyntax_fs_lex = new TeiElements("fs");
							$ann_morphosyntax_fs_lex->addAttribute("type", "\"lex\"");
							$ann_morphosyntax_fs_lex->addAttribute("xml:id", "\"morph_p-{$p_num}.{$segm_num}-seg_{$key}-lex\"");
							
							$ann_morphosyntax_f_base = new TeiElements("f");
							$ann_morphosyntax_f_base->addAttribute("name", "\"base\"");
							
							$ann_morphosyntax_lex_string = new TeiElements("string");
							$ann_morphosyntax_lex_string->setTeiBody($lex['base']);
							
							$ann_morphosyntax_f_base->addTeiElements($ann_morphosyntax_lex_string);
							$ann_morphosyntax_fs_lex->addTeiElements($ann_morphosyntax_f_base);
							
							$ann_morphosyntax_f_ctag = new TeiElements("f");
							$ann_morphosyntax_f_ctag->addAttribute("name", "\"ctag\"");
							
							$ann_morphosyntax_lex_symbol = new TeiElements("symbol");
							$ann_morphosyntax_lex_symbol->addAttribute("value", "\"{$lex['ctag']}\"");
							
							$ann_morphosyntax_f_ctag->addTeiElements($ann_morphosyntax_lex_symbol);
							$ann_morphosyntax_fs_lex->addTeiElements($ann_morphosyntax_f_ctag);
							
							$ann_morphosyntax_f_msd = new TeiElements("f");
							$ann_morphosyntax_f_msd->addAttribute("name", "\"msd\"");
							
							if(count($lex['msd']) > 1)
								$ann_morphosyntax_f_msd_vAlt = new TeiElements("vAlt");
									
							foreach($lex['msd'] as $msd){
								$ann_morphosyntax_msd_symbol = new TeiElements("symbol");
								$ann_morphosyntax_msd_symbol->addAttribute("value", "\"{$msd}\"");
								$ann_morphosyntax_msd_symbol->addAttribute("xml:id", "\"morph_p-{$p_num}.{$segm_num}-seg_{$msd_num}-msd\"");
								if(count($lex['msd']) > 1)
									$ann_morphosyntax_f_msd_vAlt->addTeiElements($ann_morphosyntax_msd_symbol);
								else
									$ann_morphosyntax_f_msd->addTeiElements($ann_morphosyntax_msd_symbol);
								
								if($lex['ctag'].":".$msd === $disamb){
									$disamb_array['string'] = $lex['base'].":".$lex['ctag']. ($msd != "" ? ":" : "") .$msd ;
									$disamb_array['fVal'] = "\"#morph_p-{$p_num}.{$segm_num}-seg_{$msd_num}-msd\"";
								}
								$msd_num++;
							}								
										
							if(count($lex['msd']) > 1)
								$ann_morphosyntax_f_msd->addTeiElements($ann_morphosyntax_f_msd_vAlt);
								
							$ann_morphosyntax_fs_lex->addTeiElements($ann_morphosyntax_f_msd);
							
							if(count($lex_array)>1)
								$ann_morphosyntax_f_vAlt->addTeiElements($ann_morphosyntax_fs_lex);								
							else
								$ann_morphosyntax_f_interps->addTeiElements($ann_morphosyntax_fs_lex);								
							
						}
						if(count($lex_array)>1)
							$ann_morphosyntax_f_interps->addTeiElements($ann_morphosyntax_f_vAlt);
											
						$ann_morphosyntax_fs->addTeiElements($ann_morphosyntax_f_interps);
						
						if($disamb_array){
							$ann_morphosyntax_f_disamb = new TeiElements("f");
							$ann_morphosyntax_f_disamb->addAttribute("name", "\"disamb\"");
							
							$ann_morphosyntax_fs_disamb = new TeiElements("fs");
							$ann_morphosyntax_fs_disamb->addAttribute("feats", "\"#$tagger_name\"");
							$ann_morphosyntax_fs_disamb->addAttribute("type", "\"tool_report\"");
							
							$ann_morphosyntax_f_fval = new TeiElements("f");
							$ann_morphosyntax_f_fval->addAttribute("fVal", "{$disamb_array['fVal']}");
							$ann_morphosyntax_f_fval->addAttribute("name", "\"choice\"");
							
							$ann_morphosyntax_fs_disamb->addTeiElements($ann_morphosyntax_f_fval);
							
							$ann_morphosyntax_f_interpretation = new TeiElements("f");
							$ann_morphosyntax_f_interpretation->addAttribute("name", "\"interpretation\"");
							
							$ann_morphosyntax_disamb_string = new TeiElements("string");
							$ann_morphosyntax_disamb_string->setTeiBody($disamb_array['string']);
							
							$ann_morphosyntax_f_interpretation->addTeiElements($ann_morphosyntax_disamb_string);
							$ann_morphosyntax_fs_disamb->addTeiElements($ann_morphosyntax_f_interpretation);
							
							$ann_morphosyntax_f_disamb->addTeiElements($ann_morphosyntax_fs_disamb);
							
							$ann_morphosyntax_fs->addTeiElements($ann_morphosyntax_f_disamb);
						}
						else{
							print("Brak elementu disamb (". $disamb .")\n");
							$errors["e1"] = "Brak elementu disamb";
						}
						
						$ann_morphosyntax_seg->addTeiElements($ann_morphosyntax_fs);
						$ann_morphosyntax_s->addTeiElements($ann_morphosyntax_seg);
					}
					else
						$errors["e2"] = "Brak elementu disamb";
					
					$segm_num++;
					$prev_n_space = $n_space;
				}
				$ann_morphosyntax_p->addTeiElements($ann_morphosyntax_s);
				$ann_morphosyntax->addTeiElements($ann_morphosyntax_p);
				$ann_segmentation_p->addTeiElements($ann_segmentation_s);
				$ann_segmentation->addTeiElements($ann_segmentation_p);
				$ann_named_p->addTeiElements(TeiWriter::annotationsBuffer2TeiElements($ann_buf, $p_num, $s_num));
				$ann_named->addTeiElements($ann_named_p);
				$text_structure_p->setTeiBody(trim($text_structure_p_orths));
				$text_structure_div->addTeiElements($text_structure_p);
			}
			$text_structure->addTeiElements($text_structure_div);
			
		}
		if(!count($errors)){
			if (!is_dir($subfolder)) mkdir($subfolder, 0777);
			TeiWriter::writeTextStructure($text_structure,$subfolder);
			TeiWriter::writeAnnSegmentation($ann_segmentation,$subfolder);
			if($names_count)
				TeiWriter::writeAnnNamed($ann_named,$subfolder);
			TeiWriter::writeAnnMorphosyntax($ann_morphosyntax,$subfolder);
			print "{$document_name}: OK\n";
		}
		else
			foreach($errors as $error_key=>$error)
				print "{$document_name}: SKIPPED -> error {$error_key}: {$error}\n";
				
	}
 	
 	
 	// --- Write
 	private static function writeFile($file, $xml){
 		$handle = fopen($file, "w");
		fwrite($handle, $xml);
		fclose($handle);
 	}
 	 	
 	public static function writeTextStructure($tei, $document_name){
 		$filename = $document_name ."/". $tei->getTeiName();
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
				"<teiCorpus xmlns:xi=\"http://www.w3.org/2001/XInclude\" xmlns=\"http://www.tei-c.org/ns/1.0\">\n" .
				" <TEI>\n" .
				"  <text>\n" .
				"   <front>\n" .
				"    <docTitle>\n" .
				"     <titlePart type=\"main\" xml:id=\"titlePart-1\">{$tei->getDocTitle()}</titlePart>\n" .
				"    </docTitle>\n" .
				"   </front>\n" .
				"   <body>\n";
		foreach($tei->getTeiElements() as $tei)
			$xml .= TeiWriter::teiElements2xml($tei, "    ");
		$xml .= "   </body>\n" .
				"  </text>\n" .
				" </TEI>\n" .
				"</teiCorpus>";
		TeiWriter::writeFile($filename, $xml);
 	}
 	
 	public static function writeAnnSegmentation($tei, $document_name){
 		$filename = $document_name ."/". $tei->getTeiName();
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
				"<teiCorpus xmlns=\"http://www.tei-c.org/ns/1.0\" xmlns:nkjp=\"http://www.nkjp.pl/ns/1.0\" xmlns:xi=\"http://www.w3.org/2001/XInclude\">\n".
				" <TEI>\n".
				"  <text xml:lang=\"pl\" xml:id=\"segm_text\">\n".
				"   <body xml:id=\"segm_body\">\n";		
		foreach($tei->getTeiElements() as $tei)
			$xml .= TeiWriter::teiElements2xml($tei, "    ");
		$xml .= "   </body>\n" .
				"  </text>\n" .
				" </TEI>\n" .
				"</teiCorpus>";
		TeiWriter::writeFile($filename, $xml);
 	}
 	
 	public static function writeAnnNamed($tei, $document_name){
 		$filename = $document_name ."/". $tei->getTeiName();
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n" .
				"<teiCorpus xmlns:xi=\"http://www.w3.org/2001/XInclude\" xmlns=\"http://www.tei-c.org/ns/1.0\">\n" .
				" <TEI>\n" .
				"  <text xml:lang=\"pl\">\n" .
				"   <body>\n";								
		foreach($tei->getTeiElements() as $tei)
			$xml .= TeiWriter::teiElements2xml($tei, "    ");
		$xml .= "   </body>\n" .
				"  </text>\n" .
				" </TEI>\n" .
				"</teiCorpus>";
		TeiWriter::writeFile($filename, $xml);
 	} 	
 	 
 	public static function writeAnnMorphosyntax($tei, $document_name){
 		$filename = $document_name ."/". $tei->getTeiName();
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".
				"<teiCorpus xmlns=\"http://www.tei-c.org/ns/1.0\" xmlns:nkjp=\"http://www.nkjp.pl/ns/1.0\" xmlns:xi=\"http://www.w3.org/2001/XInclude\">\n".
				" <TEI>\n".
				"  <text>\n".
				"   <body>\n";								
		foreach($tei->getTeiElements() as $tei)
			$xml .= TeiWriter::teiElements2xml($tei, "    ");
		$xml .= "   </body>\n" .
				"  </text>\n" .
				" </TEI>\n" .
				"</teiCorpus>";
		TeiWriter::writeFile($filename, $xml);
 	}	
} 
?>
