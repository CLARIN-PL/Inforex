/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

/* Obiekt reprezentujący edytor anotacji */
var wAnnotationDetails = null;
var wAnnotationRelations = null;
var wAnnotationPanel = null;
var globalSelection = null;

$(function(){

    setupAnnotationMode();

    wAnnotationDetails = new WidgetAnnotation("#annotation-details", function(){
        $("#col-config").show();
        $("#columnAnnotation").hide();
	});
    wAnnotationDetails.onUpdate(updateAnnotationOnList);

    wAnnotationRelations = new WidgetAnnotationRelations("#annotation-relations", "#content");
    wAnnotationPanel = new WidgetAnnotationPanel("??");

    setupAnnotationTableRowHover();

    /**
     * Po zwolnieniu przycisku myszy utworz obiekt zaznaczenia.
     */
    $("#content").mouseup(function(){
        globalSelection = new Selection();
        if ( !globalSelection.isValid ){
            globalSelection = null;
        }
    });

    /**
     * Obsługa kliknięcia w anotację.
     * Przypisanie zdarzenia musi być po zdarzeniu $("#content").mouseup(...), aby zdarzenia były wywołane we właściwej
     * kolejności, tj. utworzenie zaznaczenia odbyło się przed zdarzeniem kliknięcia w anotację.
     */
    $("#content span.annotation").click(annotationClickTrigger);

    /**
     * Obsługa kliknięcia w nazwę anotacji w celu jej utworzenia.
     */
    $("a.an").click(function(){
        if ( !globalSelection || !globalSelection.isValid ){
            alert("Zaznacz tekst");
        }else{
            var annotationCssClasses = $(this).parent().attr('class');
            wAnnotationPanel.createAnnotation(globalSelection, $(this).attr("value"), $(this).attr("annotation_type_id"),  getNewAnnotationStage(), annotationCssClasses);
            globalSelection.clear();
            globalSelection = null;
        }
        return false;
    });


    /*
     * User modification of default annotation visbility settings.
     *
     */
    $("a.short_all").click(function(){
        $(this).siblings(".subsets").find("li.notcommon").toggleClass('hidden');
        $(this).toggleClass('shortlist');
    });


    //refresh_default brings annotation visibility back to default state
    $(".refresh_default").hover(function() {
        $(this).css('cursor','pointer');
    });


    $(".refresh_default").click(function() {
        id = ($(this).attr('id')).substring('default'.length);
        eye = "#eye"+ id;
        shortlist_open = $(this).closest("li").parent().children(':first-child').children().hasClass('shortlist');

        $(this).toggle();
        if($(eye).hasClass( "fa-eye-slash" )){
            if(!shortlist_open) {
                $(eye).closest("li").toggleClass("notcommon hidden");
            } else{y
                $(eye).closest("li").toggleClass("notcommon");
            }
            $(eye).removeClass('fa-eye-slash').addClass('fa-eye');
        } else{
            $(eye).closest("li").removeClass('notcommon ').addClass('newClassName');
            $(eye).removeClass('fa-eye').addClass('fa-eye-slash');
        }


        var params = {
            action: 'refresh_default',
            id: id
        };

        doAjax('report_annotator_action', params);
    });

    //"Eyes" override the default visibility of an annotation (shortlist).
    $(".eye_hide").hover(function() {
        $(this).css('cursor','pointer');
    });


    $(".eye_hide").click(function(){

        var eye = $(this);
        var shortlist_open = $(this).closest("li").parent().children(':first-child').children().hasClass('shortlist');

        id = ($(eye).attr('id')).substring('eye'.length);
        if($(eye).hasClass( "fa-eye-slash" )){
            var shortlist = 1;
        } else{
            var shortlist = 0;
        }

        var success = function(){
            if(shortlist == 1){
                $(eye).closest("li").toggleClass("notcommon");
                if(!shortlist_open) {
                    $(eye).closest("li").toggleClass("hidden");
                }
                $(eye).removeClass('fa-eye-slash').addClass('fa-eye');
            } else{
                $(eye).closest("li").removeClass('notcommon ').addClass('newClassName');
                $(eye).removeClass('fa-eye').addClass('fa-eye-slash');
            }

            $('#default'+id).toggle();
        };

        var params = {
            action: 'visibility',
            id: id,
            shortlist: shortlist
        };

        doAjaxSync("report_annotator_action", params, success);
    });

    $("#annotationList tbody tr").mouseover(function(){
        $("span.ann.highlighted").removeClass("highlighted");
        var annotationId = $(this).attr("annotation_id");
        $("#an" + annotationId).addClass("highlighted");
        $("#annotationList tbody tr.highlighted").removeClass("highlighted");
        $(this).addClass("highlighted");
    })

    setupAnnotationTableEdit();
    setupAnnotationTableDelete();
});

