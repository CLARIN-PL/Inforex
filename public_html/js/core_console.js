/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

function console_add(text){
	$("#console").show();
	var n = $("#console dt").length;
	$("#console dl").prepend("<dt>"+n+":</dt><dd>"+text+"</dd>");
}