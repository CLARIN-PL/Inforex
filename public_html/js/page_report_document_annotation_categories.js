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

    var $checkboxes = $form.find(".document-annotation-category-checkbox");
    var $saveButton = $("#documentAnnotationCategoriesSave");
    var $status = $("#documentAnnotationCategoriesStatus");
    var $selectedList = $("#documentAnnotationCategoriesSelectedList");
    var $emptyState = $("#documentAnnotationCategoriesEmpty");
    var isDirty = false;

    function updateState() {
        $saveButton.prop("disabled", !isDirty);
        $status.text(isDirty ? "Unsaved changes" : "No unsaved changes");
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
        $emptyState.toggleClass("report-document-annotation-categories-empty-hidden", items.length > 0);
    }

    $checkboxes.on("change", function(){
        isDirty = true;
        renderSelected();
        updateState();
    });

    $form.on("submit", function(){
        isDirty = false;
        updateState();
    });

    $(window).on("beforeunload", function(){
        if (isDirty) {
            return "You have unsaved changes.";
        }
    });

    renderSelected();
    updateState();
});
