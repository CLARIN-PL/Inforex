/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){

    $(".sensCreate").click(function(){
        createWordDialog();
        return false;
    });

    $(".sensEdit").click(function(){
        editWordDialog($(this).attr("id"));
        return false;
    });

    $(".sensDelete").click(function(){
        deleteWordDialog($(this).attr("id"));
        return false;
    });

    $(".sensDescriptionCreate").click(function(){
        var name = $(this).attr("id");
        var id = getSelectedLemmaId();
        createNewSens(name, id);
        return false;
    });

    $("#sensContainer").on("click", ".sensName", function(){
        var $row = $(this);

        if (!$row.hasClass("selected")){
            $("tr.sensName").removeClass("selected");
            $row.addClass("selected");
        }

        var lemmaId = $row.attr("id");
        var lemmaName = getLemmaNameFromRow($row);

        $(".sensEdit").show().attr("id", lemmaName);
        $(".sensDelete").show().attr("id", lemmaName);
        $("#sense_panel").show();
        getSens($row, lemmaId, lemmaName, 1);
    });

    $("#senses_options").on("click", ".sensItemDescription, .sensItemEdit", function(event){
        event.preventDefault();
        event.stopPropagation();
        var $card = $(this).closest(".administration-wsd-sense-card");
        $card.find(".sensItemEditForm").toggle();
        $card.toggleClass("is-editing", $card.find(".sensItemEditForm").is(":visible"));
    });

    $("#senses_options").on("click", ".saveSens", function(){
        var $card = $(this).closest(".administration-wsd-sense-card");
        var description = $card.find("textarea[name='sensDescriptionEdit']").val();
        var senseName = $card.data("senseName");
        updateSens($(this), description, senseName);
        return false;
    });

    $("#senses_options").on("click", ".discardSens", function(){
        var $card = $(this).closest(".administration-wsd-sense-card");
        var currentValue = $card.find("textarea[name='sensDescriptionEdit']").val();
        var originalValue = $card.find("#hidden_text_area").val();

        if (currentValue === originalValue || window.confirm("Discard changes?")) {
            $card.find("textarea[name='sensDescriptionEdit']").val(originalValue);
            $card.find(".sensItemEditForm").hide();
            $card.removeClass("is-editing");
        }

        return false;
    });

    $("#senses_options").on("click", ".deleteSens", function(){
        var $card = $(this).closest(".administration-wsd-sense-card");
        deleteSensDialog($card.data("senseName"), $card.data("lemmaId"), $card.data("lemmaName"));
        return false;
    });

});

