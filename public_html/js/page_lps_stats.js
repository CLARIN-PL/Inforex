/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
	/** Set up tabs */
	$("#tabs").tabs({ cookie: { expires: 3000 } });
	$("#error_items").tablesorter();
	$("tr.row_capital").addClass("selected");
	
	$(".corr_type").click(function(){
		load_error_type($(this).text());
		return false;
	});

	$("#tags_frequences").tablesorter();
	
	$("#error_items tbody tr td.tag a").live('click',
		function(){
			var tag = $(this).text();
			$("#error_items tr.selected").removeClass("selected");
			$(this).closest("tr").addClass("selected");
			load_error_documents(tag);
		}
	);

	$("#interp_items tbody tr a").live('click',
			function(){
				$("#interp_items tr.selected").removeClass("selected");
				$(this).closest("tr").addClass("selected");
				load_interp($(this).attr("interp"));
			}
		);
});

function load_error_type(corr_type){
	$("#error_types tr.selected").removeClass("selected");
	$("#error_types .row_"+corr_type).addClass("selected");
	$("#error_items tbody").html('<tr class="ajax"><td colspan="6"><img src="gfx/ajax.gif" title="czekam..."/></td></tr>');
	
	var params = {
		corr_type: corr_type,
                corpus_id: $.url(window.location.href).param("corpus"),
		subcorpus_id: $("#subcorpus_id").val()
	};
	
	var success = function(data){
		var html = "";
		var n = 0;
		for (var k in data['tags']){
			var t = data['tags'][k];
			html += '<tr>' +
					'<td class="tag"><a href="#">' + t.tag + '</a></td>' +
					'<td>type="<b>' + t.type + '"</b></td>' +
					'<td>sic="<b>' + t.sic + '"</b></td>' +
					'<td>' + t.content + '</td>' +
					'<td style="text-align: right">' + t.count + '</td>' +
					'<td style="text-align: right">' + t.count_docs + '</td>' +
					'</tr>';							
			n++;														
		}
		$("#error_items tbody").html(html);
	};
	
	var complete = function(){
		$(".ajax").remove();
	};
	
	
	doAjax("lps_get_corr_tags", params, success, null, complete);
}

function load_error_documents(tag){
	$("#documents_with_errors tbody tr").remove();
	$("#documents_with_errors tbody").html('<tr class="ajax"><td colspan="2"><img src="gfx/ajax.gif" title="czekam..."/></td></tr>');

	var params = {
		tag: tag,
		corpus_id: $.url(window.location.href).param("corpus"),
		subcorpus_id: $("#subcorpus_id").val(),
		deceased_gender : $("input[name=filter_deceased_gender]").val(),
		deceased_maritial : $("input[name=filter_deceased_maritial]").val(),
		deceased_source : $("input[name=filter_deceased_source]").val()	
	};
	
	var success = function(data){
		var html = "";
		for (var k in data['docs']){
			var t = data['docs'][k];
			html += '<tr>' +
					'<td class="tag"><a href="?page=report&corpus=3&id='+k+'">' + t.title + '</a></td>' +
					'<td style="text-align: right">' + t.count + '</td>' +
					'</tr>';							
		}
		$("#documents_with_errors tbody").html(html);
	};
	
	var complete = function(){
		$(".ajax").remove();
	};
	
	doAjax("lps_get_tag_docs", params, success, null, complete);
}

function load_interp(interp){
	$("#interp tbody").html('<tr class="ajax"><td colspan="4"><img src="gfx/ajax.gif" title="czekam..."/></td></tr>');
	
	var params = {
		interp: interp,
		corpus_id: $.url(window.location.href).param("corpus")
	};
	
	var success = function(data){
		var html = "";
		var n = 1;
		for (var k in data['docs']){
			var t = data['docs'][k];
			html += '<tr>' +
					'<td>' + n + '</td>' +
					'<td>' + t.subcorpus + '</td>' +
					'<td><a href="index.php?page=report&amp;id='+t.id+'" target="_blank">' + t.title + '</td>' +
					'<td style="text-align: right">' + t.count + '</td>' +
					'</tr>';
			n++;														
		}
		$("#interp tbody").append(html);
	};
	
	var complete = function(){
		$(".ajax").remove();
	};
	
	doAjax("lps_get_interp", params, success, null, complete);
};
