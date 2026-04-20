/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

var url = $.url(window.location.href);
var corpus_id = url.param('corpus');
var data;
var chart;

//Load Google charts API
google.charts.load('current', {'packages':['corechart']});
// Callback that creates and populates a data table,

$(document).ready(function(){
    //Changes the number of pages available in Datatables pagination
    // e.g. 1 ... 10 instead of 1,2,3,4,5 ... 10 when numbers_length = 3;
    $.fn.DataTable.ext.pager.numbers_length = 5;

    var year_table = $('#user_activities_year').DataTable({
        "bInfo": false,
        "bFilter": false,
        "bLengthChange": false,
        'aaSorting': [],
        "autoWidth": false,
        "columnDefs": [
            { "width": "30%", "targets": 0 },
            { "width": "35%", "targets": 1 },
            { "width": "35%", "targets": 2 }
        ],
        "fnDrawCallback": function(){
            normalizeActivitiesDataTableFooter("#user_activities_year");
        }
    });
    $("#user_activities_year").closest(".dataTables_wrapper").hide();

    var month_year_table = $('#user_activities_year_month').DataTable({
        "bInfo": false,
	        "bFilter": false,
	        "bLengthChange": false,
	        "bPaginate": false,
	        "pageLength": 12,
	        'aaSorting': [],
	        "autoWidth": false,
	        "columnDefs": [
	            { "width": "30%", "targets": 0 },
	            { "width": "35%", "targets": 1 },
	            { "width": "35%", "targets": 2 }
	        ]
	    });

    var activity_table = $('#user_activities_details').DataTable({
        "bInfo": false,
        "order": [[ 0, "desc" ]],
        "autoWidth": false,
        "columnDefs": [
            { "width": "118px", "targets": 0 },
            { "width": "48%", "targets": 1 },
            { "width": "32%", "targets": 2 }
        ],
        "fnDrawCallback": function(){
            normalizeActivitiesDataTableFooter("#user_activities_details");
        }
    });

    loadAnonymousActivityYears(year_table);

    $("#user_activities_year").on("click", "tbody > tr",function(){
        var year = $(this).children("td:eq(0)").text();

        year_table.rows().every(function() {
           $(this.node()).removeClass("selected");
        });

        $(this).addClass("selected");
        activity_table.clear().draw();
        $(".activity_list_hidden").hide();
        $(".user_activities_months").show();
        $(".administration-activities-month-loading").show();
        $("#user_activities_year_month").closest(".dataTables_wrapper").hide();
        loadAnonymousActivityMonths(month_year_table, year);
    });

    $("#user_activities_year_month").on("click", "tbody > tr",function(){
        var year = $("#user_activities_year tbody > tr.selected").children("td:eq(0)").text();
        var month = $(this).children("td:eq(0)").text();

        month_year_table.rows().every(function() {
           $(this.node()).removeClass("selected");
        });

        $(this).addClass("selected");
        $(".activity_list_hidden").show();
        $(".administration-activities-detail-loading").show();
        $("#user_activities_details").closest(".dataTables_wrapper").hide();
        getActivityList(activity_table, year, month);
    });

    $("#select_month_value, #select_year").change(function(){
        var selected_value = $("#select_month_value").val();
        var selected_year = $("#select_year").val();
        setAnonymousChartLoading("month");
        google.charts.setOnLoadCallback(showYearMonthChart(selected_value, selected_year));

    });

    $("#select_year_value").change(function(){
        var selected_value = $("#select_year_value").val();
        setAnonymousChartLoading("year");
        google.charts.setOnLoadCallback(showYearChart(selected_value));

    });

    //Gets possible year values for year select
    $('#activity_year_month_modal').on('shown.bs.modal', function () {
        var selected_value = $("#select_month_value").val();
        var selected_year = $("#select_year").val();
        setAnonymousChartLoading("month");
        google.charts.setOnLoadCallback(showYearMonthChart(selected_value, selected_year));
    });

    $('#activity_year_modal').on('shown.bs.modal', function () {
        var selected_value = $("#select_year_value").val();
        setAnonymousChartLoading("year");
        google.charts.setOnLoadCallback(showYearChart(selected_value));
    });
});

function setAnonymousChartLoading(chartType){
    var loaderSelector = chartType === "year" ? ".activity_year_loader" : ".activity_year_month_loader";
    var chartSelector = chartType === "year" ? "#year_chart_div" : "#year_month_chart_div";
    var titleSelector = chartType === "year" ? "#year_chart_title" : "#year_month_chart_title";

    $(chartSelector).hide().empty();
    $(titleSelector).hide().text("");
    $(loaderSelector).show();
}

