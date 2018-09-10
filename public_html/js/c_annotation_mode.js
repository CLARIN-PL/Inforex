$(function(){
    setupAnnotationMode();
});

/**
 * Metoda zwraca aktualnie ustawioną wartość stage dla nowo tworzonych anotacji.
 * @return jedna z wartości "new", "final", null
 */
function getNewAnnotationStage(cookieName){
    /* Wartość stage ustalana jest na podstawie ustalonego trybu pracy, tj. annotation_mode. */
    if (cookieName == null)
        cookieName = 'annotation_mode';

    var annotation_mode = $('input[name='+ cookieName +']:checked').val();
    if ( annotation_mode == "final" ){
        return "final";
    }
    else if ( annotation_mode == "agreement" ){
        return "agreement";
    } else if(annotation_mode == "relation_agreement"){
        return "relation_agreement";
    }
    else{
        return null;
    }
}

/**
 * Ustawia aktualny tryb pracy, podpina zdarzenia do automatycznego zapisu trybu.
 * @param cookieName - nazwa cookie, do którego zapisywana jest wartość, domyślna wartość: 'annotation_mode'
 */
function setupAnnotationMode(cookieName){
    cookieName = cookieName || 'annotation_mode';

    var annotation_mode = $.cookie(cookieName);
    if ( annotation_mode != null ){
        $('input[name='+ cookieName +'][value='+annotation_mode+']').attr("checked", true);
    }
    if ( getNewAnnotationStage(cookieName) == null ){
        $('input[name='+cookieName+']:first').attr("checked", true);
        $.cookie(cookieName, $('input[name='+cookieName+']:checked').val());
    }
    $('input[name='+cookieName+']').click(function(event){
        $(this).find("input").attr("checked", true);
        $.cookie(cookieName, $('input[name='+cookieName+']:checked').val());
    });
}

function onChangeAnnotationMode(callbackFcn, cookieName){
    cookieName = cookieName || 'annotation_mode';
    $('input[name='+cookieName+']').click(function(){
        callbackFcn($('input[name='+cookieName+']:checked').val());
    });
}