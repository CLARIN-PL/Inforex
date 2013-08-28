/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

// Obiekt używany do automatycznego wstawiania szablonów
var transriber = null;
var splitY = null;
var splitX = null;
var editor = null;

$(function(){

	// Ustaw pole do edycji
	editor = CodeMirror.fromTextArea('report_content', {
		height: "100%",
		parserfile: "parsexml.js",
		stylesheet: "js/CodeMirror/css/xmlcolors.css",
		path: "js/CodeMirror/js/",
		continuousScanning: 100,
		lineNumbers: true,
		onChange: function(){
			$("#save").removeAttr("disabled");
		}
	});
	transriber = new EditorTranscription(editor);
	
	// Uaktualnij pole z treścią dokumentu przed jego zapisem
	$("#save").click(function(){
		save_content_ajax();
		return false;
	});
	
	// Przyciski do przełączania skanów
	$(".pagging a").click(function(){
		$("#zoom img").hide();
		var id = "#" + $(this).attr("title"); 
		$(id).css("width", $("#zoom_amount").text());
		$(id).show();
		$(".pagging a").removeClass("active");
		$(this).addClass("active");
	});
	
	// Slider do ustawienia rozmiaru obrazku
	$( "#slider-vertical" ).slider({
		range: "min",
		value: 100,
		min: 10,
		max: 200,
		slide: function( event, ui ) {
			$("#zoom_amount").text( ui.value + "%" );
			var id = "#" + $(".pagging a.active").attr("title");
			$(id).css("width", ui.value + "%");
		}
	});
	
	// Obsługa splittera
	$(".hsplitbar").bind("mousedown", function(e){
		splitY = e.pageY;
		var frame_images = $("#zoom");
		var frame_editor = $("#frame_editor"); 
		var frame_elements = $("#frame_elements"); 
		$(document).bind("mousemove", function(e){
			var mod = splitY - e.pageY;			
			frame_images.css("height", (frame_images.height() - mod) + "px"); 
			frame_editor.css("height", (frame_editor.height() + mod) + "px"); 
			frame_elements.css("height", (frame_elements.height() + mod) + "px"); 
			splitY = e.pageY;
		});
	});
	
	$(document).bind("mouseup", function(e){
		$(document).unbind("mousemove");
	});

	// Obsługa obrazka
	$("#zoom img").bind("mousedown", function(e){
		if (e.preventDefault) e.preventDefault();
		splitX = e.pageX;
		splitY = e.pageY;
		var frame_images = $("#zoom");
		$(document).bind("mousemove", function(e){
			e.preventDefault();
			frame_images.scrollTop( frame_images.scrollTop() + splitY - e.pageY);
			frame_images.scrollLeft( frame_images.scrollLeft() + splitX - e.pageX);
			splitX = e.pageX;
			splitY = e.pageY;
		});
	});

	// Wyświetl przybornik jako zakładek
	$("#elements_sections").tabs();
	$("#elements_sections").css("height", "");
	$("#elements_sections").css("overflow", "normal");
	
	// Obsługa walidacji dokumentu
	$("#validate").click(function(){
		validate_structure(editor.getCode());
	});

	// Jeżeli jest wpisana treść dokumentu, to automatycznie ustaw na zakładkę walidacji
	if ( $("#report_content").text().length > 0 ){
		$("#elements_sections").tabs("select", 8);
		validate_structure($("#report_content").text());
	}
});

function save_content_ajax(){
	$("#report_content").text(editor.getCode());		
	var content = editor.getCode();
	var report_id = $("#report_id").attr("value");
	$("#save").attr("disabled", "disabled");

	var params = {
		report_id: report_id, 
		content: content
	};
	
	var success = function(data){
		var currentDate = new Date();
		var min = currentDate.getMinutes();
		var time = currentDate.getHours() + ":" + (min<10 ? "0" : "") + min; 
		$("#save").after("<span class='ajax_inline_status'><span class='time'>"+time+"</span>Document was saved</span>");
		$(".ajax_inline_status").delay(2000).fadeOut();
	};
	
	var login = function(){
		save_content_ajax();
	}
	
	
	var complete = function(){
		$("#save").removeAttr("disabled");
	};
	
	doAjax("report_update_content", params, success, null, complete, null, login);		
}

/**
 * Sprawdza poprawność struktury dokumentu. Wynik zapisywany jest do zakładki.
 * @param content
 * @return
 */
function validate_structure(content){
	$("#validate").attr("disabled", "disabled");
	
	var success = function(data){
		$("#validate_result").html("<img src='gfx/ajax.gif' title='czekam...'/>");
		if (data['errors'].length > 0){
			$("#validate_result").html("<h2 style='color: red'>Dokument może zawierać błędy</h2><ol></ol>");
			for ( var n = 0; n < data['errors'].length; n++) {
				var e = data['errors'][n];
				$("#validate_result ol").append("<li>[<b>" + e['line'] + "</b>:" + e['col'] + "] " + e['description'] + "</li>");
			}
		}
		else{
			$("#validate_result").html("<h2 style='color: darkgreen'>Struktura dokumentu jest poprawna</h2>");
		}
	};
	
	var complete = function(){
		$("#validate").removeAttr("disabled");
	};
	
	doAjax("lps_validate_xml", {content: content}, success, null, complete);
}

/**
 * Przypisz zdarzenia do przycisków wstawiających elementy
 */
