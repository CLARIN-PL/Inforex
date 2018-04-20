/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

var editor = null;

$(function(){
	var parsefile = $("#format").val() === "2" ? "parsedummy.js" : "parsexml.js";

	editor = new CodeMirror.fromTextArea('report_content', {
		height: "600px",
		parserfile: parsefile,
		path: "js/CodeMirror/js/",
		stylesheet: "js/CodeMirror/css/xmlcolors.css",
		continuousScanning: 500,
		lineNumbers: true
	});

	$("#formating").click(function(){
		if ( editor == null ) {
            return false;
        } else {
            $("#report_content").text(editor.getCode());
        }
	});
	
	$("a.edit_type").on("click",function(){
		$.cookie('edit_type',$(this).attr('id'));
		if (document.location.href[document.location.href.length-1]=="#") document.location.href=document.location.href.slice(0,-1);
		document.location = document.location;
	});	
	
	$("#toggle-edit-form").click(function(){
		$("#edit-form").toggle();
	});
	
	jQuery("#table-annotations").flexigrid();
});