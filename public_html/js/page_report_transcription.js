$(function(){
	
	// Oblicz maksymalną wysokość okna do edycji
	var window_height = $(window).height();
	var textarea_height = window_height - 250;
	editor = CodeMirror.fromTextArea('report_content', {
		height: textarea_height + "px",
		parserfile: "parsexml.js",
		stylesheet: "js/CodeMirror/css/xmlcolors.css",
		path: "js/CodeMirror/js/",
		continuousScanning: 500,
		lineNumbers: true
	});
	
	$("#save").click(function(){
		if ( editor == null )
			return false;
		else
			$("#report_content").text(editor.getCode());
	});

	
	//$(".viewer").iviewer();
	
	$(".pagging a").click(function(){
		$("#zoom img").hide();
		var id = "#" + $(this).attr("title"); 
		$(id).show();
		$(".pagging a").removeClass("active");
		$(this).addClass("active");
	});
});

