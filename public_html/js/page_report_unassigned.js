/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
	
	$("input#enable").click(function(){
		var _data = {
				url: $.url(window.location.href).attr('query'),
				perspective_id : $("#unassigned_subpage").val(),
				access : 'loggedin',
				operation_type : 'add'
			};
		
		var success = function(data){
			window.location.href = window.location.href;
		};
		
		var login = function(data){
		};
		
		doAjaxSyncWithLogin("corpus_set_corpus_and_report_perspectives", _data, success, login);
	});
	
});


