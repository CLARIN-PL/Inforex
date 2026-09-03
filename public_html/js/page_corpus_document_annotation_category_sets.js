/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function() {
    var $form = $(".document-annotation-category-sets-form");
    if (!$form.length) {
        return;
    }

    var url = $.url(window.location.href);
    var corpusId = parseInt(url.param("corpus"), 10) || parseInt($("#corpusId").text(), 10) || 0;
    var $checkboxes = $form.find(".documentAnnotationPerspectiveSet");
    var $saveButton = $form.find(".document-annotation-category-sets-save");
    var $status = $form.find(".document-annotation-category-sets-status");
    var isDirty = false;

    function getSelectedIds() {
        var ids = [];
        $checkboxes.filter(":checked").each(function() {
            ids.push($(this).val());
        });
        return ids;
    }

    function updateStatus(text) {
        $status.text(text);
    }

    function updateButton() {
        $saveButton.prop("disabled", !isDirty);
    }

    $checkboxes.on("change", function() {
        var $cell = $(this).closest("td");
        $cell.toggleClass("corpus-settings-annotation-set-use-cell-active", this.checked);
        isDirty = true;
        updateButton();
        updateStatus("Unsaved changes");
    });

    $form.on("submit", function(event) {
        event.preventDefault();

        if (!isDirty) {
            return;
        }

        doAjax(
            "document_annotation_category_sets_save",
            {
                annotation_set_ids: getSelectedIds(),
                corpus_id: corpusId
            },
            function() {
                isDirty = false;
                updateButton();
                updateStatus("Configuration saved");
            },
            function() {
                updateStatus("Save failed");
            },
            null,
            $saveButton
        );
    });

    updateButton();
    updateStatus("No unsaved changes");
});