function ajaxstatus($text, $option){
    $(".ajax_status_text").show();
    $(".ajax_status_text").removeClass("loading").removeClass("success").removeClass("error").addClass($option);
    $(".ajax_status_text").html('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+ "<strong>"+escapeHtml($text)+"</strong>");
}

function escapeHtml(value) {
    return String(value == null ? "" : value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function getSelectedLemmaId() {
    return $("#sensTableItems").find(".selected").attr("id");
}

function getSelectedLemmaRow() {
    return $("#sensTableItems").find(".selected");
}

function getLemmaNameFromRow($row) {
    var name = $row.find("td.sens_name").text();
    return $.trim(name || $row.find("td").eq(1).text());
}

function refreshLemmaTable() {
    if ($.fn.DataTable && $.fn.DataTable.isDataTable("#sensTable")) {
        $("#sensTable").DataTable().ajax.reload(null, false);
    } else {
        getWords();
    }
}

/*
Pobiera słowa i wyświetla je w tabeli sensTableItems.
*/
function getWords(){
    var success = function(data){
        var html = "";

        $.each(data, function(index, item){
            html += "<tr class='sensName' id='" + escapeHtml(item.id) + "'>";
            html += "<td>" + (index + 1) + "</td>";
            html += "<td class='sens_name'>" + escapeHtml(item.annotation_name) + "</td>";
            html += "</tr>";
        });

        $("#sensTableItems").html(html);
    };

    doAjax("sens_edit_get_words", {}, success);
}

/*
Pobiera sensy słów i wyświetla je w panelu sensDescriptionList.
*/
function getSens(button, sens_id, this_sens_name, show_ajax_status){
    var $button = $(button);
    $button.find("td.sens_name").append("<img class='ajax_indicator' src='gfx/ajax.gif'/>");
    $button.attr("disabled", "disabled");

    var success = function(data){
        var html = "";

        $.each(data, function(index, item){
            html += renderSenseItem(item, sens_id, this_sens_name);
        });

        if (!html) {
            html = "<div class='administration-empty-state'>No senses defined for this lemma.</div>";
        }

        $("#sensDescriptionList").html(html);
        $(".sensDescriptionCreate").attr("id", this_sens_name);
        $button.removeAttr("disabled");
        $(".ajax_indicator").remove();

        if(show_ajax_status){
            ajaxstatus("Editing lemma: " + this_sens_name, "success");
        }
    };

    var error = function(){
        $button.removeAttr("disabled");
        $(".ajax_indicator").remove();
        ajaxstatus("Błąd ładowania słowa: " + this_sens_name, "error");
    };

    doAjax("sens_edit_get_sens", {sens_id: sens_id}, success, error);
}

function renderSenseItem(item, lemmaId, lemmaName) {
    var senseName = item.value || "";
    var description = item.description || "";

    return "" +
        "<div class='administration-wsd-sense-card' data-sense-name='" + escapeHtml(senseName) + "' data-lemma-id='" + escapeHtml(lemmaId) + "' data-lemma-name='" + escapeHtml(lemmaName) + "'>" +
            "<div class='sensItemDescription administration-wsd-sense-summary'>" +
                "<div class='administration-wsd-sense-title'>" + escapeHtml(senseName) + "</div>" +
                "<div class='administration-description-preview' title='" + escapeHtml(description) + "'>" + escapeHtml(description || "No description") + "</div>" +
                "<button type='button' class='sensItemEdit btn btn-primary adminPanelButton' title='Edit description' aria-label='Edit description'><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span><span class='sr-only'>Edit description</span></button>" +
            "</div>" +
            "<div class='sensItemEditForm administration-wsd-sense-form' style='display:none;'>" +
                "<form>" +
                    "<div class='form-group'>" +
                        "<label>Lemma</label>" +
                        "<input class='form-control input-sm' type='text' value='" + escapeHtml(lemmaName) + "' disabled='disabled'/>" +
                    "</div>" +
                    "<div class='form-group'>" +
                        "<label>Description</label>" +
                        "<textarea class='form-control input-sm' rows='5' id='edit_text_area' name='sensDescriptionEdit'>" + escapeHtml(description) + "</textarea>" +
                        "<textarea id='hidden_text_area' style='display:none'>" + escapeHtml(description) + "</textarea>" +
                    "</div>" +
                    "<div class='administration-wsd-sense-actions'>" +
                        "<button type='button' class='btn btn-primary saveSens' name='saveSens'>Save</button> " +
                        "<button type='button' class='btn btn-default discardSens' name='discardSens' title='Without making changes'>Close</button> " +
                        "<button type='button' class='btn btn-danger deleteSens' name='deleteSens'>Delete</button>" +
                    "</div>" +
                "</form>" +
            "</div>" +
        "</div>";
}

/*
Okno do dodawania słów.
*/
function createWordDialog(){
    $("#create_lemma_modal").modal("show");

    $("#create_lemma_form").validate({
        rules: {
            create_lemma_word: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: "administration_validation",
                        type: "sens_edit",
                        mode: "create"
                    }
                }
            }
        },
        messages: {
            create_lemma_word: {
                required: "Lemma must have a name.",
                remote: "This lemma already exists"
            }
        }
    });

    $(".confirm_create_lemma").unbind("click").click(function() {
        if($("#create_lemma_form").valid()) {
            var data = {
                wordname: $("#create_lemma_word").val()
            };

            var success = function(){
                refreshLemmaTable();
                ajaxstatus("Added lemma: " + data.wordname, "success");
                $("#create_lemma_modal").modal("hide");
                $("#create_lemma_form")[0].reset();
            };

            var error = function(code){
                if(code === "ERROR_APPLICATION" || code === "ERROR_AUTHORIZATION"){
                    $("#create-word-form-error").html("Wystąpił błąd.");
                }
            };

            doAjax("sens_edit_add_word", data, success, error);
        }
    });
}

/*
Okno do edycji słów.
*/
function editWordDialog(name){
    $("#edit_lemma_form").validate({
        rules: {
            edit_lemma_word: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: "administration_validation",
                        type: "sens_edit",
                        id: function(){
                            return getSelectedLemmaId();
                        },
                        mode: "edit"
                    }
                }
            }
        },
        messages: {
            edit_lemma_word: {
                required: "Lemma must have a name.",
                remote: "This lemma already exists"
            }
        }
    });

    $("#edit_lemma_word").val(name);
    $("#edit_lemma_modal").modal("show");

    $(".confirm_edit_lemma").unbind("click").click(function() {
        if($("#edit_lemma_form").valid()) {
            var params = {
                oldwordname: name,
                newwordname: $("#edit_lemma_word").val(),
                id: getSelectedLemmaId()
            };

            var success = function(){
                refreshLemmaTable();
                ajaxstatus("Edited lemma: " + params.newwordname, "success");
                $(".sensEdit").hide();
                $(".sensDelete").hide();
                $("#sense_panel").hide();
                $("#sensDescriptionList").empty();
                $("#edit_lemma_modal").modal("hide");
            };

            var error = function(code){
                if(code === "ERROR_APPLICATION" || code === "ERROR_AUTHORIZATION"){
                    $("#edit-word-form-error").html("Wystąpił błąd.");
                }
            };

            doAjax("sens_edit_update_word", params, success, error);
        }
    });
}

