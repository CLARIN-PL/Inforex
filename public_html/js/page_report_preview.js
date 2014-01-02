/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

var anaphora_target_n = 1;

// sposób dekorowania aktywnych realcji na liście Relation list
// opcje: selected - podświetla wiersz z relacją; grey - nie aktywne relacje są szare
var relation_list_decoration = "selected";

/**
 * Przypisanie akcji po wczytaniu się strony.
 */
$(document).ready(function(){
	$("sup.rel").live({
		mouseover: function(){
			$(this).addClass("hightlighted");
			var target_id = $(this).attr('target');
			$("#an" + target_id).addClass("hightlighted");
			var rel_num = $(this).text().replace("↦",""); 
			$("sup.relin").each(function(i,val){
				if($(val).text() == rel_num){
					$(val).addClass("hightlighted");
				}
			});
			if($(this).prev().hasClass("rel")){						
				$(this).prevUntil("span").prev("span").addClass("hightlighted");
			}	
			else{
				$(this).prev("span").addClass("hightlighted");
			}			
		},		
		mouseout: function(){
			$(this).removeClass("hightlighted");
			var target_id = $(this).attr('target');
			$("#an" + target_id).removeClass("hightlighted");
			var rel_num = $(this).text().replace("↦",""); 
			$("sup.relin").each(function(i,val){
				if($(val).text() == rel_num){
					$(val).removeClass("hightlighted");
				}					
			});
			$(this).prev("span").removeClass("hightlighted");
		}
	});
	
	$("sup.relin").live({
		mouseover: function(){
			$(this).addClass("hightlighted");
			var target_id = $(this).next("span").attr("id").replace('an','');
			$(this).next("span").addClass("hightlighted");
			$("sup.rel[target="+target_id+"]").each(function(i,val){
				$(val).addClass("hightlighted");
				if($(val).prev().hasClass('rel')){
					$(val).prevUntil("span","sup").prev("span").addClass("hightlighted");				
				}
				else{
					$(val).prev("span").addClass("hightlighted");
				}
			});
		},		
		mouseout: function(){
			$(this).removeClass("hightlighted");
			var target_id = $(this).next("span").attr("id").replace('an','');
			$(this).next("span").removeClass("hightlighted");
			$("sup.rel[target="+target_id+"]").each(function(i,val){				
				$(val).removeClass("hightlighted");
				$(val).prev("span").removeClass("hightlighted");	
			});
		}
	});
	
	//---------------------------------------------------------
	//Obsługa relacji
	//---------------------------------------------------------	
	$("#relation_table span,#relationList span,#annotationList span, #eventSlotsTable span ").live('mouseover',function(){
		$("#"+$(this).attr('title').split(":")[0].replace("#","")).addClass("hightlighted");
	}).live('mouseout',function(){
		$("#"+$(this).attr('title').split(":")[0].replace("#","")).removeClass("hightlighted");
		
	});
	
	$(".hideLayer").click(function(){
		if (!$(this).attr("disabled")){
			layerArray = $.parseJSON($.cookie('hiddenLayer'));
			layerId = $(this).attr("name").replace("layerId","id");
			if ($(this).hasClass("hiddenLayer")) {
				$(this).attr("title","show");
				delete layerArray[layerId];
			}
			else{
				layerArray[layerId]=1;
				$(this).attr("title","hide");
			}
			newCookie="{ ";
			$.each(layerArray,function(index,value){
				newCookie+='"'+index+'":'+value+',';
			});
			$.cookie('hiddenLayer',newCookie.slice(0,-1)+"}");
			set_visible_layers();
		}
	});

	$(".hideSublayer").click(function(){
		if (!$(this).attr("disabled")){
			layerArray = $.parseJSON($.cookie('hiddenSublayer'));
			layerId = $(this).attr("name").replace("sublayerId","id");
			if ($(this).hasClass("hiddenSublayer")) {
				$(this).attr("title","show");
				delete layerArray[layerId];
			}
			else{
				layerArray[layerId]=1;
				$(this).attr("title","hide");
			}
			newCookie="{ ";
			$.each(layerArray,function(index,value){
				newCookie+='"'+index+'":'+value+',';
			});
			$.cookie('hiddenSublayer',newCookie.slice(0,-1)+"}");
			set_visible_layers();
		}
	});
	
	$(".leftLayer").click(function(){
		$(this).parents(".layerRow").nextUntil(".layerRow").find(".leftSublayer").attr("checked","checked");
	});

	$(".leftSublayer").click(function(){
		var sublayerRow = $(this).parents(".sublayerRow");
		if (sublayerRow.prev().hasClass("layerRow")){
			sublayerRow.prev().find(".clearLayer").removeAttr("checked");
			sublayerRow.prev().find(".rightLayer").removeAttr("checked");
		}
		else{
			sublayerRow.prevUntil(".layerRow").prev().find(".clearLayer").removeAttr("checked");
			sublayerRow.prevUntil(".layerRow").prev().find(".rightLayer").removeAttr("checked");
		}
	});

	$(".rightLayer").click(function(){
		$(this).parents(".layerRow").nextUntil(".layerRow").find(".rightSublayer").attr("checked","checked");
	});

	$(".rightSublayer").click(function(){
		var sublayerRow = $(this).parents(".sublayerRow");
		if (sublayerRow.prev().hasClass("layerRow")){
			sublayerRow.prev().find(".clearLayer").removeAttr("checked");
			sublayerRow.prev().find(".leftLayer").removeAttr("checked");
		}
		else{
			sublayerRow.prevUntil(".layerRow").prev().find(".clearLayer").removeAttr("checked");
			sublayerRow.prevUntil(".layerRow").prev().find(".leftLayer").removeAttr("checked");
		}
	});

	$(".clearLayer").click(function(){
		$(this).parents(".layerRow").nextUntil(".layerRow").find(".clearSublayer").attr("checked","checked");
	});
	
	// szybkie przełączanie warstw anotacji	
	$(".layerName").click(function(){
		var act_layer = $(this).parent().parent();
		
		$.each($("#annotation_layers .layerRow .clearLayer"), function(index, value){
			$(value).click();
		});		
		$(act_layer).find(".leftLayer").click();
		
		applyLayers();
		
	});
	
	$("#applyLayer").click(function(){
		applyLayers();
	});	
	
	$(".toggleLayer").click(function(){
		if ($(this).hasClass("ui-icon-circlesmall-plus")){
			$(this).removeClass("ui-icon-circlesmall-plus").addClass("ui-icon-circlesmall-minus");
			$(this).parents(".layerRow").nextUntil(".layerRow").show();	
		} 
		else {
			$(this).removeClass("ui-icon-circlesmall-minus").addClass("ui-icon-circlesmall-plus");
			$(this).parents(".layerRow").nextUntil(".layerRow").hide();	
		}
	});
	$.each($(".toggleLayer").parents(".layerRow"), function(index, elem){
		if (!$(elem).nextUntil(".layerRow").length){
			$(elem).find(".toggleLayer").removeClass("ui-icon-circlesmall-plus").addClass("ui-icon-circlesmall-close").css("opacity","0.5").unbind("click");//.removeClass("ui-icon-circlesmall-plus").addClass("ui-icon-circlesmall-minus");
		};
	});	
	
	//split by sentences
	$("#splitSentences").change(function(){
		$.cookie("splitSentences",$(this).is(":checked"));
		set_sentences();
	});

	//show right panel
	$("#showRight").change(function(){
		$.cookie("showRight",$(this).is(":checked"));
		show_right();
	});
	
	create_anaphora_links();	
	set_stage();
	set_sentences();
	set_tokens();
	set_visible_layers();
});

