/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

/**
 * Konwertuje HTML do czystego tekstu. Dokonuję następujących operacji:
 * 1. Wycina tagi HTML-owe (SMALL, SPAN, BR i P).
 * 2. Zamienia &amp; na &
 * @param content
 * @return
 */
function html2txt(content){
	content_no_html = content;
	do{
		content_before = content_no_html;
		
		content_no_html = content_no_html.replace(/<span id="an[0-9]+" class="[^>]*" title="an#[0-9]+:[a-z_]+">([^]*?)<\/span>/gi, "$1");
		content_no_html = content_no_html.replace(/<span id="an[0-9]+" title="an#[0-9]+:[a-z_]+" class="[^>]*">([^]*?)<\/span>/gi, "$1");
		content_no_html = content_no_html.replace(/<span title="an#[0-9]+:[a-z_]+" class="[^>]*" id="an[0-9]+">([^]*?)<\/span>/gi, "$1");
		content_no_html = content_no_html.replace(/<span title="an#[0-9]+:[a-z_]+" id="an[0-9]+" class="[^>]*">([^]*?)<\/span>/gi, "$1");
		content_no_html = content_no_html.replace(/<span class="[^>]*" id="an[0-9]+" title="an#[0-9]+:[a-z_]+">([^]*?)<\/span>/gi, "$1");
		content_no_html = content_no_html.replace(/<span class="[^>]*" title="an#[0-9]+:[a-z_]+" id="an[0-9]+">([^]*?)<\/span>/gi, "$1");
		content_no_html = content_no_html.replace(/<chunk type="[^>]*">([^]*?)<\/chunk>/gi, "$1");
		content_no_html = content_no_html.replace(/<chunk [^>]*>([^]*?)<\/chunk>/gi, "$1");
		content_no_html = content_no_html.replace(/<cesana [^>]*>([^]*?)<\/cesana>/gi, "$1");
		content_no_html = content_no_html.replace(/<chunklist [^>]*>([^]*?)<\/chunklist>/gi, "$1");
		content_no_html = content_no_html.replace(/<br(\/)?>/gi, "");
		content_no_html = content_no_html.replace(/<(\/)?p>/gi, "");
		content_no_html = content_no_html.replace(/<(\/)?p>/gi, "");
		content_no_html = content_no_html.replace(/<p class="[^>]*">/gi, "");
		content_no_html = content_no_html.replace(/<div>([^]*?)<\/div>/gi, "$1");
		content_no_html = content_no_html.replace(/<h1>([^]*?)<\/h1>/gi, "$1");
		content_no_html = content_no_html.replace(/<p>([^]*?)<\/p>/gi, "$1");
		content_no_html = content_no_html.replace(/<b>([^]*?)<\/b>/gi, "$1");
		content_no_html = content_no_html.replace(/<ul>([^]*?)<\/ul>/gi, "$1");
		content_no_html = content_no_html.replace(/<li>([^]*?)<\/li>/gi, "$1");
		content_no_html = content_no_html.replace(/<span [^>]*?>/gi, "");
		content_no_html = content_no_html.replace(/<\/span>/gi, "");
		// Należałoby usuwać również pozostałe tagi HTML
	}while(content_no_html!=content_before);
	
	content_no_html = content_no_html.replace(/&amp;/g, "&");
	content_no_html = content_no_html.replace(/&nbsp;/g, String.fromCharCode(160));
	
	return content_no_html;
}

function html_entity_decode(content){

	content = content.replace(/&lt;/g, "<");
	content = content.replace(/&gt;/g, ">");
	content = content.replace(/&nbsp;/g, String.fromCharCode(160));
	content = content.replace(/&apos;/g, "'");
	// This replace at the end to avoid double replace, i.e. &amp;lt; => &lt; => <
	content = content.replace(/&amp;/g, "&");
	
	return content;

}

var fromDelimiter = '##||-';
var toDelimiter = '-||##';
