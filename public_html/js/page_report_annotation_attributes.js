/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(document).ready(function(){

	setupAnnotationTypeTree();
	setupAnnotationMode();

	assignAnnotationHighlight();
	assignAttributeEdit();
	assignAttributeSave();
	assignButtonApplyClick()
    assingButtonSaveAllClick();
});

function assignAnnotationHighlight(){
    $("tr.annotation").hover(function(){
        var annotationId = $(this).attr("annotation_id");
        $(".contentBox span.selected").removeClass("selected");
        $(".contentBox span#an" + annotationId).addClass("selected");
    });
}

function assignButtonApplyClick(){
    $("#apply").click(function(e){
        // Store the selection of annotation types, sets and subsets to the cookie
        applyAnnotationTypeTree(function(ann_layers, ann_subsets, ann_types){});
    });

};

function assingButtonSaveAllClick(){
    $("#save_all").click(function(){
        $("tr.attribute").each(function(index,item){
            var attributeRow = $(item);
            var currentValue = attributeRow.find("select.shared_attribute").val();
            var savedValue = attributeRow.attr("saved_value");
            if ( currentValue != savedValue ){
                attributeRow.find("a.save_attribute_value").click();
            }
        });
    });
}

function assignAttributeSave(){
    $(".save_attribute_value").click(function(){
        var that = $(this);
        var attributeRow = $(this).parents(".attribute");
        var annotationRow = $(this).parents(".annotation");
        var annotationId = annotationRow.attr("annotation_id");
        var attributeId = attributeRow.attr("attribute_id");
        var value = attributeRow.find("select").val();

        var success = function(data){
            attributeRow.attr("saved_value", data.value)
            setStatus(that, "saved", "green");
        };

        var complete = function(data){
            updateSaveButtonStatus();
        };

        var params = {
            annotation_id : annotationId,
            shared_attribute_id : attributeId,
            value : value
        };

        setStatus(that, "saving...", "blue");

        doAjax("annotation_shared_attribute_update", params, success, null, complete, attributeRow.find(".actions a"));

    });
}

function assignAttributeEdit(){
    $('select.shared_attribute').select2({
        tags: true,
        allowClear: true,
        width: '100%',
        placeholder: "Search for a value",
        templateResult: formatWidgetAnnotationAttributeValue,
        createTag: function (params) {
            var term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true // add additional parameters
            }
        },
        insertTag: function (data, tag) {
            data.unshift(tag);
        },
        ajax: {
            url: 'index.php',
            type: "post",
            data: function (params) {
                var query = {
                    annotation_id: this.attr("annotation_id"),
                    attribute_id: this.attr("shared_attribute_id"),
                    search: params.term,
                    type: 'public',
                    ajax: 'annotation_shared_attribute_values',
                    page: params.page || 1
                };
                // Query parameters will be ?search=[term]&type=public
                return query;
            },
            processResults: function (data) {
                return {
                    results: data.results,
                    pagination: {
                        "more": data.pagination.more
                    }
                };
            }
        }
    });
    $('select.shared_attribute').on("select2:select", function (e) {
        updateStatus($(this));
        updateSaveButtonStatus();
    });
};

/**
 * Update status for the given input.
 * @param input A reference to an input.lemma_text element.
 */
function updateStatus(input){
    var annotationRow = $(input).parents("tr.attribute");
    var currentValue = annotationRow.find("select.shared_attribute").val();
    var savedValue = annotationRow.attr("saved_value");

    var status = $(input).find("td.status").text();
    if ( currentValue == savedValue ){
        if ( status != "saved" ) {
            setStatus(input, "no change", "#999");
        }
    } else {
        setStatus(input, "changed", "#aa0000");
    }
}

/**
 * Set status for the given input.
 * @param input A reference to an input.lemma_text element.
 * @param status Name of the new status.
 * @param color Text color for the status.
 */
function setStatus(input, status, color){
    $(input).closest("tr").find(".status").html("<span style='color:"+color+"'>"+status+"<span>");
}

function updateSaveButtonStatus(){
    var changed = false;
    $("tr.attribute").each(function(index,item){
        var annotationRow = $(item);
        var currentValue = annotationRow.find("select.shared_attribute").val();
        var savedValue = annotationRow.attr("saved_value");
        if ( currentValue != savedValue ){
            changed = true;
        }
    });
    var button = $("#save_all");
    if ( changed ){
        button.removeClass("btn-default");
        button.addClass("btn-danger");
    } else {
        button.addClass("btn-default");
        button.removeClass("btn-danger");
    }
}