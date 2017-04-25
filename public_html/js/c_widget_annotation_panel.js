/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

/**
 * Object represents a panel with a document content on which one can select and add new annotations.
 * @param selector
 */
function WidgetAnnotationPanel(selector){
	this.tempNewAnnotationCounter = 1;
}

/**
 * Obsługa przycisków.
 */
WidgetAnnotationPanel.prototype.keyDown = function(e, isCtrl, isShift){

}

/**
 * Wywołanie akcji dodania anotacji określonego typu i stage-u.
 * @param selection Obiekt klasy Selection reprezentujący anotację
 * @param type Identyfikator anotacji (ToDo: Nadal używana jest nazwa zamiast identyfikatora)
 */
WidgetAnnotationPanel.prototype.createAnnotation = function(selection, type, annotation_type_id, stage){
    var parent = this;

    $("span.eosSpan").remove();

    var tmpid = this.tempNewAnnotationCounter++;

    if (!selection.isSimple){
        alert("Błąd ciągłości adnotacji.\n\nMożliwe przyczyny:\n 1) Zaznaczona adnotacja nie tworzy ciągłego tekstu w ramach jednego elementu.\n 2) Adnotacja jest zagnieżdżona w innej adnotacji.\n 3)Adnotacja zawiera wewnętrzne adnotacje.");
        return false;
    }

    sel = selection.sel;
    var report_id = $("#report_id").val();

    var newNode = document.createElement("xyz");
    newNode.id = tmpid;
    sel.surroundContents(newNode);

    /** Jeżeli zaznaczony tekst jest wewnątrz tokeny, to rozszerz na cały token. */
    if ($(newNode).parent().is(".token")){
        $(newNode).parent().wrap("<xyz id='"+tmpid+"'></xyz>");
        $(newNode).replaceWith($(newNode).html());
        newNode = $("xyz[id=" + tmpid + "]");
    }

    var content_html = $.trim($(newNode).parents("div.content").html());

    content_html = content_html.replace(/<sup.*?<\/sup>/gi, '');

    var pattern = new RegExp("<xyz id=['\"]"+tmpid+"['\"]>(.*?)</xyz>");
    content_html = content_html.replace(pattern, fromDelimiter+"$1"+toDelimiter);
    content_no_html = content_html.replace(/<\/?[^>]+>/gi, '');
    content_no_html = html_entity_decode(content_no_html);

    // Pobierz treść anotacji przed usunięciem białych znaków
    var from = content_no_html.indexOf(fromDelimiter) + fromDelimiter.length;
    var to = content_no_html.indexOf(toDelimiter);
    var text = content_no_html.substring(from, to);

    // Oblicz właściwe indeksy
    content_no_html = content_no_html.replace(/\s/g, '');
    from = content_no_html.indexOf(fromDelimiter);
    to = content_no_html.indexOf(toDelimiter) - fromDelimiter.length - 1;

    status_processing("dodawanie anotacji ...");

    if (from < 0 || to < 0 ){
        remove_temporal_add_annotation_tag_by_id(tmpid);
        status_fade();
        dialog_error("Wystąpił błąd z odczytem granic anotacji. Odczytano ["+from+","+to+"]. <br/><br/>Zgłoś błąd administratorowi.");
        return;
    }

	/* Tablica z parametrami tworzonej anotacji */
	/* ToDo: wymaga dodania type_id */
    var params = {
        report_id: report_id,
        from: from,
        to: to,
        text: text,
        type: type,
        annotation_type_id: annotation_type_id,
        stage: stage
    };

	/* Callback dla pomyślnego dodania anotacji */
    var success = function(data){
        $("#content xyz[id="+tmpid+"]").wrapInner("<span id='new" + tmpid + "'/>");
        parent.remove_temporal_add_annotation_tag_by_id(tmpid);

        var annotation_id = data['annotation_id'];
        var node = $("#content span#new" + tmpid);
        var title = "an#"+annotation_id+":annotation "+type;
        node.attr('title', title);
        node.attr('id', "an"+annotation_id);
        //node.attr('groupid', $layer.attr("groupid"));
        node.attr('class', 'annotation ' + type);
        node.click(annotationClickTrigger);
        console_add("anotacja <b> "+title+" </b> została dodana do tekstu <i>"+text+"</i>");
    };

	/* Callback wywołany po przetworzeniu żądania */
    var complete = function(){
        status_fade();
    };

    doAjax("report_add_annotation", params, success, null, complete);
}

WidgetAnnotationPanel.prototype.remove_temporal_add_annotation_tag_by_id = function(id){
    $("#content xyz[id=" + id + "]").replaceWith(function(id){
            return $(this).contents();
        }
    );
}