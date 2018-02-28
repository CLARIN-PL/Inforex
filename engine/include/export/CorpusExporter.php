<?php

/**
 * Klasa służy do eksportu wybranych dokumentów i elementów korpusu do wybranego formatu.
 * 
 * Obecnie obsługiwany jest format CCL.
 * 
 * @author czuk
 *
 */
class CorpusExporter{

	/**
	 * Funckja parsuje opis ekstraktora danych
     *
     * Postać ekstraktora danych:
     * <code>
     *   EXTRACTOR    ::= FLAG:ELEMENTS
     *   FLAG         ::= FLAG_NAME=FLAG_VALUE
     *   FLAG_NAME    ::= String  // Pełna nazwa flagi
	 *   FLAG_VALUE   ::= Integer // Id wartości flagi, wartości: 0, 1, 2, 3, 4
     *   ELEMENTS     ::= ELEMENT || ELEMENT&ELEMENTS
     *   ELEMENT      ::= ELEMENT_NAME=ELEMENT_ARGS
     *   ELEMENT_NAME ::= Strng   // Nazwa elementu do ekstrakcji
     *   ELEMENT_ARGS ::= String  // Argumentu elementy zależne od typu elementu
     * </code>
     *
     * Przykłady:
     * <code>
     * // Ekstrakcja anotacji finalnych ze zbioru 1 i 20
     * names (global)=3:annotation_set_id=1&annotation_set_id=20
     *
     * // Ekstrakcja anotacji prywatnych ze zbioru 17 utworzony przez użytkownika o id 70
     * 1_key_dg=3:annotations=annotation_set_ids#17;user_ids#70
     * </code>
     *
	 * @param $description Opis ekstraktora danych.
	 * @return Ekstraktor w postaci listy parametrów i funkcji wybierającej dane dla dokumentu.
	 */
	function parse_extractor($description){
		$extractors = array();
		$parts = explode(":", $description);
		if ( count($parts) !== 2 ){
			throw new Exception("Niepoprawny opis ekstraktora " . $description);
		}
		$flag = $parts[0];
		$elements = $parts[1];
	
		$flag = split("=", $flag);
		if ( count($flag) !== 2 ){
			throw new Exception("Niepoprawny opis ekstraktora " . $description .": definicja flagi");
		}
	
		$flag_name = strtolower($flag[0]);
		$flag_ids = explode(",", $flag[1]);
	
		foreach ( explode("&", $elements) as $element ){
			$parts = explode("=", $element);
			$element_name = $parts[0];
			$extractor_name = $flag_name."=".implode(",", $flag_ids).":".$element;
			$extractor = array("flag_name"=>$flag_name, "flag_ids"=>$flag_ids, "name"=>$extractor_name);
	
			/* Esktraktor anotacji po identyfikatorze zbioru anotacji */
			if ( $element_name === "annotation_set_id" ){
				
				$extractor["params"] = explode(",", $parts[1]);
				$extractor["extractor"] = function($report_id, $params, &$elements){
					// $params -- set of annotation_set_id
					$annotations = DbAnnotation::getAnnotationsBySets(array($report_id), $params, null, 'agreement');
					if ( is_array($annotations) ) {
						$elements['annotations'] = array_merge($elements['annotations'], $annotations);
					}
				};
				$extractors[] = $extractor;
			}
			/* Esktraktor anotacji po identyfikatorze zbioru anotacji dodanych przez określonego użytkownika */
			elseif ( $element_name === "annotations" ){
				$params = array();
				$params['user_ids'] = null;
				$params['annotation_set_ids'] = null;
				$params['annotation_subset_ids'] = null;
				$params['stages'] = null;

				foreach ( explode(";", $parts[1]) as $part ){
					$name_value = explode("#", $part);
					$name = $name_value[0];
					$values = explode(",", $name_value[1]);
					if ( array_key_exists($name, $params) ){
						$params[$name] = $values;
					}
					else{
						$this->log_error(__FILE__, __LINE__, $report_id, "Nieznany parametr: " . $name);
					}
				}

				$extractor["params"] = $params;
				$extractor["extractor"] = function($report_id, $params, &$elements){
					// $params -- annotations_set_ids, $stages
					$annotations = DbAnnotation::getReportAnnotations($report_id,
							$params["user_ids"], $params["annotation_set_ids"], $params["annotation_subset_ids"], null, $params["stages"]);
					if ( is_array($annotations) ) {
						$elements['annotations'] = array_merge($elements['annotations'], $annotations);
					}
				};
				$extractors[] = $extractor;
			}
			/* Esktraktor anotacji po identyfikatorze podzbioru anotacji */
			elseif ( $element_name === "annotation_subset_id" ){
				$extractor["params"] = explode(",", $parts[1]);
				$extractor["extractor"] = function($report_id, $params, &$elements){
					// $params -- set of annotation_set_id
					$annotations = DbAnnotation::getAnnotationsBySubsets(array($report_id), $params);
					if ( is_array($annotations) ) {
						$elements['annotations'] = array_merge($elements['annotations'], $annotations);
					}
				};
				$extractors[] = $extractor;
			}
			/* Esktraktor relacji po identyfikatorze zbioru */
			elseif ( $element_name === "relation_set_id" ){
				$extractor["params"] = explode(",", $parts[1]);
				$extractor["extractor"] = function($report_id, $params, &$elements){
					// $params -- set of annotation_set_id
					$relations = DbCorpusRelation::getRelationsBySets2(array($report_id), $params);
					if ( is_array($relations) ) {
						$elements['relations'] = array_merge($elements['relations'], $relations);
					}
				};
				$extractors[] = $extractor;
			}
			/* Ekstraktor lematów dla zbioru anotacji*/
			elseif ( $element_name === "lemma_annotation_set_id" ){
				$extractor["params"] = explode(",", $parts[1]);
				$extractor["extractor"] = function($report_id, $params, &$elements){
					// $params -- set of annotation_set_id
					$lemmas = DbReportAnnotationLemma::getLemmasBySets(array($report_id), $params);
					if ( is_array($lemmas) ) {
						$elements['lemmas'] = array_merge($elements['lemmas'], $lemmas);
					}
				};
				$extractors[] = $extractor;
			}
			/* Ekstraktor lematów dla podzbioru anotacji*/
			elseif ( $element_name === "lemma_annotation_subset_id" ){
				$extractor["params"] = explode(",", $parts[1]);
				$extractor["extractor"] = function($report_id, $params, &$elements){
					// $params -- set of annotation_set_id
					$lemmas = DbReportAnnotationLemma::getLemmasBySubsets(array($report_id), $params);
					if ( is_array($lemmas) ) {
						$elements['lemmas'] = array_merge($elements['lemmas'], $lemmas);
					}
				};
				$extractors[] = $extractor;
			}
            /* Ekstraktor atrybutów dla zbioru anotacji*/
            elseif ( $element_name === "attributes_annotation_set_id" ){
                $extractor["params"] = explode(",", $parts[1]);
                $extractor["extractor"] = function($report_id, $params, &$elements){
                    // $params -- set of annotation_set_id
                    $attributes = DbReportAnnotationLemma::getAttributes(array($report_id), $params);
                    if ( is_array($attributes) ) {
                        $elements['attributes'] = array_merge($elements['attributes'], $attributes);
                    }
                };
                $extractors[] = $extractor;
            }
            /* Ekstraktor lematów dla podzbioru anotacji*/
            elseif ( $element_name === "attributes_annotation_subset_id" ){
                $extractor["params"] = explode(",", $parts[1]);
                $extractor["extractor"] = function($report_id, $params, &$elements){
                    // $params -- set of annotation_set_id
                    $attributes = DbReportAnnotationLemma::getAttributes(array($report_id), null, null, $params);
                    if ( is_array($attributes) ) {
                        $elements['attributes'] = array_merge($elements['attributes'], $attributes);
                    }
                };
                $extractors[] = $extractor;
            }
			else{
				throw new Exception("Niepoprawny opis ekstraktora " . $description . ": nieznany ektraktor " . $element_name);
			}
		}
		return $extractors;
	}

