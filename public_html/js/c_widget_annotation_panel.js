/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

var regexAstralSymbols = /[\uD800-\uDBFF][\uDC00-\uDFFF]/g;

function countSymbols(string) {
    return string.replace(regexAstralSymbols, '_').length;
}

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
 * @param annotationCssClasses string containing css classes that should be added to the new annotation
 */
WidgetAnnotationPanel.prototype.createAnnotation = function(selection, type, annotation_type_id, stage, annotationCssClasses){
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


    var pattern = new RegExp("<xyz id=['\"]"+tmpid+"['\"]>([\\\s\\\S]*?)</xyz>", "g");
    var content_html = $.trim($(newNode).parents("div.content").html());
    content_html = content_html.replace(/<sup.*?<\/sup>/gi, '');
    content_html = content_html.replace(pattern, fromDelimiter+"$1"+toDelimiter);
    content_no_html = content_html.replace(/<\/?("[^"]*"|'[^']*'|[^>])*>/g,'');
    content_no_html = html_entity_decode(content_no_html);

    // Pobierz treść anotacji przed usunięciem białych znaków
    var from = content_no_html.indexOf(fromDelimiter) + fromDelimiter.length;
    var to = content_no_html.indexOf(toDelimiter);
    var text = content_no_html.substring(from, to);

    // Oblicz właściwe indeksy
    content_no_html = content_no_html.replace(/\s/g, '');
    // ToDo: Keep until validation
    //from = content_no_html.indexOf(fromDelimiter);
    //to = content_no_html.indexOf(toDelimiter) - fromDelimiter.length - 1;

    // This should replace the above lines to handle utf8mb4
    from = countSymbols(content_no_html.substring(0, content_no_html.indexOf(fromDelimiter)));
    to = countSymbols(content_no_html.substring(0, content_no_html.indexOf(toDelimiter))) - fromDelimiter.length - 1;

    status_processing("adding annotation ...");

    if (from < 0 || to < 0 ){
        parent.remove_temporal_add_annotation_tag_by_id(tmpid);
        status_fade();
        dialog_error("Something went wrong — invalid boundaries: ["+from+","+to+"].");
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
        node.attr('class', 'annotation ' + annotationCssClasses);
        node.click(annotationClickTrigger);

        /* // Wykomentowane, do wyjaśnienia
        $(node.children("span")).attr('class', class_css + ' annotation ' + type);
        var node = $("#content span#new" + tmpid);
        var annotation_id = data['annotation_id'];

        var title = "an#"+annotation_id+":annotation "+type;
        var child_node = $(node.children("span"));

        node.attr('id', "an0");
        node.attr('class', 'annotation_set_0 token');
        child_node.attr('title', title);
        child_node.attr('id', "an"+annotation_id);
        //node.attr('groupid', $layer.attr("groupid"));
        child_node.attr('class', class_css + ' annotation ');
        child_node.click(annotationClickTrigger);

        console_add("anotacja <b> "+title+" </b> została dodana do tekstu <i>"+text+"</i>");
        */
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
