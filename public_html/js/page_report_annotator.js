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

    wAnnotationDetails = new WidgetAnnotation("#annotation-details", function(){
        $("#columnAccordion").show();
        $("#columnAnnotation").hide();
	});

    wAnnotationRelations = new WidgetAnnotationRelations("#annotation-relations", "#content");
    wAnnotationPanel = new WidgetAnnotationPanel("??");

    /**
     * Obsługa kliknięcia w anotację.
     */
    $("#content span.annotation").on("click", function(){
        if ( wAnnotationRelations.isNewRelationMode() ){
            wAnnotationRelations.createRelation(this);
        } else {
            setCurrentAnnotation(this);
        }
        return false;
    });

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
            wAnnotationPanel.createAnnotation(globalSelection, $(this).attr("value"), getNewAnnotationStage());
            globalSelection.clear();
            globalSelection = null;
        }
        return false;
    });

	//Show annotations in the shortlist or display them out of the shortlist
    $(".eye_hide").click(function(){

        //if(($(this).parent().parent().siblings().not(".notcommon").length)<0 && $(this).hasClass( "fa-eye-slash" )){
        //    alert("At least one has to remain visible!");
        //} else{
            if($(this).hasClass( "fa-eye-slash" )){
                var shortlist = 1;
                $(this).closest("li").toggleClass("notcommon hidden");
                $(this).removeClass('fa-eye-slash').addClass('fa-eye');
            } else{
                var shortlist = 0;
                $(this).closest("li").removeClass('notcommon ').addClass('newClassName');
                $(this).removeClass('fa-eye').addClass('fa-eye-slash');


            }

            var params = {
                url: 'index.php',
                id: $(this).attr('id'),
                shortlist: shortlist
            };

            var success = function(data){
                //alert("Success");
            };

            var error = function(error_code){
                alert("error");
            };

            doAjax('report_annotator_action', params, success, error);

    });
});

/**
 * Ustaw anotację do edycji.
 * @param annotation referencja na znacznik SPAN reprezentujący anotację.
 */
function setCurrentAnnotation(annotation){
    $("#content span.selected").removeClass("selected");
    var context = $("#content .context");
    context.removeClass("context");
    if ( context.attr("class") == "" ) context.removeAttr("class");
    $("#annotationLoading").show();
    $("#columnAccordion").hide();
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

		/* Copy list of annotation types */
        var $annTypeClone = $("#widget_annotation").clone();
        // Remove elements that are not needed in the new context.
        $annTypeClone.find("*").removeAttr("id");
        $annTypeClone.find("input").remove();
        $annTypeClone.find("button").remove();
        $annTypeClone.find("small").remove();
        $annTypeClone.find("a.short_all").parent().remove();
        // Show all hidden groups
        $annTypeClone.find("*").show();
        $("#annotation_type").html($annTypeClone.html());
        $("#annotation_redo_type").attr("title","Original: "+$(annotation).attr("title").split(":")[1]);
    }
}
