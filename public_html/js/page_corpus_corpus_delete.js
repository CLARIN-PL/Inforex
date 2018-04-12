/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
    $(".delete_corpora_button").click(function(){
        deleteCorpus();
    });
})

function deleteCorpus(){
    $("#deleteCorpusHeader").text("Are you sure you want to delete the corpus?");
    $("#deleteCorpusName").text($('#corpus_name').val());
    $("#deleteCorpusDesc").text($('#corpus_description').val());

    $( ".confirmDeleteCorpus" ).unbind( "click" ).click(function() {
        var params = {
            corpus: $('#corpus_id').val()
        };

        var success = function(data){
            var href = document.location.origin + document.location.pathname + '?page=home';
            document.location = href;
        };

        var login = function(){
            deleteCorpus();
        };

        doAjaxSync("corpus_delete", params, success, null, null, null, login);
    });
}
