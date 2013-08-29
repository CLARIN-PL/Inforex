/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
	$("ul.topics a").click(function(){
		$("ul.topics a").removeClass("marked");
		
		var topic_id = $(this).attr("id").replace("topic_", "");
		var report_id = $("#report_id").attr("value");
		var item = $(this);

		var params = {
			report_id: report_id, 
			topic_id: topic_id
		};
		
		var success = function(data){
			item.addClass("marked");							
			window.location = $("#article_next").attr("href");
		};
		
		var complete = function(){
			$("#save").removeAttr("disabled");
		};
		
		var login = function(){
			save_content_ajax();
		};
		
		doAjax("report_update_topic", params, success, null, complete, null, login);
				
		return false;
	});
	
});


