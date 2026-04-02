/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

var editor = null;

function createPerspectiveEditCodeMirror() {
    return new CodeMirror.fromTextArea('report_content', {
        parserfile: "parsexml.js",
        path: "js/CodeMirror/js/",
        stylesheet: "js/CodeMirror/css/xmlcolors-light.css",
        lineNumbers: false,
        textWrapping: true
    });
}

function fitPerspectiveEditTextareaToScreen() {
    if ($("#disable_codemirror").val() !== "1") {
        return;
    }

    var textarea = $("#report_content");
    var panel = $("#edit_content_panel");
    if (!textarea.length || !panel.length) {
        return;
    }

    var panelTop = panel.offset() ? panel.offset().top : 0;
    var viewportHeight = $(window).height();
    var reservedBottomSpace = 360;
    var availableHeight = Math.max(viewportHeight - panelTop - reservedBottomSpace, 280);
    var maxHeight = Math.max(Math.floor(viewportHeight * 0.55), 280);
    textarea.css("height", Math.min(availableHeight, maxHeight) + "px");
}

$(function(){
    var disableCodeMirror = $("#disable_codemirror").val() === "1";

    if (!disableCodeMirror) {
	    editor = createPerspectiveEditCodeMirror();
    } else {
        fitPerspectiveEditTextareaToScreen();
        $(window).on("resize", fitPerspectiveEditTextareaToScreen);
    }
	//editor.setSize(null, $("#report_content").height());

    $("#enable_codemirror").on("click", function(){
        $.cookie("edit_use_codemirror", "1");
        if (document.location.href[document.location.href.length-1] === "#") {
            document.location.href = document.location.href.slice(0, -1);
        }
        document.location = document.location;
        return false;
    });

    $("#disable_codemirror_button").on("click", function(){
        $.cookie("edit_use_codemirror", "0");
        if (document.location.href[document.location.href.length-1] === "#") {
            document.location.href = document.location.href.slice(0, -1);
        }
        document.location = document.location;
        return false;
    });

	$("#formating").click(function(){
		if ( editor == null ) {
            return true;
        } else {
            $("#report_content").text(editor.getCode());
        }
	});
	
	$("a.edit_type").on("click",function(){
		$.cookie('edit_type',$(this).attr('id'));
		if (document.location.href[document.location.href.length-1]==="#") document.location.href=document.location.href.slice(0,-1);
		document.location = document.location;
	});	
	
	$("#toggle-edit-form").click(function(){
		$("#edit-form").toggle();
	});
});
