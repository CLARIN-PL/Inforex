/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(document).ready(function () {
    onTableRowMouseEnter();

    //onTableRowSelectClick();

    $('.selectpicker').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let value = $(this).val();
        let row =  $(this).parents("tr");
        saveFinalAttribute(value, row);
    });

    $('.removeFinalAttribute').click(function () {
        removeFinalAttribute($(this));
    });

    $('.acceptFinalAnnotation').click(function () {
        let row =  $(this).parents("tr");
        let value = row.find(".selectpicker").val();
        saveFinalAttribute(value, row);
    });

});

function removeFinalAttribute(obj){
    let row =  obj.parents("tr");
    let btn = row.find(".removeFinalAttribute").parent("span");
    let annotation_id = row.attr("ann_id");

    let params = {
        annotation_id: annotation_id,
        stage: 'final',
    };

    let success = function (data) {
        btn.attr("style", "display: none;");
        row.find(".selectpicker").selectpicker('val', '-');
    };

    let error = function () {
        console.log('Error unable to save attribute')
    };

    doAjax("report_delete_wsd_annotation", params, success, error);
}

function saveFinalAttribute(value, row){
    let annotation_id = row.attr("ann_id");
    if (value !== '') {
        let params = {
            annotation_id: annotation_id,
            stage: 'final',
            value: value
        };

        let success = function (data) {
            row.find(".acceptFinalAnnotation").parent("span").attr("style", "display: none;");
            row.find(".removeFinalAttribute").parent("span").attr("style", "display: inline;");
        };

        let error = function () {
            console.log('error')
        };

        doAjax("report_update_wsd_annotation", params, success, error);
    }
}
function onTableRowSelectClick() {
    $("#agreement table tr").click(function () {
        $("#agreement table tr.selected").removeClass("selected");
        $(this).addClass("selected");
        highlight_text(
            parseInt($(this).children("td.from").text()),
            parseInt($(this).children("td.to").text()), "selected");
        $("#content").animate({
            scrollTop: $("#content").scrollTop() + $("#content span.token" + parseInt($(this).children("td.from").text())).position().top - 60
        }, 500);
    });
}

function onTableRowMouseEnter() {
    $("#agreement table tr").mouseenter(function () {
        highlight_text(
            parseInt($(this).children("td.from").text()),
            parseInt($(this).children("td.to").text()), "highlight");
    });
}

function highlight_text(begin, end, cl) {
    $("#content span." + cl).removeClass(cl);
    for (let i = begin; i <= end; i++) {
        $("#content span.token" + i).addClass(cl);
    }
}
