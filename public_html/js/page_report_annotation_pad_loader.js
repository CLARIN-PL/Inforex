var annotationPadLoaderUrl = $.url(window.location.href);
var annotationPadCorpusId = annotationPadLoaderUrl.param("corpus");

function applyAnnotationPadToggleState(context){
    $(context).find("a.toggle_cookie").each(function(){
        var selector = $(this).attr("label");
        if ($.cookie(selector) == "hide") {
            $(selector).hide();
        } else {
            $(selector).show();
        }
    });
}

function setupAnnotatorAnnotationPadActions(){
    var annotationTypes = $("#annotation-types");

    annotationTypes.off("click.annotationPad", "a.toggle_cookie").on("click.annotationPad", "a.toggle_cookie", function(){
        var selector = $(this).attr("label");
        if ($(selector).is(":visible")) {
            $.cookie(selector, "hide");
        } else {
            $.cookie(selector, "show");
        }
        $(selector).toggle();
        return false;
    });

    annotationTypes.off("click.annotationPad", "a.short_all").on("click.annotationPad", "a.short_all", function(){
        $(this).siblings(".subsets").find("li.notcommon").toggleClass("hidden");
        $(this).toggleClass("shortlist");
        return false;
    });

    annotationTypes.off("click.annotationPad", "a.an").on("click.annotationPad", "a.an", function(){
        if (typeof wAnnotationPanel === "undefined" || !wAnnotationPanel || typeof globalSelection === "undefined") {
            return false;
        }
        if (!globalSelection || !globalSelection.isValid) {
            alert("Zaznacz tekst");
        } else {
            var annotationCssClasses = $(this).parent().attr("class");
            wAnnotationPanel.createAnnotation(globalSelection, $(this).attr("value"), $(this).attr("annotation_type_id"), getNewAnnotationStage(), annotationCssClasses);
            globalSelection.clear();
            globalSelection = null;
        }
        return false;
    });

    applyAnnotationPadToggleState(annotationTypes);
}

function loadAnnotatorAnnotationPad(callback, sync){
    var tree = $("#annotation-types .tree");
    if (!tree.length) {
        if (callback && $.isFunction(callback)) {
            callback();
        }
        return;
    }

    if (tree.data("loaded")) {
        setupAnnotatorAnnotationPadActions();
        if (callback && $.isFunction(callback)) {
            callback();
        }
        return;
    }

    if (tree.data("loading") && !sync) {
        tree.one("annotationPadLoaded", function(){
            if (callback && $.isFunction(callback)) {
                callback();
            }
        });
        return;
    }

    tree.data("loading", 1).html("<div>Loading...</div>");

    var success = function(data){
        tree.html(data.html);
        tree.data("loaded", 1).data("loading", 0);
        setupAnnotatorAnnotationPadActions();
        tree.trigger("annotationPadLoaded");
        if (callback && $.isFunction(callback)) {
            callback();
        }
    };

    var error = function(){
        tree.data("loading", 0).html("<div>Cannot load annotation types.</div>");
    };

    if (sync) {
        doAjaxSync("report_annotation_pad_tree", {corpus: annotationPadCorpusId}, success, error);
    } else {
        doAjax("report_annotation_pad_tree", {corpus: annotationPadCorpusId}, success, error);
    }
}

$(function(){
    $("#collapsePad").one("show.bs.collapse", function(){
        loadAnnotatorAnnotationPad();
    });

    if ($("#collapsePad").hasClass("in")) {
        loadAnnotatorAnnotationPad();
    }
});