	/**
	 * Parsuje opis indeksu do wygenerowania.
	 * @param $description Opis indeksu
	 * @return ...
	 */
	function parse_list($description){
		$cols = explode(":", $description);
		if ( count($cols) != 2 ){
			throw new Exception("Niepoprawny opis listy: $description");
		}
		$list_name = $cols[0];
		$list_flags = array();
		foreach ( explode("&", $cols[1]) as $flags ){
			$fc = explode("=", $flags);
			if ( count($fc) != 2 ){
				throw new Exception("Niepoprawny warunek dla flagi '$flags' w $description");
			}
			$flag_name = strtolower($fc[0]);
			$flag_ids = explode(",", $fc[1]);
			$list_flags[] = array("flag_name"=>$flag_name, "flag_ids"=>$flag_ids);
		}
		return array("name"=>$list_name, "flags"=>$list_flags, "report_ids" => array());
	}

	/**
	 * Loguje błąd na konsolę
	 */
	function log_error($file_name, $line_no, $report_id, $message){
		$file_name = basename($file_name);
		echo "[$file_name:$line_no] Błąd dla dokumentu id=$report_id: $message\n";
	}

	/**
	 * Eksport dokumentu o wskazanym identyfikatorze
	 * @param $report_id Identyfikator dokumentu do eksportu
	 * @param $extractors Lista extraktorów danych
	 * @param $disamb_only Jeżeli true, to eksportowany są tylko tagi oznaczone jako disamb
	 * @param $extractors_stats Tablica ze statystykami ekstraktorów
	 */
	function export_document($report_id, &$extractors, $disamb_only, &$extractor_stats, &$lists, $output_folder, $subcorpora){
		$flags = DbReportFlag::getReportFlags($report_id);
		$elements = array("annotations"=>array(), "relations"=>array(), "lemmas"=>array(), "attributes"=>array());
	
		// Wykonaj esktraktor w zależności od ustalonej flagi
		foreach ( $extractors as $extractor ){
			$func = $extractor["extractor"];
			$params = $extractor["params"];
			$flag_name = $extractor["flag_name"];
			$flag_ids = $extractor["flag_ids"];
			if ( isset($flags[$flag_name]) && in_array($flags[$flag_name], $flag_ids) ){
				$extractor_elements = array();
				foreach (array_keys($elements) as $key){
					$extractor_elements[$key] = array();
				}
				$func($report_id, $params, $extractor_elements);
				foreach (array_keys($extractor_elements) as $key){
					$elements[$key] = array_merge($elements[$key], $extractor_elements[$key]);
				}
				// Zapisz statystyki
				$name = $extractor["name"];
				if ( !isset($extractor_stats[$name]) ){
					$extractor_stats[$name] = array();
				}
				foreach ( $extractor_elements as $type=>$items ){
					if ( !isset($extractor_stats[$name][$type]) ){
						$extractor_stats[$name][$type] = count($items);
					}
					else{
						$extractor_stats[$name][$type] += count($items);
					}
				}
			}
		}

		$tokens = DbToken::getTokenByReportId($report_id);
		$tags = DbTag::getTagsByReportId($report_id);

		$tags_by_tokens = array();
		foreach ($tags as $tag){
			$token_id = $tag['token_id'];
			if ( !isset($tags_by_tokens[$token_id]) ){
				$tags_by_tokens[$token_id] = array();
			}
			if ( $disamb_only == false || $tag['disamb'] ){
				$tags_by_tokens[$token_id][] = $tag;
			}
		}

		$report = DbReport::getReportById($report_id);
		try{
			$ccl = CclFactory::createFromReportAndTokens($report, $tokens, $tags_by_tokens);
		}
		catch(Exception $ex){
			$this->log_error(__FILE__, __LINE__, $report_id, "Problem z utworzeniem ccl: " . $ex->getMessage());
			return;
		}
		$annotations = array();
		$relations = array();
		$lemmas = array();
        $attributes = array();
		if ( isset($elements["annotations"]) && count($elements["annotations"]) ){
			$annotations = $elements["annotations"];
		}
		if ( isset($elements["relations"]) && count($elements["relations"]) ){
			$relations = $elements["relations"];
		}
		if ( isset($elements["lemmas"]) && count($elements["lemmas"]) ){
			$lemmas = $elements["lemmas"];
		}

        if ( isset($elements["attributes"]) && count($elements["attributes"]) ){
            $attributes = $elements["attributes"];
        }
	
		/* Usunięcie zduplikowanych anotacji */
		$annotations_by_id = array();
		foreach ($annotations as $an){
			$anid = intval($an['id']);
			if ( $anid > 0 ){
				$annotations_by_id[$anid] = $an;
			}
			else{
				$this->log_error(__FILE__, __LINE__, $report_id, "brak identyfikatora anotacji");
			}
		}
		$annotations = array_values($annotations_by_id);
		/* Sprawdzenie, anotacji źródłowych i docelowych dla relacji */
		foreach ( $relations as $rel ){
			$source_id = $rel["source_id"];
			$target_id = $rel["target_id"];
			if ( !isset($annotations_by_id[$source_id]) ){
				$this->log_error(__FILE__, __LINE__, $report_id, "brak anotacji źródłowej o identyfikatorze $source_id ({$rel["name"]}) -- brakuje warsty anotacji?");
			}
			if ( !isset($annotations_by_id[$target_id]) ){
                $this->log_error(__FILE__, __LINE__, $report_id, "brak anotacji źródłowej o identyfikatorze $target_id ({$rel["name"]}) -- brakuje warsty anotacji?");
			}
		}

		/* Sprawdzenie lematów */
		foreach ($lemmas as $an){
			$anid = intval($an['id']);
			if ( !isset($annotations_by_id[$anid]) ){
				//print_r($an);
                $this->log_error(__FILE__, __LINE__, $report_id, "brak anotacji $anid dla lematu ({$an["name"]}) -- brakuje warsty anotacji?");
			}
		}

		/* Wygeneruj xml i rel.xml */
		CclFactory::setAnnotationsAndRelations($ccl, $annotations, $relations);
		CclFactory::setAnnotationLemmas($ccl, $lemmas);
        CclFactory::setAnnotationProperties($ccl, $attributes);
		CclWriter::write($ccl, $output_folder . "/" . $ccl->getFileName() . ".xml", CclWriter::$CCL);
		CclWriter::write($ccl, $output_folder . "/" . $ccl->getFileName() . ".rel.xml", CclWriter::$REL);

		/* Eksport metadanych */
		$report = DbReport::getReportById($report_id);
		$ext = DbReport::getReportExtById($report_id);

		$basic = array("id", "date", "title", "source", "author", "tokenization", "subcorpus");
		$lines = array();
		$lines[] = "[document]";
		$report["subcorpus"] = $subcorpora[$report['subcorpus_id']];

		foreach ($basic as $name){
			$lines[] = sprintf("%s = %s", $name, $report[$name]);
		}
		if ( count($ext) > 0 ){
			$lines[] = "";
			$lines[] = "[metadata]";
			foreach ($ext as $key=>$val){
				if ($key != "id"){
					$key = preg_replace("/[^\p{L}|\p{N}]+/u", "_", $key);
					$lines[] = sprintf("%s = %s", $key, $val);
				}
			}
		}
		file_put_contents($output_folder . "/" . $ccl->getFileName() . ".ini", implode("\n", $lines));

		/* Przypisanie dokumentu do list */
		foreach ( $lists as $ix=>$list){
			foreach ( $list['flags'] as $flag){
				$flag_name = $flag["flag_name"];
				$flag_ids = $flag["flag_ids"];
				if ( isset($flags[$flag_name]) && in_array($flags[$flag_name], $flag_ids) ){
					$lists[$ix]["document_names"][$ccl->getFileName() . ".xml"] = 1;
				}
			}
		}

	}

