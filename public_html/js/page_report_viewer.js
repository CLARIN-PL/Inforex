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
});