/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

var url = $.url(window.location.href);
var corpus_id = url.param('corpus');

$(document).ready(function(){
    //Changes the number of pages available in Datatables pagination
    // e.g. 1 ... 10 instead of 1,2,3,4,5 ... 10 when numbers_length = 3;
    $.fn.DataTable.ext.pager.numbers_length = 5;

    var userActivitiesTable = $('#user_activities').DataTable({
        "bInfo": false,
        "order": [[ 3, "desc" ]],
        "autoWidth": false,
        "columnDefs": [
            { "width": "30px", "targets": 0 },
            { "width": "auto", "targets": 1 },
            { "width": "34px", "targets": 2 },
            { "width": "106px", "targets": 3 },
            { "width": "40px", "targets": 4 },
            { "width": "38px", "targets": 5 },
            { "width": "30px", "targets": 6, "orderable": false }
        ],
        "columns": [
            null,
            null,
            null,
            null,
            null,
            null,
            { "orderable": false }
        ]
    });
    $("#user_activities").closest(".dataTables_wrapper").hide();

    var user_activity_table = $('#user_activity_table').DataTable({
        "bInfo": false,
        "autoWidth": false,
        "columnDefs": [
            { "width": "60%", "targets": 0 },
            { "width": "20%", "targets": 1 },
            { "width": "20%", "targets": 2 }
        ],
        "fnDrawCallback": function(){
            normalizeCorpusActivitiesDataTableFooter("#user_activity_table");
        }
    });
    $("#user_activity_table").closest(".dataTables_wrapper").hide();

    var user_activity_list_table = $('#user_activity_list_table').DataTable({
        "bInfo": false,
        "order": [[ 0, "desc" ]],
        "autoWidth": false,
        "columnDefs": [
            { "width": "132px", "targets": 0 },
            { "width": "34%", "targets": 1 },
            { "width": "34%", "targets": 2 },
            { "width": "58px", "targets": 3 }
        ],
        "fnDrawCallback": function(){
            normalizeCorpusActivitiesDataTableFooter("#user_activity_list_table");
        }
    });

    normalizeCorpusActivitiesDataTableFooter("#user_activities");
    normalizeCorpusActivitiesDataTableFooter("#user_activity_table");
    normalizeCorpusActivitiesDataTableFooter("#user_activity_list_table");
    getCorpusUserActivities(userActivitiesTable);

    $("#user_activities").on("click", "tbody > tr",function(element){
        if(!$(element.target).closest(".browse_user_activity").length) {
            if (!$(this).hasClass("selected")) {
                $("#user_activities tbody > tr").removeClass("selected");
                $(this).addClass("selected");
            }

            $(".user_activities_details").show();
            $(".corpus-settings-activities-summary-loading").show();
            $("#user_activity_table").closest(".dataTables_wrapper").hide();
            var user_id = $(this).find("td:first").text();
            getUserActivitySummary(user_id, user_activity_table);
            autoreizeFitToScreen();
        }
    });

    $("#user_activities").on("click", ".browse_user_activity", function(){
        var user_id = $(this).attr('id');
        $(".activity_list_hidden").hide();
        $("#activity_list_modal").modal("show");
        $(".loader").show();
        getUserActivityList(user_id, user_activity_list_table);

    });
});

function getCorpusUserActivities(userActivitiesTable){
    var data = {
        'corpus_id': corpus_id
    };

    var success = function(response){
        var table_rows = [];
        $.each(response, function(index, value){
            table_rows.push([
                value.user_id,
                corpusActivityEscapeHtml(value.login),
                corpusActivityUserCell(value.screename),
                corpusActivityTimestampCell(value.last_activity),
                value.num_of_activities_30,
                value.num_of_activities,
                corpusActivityListButton(value.user_id)
            ]);
        });

        userActivitiesTable.clear();
        userActivitiesTable.rows.add(table_rows).draw();
        userActivitiesTable.columns.adjust();
        $(".corpus-settings-activities-main-loading").hide();
        $("#user_activities").closest(".dataTables_wrapper").show();
        normalizeCorpusActivitiesDataTableFooter("#user_activities");
    };

    $(".corpus-settings-activities-main-loading").show();
    doAjax("corpus_user_activity", data, success);
}