/*
Okno do usuwania słów.
*/
function deleteWordDialog(name){
    $("#delete_lemma_word").val(name);
    $("#delete_lemma_word_preview").text(name);
    $("#delete_lemma_modal").modal("show");

    $(".confirm_delete_lemma").unbind("click").click(function() {
        var success = function(){
            $("#sense_panel").hide();
            $("#sensDescriptionList").empty();
            refreshLemmaTable();
            $(".sensEdit").hide();
            $(".sensDelete").hide();
            $("#delete_lemma_modal").modal("hide");
            ajaxstatus("Deleted lemma: " + name, "success");
        };

        var error = function(code){
            if(code === "ERROR_APPLICATION" || code === "ERROR_AUTHORIZATION"){
                $("#delete-word-form-error").html("Wystąpił błąd.");
            }
        };

        doAjax("sens_edit_delete_word", {name: name, id: getSelectedLemmaId()}, success, error);
    });
}

function createNewSens(name, id){
    $("#create_sens_form")[0].reset();
    $("#create_sens_modal").modal("show");
    $("#sens_name").text(name + "-");

    $("#create_sens_form").validate({
        rules: {
            create_sens_name: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: "administration_validation",
                        type: "new_sense",
                        name: name + "-",
                        id: id
                    }
                }
            }
        },
        messages: {
            create_sens_name: {
                required: "Sense must have a name.",
                remote: "This sense already exists."
            }
        }
    });

    $(".confirm_create_sens").unbind("click").click(function() {
        if($("#create_sens_form").valid()) {
            var sensnum = $("#sensnum").val();
            var params = {
                sensname: name,
                sensid: id,
                sensnum: sensnum,
                description: $("#create_sens_description").val()
            };

            var success = function(){
                ajaxstatus("Added sense: " + params.sensname + "-" + sensnum, "success");
                $("#create_sens_modal").modal("hide");
                getSens(getSelectedLemmaRow(), id, name, 0);
            };

            var error = function(code){
                $("#create_sens_modal").modal("hide");
                if(code === "ERROR_APPLICATION" || code === "ERROR_AUTHORIZATION"){
                    $("#create-sens-form-error").html("Wystąpił błąd.");
                }
            };

            doAjax("sens_edit_add_sens", params, success, error);
        }
    });
}

/*
Okno do usuwania sensów.
*/
function deleteSensDialog(name, sens_id, sens_name){
    $("#delete_sens_name_preview").text(name);
    $("#delete_sens_modal").modal("show");

    $(".confirm_delete_sens").unbind("click").click(function() {
        deleteSens(name, sens_id, sens_name);
    });
}

/*
Edycja sensów.
*/
function updateSens(save_button, description, sens_name){
    var $saveButton = $(save_button);
    $saveButton.after("<img class='ajax_indicator' src='gfx/ajax.gif'/>");
    $saveButton.attr("disabled", "disabled");

    var params = {
        name: sens_name,
        description: description,
        sens_name: sens_name
    };

    var success = function(){
        var $card = $(".administration-wsd-sense-card").filter(function(){
            return $(this).data("senseName") === sens_name;
        });

        $card.find("#hidden_text_area").val(description);
        $card.find(".administration-description-preview").attr("title", description).text(description || "No description");
        $card.find(".sensItemEditForm").hide();
        $card.removeClass("is-editing");
        ajaxstatus("Edited sense: " + sens_name, "success");
    };

    var error = function(code){
        ajaxstatus("Error sense: " + code, "error");
    };

    var complete = function(){
        $saveButton.removeAttr("disabled");
        $(".ajax_indicator").remove();
    };

    doAjax("sens_edit_update_sens", params, success, error, complete);
}

/*
Usuwanie sensów.
*/
function deleteSens(name, sens_id, sens_name){
    var success = function(){
        $("#delete_sens_modal").modal("hide");
        getSens(getSelectedLemmaRow(), sens_id, sens_name, 0);
        ajaxstatus("Deleted sense: " + name, "success");
    };

    var error = function(code){
        if(code === "ERROR_APPLICATION" || code === "ERROR_AUTHORIZATION"){
            $("#delete-sens-form-error").html("Wystąpił błąd.");
        }
    };

    doAjax("sens_edit_delete_sens", {name: name}, success, error);
}
