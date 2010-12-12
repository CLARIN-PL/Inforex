// Obiekt używany do automatycznego wstawiania szablonów
var transriber = null;
var splitY = null;
var splitX = null;

$(function(){

	// Sprawdź rodzaj ułożenia
	if ($(".horizontal").size()>0){
		var other_content_height = $(document).height() - $(".horizontal").outerHeight();
		// Odejmij wysokość nagłówków
		$(".horizontal .height_fix").each(function(index){
			other_content_height -= $(this).outerHeight();
		});
		var panel_height = $(window).height() - other_content_height - 130;
	}
	else{
		
	}
	// Oblicz maksymalną wysokość okna do edycji

	// Ustaw pole do edycji
	editor = CodeMirror.fromTextArea('report_content', {
		height: "100%", //panel_height/2 + "px",
		parserfile: "parsexml.js",
		stylesheet: "js/CodeMirror/css/xmlcolors.css",
		path: "js/CodeMirror/js/",
		continuousScanning: 500,
		lineNumbers: true
	});
	transriber = new EditorTranscription(editor);
	
	$("#zoom").css("height", panel_height/2 + "px");
	$("#frame_editor").css("height", panel_height/2 + "px");
	$("#frame_elements").css("height", panel_height/2 + "px");
	
	// Uaktualnij pole z treścią dokumentu przed jego zapisem
	$("#save").click(function(){
		if ( editor == null )
			return false;
		else
			$("#report_content").text(editor.getCode());
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
	
});

/**
 * Przypisz zdarzenia do przycisków wstawiających elementy
 */
$(function(){
	$("#tei_struct").click(function(){
		var tei = "<text>\n<body>\n";
		var n = 1;
		$("#zoom img").each(function(){
			tei += '<pb facs="'+$(this).attr("title")+'" n="'+n+'" place="" rend=""/>\n';
			n++;
		});
		tei += "</body>\n</text>";
		transriber.insertLine(tei);
		transriber.reindent();
	});	
	$("#element_opener").click(function(){
		transriber.insertLine("<opener>\n</opener>");
		transriber.reindent();
	});
	$("#element_opener_dateline").click(function(){
		transriber.insertLine("<dateline>\n<date></date>\n</dateline>");
		transriber.reindent();
	});
	$("#element_opener_salute").click(function(){
		transriber.insertLine("<salute></salute>");
		transriber.reindent();
	});
	$("#element_signed").click(function(){
		transriber.insertLine("<signed></signed>");
		transriber.reindent();
	});
	$("#element_ps").click(function(){
		transriber.insertLine("<ps>\n</ps>");
		transriber.reindent();
	});
	$(".ornament").click(function(){
		transriber.insertLine("<ornament type=\"" + $(this).text() + "\"/>");
		transriber.reindent();
	});
	$(".element_signed").click(function(){
		var tag = $(this).text().trim();
		transriber.insertLineWithin("<"+tag+"></"+tag+">", "signed");
		transriber.reindent();
	});	
	$("#element_p_lb").click(function(){
		if (!transriber.insertWithin("<lb/>", "p"))
			alert("Znacznik LB musi znajdować się wewnątrz znacznika P.");
	});
	$("#element_p_del").click(function(){
		if (!transriber.insertAroundWithin("<del type=\"\" source=\"\">", "</del>", "p"))
			alert("Znacznik DEL musi znajdować się wewnątrz znacznika P.");
	});
	$("#element_p_add").click(function(){
		if (!transriber.insertAroundWithin("<add place=\"\">", "</add>", "p"))
			alert("Znacznik ADD musi znajdować się wewnątrz znacznika P.");
	});
	$("#element_p").click(function(){
		var n = transriber.currentLineNumber();
		if (transriber.insertLineWithin("<p></p>", "body")){
			transriber.reindent();
			transriber.setCursorAfter(n, "<p>");
		}
		else
			alert("Znacznik P musi znajdować się wewnątrz znacznika BODY.");
	});	
	
});

