/** Global variables **/
var _wccl_rules = null;
var _annotations = null;
var _reports_id = [];
var _reports_id_i = 0;
var _stopped = false;

var _global_stopping = "Stopping ...";
var _global_stopped = "Stopped";
var _global_stop = "Stop";

var _editor = null;
var _editor_annotations = null;

var _rules_saver = null;
var _time_started = null;

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
		
	_editor = CodeMirror.fromTextArea(document.getElementById("wccl_rules_textarea"), {
		styleActiveLine: true,
		lineWrapping: true,
		lineNumbers: true		
	});

	_editor_annotations = CodeMirror.fromTextArea(document.getElementById("annotation_types_textarea"), {
		styleActiveLine: true,
		lineWrapping: true,
		lineNumbers: true
	});
	_editor_annotations.setSize("100%", 80);

	CodeMirror.fromTextArea(document.getElementById("wccl_rule_template"), {
		styleActiveLine: true,
		lineWrapping: true,
        lineNumbers: false,
        readOnly: true
	});

	// Wyświetl przybornik jako zakładek
	$("#toolbox").tabs();
	
	// Setup rule saver
	_rules_saver = new WcclRulesSaver($("#save"), _editor, _editor_annotations, $("#save_status"));	
	
	$("#toolbox div a").click(function(){
		var text = $(this).text();
		var position = _editor.getCursor("from");
		_editor.replaceSelection(text);
		_editor.setCursor(position);
		_editor.focus();
		
		//var currentLineLength = _editor.lineContent(position.line).length;	
		//var lineNumber = _editor.lineNumber(position.line);
		//_editor.reindent();
		//var newLineLength = _editor.lineContent(position.line).length;
		//_editor.setSelection(position);
	});
	
	$("#annotation_types_toogle a").click(function(){
		$("#annotation_types").toggle();
		resize_view();
	});
	
	$("#toolbox_toogle a").click(function(){
		$("#toolbox").toggle();
		resize_view();
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
	_annotations = _editor_annotations.getValue();
	_reports_id = [];
	_reports_id_i = 0;
	_stopped = false;
	_time_started = new Date().getTime();
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
	params['annotations'] = _annotations;
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
	            		var html = "<li><b><a href='index.php?page=report&subpage=preview&id="+report_id+"' target='_blank'>" + report_id + "</a></b><ol>";
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
	            		$("#count").text("Done (" + ( (new Date().getTime() - _time_started) / 1000) + "s)");
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

/**
 * Class for storing user rules in the database. 
 */
function WcclRulesSaver(button, editor, annotations, status) {
    this.last_rules = "";
    this.last_annotations = "";    
    this.button = button;
    this.editor = editor;
    this.annotations = annotations;
    this.status = status;
    var saver = this;
    
    saver.disableSaveButton("Saved");
    saver.last_rules = saver.editor.getValue();
        
	this.button.click(function(){		
		saver.save(saver.editor.getValue(), saver.annotations.getValue());
	});    
	
	this.editor.on("change", function(){
		if ( saver.last_rules != saver.editor.getValue()
				|| saver.last_annotations != saver.annotations.getValue() ){
			saver.enableSaveButton("Save");
		}
		else{
			saver.disableSaveButton("Saved");			
		}
	});
	
	this.annotations.on("change", function(){
		if ( saver.last_rules != saver.editor.getValue()
				|| saver.last_annotations != saver.annotations.getValue() ){
			saver.enableSaveButton("Save");
		}
		else{
			saver.disableSaveButton("Saved");			
		}
	});
}

WcclRulesSaver.prototype.disableSaveButton = function(button_caption) {
    this.button.attr("disabled", "disabled");
    this.button.addClass("disabled");
    if ( typeof button_caption != 'undefined' ){
    	this.button.val(button_caption);
    }
};

WcclRulesSaver.prototype.enableSaveButton = function(button_caption) {
    this.button.removeAttr("disabled");
    this.button.removeClass("disabled");
    if ( typeof button_caption != 'undefined' ){
    	this.button.val(button_caption);
    }};

WcclRulesSaver.prototype.save = function(rules, annotations) {
	this.disableSaveButton("Saving ...");
	var params = { wccl_rules : rules, annotations : annotations };
	params['url'] = 'corpus=' + $.url(window.location.href).param("corpus");	
	var saver = this;	
	doAjaxWithLogin("wccl_match_save", params, function(){
		saver.disableSaveButton("Saved");
		saver.last_rules = rules;
		saver.status.text("Last save at " + saver.getCurrentTime());
	}, function(){
		saver.save(rules, annotations);
	});
};

WcclRulesSaver.prototype.getCurrentTime = function(){
	var currentdate = new Date(); 
	return currentdate.getHours() + ":"  
	        + currentdate.getMinutes() + ":" 
	        + currentdate.getSeconds();	
};