//split report by sentences
function set_sentences(){
	if ($.cookie("splitSentences")=="true"){
		
		if($("sentence").length){
			$("sentence").after('<div class="eosSpan"><hr/></div>');
		}
		else{
			$("span.token.eos").each(function(){
				var $this = $(this);
				while ( $this.get(0) == $this.parent().children().last().get(0)
						&& !$this.parent().hasClass("contentBox") ){
			    	$this = $this.parent();
				}
				$this.after('<div class="eosSpan"><hr/></div>');
			});
		}
	}else 
		$("div.eosSpan").remove();
}

//show/hide right content
function show_right(){
	if ($.cookie("showRight")=="true"){
		$("#leftContent").css('width','50%');
		$("#rightContent").show();
	}
	else {
		$("#leftContent").css('width','100%');
		$("#rightContent").hide();
	}
}

function set_stage(){	
	$(".stageItem").css("cursor","pointer").click(function(){
		$.cookie('listStage',$(this).attr('stage'));
		$("#annotationList tr[stage]").hide();
		$("#annotationList tr[stage='"+$(this).attr('stage')+"']").show();
		$(".stageItem").removeClass("hightlighted");
		$(this).addClass('hightlighted');
	});	
	if (!$.cookie('listStage')) $.cookie('listStage','final');	
	var stage = $.cookie('listStage');
	$(".stageItem[stage='"+stage+"']").addClass("hightlighted");
	$("#annotationList tr[stage]").hide();
	$("#annotationList tr[stage='"+stage+"']").show();
}

