/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */
var url = $.url(window.location.href);
var report_id = url.param('id');
var corpus_id = url.param('corpus');

$(function(){
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
        minimumInputLength: 2,
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
