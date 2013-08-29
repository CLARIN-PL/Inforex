/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

var isCtrl = false; 
var _wAnnotation = null;
var _oNavigator = null;
var hiddenAnnotations = 0;

/**
 * Przypisanie akcji po wczytaniu się strony.
 */
$(function(){
	$.each($("#content *"), function(index, value){
		$(value).after('<var style="display:none">&nbsp;</var>');
	});	
	
	$("#annotationList span").live('mouseover',function(){
		$("#"+$(this).attr('title').split(":")[0].replace("#","")).addClass("hightlighted");
	}).live('mouseout',function(){
		$("#"+$(this).attr('title').split(":")[0].replace("#","")).removeClass("hightlighted");
	});
	
	$('#content span[class^="__"]').addClass("relationGrey");
	
	$("#runNerModule").click(function(){

		var text = $.trim($("#content").text());
		
		$("#runNerModule").attr("disabled", "disabled");
		$("#runNerModule").after("<img class='ajax_indicator' src='gfx/ajax.gif'/>");
		
		var model = $("#ner-model option:selected").val();
		
		var params = {
			text: text,
			model: model,
			report_id : $("#report_id").text(),
			corpus_id : $("#corpus_id").text()	
		};
		
		var success = function(data){
			$("#message").text("Process completed. Restart this page to see the result.");
		};
		
		var complete = function(){
			$(".ajax_indicator").remove();
			$("#runNerModule").removeAttr("disabled");
		};
		
		
		doAjax("report_autoextension_ner_process", params, success, null, complete);		
	});	
	
	/** Button that invoke recognition of proper names. */
	$("#recognize").click(function(){
		
		$(this).after("<img class='ajax_indicator' src='gfx/ajax.gif' title='czekam...'/>");
		$(this).attr("disabled", "disabled");
		
		var regex_id = /id=([0-9]+)/;
		var report_id = window.location.href.match(regex_id)[1];
		var button = this;

		var success = function(data){
			window.location.href = window.location.href + "&verify=1"
		};
		
		var error = function(code){
			if(code == "ERROR_TRANSMISSION"){
				$(button).after('<div class="ui-state-highlight" style="text-align: center; padding: 3px">No annotations found</div>');
				$(button).removeAttr("disabled");
				$(".ajax_indicator").remove();
			}
		};
		
		var complete = function(){
			$(button).removeAttr("disabled");
			$(".ajax_indicator").remove();
		};
		
		doAjax("report_autoextension_proper_names", {report_id: report_id}, success, error, complete);
	});
	
	/** Resetuje listę wyboru relacji, na którą ma być zmieniona anotacja */
	$("input[type=radio]").click(function(){
		$(this).closest("tr").find("select").val("-");
	});
	
	/** Resetuje radio butony wyboru przy ustawieniu typu relacji */
	$("select").change(function(){
		if ( $(this).val() == '-' )
		{
			if ( $(this).closest("tr").find("input:checked").val() == "change" )
				$(this).closest("tr").find("input[value=later]").attr("checked", "checked");
		}
		else
			$(this).closest("tr").find("input[value=change]").attr("checked", "checked");
	});
});


