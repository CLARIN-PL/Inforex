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
	var $table = $("#sensTable");
	var $tableContainer = $table.parent();
	var indicator_html = "<div class='administration-wsd-loading' id='aindicator'><img src='gfx/ajax.gif' alt='Loading'/> <span>Loading lemmas...</span></div>";
	$table.detach();
	$tableContainer.append(indicator_html);

	window.setTimeout(function(){
		$table.hide();
		$tableContainer.append($table);
		$table.DataTable({
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
						$("#sensTable").show().closest(".dataTables_wrapper").show();
						normalizeWsdDataTableFooter();
					},
					"dataType":"json"
				},
		// null - domyślne ustawienia dla columns
		//  { "orderable": true, "searchable": true },
		"columns": [
			{ "data" : "index" , "orderable" : false , "searchable" : false, "className": "administration-wsd-index-column" },
			{ "data" : "name" , "orderable" : false , "searchable" : true, "className": "sens_name" }
		],
		"order": [[ 1, "asc" ]],
        	// bInfo steruje wyświetlaniem informacji o ilości
        	// rekordów i filtrze
		"bInfo": true,
		"fnDrawCallback": function(){
			normalizeWsdDataTableFooter();
		}
		});
		//ajaxIndicatorHide(document.getElementById("sensTable"));
		$table.closest(".dataTables_wrapper").hide();
		normalizeWsdDataTableFooter();
	}, 0);

}); // ready()

function normalizeWsdDataTableFooter(){
	var $wrapper = $("#sensTable").closest(".dataTables_wrapper");
	var $info = $wrapper.find(".dataTables_info");
	var $pagination = $wrapper.find(".dataTables_paginate");

	if (!$wrapper.length || !$info.length || !$pagination.length) {
		return;
	}

	var $footer = $wrapper.find(".administration-wsd-datatables-footer");

	if (!$footer.length) {
		$footer = $("<div class='administration-wsd-datatables-footer'></div>");
		$wrapper.append($footer);
	}

	$footer.append($info);
	$footer.append($pagination);
}