	/**
	 * Wykonuje eksport korpusu zgodnie z określonymi parametrami (selektory, ekstraktory i indeksy).
	 * @param $output Ścieżka do katalogu wyjściowego
	 * @param $selectors Lista opisu selektorów
	 * @param $extractors Lista opisu ekstraktorów danych
	 * @param $lists Lista opisu indeksów plików
	 */
	function exportToCcl($output_folder, $selectors_description, $extractors_description, $lists_description, $export_id = null){

		/* Przygotuje katalog docelowy */
		if ( !file_exists("$output_folder/documents") ){
			mkdir("$output_folder/documents", 0777, true);
		}


		/* Przygotuj listę podkorpusów w postaci tablicy id=>nazwa*/
		$subcorpora_assoc = DbCorpus::getSubcorpora();
		$subcorpora = array();
		foreach ( $subcorpora_assoc as $sub ){
			$subcorpora[$sub['subcorpus_id']] = $sub['name'];
		}

		$extractors = array();
		foreach ( $extractors_description as $extractor ){
			$extractors = array_merge($extractors, $this->parse_extractor($extractor));
		}

		$lists = array();
		foreach ( $lists_description as $list){
			$lists[] = $this->parse_list($list);
		}

		$document_ids = array();
		foreach ( $selectors_description as $selector ){
			foreach ( DbReport::getReportsBySelector($selector, "id") as $d ){
				$document_ids[$d['id']] = 1;
			}
		}

		$document_ids = array_keys($document_ids);
		echo "Liczba dokumentów do eksportu: " . count($document_ids) . "\n";

		$extractor_stats = array();
	    $number_of_docs = count($document_ids);
        $current_doc = 0;
        $progress = 0;

		foreach ($document_ids as $id){
            $current_doc += 1;
            $this->export_document($id, $extractors, true, $extractor_stats, $lists, "$output_folder/documents", $subcorpora);
            $percent_done = floor(100 * $current_doc / $number_of_docs);
            if($percent_done > $progress){
                $progress = $percent_done;
                DbExport::updateExportProgress($export_id, $progress);
                echo intval($progress) . "%" . "\n";
            }
		}
		foreach ($lists as $list){
			echo sprintf("%4d %s\n", count(array_keys($list["document_names"])), $list["name"]);
			$lines = array();
			foreach ( array_keys($list["document_names"]) as $document_name ){
				$lines[] = "./documents/" . $document_name;
			}
			sort($lines);
			file_put_contents("$output_folder/{$list['name']}", implode("\n", $lines));
		}

		$types = array();
		$max_len_name = 0;
		foreach ($extractor_stats as $name=>$items){
			$max_len_name = max(strlen($name), $max_len_name);
			foreach (array_keys($items) as $type){
				$types[$type] = 1;
			}
		}
        echo "\n";

        $stats_str = str_repeat(" ", $max_len_name);
        foreach ( array_keys($types) as $type ){
            $stats_str .= " $type";
        }
        $stats_str .= "\n";
        foreach ($extractor_stats as $name=>$items){
            $stats_str.= sprintf("%-".$max_len_name."s", $name);
            foreach ( array_keys($types) as $type ){
                $val = "-";
                if ( isset($items[$type]) && intval($items[$type]) > 0 ){
                    $val = "" . $items[$type];
                }
                $stats_str.= sprintf(" %".strlen($type)."s", $val);
            }
            $stats_str .= "\n";
        }

        if(!empty($extractor_stats)){
            /* Utworzenie pliku */
            if( !file_exists("$output_folder/statistics.txt")){
                $stats_file = fopen("$output_folder/statistics.txt", "w");
            }
            fwrite($stats_file, $stats_str);
            DbExport::saveStatistics($export_id, $extractor_stats);
        }

        echo $stats_str;
	}		
}

?>