$(function(){
	$("#tei_struct").click(function(){
		var tei = "<text>\n<body>\n";
		var n = 1;
		$("#zoom img").each(function(){
			tei += '<pb facs="'+$(this).attr("title")+'" n="'+n+'"/>\n\n';
			n++;
		});
		tei += "</body>\n</text>";
		transriber.insertLine(tei);
		transriber.reindent();
		transriber.setCursor(4, 4);
	});
		
	$(".element_add").click(function(){
		var value = $(this).children(".value").text();
		transriber.insertText("<add place=\""+value+"\">##@</add>");
	});
	$("#element_closer").click(function(){
		transriber.insertLine("<closer>\n@ </closer>");
		transriber.reindent();
	});
	$("#element_closer_inline").click(function(){
		transriber.insertLine("<closer rend=\"inline\">\n@ </closer>");
		transriber.reindent();
	});
	$(".element_closer_signed_rend").click(function(){
		var rend = $(this).children(".value").text();
		transriber.insertAroundWithin("<signed rend=\""+rend+"\">", "</signed>", "body");
	});
	$(".element_corr_editor").click(function(){
		transriber.insertText("<corr resp=\"editor\" type=\"@\" sic=\"##\"></corr>");		
	});
	$(".element_corr_author").click(function(){
		transriber.insertText("<corr resp=\"author\" count=\"@\">##</corr>", "body");
	});
	$(".element_corr_editor_type").click(function(){
		var prefix = transriber.substr(-1); 
		if ( prefix == "," || prefix == '"' )
			transriber.insertText($(this).text());
		else
			transriber.insertText("," + $(this).text());
	});
	$(".element_del").click(function(){
		var value = $(this).children(".value").text();
		transriber.insertText("<del type=\""+value+"\">##@</del>");
		transriber.reindent();
	});
	$("#element_envelope").click(function(){
		transriber.insertLine("<envelope>\n@ </envelope>");
		transriber.reindent();
	});	
	$(".element_figure_open").click(function(){
		transriber.insertAroundWithin("<figure type=\""+$(this).attr("val")+"\">", "</figure>", "body");		
	});
	$(".element_figure_type").click(function(){
		transriber.insertText("<figure type=\""+$(this).attr("title")+"\"/>@");
		transriber.reindent();
	});
	$(".element_figure_rend").click(function(){
		if (transriber.substr(-2) == "/>") transriber.insertText(" rend=\"multiple\"@", -2);
		else if (transriber.substr(-1) == ">") transriber.insertText(" rend=\"multiple\"@", -1);
		else transriber.insertText("rend=\"multiple\"@");
	});
	$(".element_head_rend").click(function(){
		var str = $(this).children(".value").text();
		transriber.insertLine("<head rend=\""+str+"\">@</head>", "body");
		transriber.reindent();
	});
	$(".element_hi_rend").click(function(){
		var str = $(this).children(".value").text();
		transriber.insertText("<hi rend=\""+str+"\">##@</hi>");
		transriber.reindent();
	});
	$(".element_hyph").click(function(){
		var str = $(this).children(".value").text();
		transriber.insertText("<hyph>"+str+"</hyph>");
		transriber.reindent();
	});
	$(".element_hyph_empty").click(function(){
		transriber.insertText("<hyph/>");
		transriber.reindent();
	});
	$(".element_gap_reason").click(function(){
		var value = $(this).children(".value").text();
		transriber.insertText("<gap reason=\""+value+"\"/>");
		transriber.reindent();
	});	
	$("#element_opener").click(function(){
		transriber.insertLine("<opener>\n@ </opener>");
		transriber.reindent();
	});
	$(".element_opener_dateline_rend").click(function(){
		var rend = $(this).children(".value").text();
		transriber.insertLine("<dateline rend=\""+rend+"\">@</dateline>");
		transriber.reindent();
	});
	$(".element_opener_salute_rend").click(function(){
		var rend = $(this).children(".value").text();
		transriber.insertAroundWithin("<salute rend=\""+rend+"\">", "</salute>", "body");
	});
	$(".element_ornament").click(function(){		
		transriber.insertLine("<ornament type=\"" + $(this).children(".value").text() + "\"/>");
		transriber.reindent();
	});
	$(".element_p_lb").click(function(){
		if (!transriber.insertWithin("<lb/>", "p"))
			alert("Znacznik LB musi znajdować się wewnątrz znacznika P.");
	});	
	$(".element_p_place").click(function(){
		var value = $(this).children(".value").text();
		transriber.insertLine("<p place=\""+value+"\">@</p>");
		transriber.reindent();
	});	
	$(".element_p_rend").click(function(){
		var rend = $(this).children(".value").text();
		transriber.insertLine("<p rend=\""+rend+"\">@</p>");
		transriber.reindent();
	});	
	$("#element_ps").click(function(){
		transriber.insertLine("<ps>\n@ </ps>");
		transriber.reindent();
	});
	$("#element_ps_p_meta_block").click(function(){
		transriber.insertLine("<p type=\"meta\">@</p>");
		transriber.reindent();
	});
	$("#element_ps_p_meta_inline").click(function(){
		transriber.insertLine("<p type=\"meta\" rend=\"inline\">@</p>");
		transriber.reindent();
	});
	$("#element_ps_content").click(function(){
		transriber.insertLine("<p>@</p>");
		transriber.reindent();
	});
	$(".element_salute").click(function(){
		transriber.insertText("<salute>##@</salute>");
	});
	$("#element_signed").click(function(){
		transriber.insertAroundWithin("<signed>", "</signed>", "body");
	});
	$(".element_unclear_cert").click(function(){
		var str = $(this).attr("title");
		transriber.insertAroundWithin("<unclear cert=\""+str+"\">", "</unclear>", "p");
	});	
	$(".element_verte").click(function(){
		transriber.insertText("<verte>##@</verte>");
	});
	$("#list_of_symbols a").click(function(){
		transriber.insertWithin($(this).text(), "body");
	});
});

