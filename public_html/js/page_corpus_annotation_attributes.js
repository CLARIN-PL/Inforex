/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
    setupAnnotationAttributeSelect();
    setupAttributeValueHover();
    setupAttributeValueClick();
    setupSearchAttributeValues();
});


function setupAnnotationAttributeSelect(){
    $("#annotation-attribute").select2();
};

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
}

function loadAnnotationsForAttributeValue(attribute_id, attribute_value, corpus_id){
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
            console.log(item);
        });
        console.log(html);
        $("#annotations tbody").html(html);
    };

    var complete = function(){
        $("#panelAnnotations").LoadingOverlay("hide");
    };

    var params = {attribute_id: attribute_id, attribute_value: attribute_value, corpus_id: corpus_id};

    $("#panelAnnotations").LoadingOverlay("show");

    doAjax("annotations_with_attribute_value_get", params, success, null, complete);
}