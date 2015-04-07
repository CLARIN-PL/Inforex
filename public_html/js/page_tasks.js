/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

var global_task_id = null;
var documents_status = {};

//GRAB TASK
var MAX_DOWNLOAD_TIME = 3 * 60 * 1000; //in milliseconds : 3 minutes
var processing_status = {
	0 : "downloading",
	1 : "source flat deduplication",
	2 : "local copy",
	3 : "extracting plain text",
	4 : "plain text flat deduplication",
	5 : "deep deduplication preprocessing",
	6 : "plain text deep deduplication",
	7 : "deep deduplication postprocessing",
	8 : "unwanted data filtering",
	9 : "tagging",
	10 : "ready to import"
};

$(function(){
	var form = $("#task");
	
	form.find("input[type=button]").click(function (){
		taskSubmit();
	});
	
	/*$("#taskHistory a").live("click", function(){
		showTaskStatus($(this).attr("task_id"));
	});*/

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
	
	$("#corpoGrabberTask").click(function(){
		var dialog_box = 
			$('<div class="corpoGrabberDialog">'+
					'<table>'+
						'<tr>'+
							'<th style="text-align:right">URL</th>'+
							'<td><input id="corpograbber_url" type="text" /></td>'+
						'</tr>'+
						/*'<tr>'+
							'<th style="text-align:right">Recursive</th>'+
							'<td><input id="corpograbber_recursive" type="checkbox" /></td>' +
						'</tr>'+*/
					'</table>'+
			'</div>')		
			.dialog({
				width : 500,
				modal : true,
				title : 'CorpoGrabber task',
				buttons : {
					Cancel: function() {
						dialog_box.dialog("destroy").remove();
					},
					Ok : function(){
						corpus_id = $.url(window.location.href).param("corpus");
						doAjax("corpograbber_new",
							//params
							{
								'corpograbber_url' : $("#corpograbber_url").val(),
								'corpograbber_recursive' : $("#corpograbber_recursive").is(":checked"),
								'url' : 'corpus=' + corpus_id
							},
							//success
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
	checkTaskStatus();
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
function getDocumentUrl(perspective, report_id){
	return 'index.php?page=report&subpage='+perspective+'&id='+report_id;
}

/**
 * Check status for the active status.
 */
function checkTaskStatus(){
	if ( global_task_id != null ){
		doAjax("task_check_status",
			{task_id: global_task_id},
			// Success
			function (data){
				$("#progressbarValue").css("width", data.percent + "%");
				$("#taskProgress td.documents").text(data.documents);
				$("#taskProgress td.processed").text(data.processed);
				$("#taskProgress td.errors").text(data.errors);
				if ( data.task.status == "new" ){
					$("#taskProgress .status").text("Waiting in queue");
					$("#progressbar").hide();
					$("#taskProgress .status_msg").show();
					$("#taskProgress .status_msg").text("Position in queue: "+(data.queue+1));
					
				}
				else if ( data.task.status == "process" ){
					$("#taskProgress .status").text("Processing: "+data.percent+"%");
					$("#progressbar").show();
					$("#taskProgress .status_msg").hide();
				}
				else if ( data.task.status == "error" ){
					$("#taskStatus").hide();
					$("#taskError .message").text(data.task.message);
					$("#taskError").show();
				}
				else{
					$("#taskProgress .status").text(data.task.status);
					$("#progressbar").show();
					$("#taskProgress .status_msg").hide();
				}
				
				$("#taskProgress td.type").text(data.task.type);
				$("#taskProgress td.parameters").text(data.task.parameters);
				if ( $("table.documents tr").length == 0 ){
					var html = "";
					for(var a=0; a<data.documents_status.length; a++){
						var status = data.documents_status[a];						
						html += makeDocumentTableRow(data.task.type, status);						
						documents_status[status.report_id] = { status : status.status, message : status.message };
					};					
					$("table.documents tbody").html(html);
				}
				else{
					var table = $("table.documents");
					for(var a=0; a<data.documents_status.length; a++){
						var status = data.documents_status[a];
						if (! (status.report_id in documents_status)){
							documents_status[status.report_id] = { status : status.status, message : status.message };
							var row = makeDocumentTableRow(data.task.type, status);
							$("table.documents tbody").append(row);
						}
						if ( documents_status[status.report_id]['status'] != status.status ){
							var actions = makeDocumentLinks(data.task.type, status);
							$(table).find("tr#document"+status.report_id+" td:nth-child(2)").text(status.status);
							if ( status.status == 'done' ){
								$(table).find("tr#document"+status.report_id+" td:nth-child(4)").html(actions);
							}
							documents_status[status.report_id]['status'] = status.status;
						}
						if ( documents_status[status.report_id]['message'] != status.message ){
							var message = status.message;
							$(table).find("tr#document"+status.report_id+" td:nth-child(3)").html(message);
							documents_status[status.report_id]['message'] = status.message;							
						}
					}
				}
				if ( data.task.status == 'process' || data.task.status == 'new' ){
					window.setTimeout("checkTaskStatus()", 1000);
				}
				checkTaskGrabStatus(data);
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

function checkTaskGrabStatus(data){
	task_type = $("#taskType").text();
	if (task_type == "grab"){			
		if (!$("#downloadStatus").length){
			$("#taskStatus").prepend( 
	    	'<tr>' + 
	            '<th><span id="downloadStatus"></span></th>' + 
	            '<td><span id="downloadStatusMsg"></span><div id="downloadProgressbar" style="display:none" class="ui-progressbar ui-widget ui-widget-content ui-corner-all" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="20"><div id="downloadProgressbarValue" class="ui-progressbar-value ui-widget-header ui-corner-left" style="width: 0%;"></div></div></td>' +
            '</tr>');
			$("#downloadProgressbar").show();
		}
		status = data['grab_status'];
		task_datetime = new Date(
				data.task.datetime_start.substr(0, 4), 
				data.task.datetime_start.substr(5, 2) - 1, 
				data.task.datetime_start.substr(8, 2), 
				data.task.datetime_start.substr(11, 2), 
				data.task.datetime_start.substr(14, 2), 
				data.task.datetime_start.substr(17, 2)
		);
		now_datetime = new Date();			
		time_difference = now_datetime - task_datetime;		
		if (data.task.status != "process" || status == 10){
			$("#downloadProgressbar").hide();
			$("#downloadStatus").hide();
			$("#taskStatusRow").show();
		}
		else {
			$("#taskStatusRow").hide();
			$("#downloadProgressbar").show();
			$("#downloadStatus").show();
			if (status == 0 && time_difference < MAX_DOWNLOAD_TIME){
				downloadProgressbarValue = parseInt(time_difference / MAX_DOWNLOAD_TIME * 100);
				if (downloadProgressbarValue > 100)
					downloadProgressbarValue = 100;
				$("#downloadStatus").text("downloading");
				$("#downloadProgressbarValue").css("width", downloadProgressbarValue + "%");
			}
			else if (status > 0 && status < 11){
				$("#downloadStatus").text(status + "/10: " + processing_status[status]);
				$("#downloadProgressbarValue").css("width", (status * 10) + "%");
			}
		}
	}
}

/**
 * Create full row for task report data. 
 * @param task {String} Type of task
 * @param data {array} Task report data
 * @returns {String}
 */
function makeDocumentTableRow(task, data){
	var row = "<tr id='document"+data.report_id+"'>;";
	row += "<td>"+data.report_id+"</td>";
	row += "<td>"+data.status+"</td>";
	row += "<td>"+data.message+"</td>";
	if ( data.status == "done" ){
		row += '<td>' + makeDocumentLinks(task, data)+ '</td>';														
	}
	else{
		row += "<td></td>";
	}
	row += "</tr>";
	return row;							
}

/**
 * Create links for document which will be displayed in the action column.
 * @param task {String} Type of task
 * @param data {array} Task report data
 * @return {String}
 */
function makeDocumentLinks(task, data){
	url = "";
	url += '<a href="'+getDocumentUrl('preview', data.report_id)+'" target="_blank">show content</a>';
	if ( task == "liner2" ){
		url += ', <a href="'+getDocumentUrl('autoextension', data.report_id)+'" target="_blank">verify annotations</a>';		
	}
	return url;
}