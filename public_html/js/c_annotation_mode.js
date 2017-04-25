$(function(){
    setupAnnotationMode();
})

/**
 * Metoda zwraca aktualnie ustawioną wartość stage dla nowo tworzonych anotacji.
 * @return jedna z wartości "new", "final", null
 */
function getNewAnnotationStage(){
    /* Wartość stage ustalana jest na podstawie ustalonego trybu pracy, tj. annotation_mode. */
    var annotation_mode = $('input[name=annotation_mode]:checked').val();
    if ( annotation_mode == "final" ){
        return "final";
    }
    else if ( annotation_mode == "agreement" ){
        return "agreement";
    }
    else{
        return null;
    }
}

/**
 * Ustawia aktualny tryb pracy, podpina zdarzenia do automatycznego zapisu trybu.
 */
function setupAnnotationMode(){
    var annotation_mode = $.cookie('annotation_mode');
    if ( annotation_mode != null ){
        $('input[name=annotation_mode][value='+annotation_mode+']').attr("checked", true);
    }
    if ( getNewAnnotationStage() == null ){
        $('input[name=annotation_mode]:first').attr("checked", true);
        $.cookie('annotation_mode', $('input[name=annotation_mode]:checked').val());
    }
    $('input[name=annotation_mode]').click(function(event){
        $(this).find("input").attr("checked", true);
        $.cookie('annotation_mode', $('input[name=annotation_mode]:checked').val());
    });
}