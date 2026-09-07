var bindingsTree = {};
var bindingsAttributes = [];
var bindingsInitialIds = [];
var bindingsSelection = {corpus: "", set: "", subset: "", type: ""};
var bindingsTreeRequestId = 0;
var bindingsRequestId = 0;
var bindingsSaving = false;

$(function(){
    $("#bindingsCorpusId").on("change", function(){
        var nextValue = $(this).val();
        if (!confirmDiscardBindings()) {
            $(this).val(bindingsSelection.corpus);
            return;
        }

        bindingsSelection = {corpus: nextValue, set: "", subset: "", type: ""};
        bindingsTree = {};
        bindingsTreeRequestId++;
        clearBindingsEditor("Select a corpus and annotation type.");
        setSelect("#bindingsAnnotationSetId", "Select annotation set", [], true);
        setSelect("#bindingsAnnotationSubsetId", "Select annotation subset", [], true);
        setSelect("#bindingsAnnotationTypeId", "Select annotation type", [], true);
        if (nextValue) {
            loadBindingsTree(nextValue);
        }
    });

    $("#bindingsAnnotationSetId").on("change", function(){
        var nextValue = $(this).val();
        if (!confirmDiscardBindings()) {
            $(this).val(bindingsSelection.set);
            return;
        }

        bindingsSelection.set = nextValue;
        bindingsSelection.subset = "";
        bindingsSelection.type = "";
        populateBindingsSubsets(nextValue);
        clearBindingsEditor("Select an annotation subset and type.");
    });

    $("#bindingsAnnotationSubsetId").on("change", function(){
        var nextValue = $(this).val();
        if (!confirmDiscardBindings()) {
            $(this).val(bindingsSelection.subset);
            return;
        }

        bindingsSelection.subset = nextValue;
        bindingsSelection.type = "";
        populateBindingsTypes(bindingsSelection.set, nextValue);
        clearBindingsEditor("Select an annotation type.");
    });

    $("#bindingsAnnotationTypeId").on("change", function(){
        var nextValue = $(this).val();
        if (!confirmDiscardBindings()) {
            $(this).val(bindingsSelection.type);
            return;
        }

        bindingsSelection.type = nextValue;
        if (nextValue) {
            loadBindings(nextValue);
        } else {
            clearBindingsEditor("Select an annotation type.");
        }
    });

    $("#bindingsAttributeSearch").on("input", renderBindingsAttributes);
    $("#bindingsAttributeList").on("change", ".binding-attribute-checkbox", updateBindingsState);

    $("#bindingsSelectAll").on("click", function(){
        $("#bindingsAttributeList .binding-attribute-card:visible .binding-attribute-checkbox").prop("checked", true);
        updateBindingsState();
    });

    $("#bindingsClearAll").on("click", function(){
        $("#bindingsAttributeList .binding-attribute-checkbox").prop("checked", false);
        updateBindingsState();
    });

    $("#bindingsSaveButton").on("click", saveBindings);

    $(window).on("beforeunload", function(){
        if (bindingsAreDirty()) {
            return "You have unsaved attribute binding changes.";
        }
    });
});

function confirmDiscardBindings(){
    return !bindingsAreDirty() || window.confirm("Discard unsaved attribute binding changes?");
}

function bindingsAreDirty(){
    if (!bindingsSelection.type || bindingsSaving) {
        return false;
    }
    return selectedAttributeIds().join(",") !== bindingsInitialIds.join(",");
}

function setSelect(selector, placeholder, options, disabled){
    var html = '<option value="">' + escapeBindingsHtml(placeholder) + '</option>';
    $.each(options, function(index, option){
        html += '<option value="' + escapeBindingsHtml(option.id) + '">' + escapeBindingsHtml(option.name) + '</option>';
    });
    $(selector).html(html).prop("disabled", disabled);
}

