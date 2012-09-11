$(function(){
	/** Set up tabs */
	$("#tabs").tabs();
	$("#error_items").tablesorter();
	$("tr.row_capital").addClass("selected");
	
	$(".corr_type").click(function(){
		load_error_type($(this).text());
		return false;
	});

	$(".interp").click(function(){
		load_interp($(this).attr("interp"));
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
});

function load_error_type(corr_type){
	$("#error_types tr.selected").removeClass("selected");
	$("#error_types .row_"+corr_type).addClass("selected");
	$("#error_items tbody").html('<tr class="ajax"><td colspan="6"><img src="gfx/ajax.gif" title="czekam..."/></td></tr>');
	
	$.ajax({
		type: 	'POST',
		url: 	"index.php",
		data:	{ 	
					ajax: "lps_get_corr_tags", 
					corr_type: corr_type,
					subcorpus_id: $("#subcorpus_id").val()
				},
		success:function(data){
					if (data['success']){
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
					}else{
						$(".ajax").remove();
						alert('Wystąpił nieznany błąd.');
					}
				},
		error: function(request, textStatus, errorThrown){
				},
		dataType:"json"
	});		
}

function load_error_documents(tag){
	$("#documents_with_errors tbody tr").remove();
	$("#documents_with_errors tbody").html('<tr class="ajax"><td colspan="2"><img src="gfx/ajax.gif" title="czekam..."/></td></tr>');

	$.ajax({
		type: 	'POST',
		url: 	"index.php",
		data:	{ 	
					ajax: "lps_get_tag_docs", 
					tag: tag,
					subcorpus_id: $("#subcorpus_id").val()
				},
		success:function(data){
					if (data['success']){
						var html = "";
						for (var k in data['docs']){
							var t = data['docs'][k];
							html += '<tr>' +
									'<td class="tag"><a href="?page=report&corpus=3&id='+k+'">' + t.title + '</a></td>' +
									'<td style="text-align: right">' + t.count + '</td>' +
									'</tr>';							
						}
						$("#documents_with_errors tbody").html(html);
					}else{
						$(".ajax").remove();
						alert('Wystąpił nieznany błąd.');
					}
				},
		error: function(request, textStatus, errorThrown){
				},
		dataType:"json"
	});	
}

function load_interp(interp){
	$("#interp tbody").html('<tr class="ajax"><td colspan="3"><img src="gfx/ajax.gif" title="czekam..."/></td></tr>');
	$.ajax({
		type: 	'POST',
		url: 	"index.php",
		data:	{ 	
					ajax: "lps_get_interp", 
					interp: interp
				},
		success:function(data){
					if (data['success']){
						$(".ajax").remove();
						var html = "";
						var n = 0;
						for (var k in data['docs']){
							var t = data['docs'][k];
							html += '<tr>' +
									'<td>' + n + '</td>' +
									'<td>' + t.subcorpus + '</td>' +
									'<td><a href="index.php?page=report&amp;id='+t.id+'" target="_blank">' + t.title + '</td>' +
									'</tr>';
							n++;														
						}
						$("#interp tbody").append(html);
					}else{
						$(".ajax").remove();
						alert('Wystąpił nieznany błąd.');
					}
				},
		error: function(request, textStatus, errorThrown){
				},
		dataType:"json"
	});		
	
};