function loadAnonymousActivityYears(year_table){
    var success = function(response){
        var yearRows = [];
        var yearOptions = "";

        $.each(response.years, function(index, value){
            yearRows.push([value.year, value.number_of_activities, value.number_of_users]);
            yearOptions += "<option value='" + value.year + "'>" + value.year + "</option>";
        });

        year_table.clear().rows.add(yearRows).draw();
        year_table.columns.adjust();
        $("#select_year").html(yearOptions);
        $(".administration-activities-year-loading").hide();
        $("#user_activities_year").closest(".dataTables_wrapper").show();
        normalizeActivitiesDataTableFooter("#user_activities_year");
    };

    doAjax("anonymous_user_activity", {'mode': 'initial_summary'}, success);
}

function loadAnonymousActivityMonths(month_year_table, year){
    month_year_table.clear().draw();

    var success = function(response){
        var monthRows = [];

	        $.each(response, function(index, value){
	            monthRows.push([value.month, value.number_of_activities, value.number_of_users]);
	        });

        month_year_table.rows.add(monthRows).draw();
        month_year_table.columns.adjust();
        $(".administration-activities-month-loading").hide();
        $("#user_activities_year_month").closest(".dataTables_wrapper").show();
        normalizeActivitiesDataTableFooter("#user_activities_year_month");
    };

    doAjax("anonymous_user_activity", {'mode': 'year_month_summary', 'year': year}, success);
}

function showYearChart(mode){
    var data = {
        'mode': 'year_summary'
    };

    var success = function(response){
        var chart_rows = [['Year', mode]];
        $.each(response, function(index, value){
            var row = [value.year, parseInt(mode == "Activities" ? value.number_of_activities : value.number_of_users)];
            chart_rows.push(row);
        });

        var chart_data = google.visualization.arrayToDataTable(chart_rows);

        var options = {
            legend: {
                position: 'top',
                alignment: 'center'
            },
            chartArea: {
                left: 88,
                top: 36,
                width: '76%',
                height: '62%'
            },
            hAxis: {
                title: 'Year'
            },
            vAxis: {
                format: 'decimal',
                title: getAnonymousActivityAxisTitle(mode),
                textStyle: {
                    fontSize: 10
                },
                titleTextStyle: {
                    fontSize: 11
                }
            },
            colors: ['#428bca', '#d95f02', '#7570b3']
        };

        $(".activity_year_loader").hide();
        $("#year_chart_title").text("Anonymous activity by year").show();
        $("#year_chart_div").show();

        var chart = new google.visualization.ColumnChart(document.getElementById('year_chart_div'));

        chart.draw(chart_data, options);
    };

    doAjax("anonymous_user_activity", data, success);

}

function showYearMonthChart(type, year){

    var data = {
        'mode': 'year_month_summary_chart',
        'year': year
    };

    var success = function(response){
        var chart_rows = [['Year ' + year, type]];
        $.each(response, function(index, value){
            var row = [value.month, parseInt(type == "Activities" ? value.number_of_activities : value.number_of_users)];
            chart_rows.push(row);
        });

        var chart_data = google.visualization.arrayToDataTable(chart_rows);

        var options = {
            legend: {
                position: 'top',
                alignment: 'center'
            },
            chartArea: {
                left: 88,
                top: 36,
                width: '76%',
                height: '62%'
            },
            hAxis: {
                title: 'Month'
            },
            vAxis: {
                format: 'decimal',
                title: getAnonymousActivityAxisTitle(type),
                textStyle: {
                    fontSize: 10
                },
                titleTextStyle: {
                    fontSize: 11
                }
            },
            colors: ['#428bca', '#d95f02', '#7570b3']
        };

        $(".activity_year_month_loader").hide();
        $("#year_month_chart_title").text("Anonymous activity by month").show();
        $("#year_month_chart_div").show();

        var chart = new google.visualization.ColumnChart(document.getElementById('year_month_chart_div'));

        chart.draw(chart_data, options);
    };

    doAjax("anonymous_user_activity", data, success);

	}

function getAnonymousActivityAxisTitle(metric){
    return metric === "Users" ? "Unique users" : "Number of activities";
}

function getActivityList(activity_table, year, month){
    activity_table.clear();

    var data = {
        'year': year,
        'month': month,
        'mode': 'activity_list'
    };

    var success = function(response){
        var table_rows = [];
        $.each(response, function(index, value){
            var row = [activityTimestampCell(value.date), value.name, value.ip];
            table_rows.push(row);
        });

        activity_table.rows.add(table_rows).draw();
        activity_table.columns.adjust();
        $(".administration-activities-detail-loading").hide();
        $("#user_activities_details").closest(".dataTables_wrapper").show();
        normalizeActivitiesDataTableFooter("#user_activities_details");
        autoreizeFitToScreen();

    };

    doAjax("anonymous_user_activity", data, success);
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
