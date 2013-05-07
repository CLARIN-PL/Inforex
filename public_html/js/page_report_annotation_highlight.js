/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$("#content span").live("mouseover", function(){
	$(this).addClass("hightlighted");
	var this_id = $(this).attr("id").replace("an","");
	$("sup.rel[target="+this_id+"]").each(function(i,val){
		$(val).addClass("hightlighted");
		if($(val).prev().hasClass("rel")){
			$(val).prevUntil("span").prev("span").addClass("hightlighted");
		}
		else{
			$(val).prev("span").addClass("hightlighted");
		}
	});
	$(this).prev("sup.relin").addClass("hightlighted");
	if($(this).next().hasClass("rel")){
		$(this).nextUntil("span").each(function(i,val){
			$(val).addClass("hightlighted");
			$("#an"+$(val).attr("target")).addClass("hightlighted");
			$("#an"+$(val).attr("target")).prev("sup").addClass("hightlighted");
		});
	}
	
	return $(this).hasClass("token");
}).live("mouseout", function(){
	$("#content span.hightlighted").removeClass("hightlighted");	
	$("#content sup").removeClass("hightlighted");
});

// Podświetlanie elementów z tabeli
$(".an_row").live("mouseover", function(){
	var id = $(this).attr("label");
	$(this).addClass("hightlighted");
	$("#"+id).addClass("hightlighted");
	$("#"+id).prev("small").addClass("hightlighted");
}).live("mouseout", function(){
	var id = $(this).attr("label");
	$(this).removeClass("hightlighted");
	$("#"+id).removeClass("hightlighted");
	$("#"+id).prev("small").removeClass("hightlighted");
});
