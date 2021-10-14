/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(document).ready(function () {
    onTableRowMouseEnter();
    //onTableRowSelectClick();

    $('.selectpicker').on('shown.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        let row = $(this).parents("tr");
        let annId = row.attr("ann_id");
        load_arguments_by_annotation_id(annId, row);
    });

});

function load_arguments_by_annotation_id(annotation_id, row) {

    wsd_loading = true;
    current_annotation_id = annotation_id;

    let params = {
        annotation_id: annotation_id
    };

    let success = function (data) {
        let select = row.find("select");
        for (let a in data.values) {
            let v = data.values[a];
            $("<option></option>",{
                value: v.value,
                text: v.value}).appendTo(select);
        }
        console.log(select);
    }
    wsd_loading = false;

    let error = function () {
        wsd_loading = false;
    };

    doAjax("report_get_wsd_annotation", params, success, error);
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
