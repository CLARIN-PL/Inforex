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
		
		content_no_html = content_no_html.replace(/<small[^<]*<\/small>/gi, "");
		content_no_html = content_no_html.replace(/<span id="an[0-9]+" class="[^>]*" title="an#[0-9]+:[a-z_]+">([^]*?)<\/span>/gi, "$1");
		content_no_html = content_no_html.replace(/<span id="an[0-9]+" title="an#[0-9]+:[a-z_]+" class="[^>]*">([^]*?)<\/span>/gi, "$1");
		content_no_html = content_no_html.replace(/<span title="an#[0-9]+:[a-z_]+" class="[^>]*" id="an[0-9]+">([^]*?)<\/span>/gi, "$1");
		content_no_html = content_no_html.replace(/<span title="an#[0-9]+:[a-z_]+" id="an[0-9]+" class="[^>]*">([^]*?)<\/span>/gi, "$1");
		content_no_html = content_no_html.replace(/<span class="[^>]*" id="an[0-9]+" title="an#[0-9]+:[a-z_]+">([^]*?)<\/span>/gi, "$1");
		content_no_html = content_no_html.replace(/<span class="[^>]*" title="an#[0-9]+:[a-z_]+" id="an[0-9]+">([^]*?)<\/span>/gi, "$1");
		content_no_html = content_no_html.replace(/<br(\/)?>/gi, "");
		content_no_html = content_no_html.replace(/<(\/)?p>/gi, "");
	}while(content_no_html!=content_before);
	
	content_no_html = content_no_html.replace(/&amp;/g, "&");
	content_no_html = content_no_html.replace(/&nbsp;/g, String.fromCharCode(160));
	
	return content_no_html;
}

var fromDelimiter = String.fromCharCode(11)+String.fromCharCode(12);
var toDelimiter = String.fromCharCode(12)+String.fromCharCode(11);
