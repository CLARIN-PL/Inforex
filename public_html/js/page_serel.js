function wait_elements(question){	
	$(".question").attr('disabled', 'disabled');
	set_element_html($(".measure"), "<img class='ajax_indicator' src='gfx/ajax.gif'/>");
	set_element_html($(".question_result"), "<b>"+question+"</b>");
	set_element_html($(".relation_type"), "<img class='ajax_indicator' src='gfx/ajax.gif'/>");
	set_element_html($(".relation_subtype"), "<img class='ajax_indicator' src='gfx/ajax.gif'/>");
	set_element_html($(".type"), "<img class='ajax_indicator' src='gfx/ajax.gif'/>");
	set_element_html($(".argument"), "<img class='ajax_indicator' src='gfx/ajax.gif'/>");
	set_element_html($(".sql_code"), "<img class='ajax_indicator' src='gfx/ajax.gif'/>");
	set_element_html($(".results_list"), "<img class='ajax_indicator' src='gfx/ajax.gif'/>");
	set_element_html($(".result_element"), "");
	set_element_html($(".result_element_title"), "Szczegóły");
}

function show_semquel_data(semquel_data){
	set_element_html($(".measure"), semquel_data.measure);
	set_element_html($(".relation_type"), semquel_data.relation_type);
	set_element_html($(".relation_subtype"), semquel_data.relation_subtype);
	set_element_html($(".type"), semquel_data.object_type);
	set_element_html($(".argument"), semquel_data.argument);
	set_element_html($(".sql_code"), "<code style=\"white-space: pre\">"+semquel_data.semql+"</code>");
}

function set_element_html($element, html){
	$element.html(html);
}

function run_semql(question){
	$.ajax({
		url : "index.php",
		dataType : "json",
		type : "post",
		data : {
			ajax : "semquel_run",
			question : question
		},				
		success : function(data){
			ajaxErrorHandler(data,
				function(){	
					var semquel_data = data.output[0];
					show_semquel_data(semquel_data);
					$("#box-interpretation").show();
					$("#ajax-big").hide();
					
					//get_sql_results(semquel_data.semql);
					//get_sql_results("SELECT ans.text, GROUP_CONCAT(r.relation_id) AS relation_ids FROM relations r JOIN annotations ans ON r.annotation_source_id = ans.annotation_id JOIN annotations ant ON r.annotation_target_id = ant.annotation_id JOIN annotation_types ans_type ON ans.annotation_type_id = ans_type.annotation_type_id JOIN annotation_types ant_type ON ant.annotation_type_id = ant_type.annotation_type_id JOIN relation_types r_type ON r.relation_type_id = r_type.relation_type_id WHERE ant.text = 'Polsce' AND ant_type.type = 'country_nam' AND ans_type.type = 'city_nam' GROUP BY ans.text");
					$(".question").attr('disabled', '');
					$(".buttonRun").attr('disabled', '');
				},
				function(){
					run_semql(question);
				}
			);								
		}
	});
}

function get_sql_results(semquel){
	$.ajax({
		url : "index.php",
		dataType : "json",
		type : "post",
		data : {
			ajax : "semquel_get_sql",
			semquel : semquel
		},				
		success : function(data){
			ajaxErrorHandler(data,
				function(){	
					$(".results_list").html("");
					$.each(data.output, function(key, value){
						$(".results_list").append((key == 0 ? "" : ", ")+"<a href=\"#\" id=\""+value.relation_ids+"\">"+value.text+"</a> ("+value.relation_ids.split(',').length+")");
					});
					$(".buttonRun").attr('disabled', '');
					
				},
				function(){
					get_sql_results(semquel);
				}
			);								
		}
	});
}

function get_result_descriptions(ids, result_name){
	$(".result_element_title").html("Szczegóły dla <small>&raquo;"+result_name+"&laquo;</small> <img class='ajax_indicator' src='gfx/ajax.gif'/>");
	$.ajax({
		url : "index.php",
		dataType : "json",
		type : "post",
		data : {
			ajax : "semquel_get_result",
			id_list : ids
		},				
		success : function(data){
			ajaxErrorHandler(data,
				function(){	
					var html = "<ol>";					
					$.each(data.output, function(key, value){
						html += "<li>"+ value +"</li>";
					});					
					html += "</ol>";
					$(".result_element").html(html);
					$(".result_element_title").html("Szczegóły dla <small>&raquo;"+result_name+"&laquo;</small>");
				},
				function(){
					get_result_descriptions(ids, result_name);
				}
			);								
		}
	});
}

$(function(){
	$(".buttonRun").live("click",function(){
		var question = $(".question").val();
		wait_elements(question);		
		$(this).attr('disabled', 'disabled');
		$("#ajax-big").show();
		run_semql(question);		
	});

	$(".show_hide_semql").live({
		click: function(){
			if ($(this).hasClass("showItems")){
				$(this).removeClass("showItems");
				$(this).html("ukryj");
				$(".semquel_results").show();
			}
			else{  
				$(this).addClass("showItems");
				$(this).html("pokaż");
				$(".semquel_results").hide();
			}
			return false;
		}				
	});
	
	$("a[href=#]").live({
		click: function(){
			if ($(this).attr("id") != ''){
				$("a[href=#]").css("font-weight", "normal");
				$(this).css("font-weight", "bold");

				get_result_descriptions($(this).attr("id"), $(this).text());
			}
			return false;
		}				
	});
});
