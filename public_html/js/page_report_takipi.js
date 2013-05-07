/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
	$("#content").html("<img src='gfx/ajax.gif'/> Tagowanie tekstu za pośrednictwem TaKIPI-WS ...");
	var content = $("#report_content").val();
	var id = $("#report_id").val();
	$.post("index.php", { ajax: "report_takipi", content: content, id: id},
	  function(data){
		$("#content").html(data['tagged']);
	  }, "json");			

});

$("#content span").live("mouseover", function(){
	$("#token code").html($(this).attr("label"));
});

$("#content span.w").live("mouseover", function(){
	$(this).parents("span.ann").addClass("marked");
});

$("#content span.w").live("mouseout", function(){
	$(this).parents("span.ann").removeClass("marked");
});