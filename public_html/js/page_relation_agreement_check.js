/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

var relation_set_name = "_rel_set_relation_agreement_check";
var relation_type_name = "_rel_type_relation_agreement_check";

var annotation_set_name = "_ann_set_relation_agreement_check";
var annotation_subset_name = "_ann_subset_relation_agreement_check";
var annotation_type_name = "_ann_type_relation_agreement_check";
$(function(){
    setupAnnotationTypeTree();
    setupRelationAgreementTypeTree();

    $("#apply").click(function(){
        applyRelationAgreementTree(function(ann_layers, ann_subsets, ann_types){});
    });

    $("a.filter_by_category_name").click(function(){
        $("#agreement td").css("background",  "");
        $(this).closest("table").find("td").css("background",  "");
        $(this).closest("tr").children("td").css("background",  "#ffcccc");
        $("#agreement td." + $(this).text()).parent("tr").children("td").css("background",  "#ffcccc");
    });
});

/**
 * Store the current selection of relation & annotation types to the cookie.
 * The list of selected annotation types is stored as a variable named [CORPUS_ID]_annotation_lemma_types,
 * where [CORPUS_ID] is the identifier of the current corpus.
 *
 * @param on_apply Feedback function called after storing the selection to the cookie.
 * 					Signature: on_apply(ann_layers, ann_subsets, ann_types)
 */
function applyRelationAgreementTree(on_apply){

    /* Zapisz zaznaczone zbiory anotacji do ciasteczka */
    var ann_layers = new Array();
    $("input[type=checkbox].group_cb").each(function(i,checkbox){
        if($(checkbox).prop("checked")){
            ann_layers.push($(checkbox).prop("name").split("-")[1]);
        }
    });
    $.cookie(corpus_id + annotation_set_name, ann_layers);

    /* Zapisz zaznaczone zbiory do ciasteczka */
    var ann_subsets = new Array();
    $("input[type=checkbox].subset_cb").each(function(i,checkbox){
        if($(checkbox).prop("checked")){
            ann_subsets.push($(checkbox).prop("name").split("-")[1]);
        }
    });
    $.cookie(corpus_id + annotation_subset_name, ann_subsets);

    /* Zapisz zaznaczone typy anotacji do ciasteczka */
    var ann_types = new Array();
    $("input[type=checkbox].type_cb").each(function(i,checkbox){
        if($(checkbox).prop("checked")){
            ann_types.push($(checkbox).prop("name").split("-")[1]);
        }
    });
    $.cookie(corpus_id + annotation_type_name, ann_types);

    var rel_sets = new Array();
    $("input[type=checkbox].relation_group_cb").each(function(i,checkbox){
        if($(checkbox).prop("checked")){
            rel_sets.push($(checkbox).prop("name").split("-")[1]);
        }
    });
    console.log(rel_sets);
    $.cookie(corpus_id + relation_set_name, rel_sets);

    /* Zapisz zaznaczone zbiory do ciasteczka */
    var rel_types = new Array();
    $("input[type=checkbox].relation_type_cb").each(function(i,checkbox){
        if($(checkbox).prop("checked")){
            rel_types.push($(checkbox).prop("name").split("-")[1]);
        }
    });
    $.cookie(corpus_id + relation_type_name, rel_types);
    on_apply(ann_layers, ann_subsets, ann_types);

    // Zapisz anotatorów do ciasteczek.
    var annotator_a = $("input:radio.annotator_a_id:checked").val();
    var annotator_b = $("input:radio.annotator_b_id:checked").val();

    $.cookie("relation_check_annotator_a_id", annotator_a);
    $.cookie("relation_check_annotator_b_id", annotator_b);

    // Zapisz wybrane flagi

    var flag = $(".corpus_flag_id").val();
    var flag_type = $(".flag_type").val();

    if(flag !== "Select flag" && flag_type !== "type"){
        $.cookie("relation_check_flag", flag);
        $.cookie("relation_check_flag_type", flag_type);
    } else{
        $.cookie("relation_check_flag", null);
        $.cookie("relation_check_flag_type", null);
    }

    //Zapisz subkorpusy
    var subcorpora = new Array();
    $(".subcorpus_id:checked").each(function(){
        subcorpora.push($(this).val() + ",");
    });
    $.cookie("relation_check_subcorpora", subcorpora);
    window.location.href = "index.php?page=relation_agreement_check&corpus="+corpus_id;
}

/**
 * Ustawia zdarzenia zwijania, rozwijania i klikania w checkboxy.
 */
function setupRelationAgreementTypeTree(){
    $(".relationToggleLayer").click(function(){
        if ($(this).hasClass("ui-icon-circlesmall-plus")){
            $(this).removeClass("ui-icon-circlesmall-plus").addClass("ui-icon-circlesmall-minus");
            $(this).parents(".relationLayerRow").nextUntil(".relationLayerRow",".relationSublayerRow").show();
        }
        else {
            $(this).removeClass("ui-icon-circlesmall-minus").addClass("ui-icon-circlesmall-plus");
            $(this).parents(".relationLayerRow").nextUntil(".relationLayerRow").hide();
        }
    });

    $.each($(".relationToggleLayer").parents(".relationLayerRow"), function(index, elem){
        if (!$(elem).nextUntil(".relationLayerRow").length){
            $(elem).find(".relationToggleLayer").removeClass("ui-icon-circlesmall-plus").addClass("ui-icon-circlesmall-close").css("opacity","0.5").unbind("click");
        }
    });

    $("input[type=checkbox].relation_group_cb").click(function(){
        var checked = $(this).is(":checked");
        $(this).closest("tr").nextUntil(".relationLayerRow").each(function(){
            $(this).find("input[type=checkbox]").prop("checked", checked);
        });
    });

    $("input[type=checkbox].relation_type_cb").click(function(){
        var checked = $(this).is(":checked");
        var checked_siblings = 0;
        var relation_set_id = $(this).closest("tr").attr("relation_set_id");
        var layer_checkbox = $("#relation_set_"+relation_set_id).find("input[type=checkbox]");

        $("#set_"+relation_set_id).nextUntil(".relationLayerRow").each(function(){
            if($(this).find("input[type=checkbox]").is(":checked")){
                checked_siblings += 1;
            }
        });

        if(checked_siblings > 0 || checked){
            layer_checkbox.prop("checked", true);
        } else{
            layer_checkbox.prop("checked", false);
        }
    });

    var rel_layers = $.cookie(corpus_id + relation_set_name);
    rel_layers = rel_layers === null ? [] : rel_layers.split(",");

    var rel_types = $.cookie(corpus_id + relation_type_name);
    rel_types = rel_types === null ? [] : rel_types.split(",");

    if(rel_layers){
        $.each(rel_layers, function(i,e){
            var checkbox = $("input[name=relationLayerId-"+parseInt(e)+"]");
            $(checkbox).attr("checked", true);
        });
    }

    if(rel_types){
        $.each(rel_types, function(i,e){
            var checkbox = $("input[name=relationTypeId-"+parseInt(e)+"]");
            $(checkbox).attr("checked", true)
            //var subset_cb = unfoldSubset(checkbox)
            //unfoldLayer(subset_cb);
        });
    }
}
