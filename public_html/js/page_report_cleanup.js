/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

var editor = null;

function createPerspectiveCleanupCodeMirror() {
    return new CodeMirror.fromTextArea('report_content', {
        parserfile: "parsexml.js",
        stylesheet: "js/CodeMirror/css/xmlcolors.css",
        path: "js/CodeMirror/js/",
        continuousScanning: 500,
        lineNumbers: true
    });
}

function fitPerspectiveCleanupTextareaToScreen() {
    if ($("#disable_codemirror").val() !== "1") {
        return;
    }

    var textarea = $("#report_content");
    var panel = $("#edit_content");
    if (!textarea.length || !panel.length) {
        return;
    }

    var panelTop = panel.offset() ? panel.offset().top : 0;
    var viewportHeight = $(window).height();
    var reservedBottomSpace = 260;
    var availableHeight = Math.max(viewportHeight - panelTop - reservedBottomSpace, 260);
    var maxHeight = Math.max(Math.floor(viewportHeight * 0.5), 260);
    textarea.css("height", Math.min(availableHeight, maxHeight) + "px");
}

function loadCleanupSourceFrame() {
    var frame = $("#cleanup_source_frame");
    var placeholder = $("#source_placeholder");
    var sourceUrl = frame.attr("data-src");

    if (!frame.length || !sourceUrl || frame.attr("data-loaded") === "1") {
        return;
    }

    frame.attr("data-loaded", "1");
    frame.attr("src", sourceUrl);
    frame.removeClass("report-cleanup-source-frame-lazy");
    frame.css("display", "block");
    placeholder.hide();
}

$(function(){
    $("#load_source").on("click", function(){
        loadCleanupSourceFrame();
        return false;
    });
});

$(function(){
    var disableCodeMirror = $("#disable_codemirror").val() === "1";

    try {
        if (!disableCodeMirror) {
            editor = createPerspectiveCleanupCodeMirror();
        } else {
            fitPerspectiveCleanupTextareaToScreen();
            $(window).on("resize", fitPerspectiveCleanupTextareaToScreen);
        }
    } catch (e) {
        fitPerspectiveCleanupTextareaToScreen();
        $(window).on("resize", fitPerspectiveCleanupTextareaToScreen);
    }

	$("#formating").click(function(){
		if (editor == null) {
            return true;
        } else {
			$("#report_content").text(editor.getCode());
        }
	});

    $("#enable_codemirror").on("click", function(){
        $.cookie("edit_use_codemirror", "1");
        if (document.location.href[document.location.href.length - 1] === "#") {
            document.location.href = document.location.href.slice(0, -1);
        }
        document.location = document.location;
        return false;
    });

    $("#disable_codemirror_button").on("click", function(){
        $.cookie("edit_use_codemirror", "0");
        if (document.location.href[document.location.href.length - 1] === "#") {
            document.location.href = document.location.href.slice(0, -1);
        }
        document.location = document.location;
        return false;
    });
	
	$("a.edit_type").on("click",function(){
		var newCookie = $(this).attr('id');
		$.cookie('edit_type',newCookie);
		
		if (document.location.href[document.location.href.length-1]=="#") {
            document.location.href=document.location.href.slice(0,-1);
        }
		document.location = document.location;
	});
	
	$("#toggle-edit-form").click(function(){
		$("#edit-form").toggle();
	});
	
	jQuery("#table-annotations").flexigrid();
});