function set_visible_layers(){
	if (!$.cookie('hiddenLayer')) $.cookie('hiddenLayer','{}');
	if (!$.cookie('hiddenSublayer')) $.cookie('hiddenSublayer','{}');
	if (!$.cookie('clearedLayer')) $.cookie('clearedLayer','{}');
	if (!$.cookie('clearedSublayer')) $.cookie('clearedSublayer','{}');
	if (!$.cookie('rightLayer')) $.cookie('rightLayer','{}');
	if (!$.cookie('rightSublayer')) $.cookie('rightSublayer','{}');
	if (!$.cookie('leftLayer')) $.cookie('leftLayer','{}');
	if (!$.cookie('active_annotation_types')) $.cookie('active_annotation_types','{}');
	var layerArray = $.parseJSON($.cookie('hiddenLayer'));
	$(".hideLayer").removeClass('hiddenLayer').attr("title","hide").attr("checked","checked");//.css("background-color","");
	$("#content span:not(.token)").removeClass('hiddenAnnotation');
	$("#content sup").show();
	// --- dekoracja lista relacji w zależności od widoczności warstw annotacji
	if(relation_list_decoration == "selected"){ 
		$("#relationList tr").addClass("selected");
	}
	if(relation_list_decoration == "grey"){ 
		$("#relationList span").addClass('relationAvailable').removeClass('relationGrey');
	}		
	
	$("#widget_annotation div[groupid]").children().show().filter(".hiddenAnnotationPadLayer").remove();
	$(".layerName").css("color","").css("text-decoration","");
	$("#annotationList ul").show();
	
	$.each(layerArray,function(index,value){
		layerId = index.replace("id","");
		$('.hideLayer[name="layerId'+layerId+'"]').addClass('hiddenLayer').attr("checked","").attr("title","show");
		$("#content span[groupid="+layerId+"]").addClass('hiddenAnnotation');
		$("#content sup[targetgroupid="+layerId+"]").hide();
		$("#content sup[sourcegroupid="+layerId+"]").hide();
		
		// --- dekoracja lista relacji w zależności od widoczności warstw annotacji
		if(relation_list_decoration == "selected"){ 
			$("#relationList td[sourcegroupid="+layerId+"]").parent().removeClass("selected");
			$("#relationList td[targetgroupid="+layerId+"]").parent().removeClass("selected");
		}
		if(relation_list_decoration == "grey"){ 
			$("#relationList td[sourcegroupid="+layerId+"] span").addClass('relationGrey').removeClass('relationAvailable');
			$("#relationList td[targetgroupid="+layerId+"] span").addClass('relationGrey').removeClass('relationAvailable');
		}
		
		$('#annotationList ul[groupid="'+layerId+'"]').hide();
	});
	
	layerArray = $.parseJSON($.cookie('hiddenSublayer'));
	$(".hideSublayer").removeClass('hiddenSublayer').attr("title","hide").attr("checked","checked");
	$("#widget_annotation li[subsetid]").children().show().filter(".hiddenAnnotationPadSublayer").remove();
	$("#annotationList li.subsetName").show();
	
	$.each(layerArray,function(index,value){
		layerId = index.replace("id","");
		$('.hideSublayer[name="sublayerId'+layerId+'"]').addClass('hiddenSublayer').attr("checked","").attr("title","show");
		$("#content span[subgroupid="+layerId+"]").addClass('hiddenAnnotation');
		$("#content sup[targetsubgroupid="+layerId+"]").hide();
		$("#content sup[sourcesubgroupid="+layerId+"]").hide();
		
		// --- dekoracja lista relacji w zależności od widoczności warstw annotacji
		if(relation_list_decoration == "selected"){ 
			$("#relationList td[sourcesubgroupid="+layerId+"]").parent().removeClass("selected");
			$("#relationList td[targetsubgroupid="+layerId+"]").parent().removeClass("selected");
		}
		if(relation_list_decoration == "grey"){ 
			$("#relationList td[sourcesubgroupid="+layerId+"] span").addClass('relationGrey').removeClass('relationAvailable');
			$("#relationList td[targetsubgroupid="+layerId+"] span").addClass('relationGrey').removeClass('relationAvailable');
		}
		
		$('#widget_annotation li[subsetid="'+layerId+'"]').append('<div class="hiddenAnnotationPadSublayer">This annotation sublayer was hidden (see Annotation layers)</div>').children("ul").hide();
		$('#annotationList li[subsetid="'+layerId+'"]').hide();
	});
	//ukrywa elementy relin, jeżeli nie wskazuje na nie żaden widoczny element rel
	$("sup.relin:visible").each(function(index,element){
		var target_num1 = "↷"+$(this).text();
		var target_num2 = "⇢"+$(this).text();
		var count_visible_rel = 0;
		$("sup.rel:visible").each(function(i,val){
			if($(val).text() == target_num1 || $(val).text() == target_num2){
				count_visible_rel++;				
			} 
		});		
		if(count_visible_rel == 0){
			$(element).hide();
		}
	});
	
	layerArray = $.parseJSON($.cookie('clearedLayer'));
	$.each(layerArray,function(index,value){
		layerId = index.replace("id","");
		$('.clearLayer[name="layerId'+layerId+'"]').addClass('clearedLayer').attr("checked","checked");
		$('.hideLayer[name="layerId'+layerId+'"]').attr("disabled", "disabled");
		$('.layerRow[setid='+layerId+'] a').css('color', 'grey');
		var $container = $('#widget_annotation div[groupid="'+layerId+'"]');
		if ($container.children(".hiddenAnnotationPadLayer").length==0){
			$container.children("a").attr("title", "This annotation layer is hidden. See View configuration.");
			$container.prev().html('<div>' + $container.prev().children("a").children("b").text() + '</div>');
			$container.children("ul").hide();
		}
//		else $container.children(".hiddenAnnotationPadLayer").text("This annotation layer is hidden");
	});

	layerArray = $.parseJSON($.cookie('clearedSublayer'));
	$.each(layerArray,function(index,value){
		layerId = index.replace("id","");
		$('.clearSublayer[name="sublayerId'+layerId+'"]').addClass('clearedSublayer').attr("checked","checked");
		$('.hideSublayer[name="sublayerId'+layerId+'"]').attr("disabled", "disabled");
		$('.sublayerRow[subsetid='+layerId+'] a').css('color', 'grey');
		var $container = $('#widget_annotation li[subsetid="'+layerId+'"]');
		if ($container.children(".hiddenAnnotationPadSublayer").length==0)
			$container.append('<div class="hiddenAnnotationPadSublayer">This annotation layer is hidden</div>').children("ul").hide();
		else $container.children(".hiddenAnnotationPadSublayer").text("This annotation layer is hidden");
	});
	
	layerArray = $.parseJSON($.cookie('rightLayer'));
	$.each(layerArray,function(index,value){
		layerId = index.replace("id","");
		$('.rightLayer[name="layerId'+layerId+'"]').attr("checked","checked");
	});

	layerArray = $.parseJSON($.cookie('leftLayer'));
	$.each(layerArray,function(index,value){
		layerId = index.replace("id","");
		$('.leftLayer[name="layerId'+layerId+'"]').attr("checked","checked");
	});

	layerArray = $.parseJSON($.cookie('rightSublayer'));
	$.each(layerArray,function(index,value){
		layerId = index.replace("id","");
		$('.rightSublayer[name="sublayerId'+layerId+'"]').attr("checked","checked");//.parent().prev().children().attr("disabled","disabled").parent().prev().children("span").css("text-decoration","line-through");
	});
}

