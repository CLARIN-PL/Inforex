/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

/**
 * Kod do obsługi ładowania danych o sensach z serwera porcjami.
 */

$(document).ready(function(){

	//ajaxIndicatorShow(document.getElementById("sensTable"));
	var table = document.getElementById("sensTable");
	var indicator_html = "<img class='ajax-indicator' id='aindicator' src='gfx/ajax.gif'/>";
	$(table).hide();
	$(table).after(indicator_html);
	$('#sensTable').DataTable({
		"serverSide": true,
		"ajax": {	"url" : "index.php",
					"type": "POST",
					// below adds POST redirector to script
					// ajax/ajax_administration_wsd_schema.php
					// at the server side
					"data" : {
						"ajax" : "administration_wsd_schema"
					},
					// and this unwrap returned data
					"dataSrc" : function(json) {
						json.draw = json.result.draw;
						json.recordsTotal = json.result.recordsTotal;
						json.recordsFiltered = json.result.recordsFiltered;
						return json.result.data;
					},
					complete: function(){
						$(document.getElementById("aindicator")).remove();
					},
					"dataType":"json"
				},
		// null - domyślne ustawienia dla columns
		//  { "orderable": true, "searchable": true },
		"columns": [
			{ "data" : "index" , "orderable" : false , "searchable" : false },
        		{ "data" : "name" , "orderable" : false , "searchable" : true }
		],
		"order": [[ 1, "asc" ]],
        	// bInfo steruje wyświetlaniem informacji o ilości
        	// rekordów i filtrze
		"bInfo": true
		});
		//ajaxIndicatorHide(document.getElementById("sensTable"));
		$(table).show();

}); // ready()
