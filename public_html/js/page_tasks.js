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

	$("#buttonNewTask").click(function(){
		var dialog_html = $("#dialogNewTask").html(); 
		var dialog_box = 
			$(dialog_html).dialog({
				width : 500,
				modal : true,
				title : 'New task',
				buttons : {
					Cancel: function() {
						dialog_box.dialog("destroy").remove();
					},
					Ok : function(){
						var corpus_id = $.url(window.location.href).param("corpus");
						var params = taskGetParameters();
						params['url'] = 'corpus=' + corpus_id;
									
						doAjax("task_new", params,
								// success
								function(data){
									if (data['task_id']>0){
										var task_id = data['task_id'];
										window.location.href = "index.php?page=tasks&corpus="+corpus_id+"&task_id="+task_id;
									}
								}, 
								// error
								function(){},
								// complete
								function(){},
								null,
								null,
								false
								);
					}
				},
				close: function(event, ui) {
					dialog_box.dialog("destroy").remove();
					dialog_box = null;
				}
			});	
	});
	
	global_task_id = $("#taskProgress").attr("task_id");
	checkTaskSatus();
});


/**
 * Waliduje parametry w formularzu events.
 * @return tablicę z danymi zczytanymi z formularza.
 */
function taskGetParameters(){
	var form = $(".ui-dialog .dialogNewTask");
	var task = form.find("input[name=task]:checked").val();
	var documents = form.find("input[name=documents]:checked").val();
	var error = false;	
		
	var output = {};
	output['error'] = error;
	output['task'] = task;
	output['documents'] = documents;
	
	return output;
}

/**
 * 
 * @param report_id
 */
function getDocumentPreviewUrl(report_id){
	return 'index.php?page=report&subpage=autoextension&id='+report_id;
}

/**
 * Check status for the active status.
 */
function checkTaskSatus(){
	if ( global_task_id != null ){
		doAjax("task_check_status",
			{task_id: global_task_id},
			// Success
			function (data){
				$(".ui-progressbar-value").css("width", data.percent + "%");
				$("#taskProgress td.documents").text(data.documents);
				$("#taskProgress td.processed").text(data.processed);
				$("#taskProgress td.errors").text(data.errors);
				if ( data.task.status == "new" ){
					$("#taskProgress .status").text("Waiting in queue (position in queue: "+(data.queue+1)+")");
				}
				else if ( data.task.status == "process" ){
					$("#taskProgress .status").text("Processing: "+data.percent+"%");
				}
				else{
					$("#taskProgress .status").text(data.task.status);
				}
				
				$("#taskProgress td.type").text(data.task.type);
				$("#taskProgress td.parameters").text(data.task.parameters);
				if ( $("table.documents tr").length == 0 ){
					var html = "";
					for(var a=0; a<data.documents_status.length; a++){
						var status = data.documents_status[a];
						var row = "<tr id='document"+status.report_id+"'>;";
						row += "<td>"+status.report_id+"</td>";
						row += "<td>"+status.status+"</td>";
						if ( status.status == "done"){
							row += '<td><a href="'+getDocumentPreviewUrl(status.report_id)+'" target="_blank">' + status.message + '</a></td>';							
						}
						else{
							row += "<td>"+status.message+"</td>";
						}
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
						if (! (status.report_id in documents_status)){
							documents_status[status.report_id] = { status : status.status, message : status.message };
							var row = "<tr id='document"+status.report_id+"'>;";
							row += "<td>"+status.report_id+"</td>";
							row += "<td>"+status.status+"</td>";
							if ( status.status == "done"){
								row += '<td><a href="'+getDocumentPreviewUrl(status.report_id)+'" target="_blank">' + status.message + '</a></td>';							
							}
							else{
								row += "<td>"+status.message+"</td>";
							}
							row += "</tr>";
							$("table.documents tr:last").after(row);
						}
						if ( documents_status[status.report_id]['status'] != status.status ){
							$(table).find("tr#document"+status.report_id+" td:nth-child(2)").text(status.status);
							documents_status[status.report_id]['status'] = status.status;
						}
						if ( documents_status[status.report_id]['message'] != status.message ){
							var message = status.message;
							if ( status.status == "done" ){
								message = '<a href="'+getDocumentPreviewUrl(status.report_id)+'" target="_blank">' + message + '</a>';
							}
							$(table).find("tr#document"+status.report_id+" td:nth-child(3)").html(message);
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