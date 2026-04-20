/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

var privateCorporaPage = 1;
var privateCorporaPageSize = 15;

$(document).ready(function(){
    $('.search-form').submit(false);

    $(".search_input").keyup(function () {
        var data = this.value.toLowerCase();
        var table_name = $(this).attr('name');

        if (table_name == "private_corpora_table") {
            privateCorporaPage = 1;
            renderPrivateCorporaPage(data);
            return;
        }

        var table = $("#"+table_name);
        $(table).children().each(function (index, row) {
            var text = $(row).text().toLowerCase();
            if (text.indexOf(data) >= 0 || data == "") {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    renderPrivateCorporaPage("");

	$('.add_corpora_button').click(function() {
		add_corpora();
	});

    $("#createCorpus").on("hidden.bs.modal", function() {
        $("#create_corpus_form")[0].reset();
        $("#create_corpus_form").validate().resetForm();
        $("#create_corpus_form .form-control").removeClass("error");
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

function renderPrivateCorporaPage(filter) {
    var $rows = $("#private_corpora_table").children("tr");
    var $info = $("#private_corpora_pagination_info");
    var $controls = $("#private_corpora_pagination_controls");

    if (!$rows.length || !$controls.length) {
        return;
    }

    var normalizedFilter = (filter || "").toLowerCase();
    var $filteredRows = $rows.filter(function() {
        return normalizedFilter == "" || $(this).text().toLowerCase().indexOf(normalizedFilter) >= 0;
    });
    var totalRows = $filteredRows.length;
    var totalPages = Math.max(1, Math.ceil(totalRows / privateCorporaPageSize));

    privateCorporaPage = Math.min(privateCorporaPage, totalPages);

    var start = (privateCorporaPage - 1) * privateCorporaPageSize;
    var end = start + privateCorporaPageSize;

    $rows.hide();
    $filteredRows.slice(start, end).show();

    if (totalRows === 0) {
        $info.text("No matching corpora");
    } else {
        $info.text("Showing " + (start + 1) + " to " + Math.min(end, totalRows) + " of " + totalRows + " corpora");
    }

    renderPrivateCorporaPaginationControls(totalPages, normalizedFilter);
}

function renderPrivateCorporaPaginationControls(totalPages, filter) {
    var $controls = $("#private_corpora_pagination_controls");
    var html = "";
    var pageWindow = 5;
    var firstPage = Math.max(1, privateCorporaPage - Math.floor(pageWindow / 2));
    var lastPage = Math.min(totalPages, firstPage + pageWindow - 1);

    firstPage = Math.max(1, lastPage - pageWindow + 1);

    html += buildPrivateCorporaPageButton("Previous", privateCorporaPage - 1, privateCorporaPage == 1);

    for (var page = firstPage; page <= lastPage; page++) {
        html += buildPrivateCorporaPageButton(page, page, false, page == privateCorporaPage);
    }

    html += buildPrivateCorporaPageButton("Next", privateCorporaPage + 1, privateCorporaPage == totalPages);
    $controls.html(html);

    $controls.find("button").click(function() {
        if ($(this).prop("disabled")) {
            return;
        }
        privateCorporaPage = parseInt($(this).attr("data-page"), 10);
        renderPrivateCorporaPage(filter);
    });
}

function buildPrivateCorporaPageButton(label, page, disabled, active) {
    return '<button type="button" class="home-corpora-page-button' + (active ? ' active' : '') + '" data-page="' + page + '"' + (disabled ? ' disabled="disabled"' : '') + '>' + label + '</button>';
}

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
