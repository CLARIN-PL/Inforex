<?
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/**
 * Testy spójności dla chunków w dokumencie 
 */

class CclIntegrity{
	
	/** 
	 * Zlicza ilość pustych chunków w dokumencie
	 * Input - treść dokumentu
	 * Return - liczba pustych chunków w dokumencie, lista elementów naruszających spójność 
	 */	
	static function checkChunks($content){
		$count_empty_chunks = 0;
		$empty_chunks_data = array();
		$chunk_list = explode('</chunk>', $content);
		$line_in_document = 1;
		foreach ($chunk_list as $chunk){
			$line_in_document += substr_count($chunk, "\n");
			$chunk_basic = $chunk;
			$chunk = str_replace("<"," <",$chunk);
			$chunk = str_replace(">","> ",$chunk);
			//$tmpStr = trim(preg_replace("/\s\s+/"," ",html_entity_decode(strip_tags($chunk),ENT_COMPAT, 'UTF-8')));
			$tmpStr = trim(preg_replace("/\s\s+/"," ",custom_html_entity_decode(strip_tags($chunk))));
			$tmpStr2 = preg_replace("/\n+|\r+|\s+/","",$tmpStr);
			if($tmpStr2 == ""){
				$count_empty_chunks++;
				//preg_match('/(?P<chunk><chunk.type(.*))/', $chunk, $matches);
				$empty_chunks_data[] = array('line' => $line_in_document);//htmlspecialchars($matches['chunk'], ENT_QUOTES);//$chunk, ENT_QUOTES);
			}
		}
		array_pop($empty_chunks_data);
		return array("count"=>($count_empty_chunks ? $count_empty_chunks-1 : $count_empty_chunks),"data"=>$empty_chunks_data);
	}	
	
	/** 
	 * Sprawdza strukturę dokumentu
	 * Input - treść dokumentu
	 * Return - tablica komunikatów o błędzie 
	 */	
	static function checkXSDContent($content){
		global $config;
		$content = str_replace("xml:base=\"text.xml\"", "", $content);
		$content = preg_replace('/xlink:href="[^"]*"/', "", $content);
		$c = new MyDOMDocument();
		$c->loadXML($content);
		$c->schemaValidate("{$config->path_engine}/resources/synat/premorph.xsd");
		
		return array("count"=> count($c->errors),"data"=>$c->errors);
	}
	
}

?>