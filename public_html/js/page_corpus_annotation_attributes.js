/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
    setupAttributeValueHover();
    setupAttributeValueClick();
    setupSearchAttributeValues();
    setupDownloadButton();
});

function setupAttributeValueHover(){
    $("#attribute-values tbody tr").hover(function(){
        $("#attribute-values tbody tr.highlighted").removeClass("highlighted");
        $(this).addClass("highlighted");
    });
};

function setupAttributeValueClick(){
    $("#attribute-values tbody tr").click(function(){
        $("#attribute-values tbody tr.selected").removeClass("selected");
        $(this).addClass("selected");
        var attribute_value = $(this).find("td.value").text();
        var attribute_id = getUrlParameter("attribute_id");
        var corpus_id = getUrlParameter("corpus");
        loadAnnotationsForAttributeValue(attribute_id, attribute_value, corpus_id);
    });
};

function setupSearchAttributeValues(){
    $("input[name=search]").keyup(function () {
        var data = this.value.toLowerCase();
        var table = $("#attribute-values");
        $(table).find("tbody tr").each(function (index, row) {
            var text = $(row).text().toLowerCase();
            if (text.indexOf(data) >= 0 || this.value == "") {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
};

function setupDownloadButton(){
    $("#download-attribute-values").click(function(){
        window.location.href=window.location.href.replace("page=corpus_annotation_attributes", "page=corpus_annotation_attributes_export");
    });
};

function loadAnnotationsForAttributeValue(attribute_id, attribute_value, corpus_id){
    $("#annotations-loading").show();
    $("#annotations").closest(".corpus-annotation-attributes-table-wrapper").hide();

    var success = function(data){
        var html = "";
        $.each(data, function(index,item){
            html += "<tr>";
            html += sprintf("<td>%s</td>", item.id);
            html += sprintf("<td>%s</td>", item.type);
            html += sprintf("<td>%s</td>", item.text);
            html += sprintf("<td>%s</td>", item.lemma);
            html += sprintf("<td><a href='index.php?page=report&id=%s' target='_blank'>%s</a></td>", item.report_id, item.report_id);
            html += "</tr>";
        });
        if (!html) {
            html = "<tr class='corpus-annotation-attributes-empty-row'><td colspan='5'><div class='corpus-annotation-attributes-empty'>No annotations found for the selected value</div></td></tr>";
        }
        $("#annotations tbody").html(html);
    };

    var complete = function(){
        $("#annotations-loading").hide();
        $("#annotations").closest(".corpus-annotation-attributes-table-wrapper").show();
    };

    var params = {
        attribute_id: attribute_id,
        attribute_value: attribute_value,
        corpus_id: corpus_id};

    params['language'] = $("#annotation-language option:selected").val();
    params['subcorpus_id'] = $("#annotation-subcorpus option:selected").val();

    doAjax("annotations_with_attribute_value_get", params, success, null, complete);
}
