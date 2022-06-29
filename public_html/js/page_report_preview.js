/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */
var anaphora_target_n = 1;
var wRelationSets = null;

var url = $.url(window.location.href);
var corpus_id = url.param("corpus");
var report_id = url.param("id");

/**
 * Przypisanie akcji po wczytaniu się strony.
 */
$(document).ready(function(){
    loadAnnotationTypesFromTemplates(corpus_id);
    wRelationSets = new WidgetRelationSetSelector("#relation-sets", corpus_id);
    wRelationSets.load();

    // Zapis aktualnej konfiguracji i przeładowanie strony
    $("#applyLayer").click(function(){
        applyAnnotationTypeTree(function(ann_layers, ann_subsets, ann_types){});
        saveViewConfiguration();
        wRelationSets.save();
        if (document.location.href[document.location.href.length-1]=="#") {
            document.location.href=document.location.href.slice(0, -1);
        }
        document.location = document.location;
        return false;
    });

    $("span.annotation").on({
    	mouseover: function(e){
    		$(this).addClass("highlighted");
    		e.stopPropagation();
		},
		mouseout: function(e){
            $("span.annotation.highlighted").removeClass("highlighted");
            e.stopPropagation();
		}
	});

    $("sup.rel").on({
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
	
	$("sup.relin").on({
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
	$("#relation_table span,#relationList span,#annotationList span, #eventSlotsTable span ").on('mouseover',function(){
		$(getAnnotationIdFromTitle((this).attr('title'))).addClass("hightlighted");
	}).on('mouseout',function(){
		$(getAnnotationIdFromTitle((this).attr('title'))).removeClass("hightlighted");
	});


	//split by sentences
	$("#splitSentences").change(function(){
		$.cookie("splitSentences",$(this).is(":checked"));
		setSentences();
	});

	$("#content span").on("mousemove", function(){
		if ( !$(this).hasClass("token") ) {
            $("#content span.highlight").removeClass("highlight");
            $(this).addClass("highlight");
        }
	});

    setupAnnotationTypeTree();

	displatyAnnotationRelations();
	setStage();
	setSentences();

    $("#annotations").tablesorter();
    $(".autogrow").autogrow();

});

function getAnnotationIdFromTitle(title){
	if ( title == undefined ){
		return "#none";
	} else {
		return "#" + title.split(":")[0].replace("#","");
	}
}

//split report by sentences
function setSentences(){
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

/**
 *
 */
function setStage(){
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

/**
 * Tworzy wizualizację połączeń anaforycznych. Indeksuje anotacje, które biorą udział w relacji.
 */
function displatyAnnotationRelations(){
	$("sup.relin").remove();
	$("sup.rel").each(function(){
		var target_id = $(this).attr('target');
		$(".ann#an" + target_id).addClass("_relation_target");
		$(this).attr('targetgroupid',$("#an" + target_id).attr('groupid'));
		$(this).attr('targetsubgroupid',$("#an" + target_id).attr('subgroupid'));
		$(this).attr('sourcesubgroupid',$("#an" + $(this).attr('sourcegroupid')).attr('subgroupid'));
		$(this).attr('sourcegroupid',$("#an" + $(this).attr('sourcegroupid')).attr('groupid'));
	});
	$("span._relation_target").each(function(){
		$(this).before("<sup class='relin' targetsubgroupid="+$(this).attr('subgroupid')+" targetgroupid="+$(this).attr('groupid')+">"+anaphora_target_n+"</sup>");
		$(this).removeClass("_anaphora_target");
		anaphora_target_n++;
	});
	$("sup.rel").each(function(){
		var target_id = $(this).attr('target');
		var target_anaphora_n = $(".ann#an" + target_id).prev("sup").text();
		var title = $(this).attr("title");
		if(title == 'Continous'){
			$(this).text("⇢" + target_anaphora_n);
			$(this).css({color: "#0055BB", background: "#EEFFFF"});
		} else {
			$(this).text("↷" + target_anaphora_n);
		}
		$("sup.relin").each(function(i,val){
			if($(val).text() == target_anaphora_n){
				$(val).attr("title",$(val).attr("title")+" "+title);
			}
		});
	});
}

/**
 * Zapisuje parametry widoku do ciasteczek.
 */
function saveViewConfiguration(){
	$.cookie('stage_annotations', $("select[name=stage_annotations] option:selected").val());
    $.cookie('stage_relations', $("select[name=stage_relations] option:selected").val());
	$.cookie('annotationMode', $("input[name=annotation_mode]:checked").val());

	var preview_user = $("#preview_user_select").val();
	if(preview_user !== '-'){
        $.cookie(report_id + '_preview_user', preview_user);
    } else{
        $.cookie(report_id + '_preview_user', null);
    }
}
