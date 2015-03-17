/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

var global_task_id = null;
var documents_status = {};

$(function(){
	var form = $("#task");
	
	form.find("input[type=button]").click(function (){
		taskSubmit();
	});
	
	$("#taskHistory a").live("click", function(){
		showTaskStatus($(this).attr("task_id"));
	});

});

/**
 * Wywołanie przycisku Submit
 */
function taskSubmit(){
	var form = $("#services");
	var button = form.find("input[type=button]");
	var url = $.url(window.location.href);
	var corpus_id = url.param("corpus");
	
	button.attr("disabled", "disabled");
	var data = taskGetParameters();
	data['url'] = 'corpus=' + corpus_id;
				
	doAjax("task_new", data,
			// success
			function(data){
				if (data['task_id']>0){
					var task_id = data['task_id'];
					showTaskStatus(task_id);
				}
			}, 
			// error
			function(){
				
			},
			// complete
			function(){
				button.removeAttr("disabled");
			},
			null,
			null,
			false
			);
}

/**
 * Waliduje parametry w formularzu events.
 * @return tablicę z danymi zczytanymi z formularza.
 */
function taskGetParameters(){
	var form = $("#task");
	var task = form.find("input[name=task]").val();
	var documents = form.find("input[name=documents]").val();
	var error = false;	
		
	var output = {};
	output['error'] = error;
	output['task'] = task;
	output['documents'] = documents;
	
	return output;
}

/**
 * Shows dialog box with task status.
 */
function showTaskStatus(task_id){
	$("table.documents tr").remove();
	global_task_id = task_id;
	checkTaskSatus(task_id);
	$("#taskProgress").dialog({
		title: "Task status",
		autoOpen: true,
		width: 380,
		modal: true,
		buttons: {
			'Close': function() {
				global_task_id = null;
				$(this).dialog('destroy');
			}
		},
		close: function() {			
			global_task_id = null;
			$(this).dialog('destroy');
		}
	});		
}

/**
 * 
 */
function checkTaskSatus(){
	if ( global_task_id != null ){
		doAjax("task_check_status",
			{task_id: global_task_id},
			// Success
			function (data){
				$(".ui-progressbar-value").css("width", data.percent + "%");
				$("#taskProgress td.documents").text(data.documents);
				$("#taskProgress td.queue").text(data.queue);
				$("#taskProgress td.processed").text(data.processed);
				$("#taskProgress td.errors").text(data.errors);
				$("#taskProgress span.progress").text(data.percent);
				$("#taskProgress span.status").text(data.task.status);
				$("#taskProgress td.type").text(data.task.type);
				$("#taskProgress td.parameters").text(data.task.parameters);
				$("#taskProgress td.status").text(data.task.status);
				if ( $("table.documents tr").length == 0 ){
					var html = "";
					for(var a=0; a<data.documents_status.length; a++){
						var status = data.documents_status[a];
						var row = "<tr id='document"+status.report_id+"'>;";
						row += "<td>"+status.report_id+"</td>";
						row += "<td>"+status.status+"</td>";
						row += "<td>"+status.message+"</td>";
						row += "</tr>";
						html += row;						
						documents_status[status.report_id] = { status : status.status, message : status.message };
					};					
					$("table.documents").append(html);
				}
				else{
					var table = $("table.documents");
					for(var a=0; a<data.documents_status.length; a++){
						var status = data.documents_status[a];
						if ( documents_status[status.report_id]['status'] != status.status ){
							$(table).find("tr#document"+status.report_id+" td:nth-child(2)").text(status.status);
							documents_status[status.report_id]['status'] = status.status;
						}
						if ( documents_status[status.report_id]['message'] != status.message ){
							$(table).find("tr#document"+status.report_id+" td:nth-child(3)").text(status.message);
							documents_status[status.report_id]['message'] = status.message;							
						}
					}
				}
				if ( data.task.status == 'process' || data.task.status == 'new' ){
					window.setTimeout("checkTaskSatus()", 1000);
				}
			},
			// Error
			function (){
				
			},
			// Complete
			function (){
				
			},
			null,
			null,
			false);			
	}
}