function loadBindingsTree(corpusId){
    var requestId = ++bindingsTreeRequestId;
    setBindingsStatus("Loading annotation schema...", "loading");

    doAjax("annotation_type_tree", {corpusId: corpusId}, function(data){
        if (requestId !== bindingsTreeRequestId || corpusId !== bindingsSelection.corpus) {
            return;
        }

        bindingsTree = data.tree || {};
        var sets = [];
        $.each(bindingsTree, function(setId, setData){
            sets.push({id: setId, name: setData.name});
        });
        setSelect("#bindingsAnnotationSetId", "Select annotation set", sets, sets.length === 0);
        setBindingsStatus(sets.length ? "" : "This corpus has no annotation sets.", sets.length ? "" : "warning");
    }, function(){
        if (requestId === bindingsTreeRequestId) {
            setBindingsStatus("Could not load the annotation schema. Try again.", "warning");
        }
    }, null, null, function(){
        loadBindingsTree(corpusId);
    });
}

function populateBindingsSubsets(setId){
    var subsets = [];
    $.each(bindingsTree[setId] || {}, function(subsetId, subsetData){
        if (subsetId !== "name") {
            subsets.push({id: subsetId, name: subsetData.name});
        }
    });
    setSelect("#bindingsAnnotationSubsetId", "Select annotation subset", subsets, subsets.length === 0);
    setSelect("#bindingsAnnotationTypeId", "Select annotation type", [], true);
}

function populateBindingsTypes(setId, subsetId){
    var types = [];
    var subsetData = bindingsTree[setId] && bindingsTree[setId][subsetId] ? bindingsTree[setId][subsetId] : {};
    $.each(subsetData, function(typeId, typeName){
        if (typeId !== "name" && typeName !== "...") {
            types.push({id: typeId, name: typeName});
        }
    });
    setSelect("#bindingsAnnotationTypeId", "Select annotation type", types, types.length === 0);
}

function loadBindings(annotationTypeId){
    var requestId = ++bindingsRequestId;
    bindingsAttributes = [];
    bindingsInitialIds = [];
    setBindingsEditorEnabled(false);
    $("#bindingsAttributeList").html('<div class="administration-bindings-empty"><img src="gfx/ajax.gif" alt=""/> Loading bindings...</div>');
    setBindingsStatus("Loading current bindings...", "loading");

    doAjax("administration_annotation_attribute_bindings_get", {
        annotation_type_id: annotationTypeId
    }, function(data){
        if (requestId !== bindingsRequestId || annotationTypeId !== bindingsSelection.type) {
            return;
        }

        bindingsAttributes = data.attributes || [];
        bindingsInitialIds = assignedAttributeIds(bindingsAttributes);
        $("#bindingsTypeSummary").html(
            '<strong>' + escapeBindingsHtml(data.annotation_type.name) + '</strong>' +
            '<span>' + escapeBindingsHtml(data.annotation_type.set_name || "") + ' / ' +
            escapeBindingsHtml(data.annotation_type.subset_name || "") + '</span>'
        );
        $("#bindingsAttributeSearch").val("");
        renderBindingsAttributes();
        setBindingsEditorEnabled(true);
        setBindingsStatus("", "");
        updateBindingsState();
    }, function(){
        if (requestId === bindingsRequestId) {
            clearBindingsEditor("Could not load attribute bindings.");
            setBindingsStatus("Could not load attribute bindings. Try again.", "warning");
        }
    }, null, null, function(){
        loadBindings(annotationTypeId);
    });
}

function renderBindingsAttributes(){
    var query = $.trim($("#bindingsAttributeSearch").val()).toLowerCase();
    var currentIds = $("#bindingsAttributeList .binding-attribute-checkbox").length
        ? selectedAttributeIds()
        : bindingsInitialIds;
    var html = "";

    $.each(bindingsAttributes, function(index, attribute){
        var searchValue = (attribute.name + " " + (attribute.description || "") + " " + attribute.type).toLowerCase();
        var hidden = query && searchValue.indexOf(query) === -1;
        html += '<label class="binding-attribute-card' + (hidden ? ' binding-attribute-card-hidden' : '') + '">' +
            '<input type="checkbox" class="binding-attribute-checkbox" value="' + escapeBindingsHtml(attribute.id) + '"' +
                ($.inArray(String(attribute.id), currentIds) !== -1 ? ' checked="checked"' : '') + '>' +
            '<span class="binding-attribute-main">' +
                '<span class="binding-attribute-name">' + escapeBindingsHtml(attribute.name) + '</span>' +
                '<span class="binding-attribute-description">' + escapeBindingsHtml(attribute.description || "No description") + '</span>' +
            '</span>' +
            '<span class="binding-attribute-type binding-attribute-type-' + escapeBindingsHtml(attribute.type) + '">' +
                escapeBindingsHtml(attribute.type) +
            '</span>' +
        '</label>';
    });

    $("#bindingsAttributeList").html(html || '<div class="administration-bindings-empty">No shared attributes are defined.</div>');
    updateBindingsState();
}

