$(function(){
	//$("#report_content").markItUp(mySettings);
});

var editor = null;

$(function(){
	editor = CodeMirror.fromTextArea('report_content', {
		height: "350px",
		parserfile: "parsexml.js",
		stylesheet: "js/CodeMirror/css/xmlcolors.css",
		path: "js/CodeMirror/js/",
		continuousScanning: 500,
		lineNumbers: true
	});
	
	$("#formating").click(function(){
		if ( editor == null )
			return false;
		else
			$("#report_content").text(editor.getCode());			
	});
	
	$("select.edit_type").live("change",function(){
		newCookie = $(this).val();		
		$.cookie('edit_type',newCookie);
		
		if (document.location.href[document.location.href.length-1]=="#") document.location.href=document.location.href.slice(0,-1);
		document.location = document.location;
	});	
	
	$("#toggle-edit-form").click(function(){
		$("#edit-form").toggle();
	});
	
	jQuery("#table-annotations").flexigrid();
});