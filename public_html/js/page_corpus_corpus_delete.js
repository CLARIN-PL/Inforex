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
        $(".delete_info").hide();
        $(".delete_loader").show();
        $(".confirmDeleteCorpus").prop("disabled", true);
        var params = {
            corpus: $('#corpus_id').val(),
            actionKey: "xc98"
        };

        var success = function(data){
            var href = document.location.origin + document.location.pathname + '?page=home';
            document.location = href;
        };

        var complete = function(){
            $(".delete_info").show();
            $(".delete_loader").hide();
            $(".loader").hide();
            $("#deleteCorpus").modal('hide');
            $(".confirmDeleteCorpus").removeProp("disabled");
        }

        var login = function(){
            deleteCorpus();
        };

        doAjax("corpus_delete", params, success, null, complete, null, login);
    });
}