//obsluga tokenow
function set_tokens(){
	$(".token").removeAttr("groupid").addClass("hiddenAnnotation");
}

//---------------------------------------------------------
// Po załadowaniu strony
//---------------------------------------------------------
$(document).ready(function(){
	$("#annotations").tablesorter(); 
	$(".autogrow").autogrow();
});

/** 
 * Tworzy wizualizację połączeń anaforycznych. Indeksuje anotacje, które biorą udział w relacji.
 */
function create_anaphora_links(){
	$("sup.relin").remove();
	$("sup.rel").each(function(){
		var target_id = $(this).attr('target');
		$("#an" + target_id).addClass("_anaphora_target");
		$(this).attr('targetgroupid',$("#an" + target_id).attr('groupid'));
		$(this).attr('targetsubgroupid',$("#an" + target_id).attr('subgroupid'));
		$(this).attr('sourcesubgroupid',$("#an" + $(this).attr('sourcegroupid')).attr('subgroupid'));
		$(this).attr('sourcegroupid',$("#an" + $(this).attr('sourcegroupid')).attr('groupid'));
	});
	$("span._anaphora_target").each(function(){
		$(this).before("<sup class='relin' targetsubgroupid="+$(this).attr('subgroupid')+" targetgroupid="+$(this).attr('groupid')+">"+anaphora_target_n+"</sup>");
		$(this).removeClass("_anaphora_target");
		anaphora_target_n++;
	});
	$("sup.rel").each(function(){
		var target_id = $(this).attr('target');
		var target_anaphora_n = $("#an" + target_id).prev("sup").text();
		var title = $(this).attr("title");
		if(title == 'Continous'){
			$(this).text("⇢" + target_anaphora_n);
			$(this).css({color: "#0055BB", background: "#EEFFFF"});
		}
		else{
			$(this).text("↷" + target_anaphora_n);
		}		 
		$("sup.relin").each(function(i,val){
			if($(val).text() == target_anaphora_n){
				$(val).attr("title",$(val).attr("title")+" "+title);
			}
		});
	});		
}


