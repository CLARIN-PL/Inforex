/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
	$("#checkboxSubcorpora").change(function(){
		if (  this.checked ){
			$("#listSubcorpora").attr('disabled','disabled');
		} else {
            $("#listSubcorpora").removeAttr('disabled');
		}
	});

	$(".nav_corpus_page a").text($(".nav_corpus_pages li.active").text());
});