function assignedAttributeIds(attributes){
    return $.map(attributes, function(attribute){
        return attribute.assigned ? String(attribute.id) : null;
    }).sort();
}

function selectedAttributeIds(){
    return $.map($("#bindingsAttributeList .binding-attribute-checkbox:checked"), function(checkbox){
        return String($(checkbox).val());
    }).sort();
}

function updateBindingsState(){
    var selected = selectedAttributeIds();
    $("#bindingsSelectedCount").text(selected.length + " selected");
    $("#bindingsSaveButton").prop(
        "disabled",
        bindingsSaving || !bindingsSelection.type || selected.join(",") === bindingsInitialIds.join(",")
    );
}

function saveBindings(){
    var annotationTypeId = bindingsSelection.type;
    if (!annotationTypeId || bindingsSaving) {
        return;
    }

    var selectedIds = selectedAttributeIds();
    bindingsSaving = true;
    setBindingsNavigationEnabled(false);
    setBindingsEditorEnabled(false);
    setBindingsStatus("Saving bindings...", "loading");

    doAjax("administration_annotation_attribute_bindings_apply", {
        annotation_type_id: annotationTypeId,
        shared_attribute_ids: selectedIds.join(",")
    }, function(data){
        bindingsInitialIds = selectedIds.slice(0);
        $.each(bindingsAttributes, function(index, attribute){
            attribute.assigned = $.inArray(String(attribute.id), bindingsInitialIds) !== -1;
        });
        setBindingsStatus("Saved " + data.binding_count + " attribute bindings for this annotation type.", "success");
    }, function(){
        setBindingsStatus("Could not save attribute bindings. No changes were applied.", "warning");
    }, function(){
        bindingsSaving = false;
        setBindingsNavigationEnabled(true);
        setBindingsEditorEnabled(true);
        updateBindingsState();
    }, null, function(){
        saveBindings();
    });
}

function clearBindingsEditor(message){
    bindingsRequestId++;
    bindingsAttributes = [];
    bindingsInitialIds = [];
    $("#bindingsTypeSummary").text(message);
    $("#bindingsAttributeList").html('<div class="administration-bindings-empty">No annotation type selected.</div>');
    $("#bindingsAttributeSearch").val("");
    setBindingsEditorEnabled(false);
    updateBindingsState();
}

function setBindingsEditorEnabled(enabled){
    var hasType = Boolean(bindingsSelection.type);
    $("#bindingsAttributeSearch, #bindingsSelectAll, #bindingsClearAll").prop("disabled", !enabled || !hasType);
    $("#bindingsAttributeList .binding-attribute-checkbox").prop("disabled", !enabled || !hasType);
}

function setBindingsNavigationEnabled(enabled){
    $("#bindingsCorpusId").prop("disabled", !enabled);
    $("#bindingsAnnotationSetId").prop("disabled", !enabled || $.isEmptyObject(bindingsTree));
    $("#bindingsAnnotationSubsetId").prop("disabled", !enabled || !bindingsSelection.set);
    $("#bindingsAnnotationTypeId").prop("disabled", !enabled || !bindingsSelection.subset);
}

function setBindingsStatus(message, type){
    $("#bindingsStatus").removeClass("is-loading is-success is-warning").addClass(type ? "is-" + type : "").text(message);
}

function escapeBindingsHtml(value){
    return $("<div>").text(value == null ? "" : value).html();
}