function applyLayers(){
	layerArray = $.parseJSON($.cookie('clearedLayer'));
	layerArray2 = $.parseJSON($.cookie('hiddenLayer'));
	layerArray3 = $.parseJSON($.cookie('rightLayer'));
	layerArray4 = $.parseJSON($.cookie('rightSublayer'));
	layerArray5 = $.parseJSON($.cookie('clearedSublayer'));
	layerArray6 = $.parseJSON($.cookie('active_annotation_types'));
	layerArray7 = $.parseJSON($.cookie('leftLayer'));
	
	$.each($(".clearLayer"),function(index, value){
		layerId = $(value).attr("name").replace("layerId","id");
		if (!$(value).attr("checked")) {
			delete layerArray[layerId];
			delete layerArray2[layerId];
		}
		else {
			layerArray[layerId]=1;
			layerArray2[layerId]=1;
		}			
	});
	$.each($(".clearSublayer"),function(index, value){
		layerId = $(value).attr("name").replace("sublayerId","id");
		if (!$(value).attr("checked")) {
			delete layerArray5[layerId];
			delete layerArray4[layerId];
		}
		else {
			layerArray5[layerId]=1;
			layerArray4[layerId]=1;
		}			
	});
	$.each($(".rightLayer"),function(index, value){
		layerId = $(value).attr("name").replace("layerId","id");
		if ($(value).attr("checked")) {
			layerArray3[layerId]=1;
		}
		else {
			delete layerArray3[layerId];
		}			
	});		
	$.each($(".rightSublayer"),function(index, value){
		layerId = $(value).attr("name").replace("sublayerId","id");
		if ($(value).attr("checked")) {
			layerArray4[layerId]=1;
		}
		else {
			delete layerArray4[layerId];
		}			
	});		
	$.each($(".relation_sets"),function(index, value){
		layerId = "id" + $(value).val();
		if ($(value).attr("checked")) {
			layerArray6[layerId]=1;
		}
		else {
			delete layerArray6[layerId];
		}			
	});
	$.each($(".leftLayer"),function(index, value){
		layerId = $(value).attr("name").replace("layerId","id");
		if ($(value).attr("checked")) {
			layerArray7[layerId]=1;
		}
		else {
			delete layerArray7[layerId];
		}			
	});	
	
	var newCookie="{ ";
	$.each(layerArray,function(index,value){
		newCookie+='"'+index+'":'+value+',';
	});
	$.cookie('clearedLayer',newCookie.slice(0,-1)+"}");
	
	newCookie="{ ";
	$.each(layerArray5,function(index,value){
		newCookie+='"'+index+'":'+value+',';
	});
	$.cookie('clearedSublayer',newCookie.slice(0,-1)+"}");
	
	newCookie="{ ";
	$.each(layerArray2,function(index,value){
		newCookie+='"'+index+'":'+value+',';
	});
	$.cookie('hiddenLayer',newCookie.slice(0,-1)+"}");
	
	newCookie="{ ";
	$.each(layerArray3,function(index,value){
		newCookie+='"'+index+'":'+value+',';
	});
	$.cookie('rightLayer',newCookie.slice(0,-1)+"}");
	
	newCookie="{ ";
	$.each(layerArray4,function(index,value){
		newCookie+='"'+index+'":'+value+',';
	});
	$.cookie('rightSublayer',newCookie.slice(0,-1)+"}");
	
	newCookie="{ ";
	$.each(layerArray6,function(index,value){
		newCookie+='"'+index+'":'+value+',';
	});
	$.cookie('active_annotation_types',newCookie.slice(0,-1)+"}");

	newCookie="{ ";
	$.each(layerArray7,function(index,value){
		newCookie+='"'+index+'":'+value+',';
	});
	$.cookie('leftLayer',newCookie.slice(0,-1)+"}");
	
	if (document.location.href[document.location.href.length-1]=="#") document.location.href=document.location.href.slice(0,-1);
	document.location = document.location;	
}
