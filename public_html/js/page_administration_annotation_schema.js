/**
 * Part of the Inforex project
 * Copyright (c) 2020
 * Code licensed under the GNU LGPL http://www.gnu.org/licenses/
 * Wroclaw University of Science and Technology
 */
var annotationSetsButtons = [
    {
        html: "<span class='mif-cog'></span>",
        cls: "sys-button",
        onclick: "$('#annotationSetsTable').data('table').toggleInspector()"
    }
];
var annotationSubsetsButtons = [
    {
        html: "<span class='mif-cog'></span>",
        cls: "sys-button",
        onclick: "$('#annotationSubsetsTable').data('table').toggleInspector()"
    }
];
var annotationTypesButtons = [
    {
        html: "<span class='mif-cog'></span>",
        cls: "sys-button",
        onclick: "$('#annotationTypesTable').data('table').toggleInspector()"
    }
];

function onCheckClickAnnotationSets(ele, value, rowNumber){
    data = '"data": [ 1, \"test\", \"Tesowy wpis\"]';
    console.log($('#annotationSetsTable').data('table').getSelectedItems());
    $('#annotationSubsetsTable').data('table')
}

$(document).ready(function () {
});