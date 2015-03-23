/** Global variables **/
var _wccl_rules = null;
var _reports_id = [];
var _reports_id_i = 0;
var _stopped = false;

var _global_stopping = "Stopping ...";
var _global_stopped = "Stopped";
var _global_stop = "Stop";

var _editor = null;

/** Init scripts after page loading **/
$(function(){

	$("#process").click(function(){
		run_wccl_match();
	});
	
	$("#interupt").click(function(){
		_stopped = true;
		$("#interupt").attr("disabled", "disabled");
		$("#interupt").addClass("disabled");
		$("#interupt").attr("value", _global_stopping);
	});
		
	_editor = CodeMirror.fromTextArea(document.getElementById("wccl_rules"), {
		styleActiveLine: true,
		lineWrapping: true,
		lineNumbers: true		
	});
});

function stop_processing(){
	_wccl_rules = null;
	$("#process").removeAttr("disabled");
	$("#interupt").attr("disabled", "disabled");
	if ( $("#interupt").attr("value") == _global_stopping )		
		$("#interupt").attr("value", _global_stoped);
	update_summary();
	$(".ajax").remove();
	$(".stoping").remove();
}

/**
 * Wywołuje proces wykonywania reguł na bieżącym korpusie.
 */
function run_wccl_match(){
	// Zapamiętaj dla jakich reguł będzie wykonywane przetwarzanie
	_wccl_rules = _editor.getValue();
	_reports_id = [];
	_reports_id_i = 0;
	_stopped = false;
	$("#process").addClass("disabled");
	$("#process").attr("disabled", "disabled");
	$("img.ajax").remove();
	$("#count").after("<img class='ajax' src='gfx/ajax.gif'/>");
	$("#items li").remove();
	$("#error").hide();
	$("#interupt").removeAttr("disabled");
	$("#interupt").removeClass("disabled");
	$("#interupt").attr("value", _global_stop);
	var params_reports = {};
	params_reports['url'] = 'corpus=' + $.url(window.location.href).param("corpus");
	doAjax("wccl_match_get_reports_id", 
			params_reports, 
			//success
			function(data){
				_reports_id = data;
				run_wccl_match_next();
			}, 
			//error
			function(){}, 
			//complete
			function(){}, 
			null, 
			null);
};

/**
 * 
 */
function run_wccl_match_next(){
	var params = {};
	params['wccl_rules'] = _wccl_rules;
	params['url'] = 'corpus=' + $.url(window.location.href).param("corpus");
	var i = _reports_id_i;
	_reports_id_i += 1;

	$("#count").text("Processing " + (i+1) + " from " + _reports_id.length);
	var report_id = _reports_id[i];
	params['report_id'] = report_id;
	doAjax("wccl_match_run", params, 
			//success
			function(result){
	        	if ( result.errors.length > 0 ){
	        		$("#process").removeAttr("disabled");
	            	$("#errors li").remove();
	            	for (var i = 0; i < result.errors.length; i++) {
	            	    $("#errors").append('<li>' + result.errors[i] + '</li>');
	            	}
	            	$("#error").show();
	            	$("#count").text("Stopped due to errors");
	            	processing_ended();
	        	}
	        	else{
	            	if ( result.items.length > 0 ){
	            		var html = "<li><b>" + report_id + "</b><ol>";
	                	for (var i = 0; i < result.items.length; i++) {
	                	    html += '<li>' + result.items[i] + '</li>';
	                	}        		
	                	html += "</ol></li>";
	                	$("#sentences").append(html);
	            	}
	            	if ( _stopped ){
	            		$("#count").text("Stopped");
	            		$("#interupt").attr("value", _global_stopped);
	            		processing_ended();	            		
	            	}
	            	else if ( _reports_id_i < _reports_id.length){
	            		run_wccl_match_next();
	            	}
	            	else{
	            		$("#count").text("Done");
	            		processing_ended();
	            	}
	        	}
			}, 
			//error
			function(){
            	$("#count").text("Stopped due to errors");
            	processing_ended();
			}, 
			//complete
			function(){
				
			}, 
			null, 
			null);				
}

/**
 * 
 */
function processing_ended(){
	$("#process").removeClass("disabled");
	$("#process").removeAttr("disabled");	
	$("img.ajax").remove();			
}