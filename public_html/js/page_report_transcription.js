// Obiekt używany do automatycznego wstawiania szablonów
var transriber = null;
var splitY = null;
var splitX = null;

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
	
});

function save_content_ajax(){
	$("#report_content").text(editor.getCode());		
	var content = editor.getCode();
	var report_id = $("#report_id").attr("value");
	$("#save").attr("disabled", "disabled");

	$.ajax({
		type: 	'POST',
		url: 	"index.php",
		data:	{ 	
					ajax: "report_update_content", 
					report_id: report_id, 
					content: content
				},
		success:function(data){
					if (data['success']){
						var currentDate = new Date();
						var min = currentDate.getMinutes();
						var time = currentDate.getHours() + ":" + (min<10 ? "0" : "") + min; 
						$("#save").after("<span class='ajax_inline_status'><span class='time'>"+time+"</span>Document was saved</span>");
						$(".ajax_inline_status").delay(2000).fadeOut();
					}else if(data['error_code'] == 'ERROR_AUTHORIZATION'){
						// Okno dialogowe do zalogowania się użytkownika
						loginForm(false, function(success){ 
							if (success){
								save_content_ajax();
							}else{
								alert('Wystąpił problem z autoryzacją. Zmiany nie zostały zapisane.');								
								$("#save").removeAttr("disabled");
							}
						});
					}else{
						alert('Wystąpił nieznany błąd. Zrób kopię dokumentu.');
					}
				},
		error: function(request, textStatus, errorThrown){
					$("#save").removeAttr("disabled");
				},
		dataType:"json"
	});		
}

/**
 * Przypisz zdarzenia do przycisków wstawiających elementy
 */
$(function(){
	$("#tei_struct").click(function(){
		var tei = "<text>\n<body>\n";
		var n = 1;
		$("#zoom img").each(function(){
			tei += '<pb facs="'+$(this).attr("title")+'" n="'+n+'" place="" rend=""/>\n\n';
			n++;
		});
		tei += "</body>\n</text>";
		transriber.insertLine(tei);
		transriber.reindent();
		transriber.setCursor(4, 4);
	});	
	$("#element_opener").click(function(){
		transriber.insertLineWithin("<opener>\n</opener>", "body");
		transriber.reindent();
	});
	$(".element_opener_dateline_rend").click(function(){
		var rend = $(this).children(".value").text();
		transriber.insertAroundWithin("<dateline rend=\""+rend+"\">", "</dateline>", "body");
	});
	$(".element_opener_salute_rend").click(function(){
		var rend = $(this).children(".value").text();
		transriber.insertAroundWithin("<salute rend=\""+rend+"\">", "</salute>", "body");
	});
	$("#element_closer").click(function(){
		transriber.insertLineWithin("<closer>\n</closer>", "body");
		transriber.reindent();
	});
	$(".element_closer_signed_rend").click(function(){
		var rend = $(this).children(".value").text();
		transriber.insertAroundWithin("<signed rend=\""+rend+"\">", "</signed>", "body");
	});
	$(".element_closer_salute_rend").click(function(){
		var rend = $(this).children(".value").text();
		transriber.insertAroundWithin("<salute rend=\""+rend+"\">", "</salute>", "body");
	});
	$(".element_figure_type").click(function(){
		if (!transriber.insertWithin("<figure type=\""+$(this).attr("title")+"\"/>", "p"))
			alert("Znacznik FIGURE musi znajdować się wewnątrz znacznika P.");
	});
	$(".element_gap_reason").click(function(){
		var str = $(this).attr("title");
		if (!transriber.insertWithin("<gap reason=\""+str+"\"/>", "p"))
			alert("Znacznik GAP musi znajdować się wewnątrz znacznika P.");
	});	
	$("#element_signed").click(function(){
		var n = transriber.currentLineNumber();
		transriber.insertLineWithin("<signed></signed>", "body");
		transriber.reindent();
		transriber.setCursorAfter(n, "<signed>");
	});
	$("#element_ps").click(function(){
		transriber.insertLineWithin("<ps>\n</ps>", "body");
		transriber.reindent();
	});
	$("#element_ps_meta").click(function(){
		var n = transriber.currentLineNumber();
		transriber.insertLineWithin("<p type=\"meta\"></p>", "body");
		transriber.reindent();
		transriber.setCursorAfter(n, "<p type=\"meta\">");
	});
	$("#element_ps_content").click(function(){
		var n = transriber.currentLineNumber();
		transriber.insertLineWithin("<p></p>", "body");
		transriber.reindent();
		transriber.setCursorAfter(n, "<p>");
	});
	$(".ornament").click(function(){
		transriber.insertLine("<ornament type=\"" + $(this).text() + "\"/>");
		transriber.reindent();
	});
	$(".element_signed").click(function(){
		var tag = $(this).text().trim();
		transriber.insertLine("<"+tag+"></"+tag+">");
		transriber.reindent();
	});	
	$("#element_p_lb").click(function(){
		if (!transriber.insertWithin("<lb/>", "p"))
			alert("Znacznik LB musi znajdować się wewnątrz znacznika P.");
	});
	$("#element_p_del").click(function(){
		if (!transriber.insertWithin("<del type=\"\" source=\"\"/>", "p"))
			alert("Znacznik DEL musi znajdować się wewnątrz znacznika P.");
	});
	$("#element_p_add").click(function(){
		if (!transriber.insertAroundWithin("<add place=\"\">", "</add>", "p"))
			alert("Znacznik ADD musi znajdować się wewnątrz znacznika P.");
	});
	$("#element_p").click(function(){
		var n = transriber.currentLineNumber();
		if (transriber.insertAroundWithin("<p>", "</p>", "body")){
			transriber.reindent();
			transriber.setCursorAfter(n, "<p>");
		}
		else
			alert("Znacznik P musi znajdować się wewnątrz znacznika BODY.");
	});	
	$("#element_attribute_rend").click(function(){
		var n = transriber.currentLineNumber();
		if (transriber.insertLineWithin("rend=\"\"", "body")){
			transriber.reindent();
			transriber.setCursorAfter(n, "rend=");
		}
		else
			alert("Znacznik P musi znajdować się wewnątrz znacznika BODY.");
	});		
	$(".element_hi_rend").click(function(){
		var str = $(this).attr("title");
		var n = transriber.currentLineNumber();
		transriber.insertAroundWithin("<hi rend=\""+str+"\">", "</hi>", "body");
		transriber.setCursorAfter(n, "<hi rend=\""+str+"\">");
	});
	$("#element_corr_editor").click(function(){
		transriber.insertAroundWithin("<corr resp=\"editor\" type=\"@\" sic=\"\">", "</corr>", "body");		
	});
	$("#element_corr_author").click(function(){
		transriber.insertAroundWithin("<corr resp=\"author\" count=\"@\">", "</corr>", "body");
	});
	$(".element_corr_editor").click(function(){
		transriber.insertWithin($(this).text(), "body");
	});
	$(".element_unclear_cert").click(function(){
		var str = $(this).attr("title");
		transriber.insertAroundWithin("<unclear cert=\""+str+"\">", "</unclear>", "p");
	});	
	
});

