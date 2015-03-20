<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/**
 * Ujednolica strukturę raportu poprzez wydzielenie paragrafów i całych zdań.	 
 * Fragmenty tekstu rozdzielone podwójnym <br> lub <br/> zamienia na paragraf <p>..</p>.
 * 
 */
function reformat_content($content){
	//$content = html_entity_decode($content);
	$content = custom_html_entity_decode($content);
	$content = str_replace("<br>", "<br/>", $content);
	$content_br = explode("<br/>", $content);
	
	$content_chunks = array();
	$content_chunk_br = array();
	foreach ($content_br as $br){
		$br = trim($br);
		if ($br){
			$content_chunk_br[] = $br;
		}elseif (count($content_chunk_br)>0){
			$content_chunks[] = implode("\n<br/>\n", $content_chunk_br);
			$content_chunk_br = array();
		}
	}
	// Ostatni element
	if (count($content_chunk_br)>0){
		$content_chunks[] = implode("\n<br/>\n", $content_chunk_br);
		$content_chunk_br = array();
	}
	
	// Przeformatuj każdy paragraf
	foreach ($content_chunks as $id=>$chunk){
		// Usuń białe znaki
		$chunk = trim($chunk);
		// Usuń otwierające i zamykające tagi paragrafu z początku i końca tekstu.
		$chunk = ltrim($chunk, "<p>");
		$chunk = rtrim($chunk, "</p>");
		$chunk = "<p>$chunk</p>\n";
		$content_chunks[$id] = $chunk;
	}
	
	$content_formated = trim(implode("\n", $content_chunks));
	return $content_formated;
}

function normalize_content($content){
	$content = trim($content);
	$content = str_replace("\r", "\t", $content);
	$content = str_replace("<P>", "<p>", $content);
	$content = str_replace("</P>", "</p>", $content);
	$content = str_replace("<BR/>", "<br/>", $content);
	$content = str_replace("<br>", "<br/>", $content);
	$content = trim($content);
	$content = stripslashes($content);
	return $content;
}

/**
 * Zmiana dekodowania encji
 */
function custom_html_entity_decode($text){
	$text = str_replace("&apos;", "'", $text);
	$text = html_entity_decode($text, ENT_COMPAT, "UTF-8");
	return $text;
}
?>
