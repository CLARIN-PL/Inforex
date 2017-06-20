/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(document).ready(function(){
	$('.add_corpora_button').click(function() {
		add_corpora();
	});

    $( "#create_corpus_form" ).validate({
        messages: {
            corpus_name: {
                required: "Corpus must have a name."
            },
			corpus_description: {
            	required: "Corpus must have a description."
			}
        }
    });
	
});

function add_corpora(){
    $( ".confirmCorpus" ).unbind( "click" ).click(function() {

			var name = $("#corpus_name").val();
			var description = $("#corpus_description").val();
			var ispublic = $("#elementPublic").attr("checked");
			var _data = {
					name : name,
					description : description,
					ispublic : ispublic
			};

			var success = function(data){
				window.location.reload();
			};

			var login = function(){
				add_corpora();
			};

			doAjaxSync("corpus_add", _data, success, null, null, null, login);

	});
}
