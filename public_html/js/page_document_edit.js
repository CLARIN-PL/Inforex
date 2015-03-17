/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

var editor = null;

$(function(){
	editor = CodeMirror.fromTextArea('report_content', {
		height: getTextareaHeight() + "px",
		parserfile: "parsexml.js",
		stylesheet: "js/CodeMirror/css/xmlcolors.css",
		path: "js/CodeMirror/js/",
		continuousScanning: 500,
		lineNumbers: true
	});
	
	$("input[name=title]").focus();
	
	$("input[name=date]").datepicker({ dateFormat: "yy-mm-dd" });
	
	setTextareaHeight();
});

function getTextareaHeight(){
	var bodyHeight = $("body").outerHeight();
	var tableHeight = $("#page_content table tbody tr").outerHeight();
	var boilerplateHeight = $("#add_content_box").outerHeight() - $("#add_content").outerHeight(); 
	var windowHeight = $(window).height();
	var textareaHeight = windowHeight - (bodyHeight - tableHeight) - boilerplateHeight - 70;	
	return textareaHeight;
}