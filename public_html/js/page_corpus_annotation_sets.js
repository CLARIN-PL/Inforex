/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function() {
	$("input[type=checkbox].annotationSet").click(function(){
		var checked = $(this).is(":checked");
		var annotationSetId = parseInt($(this).attr("annotation_set_id"));
        var url = $.url(window.location.href);
        var corpusId = parseInt(url.param("corpus"));
        var params = {};
        params['annotation_set_id'] = annotationSetId;
        params['operation_type'] = checked ? "add" : "remove";
        params['corpus_id'] = corpusId;

        var checkbox = this;

        ajaxIndicatorShow(checkbox);
        $(this).hide();
        doAjax("corpus_set_annotation_sets_corpora", params,
            function(){
                ajaxIndicatorHide(checkbox);
                if ( checked ){
                    $(checkbox).closest("td").addClass("corpus-settings-annotation-set-use-cell-active");
                } else {
                    $(checkbox).closest("td").removeClass("corpus-settings-annotation-set-use-cell-active");
                }
            },
            function(){
                ajaxIndicatorHide(checkbox);
                if ( !checked ){
                    $(checkbox).closest("td").addClass("corpus-settings-annotation-set-use-cell-active");
                    $(checkbox).attr("checked", "checked");
                } else {
                    $(checkbox).closest("td").removeClass("corpus-settings-annotation-set-use-cell-active");
                    $(checkbox).removeAttr("checked");
                }
            });
	});
});
