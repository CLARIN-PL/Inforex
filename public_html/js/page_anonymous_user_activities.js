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
google.charts.load('current', {'packages':['bar']});
// Callback that creates and populates a data table,

$(document).ready(function(){
    //Changes the number of pages available in Datatables pagination
    // e.g. 1 ... 10 instead of 1,2,3,4,5 ... 10 when numbers_length = 3;
    $.fn.DataTable.ext.pager.numbers_length = 5;

    $('#user_activities_year').DataTable({
        "bInfo": false,
        'aaSorting': []
    });

    var month_year_table = $('#user_activities_year_month').DataTable({
        "bInfo": false,
        'aaSorting': []
    });

    var activity_table = $('#user_activities_details').DataTable({
        "bInfo": false,
        'aaSorting': []
    });

    $("#user_activities_year_month").on("click", "tbody > tr",function(){
        var year = $(this).children("td:eq(0)").text();
        var month = $(this).children("td:eq(1)").text();

        month_year_table.rows().every(function() {
           $(this.node()).removeClass("selected");
        });

        $(this).addClass("selected");


        getActivityList(activity_table, year, month);
    });

    $("#select_month_value, #select_year").change(function(){
        var selected_value = $("#select_month_value").val();
        var selected_year = $("#select_year").val();
        google.charts.setOnLoadCallback(showYearMonthChart(selected_value, selected_year));

    });

    $("#select_year_value").change(function(){
        var selected_value = $("#select_year_value").val();
        google.charts.setOnLoadCallback(showYearChart(selected_value));

    });

    //Gets possible year values for year select
    $('#activity_year_month_modal').on('shown.bs.modal', function () {
        var selected_value = $("#select_month_value").val();
        var selected_year = $("#select_year").val();
        google.charts.setOnLoadCallback(showYearMonthChart(selected_value, selected_year));
    });

    $('#activity_year_modal').on('shown.bs.modal', function () {
        var selected_value = $("#select_year_value").val();
        google.charts.setOnLoadCallback(showYearChart(selected_value));
    });
});

function showYearChart(mode){

    console.log(mode);
    var data = {
        'mode': 'year_summary'
    };

    var success = function(response){
        console.log(response);

        var chart_rows = [['Year', mode]];
        $.each(response, function(index, value){
            var row = [value.year, parseInt(mode == "Activities" ? value.number_of_activities : value.number_of_users)];
            chart_rows.push(row);
        });

        var chart_data = google.visualization.arrayToDataTable(chart_rows);

        var options = {
            bars: 'vertical',
            vAxis: {format: 'decimal'},
            colors: ['#428bca', '#d95f02', '#7570b3']
        };

        var chart = new google.charts.Bar(document.getElementById('year_chart_div'));

        chart.draw(chart_data, google.charts.Bar.convertOptions(options));
        $(".activity_year_loader").hide();
    };

    doAjax("anonymous_user_activity", data, success);

}

function showYearMonthChart(type, year){

    var data = {
        'mode': 'year_month_summary_chart',
        'year': year
    };

    var success = function(response){
        console.log(response);

        var chart_rows = [['Year ' + year, type]];
        $.each(response, function(index, value){
            var row = [value.month, parseInt(type == "Activities" ? value.number_of_activities : value.number_of_users)];
            chart_rows.push(row);
        });

        var chart_data = google.visualization.arrayToDataTable(chart_rows);

        var options = {
            bars: 'vertical',
            vAxis: {format: 'decimal'},
            colors: ['#428bca', '#d95f02', '#7570b3']
        };

        var chart = new google.charts.Bar(document.getElementById('year_month_chart_div'));

        chart.draw(chart_data, google.charts.Bar.convertOptions(options));
        $(".activity_year_month_loader").hide();
    };

    doAjax("anonymous_user_activity", data, success);

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
            var row = [value.date, value.name, value.ip];
            table_rows.push(row);
        });

        activity_table.rows.add(table_rows).draw();
        $(".loader").hide();
        $(".activity_list_hidden").show();
        autoreizeFitToScreen();

    };

    doAjax("anonymous_user_activity", data, success);
}