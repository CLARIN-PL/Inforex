/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(document).ready(function(){
    $('.search-form').submit(false);

    $(".search_input").keyup(function () {
        var data = this.value.toLowerCase();
        var table_name = $(this).attr('name');
        var table = $("#"+table_name);
        $(table).children().each(function (index, row) {
            var text = $(row).text().toLowerCase();
            if (text.indexOf(data) >= 0 || this.value == "") {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });


	$('.add_corpora_button').click(function() {
		add_corpora();
	});

    $( "#create_corpus_form" ).validate({
        rules: {
            corpus_name: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'corpus_validation',
                        mode: 'create',
                        type: 'create_corpus'
                    }
                }
            }
        },
        messages: {
            corpus_name: {
                required: "Corpus must have a name.",
                remote: "A corpus with this name already exists."
            },
			corpus_description: {
            	required: "Corpus must have a description."
			}
        }
    });
	
});

function add_corpora(){
    $(".confirmCorpus").unbind( "click" ).click(function(){
        if($('#create_corpus_form').valid()){
            var name = $("#corpus_name").val();
            var description = $("#corpus_description").val();
            var ispublic = $("#elementPublic").is(':checked');
            var _data = {
                name: name,
                description: description,
                ispublic: ispublic
            };

            var success = function (data) {
                window.location.reload();
            };

            var login = function () {
                add_corpora();
            };

            doAjaxSync("corpus_add", _data, success, null, null, null, login);
        }
	});
}
