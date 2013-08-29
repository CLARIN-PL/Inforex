/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
	/* 
	Obsługa tabeli z typami relacji (po kliknięciu w dany typ relacji wczytane zostają dane wybranego typu relacji
	i wykonane zostaje zapytanie ajax zwracające listę relacji danego typu, na podstawie której aktualizowana jest 
	tabela z listami relacji i aktualizowane są odnośniki do podstron z relacjami)
	*/
	$("tr.relationName").click(function(){
		var button = this;
		$(button).after("<img class='ajax_indicator' src='gfx/ajax.gif'/>");
		$(button).attr("disabled", "disabled");
		var all_relations = $(this).find("td.relationNameCount").html(); 
		var relation_set_id = $(this).attr('id');
		var corpus_id = $(".corpus_id").attr('id');
		var limit_from = 0;
		var limit_to = $(".relation_limit").attr('id');
		
		var params = {
			corpus_id: corpus_id,
			relation_set_id: relation_set_id,
			limit_from: limit_from,
			limit_to: limit_to
		};
		
		var success = function(data){
			var html = "";
			for (a in data){
				html += "<tr>";
				v = data[a];
				html += "<td style='vertical-align: middle'>" + data[a]['document_id'] + "</td>";
				html += "<td style='vertical-align: middle'>" + data[a]['subcorpus_name'] + "</td>";
				html += "<td style='vertical-align: middle'>" + data[a]['source_text'] + "</td>";
				html += "<td style='vertical-align: middle'>" + data[a]['source_type'] + "</td>";
				html += "<td style='vertical-align: middle'>" + data[a]['target_text'] + "</td>";
				html += "<td style='vertical-align: middle'>" + data[a]['target_type'] + "</td>";
				html += "</tr>";
			}
			$("#relation_statistic_items").html(html);
			html = "";
			var i=0;
			var limit = parseInt(limit_to);
			if(limit > all_relations){
				html += "<span class='relationPage inactive' id=" + relation_set_id + "><span>[" + i + " - " + all_relations + "]</span></span>";
			}
			else{
				html += "<span class='relationPage inactive' id=" + relation_set_id + "><span>[" + i + " - " + limit + "] </span></span>";
				i += limit;
				while(i <= all_relations){
					var to = i+limit;
					if(to < all_relations){
						html += "<span class='relationPage active' id=" + relation_set_id + "><a href='#'>[" + i + " - " + to + "] </a></span>";								
					}
					else{
						html += "<span class='relationPage active' id=" + relation_set_id + "><a href='#'>[" + i + " - " + all_relations + "] </a></span>";								
					}							
					i += limit;
				}
			}						 
			$("#relation_pages").html(html);
		};
		
		var error = function(code){
			$("#messageBox").text("Load failed.");
		}
		
		var complete = function(){
			$(button).removeAttr("disabled");
			$(".ajax_indicator").remove();
		};
		
		
		doAjax("report_get_relation_statistics", params, success, null, complete);
	});
	
	/* 
	Obsługa odnośników do podstron z relacjami (po kliknięciu na aktywny link do podstrony wykonywane 
	zostaje zapytanie ajax zwracające listę relacji danego typu z zadanego obszaru "link jest postaci [od - do]", 
	na podstawie zapytania aktualizowana jest tabela z listami relacji i aktualizowane są odnośniki do 
	podstron z relacjami "aktywne/nieaktywne")
	*/	
	$("span.relationPage").live({
		click: function(){
			var button = this;
			$(button).after("<img class='ajax_indicator' src='gfx/ajax.gif'/>");
			$(button).attr("disabled", "disabled");
			var html_this = $(this).html();
			var sub_html_this = html_this.match(/\d+/g);
			var relation_set_id = $(this).attr('id');
			var corpus_id = $(".corpus_id").attr('id');
			var limit_from = sub_html_this[0];
			var limit_to = $(".relation_limit").attr('id');
			$(this).removeClass("active");
			$(this).addClass("onAction");
			
			var params = {
				corpus_id: corpus_id,
				relation_set_id: relation_set_id,
				limit_from: limit_from,
				limit_to: limit_to
			};
			
			var success = function(data){
				var html = "";
				for (a in data){
					html += "<tr>";
					v = data[a];
					html += "<td style='vertical-align: middle'>" + data[a]['document_id'] + "</td>";
					html += "<td style='vertical-align: middle'>" + data[a]['subcorpus_name'] + "</td>";
					html += "<td style='vertical-align: middle'>" + data[a]['source_text'] + "</td>";
					html += "<td style='vertical-align: middle'>" + data[a]['source_type'] + "</td>";
					html += "<td style='vertical-align: middle'>" + data[a]['target_text'] + "</td>";
					html += "<td style='vertical-align: middle'>" + data[a]['target_type'] + "</td>";
					html += "</tr>";
				}
				$("#relation_statistic_items").html(html);		
			
				var html = $("#relation_pages").find('span.relationPage.inactive').text();
				$("#relation_pages").find('span.relationPage.inactive').html("<a href='#' >" + html +"</a>");
				$("#relation_pages").find('span.relationPage.inactive').removeClass("inactive").addClass("active");
			
				var html = $("#relation_pages").find('span.relationPage.onAction').text();
				$("#relation_pages").find('span.relationPage.onAction').html("<span>" + html +"</span>");
				$("#relation_pages").find('span.relationPage.onAction').removeClass("onAction").addClass("inactive");
			};
			
			var error = function(code){
				$("#messageBox").text("Load failed.");
			};
			
			var complete = function(){
				$(button).removeAttr("disabled");
				$(".ajax_indicator").remove();
			};
			
			doAjax("report_get_relation_statistic", params, success, error, complete);
		}	
	});		
});