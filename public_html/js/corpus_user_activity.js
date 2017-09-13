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

    $('#user_activities').DataTable({
        "bInfo": false,
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
    var user_activity_table = $('#user_activity_table').DataTable({
        "bInfo": false});

    var user_activity_list_table = $('#user_activity_list_table').DataTable({
        "order": [[ 0, "desc" ]]
    });

    $("#user_activities").on("click", "tbody > tr",function(element){
        if(!$(element.target).hasClass("browse_user_activity")) {
            if (!$(this).hasClass("selected")) {
                $("tr").removeClass("selected");
                $(this).addClass("selected");
            }


            $(".user_activities_details").show();
            var user_id = $(this).find("td:first").text();
            getUserActivitySummary(user_id, user_activity_table);
            autoreizeFitToScreen();
        }
    });

    $(".browse_user_activity").click(function(){
        var user_id = $(this).attr('id');
        $(".activity_list_hidden").hide();
        $("#activity_list_modal").modal("show");
        $(".loader").show();
        getUserActivityList(user_id, user_activity_list_table);

    });
});

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
            var row = [value.datetime, value.name, value.corpus, value.report_id];
            table_rows.push(row);
        });

        user_activity_table.rows.add(table_rows).draw();
        $(".loader").hide();
        $(".activity_list_hidden").show();

    };

    doAjax("user_activity_summary", data, success);
}

function getUserActivitySummary(user_id, user_activity_table){
    user_activity_table.clear();

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
    };

    doAjaxSync("user_activity_summary", data, success);

}
