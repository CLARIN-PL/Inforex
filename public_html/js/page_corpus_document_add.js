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

    $('.select_parent_report').select2({
        minimumInputLength: 2,
        ajax: {
            url: 'index.php',
            type: "post",
            data: function (params) {
                var query = {
                    search: params.term,
                    type: 'public',
                    ajax: 'metadata_get_reports',
                    corpus_id: corpus_id,
                    page: params.page || 1
                };

                // Query parameters will be ?search=[term]&type=public
                return query;
            },
            processResults: function (data) {
                // Tranforms the top-level key of the response object from 'items' to 'results'
                console.log(data);
                return {
                    results: data.results,
                    pagination: {
                        "more": data.pagination.more
                    }
                };
            }
        }
    });

    $('.select_language').select2({
        ajax: {
            url: 'index.php',
            type: "post",
            data: function (params) {
                var query = {
                    search: params.term,
                    type: 'public',
                    ajax: 'metadata_get_languages',
                    corpus_id: corpus_id,
                    page: params.page || 1
                };

                // Query parameters will be ?search=[term]&type=public
                return query;
            },
            processResults: function (data) {
                // Tranforms the top-level key of the response object from 'items' to 'results'
                console.log(data);
                return {
                    results: data.results,
                    pagination: {
                        "more": data.pagination.more
                    }
                };
            }
        }
    });

});

function getTextareaHeight(){
	var bodyHeight = $("body").outerHeight();
	var tableHeight = $("#page_content table tbody tr").outerHeight();
	var boilerplateHeight = $("#add_content_box").outerHeight() - $("#add_content").outerHeight(); 
	var windowHeight = $(window).height();
	var textareaHeight = windowHeight - (bodyHeight - tableHeight) - boilerplateHeight - 70;
    var textareaHeight = 400;
    return textareaHeight;
}