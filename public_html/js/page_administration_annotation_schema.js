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

function onCheckClickAnnotationSets(ele, value, rowNumber) {

    let table = $('#annotationSubsetsTable').data('table');
    let tableTypes = $('#annotationTypesTable').data('table');

    let _data = {
        parent_id: rowNumber[0],
        parent_type: 'annotation_set'
    };

    let success = function (data) {
        table.items = [];
        tableTypes.items = [];
        tableTypes.reload();

        for (let key in data) {
            if (data.hasOwnProperty(key)) {
                table.addItem([data[key].id, data[key].name, data[key].description]);
            }
        }
        table.reload();
    };
    let login = function (data) {
        get(ele);
    };
    doAjaxSyncWithLogin("annotation_edit_get", _data, success, login);

}

function onCheckClickAnnotationSubsets(ele, value, rowNumber) {

    let table = $('#annotationTypesTable').data('table');

    let _data = {
        parent_id: rowNumber[0],
        parent_type: 'annotation_subset'
    };

    let success = function (data) {
        table.items = [];
        for (let key in data) {
            if (data.hasOwnProperty(key)) {
                table.addItem(
                    [data[key].id, data[key].name, data[key].short, data[key].description,
                        value.shortlist===0 ? "Visible" : "Hidden"
                ]);
            }
        }
        table.reload();
    };
    let login = function (data) {
        get(ele);
    };
    doAjaxSyncWithLogin("annotation_edit_get", _data, success, login);

}

$(document).ready(function () {
});