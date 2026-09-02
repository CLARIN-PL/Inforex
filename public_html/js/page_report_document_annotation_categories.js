/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
    var $form = $(".report-document-annotation-categories-form");
    if (!$form.length) {
        return;
    }

    $(window).off("beforeunload");
    $(window).unbind("beforeunload");
    window.onbeforeunload = null;

    var url = $.url(window.location.href);
    var corpusId = parseInt(url.param("corpus"), 10) || 0;
    var reportId = parseInt($form.find("input[name=report_id]").val(), 10) || 0;
    var $checkboxes = $form.find(".document-annotation-category-checkbox");
    var $status = $("#documentAnnotationCategoriesStatus");
    var $selectedList = $("#documentAnnotationCategoriesSelectedList");
    var $selectedCount = $("#documentAnnotationCategoriesSelectedCount");
    var $emptyState = $("#documentAnnotationCategoriesEmpty");
    var isSaving = false;
    var saveQueued = false;

    function getSelectedIds() {
        var ids = [];
        $checkboxes.filter(":checked").each(function(){
            ids.push($(this).val());
        });
        return ids;
    }

    function updateStatus(text, stateClass) {
        $status.removeClass("is-saved is-saving is-error");
        if (stateClass) {
            $status.addClass(stateClass);
        }
        $status.text(text);
    }

    function renderSelected() {
        var items = [];

        $checkboxes.filter(":checked").each(function(){
            var $checkbox = $(this);
            var setName = $checkbox.data("set-name");
            var subsetName = $checkbox.data("subset-name");
            var typeName = $checkbox.data("type-name");

            items.push(
                "<li>" +
                    '<span class="report-document-annotation-categories-selected-set">' + $("<div>").text(setName).html() + "</span>" +
                    '<span class="report-document-annotation-categories-selected-separator">/</span>' +
                    '<span class="report-document-annotation-categories-selected-subset">' + $("<div>").text(subsetName).html() + "</span>" +
                    '<span class="report-document-annotation-categories-selected-separator">/</span>' +
                    '<span class="report-document-annotation-categories-selected-type">' + $("<div>").text(typeName).html() + '</span>' +
                "</li>"
            );
        });

        $selectedList.html(items.join(""));
        $selectedCount.text(items.length);
        $emptyState.toggleClass("report-document-annotation-categories-empty-hidden", items.length > 0);
    }

    function setSavingState(saving) {
        isSaving = saving;
        $checkboxes.prop("disabled", saving);
        updateStatus(saving ? "Saving..." : "Saved", saving ? "is-saving" : "is-saved");
    }

    function saveSelection() {
        if (isSaving) {
            saveQueued = true;
            return;
        }

        setSavingState(true);
        saveQueued = false;

        doAjax(
            "document_annotation_categories_save",
            {
                report_id: reportId,
                corpus_id: corpusId,
                annotation_type_ids: getSelectedIds()
            },
            function() {
                setSavingState(false);
                if (saveQueued) {
                    saveSelection();
                }
            },
            function() {
                setSavingState(false);
                updateStatus("Save failed", "is-error");
            }
        );
    }

    $checkboxes.off("change");
    $form.off("submit");

    $checkboxes.on("change", function(){
        renderSelected();
        saveSelection();
    });

    renderSelected();
    updateStatus("Saved", "is-saved");
});