function setupAnnotationTableRowHover(){
    $("#annotationList tbody tr").hover(function() {
        $("#annotationList tr .hoverIcons").hide();
        $(this).find(".hoverIcons").show();
        $("a.annotationDelete").confirmation('hide');
    });
};

function setupAnnotationTableEdit() {
    $("#annotationList tbody tr a.annotationEdit").click(function(){
        var annotationId = $(this).parents("tr").attr("annotation_id");
        $("#an" + annotationId).click();
    });
};

function setupAnnotationTableDelete() {
    $("#annotationList tbody tr a.annotationDelete").confirmation(
        {   title: 'Delete annotation?',
            placement: "left",
            popout: true,
            onConfirm: function(){
                var row = $(this).parents("tr");
                var annotationId = row.attr("annotation_id");

                var tokenDeleteSuccess = function(data){
                    row.remove();
                };

                var params = {"annotation_id": annotationId};

                doAjax("report_delete_annotation", params, tokenDeleteSuccess, null, null);
            }
        });
};

/**
 * Zdarzenie wywoływane po kliknięciu w anotację
 * @returns {boolean}
 */
function annotationClickTrigger(){
    console.log('annotationClickTrigger');
    if (wAnnotationRelations.isNewRelationMode()) {
	console.log('annotationClickTrigger:is new relation');
        wAnnotationRelations.createRelation(this);
	// refresh local list of relations
	var source = wAnnotationRelations.span;
	wAnnotationRelations.set(source);
    } else if ( globalSelection == null ) {
	console.log('annotationClickTrigger:first selection');
        setCurrentAnnotation(this);
    }
    return false;
}

function updateAnnotationOnList(annotation){
    var values = [];
    $.each(annotation.shared_attributes, function(index, attr){
        values.push(attr.value);
    });
    var annotationRow = $("#annotationList tr[annotation_id="+annotation.annotation_id+"]");
    annotationRow.find("td.attributes").html(values.join(", "));
    annotationRow.attr("title", "You modified this annotation recently");
    annotationRow.addClass("modified");
    annotationRow.fadeOut(100);
    annotationRow.fadeIn(500);
}

/**
 * Ustaw anotację do edycji.
 * @param annotation referencja na znacznik SPAN reprezentujący anotację.
 */
function setCurrentAnnotation(annotation){
    var context = $("#content .context");
    context.removeClass("context");
    if ( context.attr("class") == "" ) {
        context.removeAttr("class");
    }
    $("#annotationLoading").show();
    $("#col-config").hide();
    $("#columnAnnotation").hide();

    wAnnotationDetails.set(annotation);
    wAnnotationRelations.set(annotation);

    if ( annotation == null ){
        $("#annotationLoading").hide();
        $("#annotationEditor").hide();
        $("#columnAnnotation").hide();
    }
    else{
        $("#annotationLoading").hide();
        $("#columnAnnotation").show();
    }
    autoreizeFitToScreen();
}
