/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(document).ready(function () {
    onTableRowMouseEnter();
    //onTableRowSelectClick();
});

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