function getUserActivityList(user_id, user_activity_table){
    user_activity_table.clear();

    var data = {
        'user_id': user_id,
        'mode': 'list',
        'type': 'corpus',
        'corpus_id': corpus_id
    };

    var success = function(response){
        var table_rows = [];
        $.each(response, function(index, value){
            var row = [corpusActivityTimestampCell(value.datetime), value.name, value.corpus, value.report_id];
            table_rows.push(row);
        });

        user_activity_table.rows.add(table_rows).draw();
        user_activity_table.columns.adjust();
        $(".loader").hide();
        $(".activity_list_hidden").show();
        normalizeCorpusActivitiesDataTableFooter("#user_activity_list_table");

    };

    doAjax("user_activity_summary", data, success);
}

function getUserActivitySummary(user_id, user_activity_table){
    user_activity_table.clear().draw();

    var data = {
        'user_id': user_id,
        'mode': 'summary',
        'type': 'corpus',
        'corpus_id': corpus_id
    };

    var success = function(response){
        var table_rows = [];
        $.each(response, function(index, value){
            var row = [value.name, value.num_of_activities, value.num_last_30];
            table_rows.push(row);
        });

        user_activity_table.rows.add(table_rows).draw();
        user_activity_table.columns.adjust();
        $(".corpus-settings-activities-summary-loading").hide();
        $("#user_activity_table").closest(".dataTables_wrapper").show();
        normalizeCorpusActivitiesDataTableFooter("#user_activity_table");
    };

    doAjax("user_activity_summary", data, success);

}

function corpusActivityUserCell(name){
    var safeName = corpusActivityEscapeHtml(name || "");

    return '<span class="administration-owner-initials administration-activities-user-initials corpus-settings-activity-user" title="' + safeName + '">' +
        corpusActivityUserInitials(name) +
        '</span>';
}

function corpusActivityUserInitials(name){
    var words = $.trim(name || "").split(/\s+/);

    if (!words.length || words[0] === "") {
        return "?";
    }

    if (words.length === 1) {
        return words[0].substr(0, 2).toUpperCase();
    }

    return (words[0].charAt(0) + words[words.length - 1].charAt(0)).toUpperCase();
}

function corpusActivityListButton(user_id){
    return '<button type="button" class="browse_user_activity btn btn-primary corpus-settings-activity-list-button" id="' + corpusActivityEscapeHtml(user_id) + '" title="Show recent activity list">' +
        '<i class="fa fa-list" aria-hidden="true"></i><span class="sr-only">Recent activity list</span>' +
        '</button>';
}

function corpusActivityTimestampCell(timestamp){
    var safeTimestamp = corpusActivityEscapeHtml(timestamp || "");
    var parts;
    var date;
    var time;

    if (!timestamp) {
        return '<span class="administration-activities-time-empty">No activity</span>';
    }

    parts = String(timestamp).split(" ");
    date = parts[0] || timestamp;
    time = parts[1] ? parts[1].substr(0, 5) : "";

    return '<span class="administration-activities-time" title="' + safeTimestamp + '">' +
        '<i class="fa fa-clock-o" aria-hidden="true"></i>' +
        '<span>' + corpusActivityEscapeHtml(date) + '</span>' +
        (time ? '<small>' + corpusActivityEscapeHtml(time) + '</small>' : '') +
        '</span>';
}

function corpusActivityEscapeHtml(value) {
    return String(value == null ? "" : value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function normalizeCorpusActivitiesDataTableFooter(tableSelector){
    var $wrapper = $(tableSelector).closest(".dataTables_wrapper");
    var $info = $wrapper.find(".dataTables_info");
    var $pagination = $wrapper.find(".dataTables_paginate");
    var $footer;

    if (!$wrapper.length || (!$info.length && !$pagination.length)) {
        return;
    }

    $footer = $wrapper.find(".administration-activities-datatables-footer");

    if (!$footer.length) {
        $footer = $("<div class='administration-activities-datatables-footer'></div>");
        $wrapper.append($footer);
    }

    if ($info.length) {
        $footer.append($info);
    }

    if ($pagination.length) {
        $footer.append($pagination);
    }
}
