/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
	
	var request_count = 0;
	
	$( "#wsdl" ).selectmenu({width: "100%"});
	
	$("#ner-process").click(function(){

		var text = $.trim($("#ner-text").val());
		var wsdl = $.trim($("#wsdl option:selected").val());
		var model_name = $.trim($("#wsdl option:selected").text());
		
		if ( text.length > 100000 ){
			alert("The text cannot be processed because is longer than 100k characters.");
			return;
		}
		
		if ( text == "" )
			dialog_error("Enter text to analyze");
		else{
			var panelHtml = $(".panel_template").html();
			var panel = $(panelHtml);
			$(".panel-results > div.panel-body").prepend(panel);
			$(".panel-results > div.panel-body").scrollTop();
			$(panel).lobiPanel({
		    	reload: false,
		    	sortable: true,
		    });
			
			request_count += 1;
			$(panel).find(".panel-heading h4").html("Request #" + request_count + " <span class='ajax_indicator'> <img class='ajax_indicator' src='gfx/ajax.gif'/> <small>Processing...</small></span>");
			$(panel).find("b.model").text(model_name);
			$(panel).find(".ner-html").html("<div style='color: #aaa'>" + text + "</div>");
			$(panel).on('onFullScreen.lobiPanel', function(ev, lobiPanel){
				fit_content_to_panel(panel);
			});
			$(panel).on('onUnpin.lobiPanel', function(ev, lobiPanel){
				fit_content_to_panel(panel);
			});
			$(panel).on('onResize.lobiPanel', function(ev, lobiPanel){
				fit_content_to_panel(panel);
			});
			
			var params = {
				text: text,
				model: wsdl,
				wsdl: wsdl	
			};
			
			var success = function(data){
				$(panel).find(".ner-html").html(data.html);								
				$(panel).find(".ner-html").css("color", "black");
				$(panel).find(".ner-annotations").html(data.annotations);
				$(panel).find(".ner-duration").html("Processed in " + data.duration);
				$(panel).find(".ner-annotations").css("color", "black");				
				$(panel).find(".ner-annotations span").hover(function(){
					var cl = $(this).attr("key");
					$(".ner-html .selected").removeClass("selected");
					$("."+cl).addClass("selected");
				});
				$(panel).find(".ner-annotations span").click(function() {
					var cl = $(this).attr("key");
					var toScroll = $(this).closest(".panel-body").find(".ner-html");
					var top = toScroll.scrollTop() - toScroll.offset().top +  $("."+cl).offset().top - 30;
					if ( top < 0 ) top = 0;
					toScroll.animate({scrollTop: top}, 1000);
				});
			};
			
			var complete = function(){
				$(panel).find(".ajax_indicator").remove();
			};			
			
			doAjax("ner_process", params, success, null, complete);			
		}
		
	});
	
	$("#samples a").click(function(){
		$("#ner-text").val($(this).attr("title"));
	});
	
    $('.panel-form').lobiPanel({
    	editTitle: false,
    	reload: false,
        close: false,
    });

    $('.panel-results').lobiPanel({
    	editTitle: false,
    	reload: false,
        close: false,
    });
});

/**
 * 
 * @param panel
 * @returns
 */
function fit_content_to_panel(panel){
    $(panel).find(".ner-annotations").hide();
    $(panel).find(".ner-html").hide();
    var height = $(panel).outerHeight(true) - $(panel).find(".panel-body table").outerHeight(true) - 100;
    $(panel).find(".ner-annotations").css("height", height + "px");
    $(panel).find(".ner-html").css("height", height + "px");;
    $(panel).find(".ner-annotations").show();
    $(panel).find(".ner-html").show();	
}
