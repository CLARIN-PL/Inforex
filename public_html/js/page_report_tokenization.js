/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
	$.each($("#content *"), function(index, value){
		$(value).after('<span style="display:none">&nbsp;</span>');
	});
	
	$("#tokenizeText").click(function(){

        var button = this;

        $(button).after("<img class='ajax_indicator' src='gfx/ajax.gif'/>");
        $(button).attr("disabled", "disabled");
        $("#process_status").show();

        var task = $("#taggers").find("input[name=task]:checked").attr('id');

        var corpus_id = $.url(window.location.href).param("corpus");
        var document_id = $.url(window.location.href).param("id");

		var params = {
            'error': false,
            'task': task,
            'document_id': document_id,
			'url': 'corpus=' + corpus_id
        };
		
		var success = function(data){
            var interval_id = window.setInterval(function() { fetchTokenizationStatus(data['task_id'], interval_id); }, 1000);
		};
		
		var complete = function(){
			$(button).removeAttr("disabled");
			$(".ajax_indicator").remove();
		};

        doAjaxSync("task_new", params, success, null, complete);
	});
	
});

function fetchTokenizationStatus(task_id, interval_id){
	var params = {
		'task_id': task_id
	};

    var success = function(data){
		var processing = data.processed;
		var percent = data.percent;

		var status;
		if(processing == 1 && percent == 0){
			status = "Processing...";
		} else if (processing == 1 && percent == 100){
			status = "Finished: <a href=''>refresh the page <i class=\"fa fa-refresh\" aria-hidden=\"true\"></i></a>";
            clearInterval(interval_id);
		} else{
			status = "Queued";
		}

		$("#status").html(status);
    };

	doAjax('task_check_status', params, success);
}