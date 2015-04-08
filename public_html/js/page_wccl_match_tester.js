/** Global variables **/
var _wccl_rules = null;
var _corpus = null;
var _offset = 50;

var _global_stopping = "Stopping ...";
var _global_stoped = "Stopped";
var _global_stop = "Stop";

var _editor = null;

/** Init scripts after page loading **/
$(function(){

	$("#process").click(function(){
		_wccl_rules = _editor.getValue();
		_corpus = $("#corpus").val();
		$(this).attr("disabled", "disabled");
		$(this).addClass("disabled");
		$(".click.selected").removeClass("selected");
		$("#items li").remove();
		update_summary();
		$("#count").text("-");
		$("#interupt").removeAttr("disabled");
		$("#interupt").removeClass("disabled");
		$("#count").after("<img class='ajax' src='gfx/ajax.gif'/>");
		$("#interupt").attr("value", _global_stop);
		
		// Wyczyść błędy i wyniki z poprzedniego zapytania
		$("#error").hide();
				
		test_wccl_rules(0, _offset);
	});
	
	$("#interupt").click(function(){
		_wccl_rules = null;
		$("#interupt").attr("disabled", "disabled");
		$("#interupt").addClass("disabled", "disabled");
		$("#interupt").attr("value", _global_stopping);
	});
	
	$("#summary td").click(function(){
		var id = $(this).attr("id");
		var cl = "." + id.replace("-", ".");
		
		$("#sentences li").each(function(){
			if ( $(this).find(cl).length == 0)
				$(this).hide();
			else
				$(this).show();
		});
		$(".click.selected").removeClass("selected");
		$(this).addClass("selected");
	});
	
	_editor = CodeMirror.fromTextArea(document.getElementById("wccl_rules"), {
		styleActiveLine: true,
		lineWrapping: true,
		lineNumbers: true		
	});
	resize_view();
});

/** Other functions **/
function update_summary_cell(chann, cl){
	var v = $("span."+chann+"."+cl).length;
	$("td#"+chann+"-"+cl).text(v);
	return v;
}

function update_summary(){
	$("#summary table tbody th span").each(function(){
		var type = $(this).attr("class");
		var tp = update_summary_cell(type, "tp");
		var fn = update_summary_cell(type, "fn");
		var fp = update_summary_cell(type, "fp");
		var p = tp+fp == 0 ? 0 :  tp*100/(tp+fp);
		$("td#"+type+"-p").text(p.toFixed(2) + "%");
		var r = tp+fn == 0 ? 0 :  tp*100/(tp+fn);
		$("td#"+type+"-r").text(r.toFixed(2) + "%");
		var f = p+r==0 ? 0 : 2*p*r/(p+r);
		$("td#"+type+"-f").text(f.toFixed(2) + "%");
	});
}

function stop_processing(){
	_wccl_rules = null;
	$("#process").removeAttr("disabled");
	$("#process").removeClass("disabled");
	$("#interupt").attr("disabled", "disabled");
	$("#interupt").addClass("disabled");
	if ( $("#interupt").attr("value") == _global_stopping )		
		$("#interupt").attr("value", _global_stoped);
	update_summary()
	$(".ajax").remove();
	$(".stoping").remove();
}

function test_wccl_rules(start, offset){
	
	if ( _wccl_rules == null ){
		stop_processing();		
	}
	else{	
		var params = {};
		params['ajax'] = "test_wccl_rules";
		params['start'] = start;
		params['offset'] = offset;
		params['wccl_rules'] = _wccl_rules;
		params['corpus'] = _corpus;
		
	    $.ajax({
	        async:  "async",
	        type:   'POST',
	        url:    "index.php",
	        data:   params,
	        success: function(data){
	        	result = data.result;
	        	        	
	        	if ( result.errors.length > 0 ){
	        		$("#process").removeAttr("disabled");
	            	$("#error").show();
	            	$("#errors li").remove();
	            	for (var i = 0; i < result.errors.length; i++) {
	            	    $("#errors").append('<li>' + result.errors[i] + '</li>');
	            	}
	            	stop_processing();       		
	        	}
	        	else if ( result.finished ){
	        		stop_processing();
	        	}
	        	else {
	        		
	        		$("#count").text(result.total_processed + " z " + result.total_documents);
	        		
	            	if ( result.items.length > 0 ){
	                	for (var i = 0; i < result.items.length; i++) {
	                	    $("#sentences").append('<li>' + result.items[i] + '</li>');
	                	}        		
	            	}
	            	update_summary();
	        		
	        		test_wccl_rules(start+offset, offset);
	        	}
	        },
	        error: function(request, textStatus, errorThrown){
	        	$("#error").show();
	        	$("#errors li").remove();
	        	$("#errors").append('<li>' + request.responseText.trim() + '</li>');
	        	stop_processing();
	        },
	        complete: function(){
	        	//$("#process").removeAttr("disabled");                
	        },
	        dataType:"json"
	    });
	}
}