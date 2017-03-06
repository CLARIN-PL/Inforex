/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){	
	$("#menu_page li").hover(function(){
		if (!$(this).hasClass("expanded")){
			$(this).addClass("expanded");
			$("#menu_page li").show();			
		}	
	});
	
	$("#menu_page").mouseleave(function(){
		$("#menu_page .expanded").removeClass("expanded");
		$("#menu_page li").hide();
		$("#menu_page li.active").show();					
	});
});