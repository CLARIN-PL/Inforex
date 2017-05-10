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

    wAnnotationRelations = new WidgetAnnotationRelations("#annotation-relations", "#content");
    wAnnotationPanel = new WidgetAnnotationPanel("??");

    /**
     * Obsługa kliknięcia w anotację.
     */
    $("#content span.annotation").click(annotationClickTrigger);

    /**
     * Po zwolnieniu przycisku myszy utworz obiekt zaznaczenia.
     */
    $("#content").mouseup(function(){
        //prevent_from_annotation_selection = getSelText() != "";
        globalSelection = new Selection();
        if ( !globalSelection.isValid ){
            globalSelection = null;
        }
    });

    /**
     * Obsługa kliknięcia w nazwę anotacji w celu jej utworzenia.
     */
    $("a.an").click(function(){
        if ( !globalSelection || !globalSelection.isValid ){
            alert("Zaznacz tekst");
        }else{
            wAnnotationPanel.createAnnotation(globalSelection, $(this).attr("value"), $(this).attr("annotation_type_id"),  getNewAnnotationStage());
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
        $(this).closest("ul").find("li.notcommon").toggleClass('hidden');
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
            } else{
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

        var shortlist_open = $(this).closest("li").parent().children(':first-child').children().hasClass('shortlist');

        id = ($(this).attr('id')).substring('eye'.length);
        if($(this).hasClass( "fa-eye-slash" )){
            var shortlist = 1;
            $(this).closest("li").toggleClass("notcommon");
            if(!shortlist_open) {
                $(this).closest("li").toggleClass("hidden");
            }
            $(this).removeClass('fa-eye-slash').addClass('fa-eye');
        } else{
            var shortlist = 0;
            $(this).closest("li").removeClass('notcommon ').addClass('newClassName');
            $(this).removeClass('fa-eye').addClass('fa-eye-slash');
        }

        $('#default'+id).toggle();


        var params = {
            action: 'visibility',
            id: id,
            shortlist: shortlist
        };

        doAjax('report_annotator_action', params);

    });
});

/**
 * Zdarzenie wywoływane po kliknięciu w anotację
 * @returns {boolean}
 */
function annotationClickTrigger(){
    if (wAnnotationRelations.isNewRelationMode()) {
        wAnnotationRelations.createRelation(this);
    } else {
        setCurrentAnnotation(this);
    }
    return false;
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
