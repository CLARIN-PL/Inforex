/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(document).ready(function(){
    //Changes the number of pages available in Datatables pagination
    // e.g. 1 ... 10 instead of 1,2,3,4,5 ... 10 when numbers_length = 3;
    $.fn.DataTable.ext.pager.numbers_length = 5;

	var userActivitiesTable = $('#user_activities').DataTable({
        "bInfo": false,
        "order": [[ 3, "desc" ]],
        "autoWidth": false,
        "columnDefs": [
            { "width": "34px", "targets": 0 },
            { "width": "32%", "targets": 1 },
            { "width": "50px", "targets": 2 },
            { "width": "118px", "targets": 3 },
            { "width": "58px", "targets": 4 },
            { "width": "58px", "targets": 5 }
        ]
    });
    $("#user_activities").closest(".dataTables_wrapper").hide();

	    var user_activity_table = $('#user_activity_table').DataTable({
	        "bInfo": false,
	        "order": [[ 0, "desc" ]],
	        "autoWidth": false,
        "columnDefs": [
            { "width": "118px", "targets": 0 },
            { "width": "32%", "targets": 1 },
            { "width": "32%", "targets": 2 },
            { "width": "58px", "targets": 3 }
        ],
        "fnDrawCallback": function(){
            normalizeActivitiesDataTableFooter("#user_activity_table");
        }
    });

    getUserActivities(userActivitiesTable);
    normalizeActivitiesDataTableFooter("#user_activities");
    normalizeActivitiesDataTableFooter("#user_activity_table");

    $("#user_activities").on("click", "tbody > tr",function(){
        if (!$(this).hasClass("selected")) {
            $("#user_activities tbody > tr").removeClass("selected");
            $(this).addClass("selected");
        }

        $(".user_activities_details").show();
        $(".administration-activities-loading").show();
        var user_id = $(this).find("td:first").text();
        getUserActivityList(user_id, user_activity_table);
        autoreizeFitToScreen();
    });
});

function getUserActivities(userActivitiesTable){
    var success = function(response){
        var table_rows = [];
        $.each(response, function(index, value){
            table_rows.push([
                value.user_id,
                value.login,
                activityUserCell(value.screename),
                activityTimestampCell(value.last_activity),
                value.num_of_activities_30,
                value.num_of_activities
            ]);
        });

        userActivitiesTable.clear();
        userActivitiesTable.rows.add(table_rows).draw();
        userActivitiesTable.columns.adjust();
        $(".administration-activities-main-loading").hide();
        $("#user_activities").closest(".dataTables_wrapper").show();
        normalizeActivitiesDataTableFooter("#user_activities");
    };

    doAjax("administration_activities", {}, success);
}

function activityUserCell(name){
    var safeName = escapeHtml(name || "");
    return '<span class="administration-owner-initials administration-activities-user-initials" title="' + safeName + '">' + activityUserInitials(name) + '</span>';
}

function activityUserInitials(name){
    var words = $.trim(name || "").split(/\s+/);

    if (!words.length || words[0] === "") {
        return "?";
    }

    if (words.length === 1) {
        return words[0].substr(0, 2).toUpperCase();
    }

    return (words[0].charAt(0) + words[words.length - 1].charAt(0)).toUpperCase();
}

function activityTimestampCell(timestamp){
    var safeTimestamp = escapeHtml(timestamp || "");

    if (!timestamp) {
        return '<span class="administration-activities-time-empty">No activity</span>';
    }

    var parts = String(timestamp).split(" ");
    var date = parts[0] || timestamp;
    var time = parts[1] ? parts[1].substr(0, 5) : "";

    return '<span class="administration-activities-time" title="' + safeTimestamp + '">' +
        '<i class="fa fa-clock-o" aria-hidden="true"></i>' +
        '<span>' + escapeHtml(date) + '</span>' +
        (time ? '<small>' + escapeHtml(time) + '</small>' : '') +
        '</span>';
}

function escapeHtml(value) {
    return String(value == null ? "" : value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function getUserActivityList(user_id, user_activity_table){
    user_activity_table.clear().draw();

    var data = {
        'user_id': user_id,
        'mode': 'list',
        'type': 'all'
    };

    var success = function(response){
        var table_rows = [];
        $.each(response, function(index, value){
            var row = [activityTimestampCell(value.datetime), value.name, value.corpus, value.report_id];
            table_rows.push(row);
        });

        user_activity_table.rows.add(table_rows).draw();
        user_activity_table.columns.adjust();
        $(".administration-activities-loading").hide();
        normalizeActivitiesDataTableFooter("#user_activity_table");

    };

    doAjax("user_activity_summary", data, success);
}

function normalizeActivitiesDataTableFooter(tableSelector){
    var $wrapper = $(tableSelector).closest(".dataTables_wrapper");
    var $info = $wrapper.find(".dataTables_info");
    var $pagination = $wrapper.find(".dataTables_paginate");

    if (!$wrapper.length || (!$info.length && !$pagination.length)) {
        return;
    }

    var $footer = $wrapper.find(".administration-activities-datatables-footer");

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
