/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
    $("#create_shared_attribute").click(function(){
        add_shared_attribute();
        $("#create_shared_attribute_modal").modal('show');
    });

    $("#delete_shared_attribute").click(function(){
        delete_shared_attribute();
    });

    setupAttributeTableRowClick();

    $("#create_shared_attribute_enum").click(function(){
        add_shared_attribute_enum();
        $("#create_shared_attribute_enum_modal").modal('show');
    });

    $("#edit_shared_attribute_enum").click(function(){
        var attributeId = getActiveSharedAttributeId();
        var enumValue = getActiveSharedAttributeEnumValue();
        var enumDescription = getActiveSharedAttributeEnumDescription();
        if (attributeId) {
            $("input[name=edit_shared_attribute_id]").val(attributeId);
            $("input[name=edit_shared_attribute_enum_old_value]").val(enumValue);
            $("input[name=edit_shared_attribute_enum_new_value]").val(enumValue);
            $("input[name=edit_shared_attribute_enum_description]").val(enumDescription);
            $("#edit_shared_attribute_enum_modal").modal('show');
        }
    });

    $("#delete_shared_attribute_enum").click(function(){
        delete_shared_attribute_enum();
    });

    $('.modal').on('hidden.bs.modal', function () {
        $(this)
            .find("input,textarea")
            .val('')
            .removeClass('error')
            .end()
            .find("label.error")
            .remove()
            .end();
    });

    $(".save_edit_shared_attribute_enum").unbind("click").click(function() {
        var modal = $("#edit_shared_attribute_enum_modal");
        var inputData = {
            attributeId : modal.find("input[name=edit_shared_attribute_id]").val(),
            enumOldValue : modal.find("input[name=edit_shared_attribute_enum_old_value]").val(),
            enumNewValue : modal.find("input[name=edit_shared_attribute_enum_new_value]").val(),
            enumDescription : modal.find("input[name=edit_shared_attribute_enum_description]").val()
        };

        var success = function(){
            $("#sharedAttributesEnumTable .selected td:nth-child(2)").text(inputData.enumNewValue);
            $("#sharedAttributesEnumTable .selected td:nth-child(3)").html(sharedDescriptionPreview(inputData.enumDescription));
        };

        var error = function() {
            $("#edit_shared_attribute_enum_modal .modal-content").LoadingOverlay("hide");
        };

        var complete = function(){
            $("#edit_shared_attribute_enum_modal .modal-content").LoadingOverlay("hide");
            $('#edit_shared_attribute_enum_modal').modal('hide');
        };

        $("#edit_shared_attribute_enum_modal .modal-content").LoadingOverlay("show");
        doAjaxSync("shared_attribute_enum_edit", inputData, success, error, complete, null, null);
    });
});

var sharedAttributeValuesLoadingRow = '<tr class="administration-shared-values-loading-row"><td colspan="3"><div class="administration-wsd-loading administration-shared-values-loading"><img src="gfx/ajax.gif" alt="Loading"/> <span>Loading shared attribute values...</span></div></td></tr>';

function showSharedAttributeValuesLoading(){
    $("#sharedAttributesEnumTable > tbody").html(sharedAttributeValuesLoadingRow);
}

function get_shared_attributes_enum(){
    var params = { shared_attribute_id : getActiveSharedAttributeId() };

    var success = function(data){
        var tableRows = "";
        $.each(data,function(index, value){
            tableRows += sprintf("<tr><td class='num'>%d</td><td>%s</td><td>%s</td></tr>", index+1, escapeHtml(value.value), sharedDescriptionPreview(value.description));
        });
        $("#sharedAttributesEnumTable > tbody").html(tableRows);
        setupAttributeValueTableClick();
        $("#edit_shared_attribute_enum, #delete_shared_attribute_enum").attr("disabled", "disabled");
    };

    var login = function(){
        get_shared_attributes_enum();
    };

    showSharedAttributeValuesLoading();
    doAjaxSyncWithLogin("shared_attribute_enum_get", params, success, login);
}

function setupAttributeTableRowClick(){
    $("#sharedAttributesTable tbody").off("click", "tr").on("click", "tr", function(){
        $(this).siblings().removeClass("selected");
        $(this).addClass("selected");

        $("#delete_shared_attribute").removeAttr("disabled");

        if ($(this).find("td").eq(2).text() == "enum"){
            $("#create_shared_attribute_enum").removeAttr("disabled");
            get_shared_attributes_enum();
        }
        else {
            $("#create_shared_attribute_enum").attr("disabled", "disabled");
            $("#edit_shared_attribute_enum, #delete_shared_attribute_enum").attr("disabled", "disabled");
            $("#sharedAttributesEnumTable > tbody").empty();
        }
    });
}

function setupAttributeValueTableClick(){
    $("#sharedAttributesEnumTable tbody").off("click", "tr").on("click", "tr", function(){
        $(this).siblings().removeClass("selected");
        $(this).addClass("selected");
        $("#delete_shared_attribute_enum, #edit_shared_attribute_enum").removeAttr("disabled");
    });
}

function getActiveSharedAttributeId(){
    return $("#sharedAttributesTable .selected td:first").text();
}

function getActiveSharedAttributeEnumValue(){
    return $("#sharedAttributesEnumTable .selected td:nth-child(2)").text();
}

function getActiveSharedAttributeEnumDescription(){
    return $("#sharedAttributesEnumTable .selected td:nth-child(3)").text();
}

function escapeHtml(value) {
    return $('<div>').text(value == null ? "" : value).html();
}

function sharedDescriptionPreview(description) {
    var escapedDescription = escapeHtml(description);
    return '<div class="administration-description-preview" title="' + escapedDescription + '">' + escapedDescription + '</div>';
}

function sharedAttributeTypeLabel(type) {
    var escapedType = escapeHtml(type);
    return '<span class="administration-type-label administration-type-label-' + escapedType + '">' + escapedType + '</span>';
}

function add_shared_attribute(){
    $("#create_shared_attribute_form").validate({
        rules: {
            create_shared_attribute_name: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'administration_validation',
                        type: 'shared_attribute',
                        mode: 'create'
                    }
                }
            }
        },
        messages: {
            create_shared_attribute_name: {
                required: "Shared attribute must have a name.",
                remote: "This shared attribute already exists"
            }
        }
    });

    $(".confirm_create_shared_attribute").unbind("click").click(function() {
        if($('#create_shared_attribute_form').valid()) {
            var data = {
                name_str : $("#create_shared_attribute_name").val(),
                type_str : $("#create_shared_attribute_type").val(),
                desc_str : $("#create_shared_attribute_description").val()
            };

            var success = function(response){
                $("#sharedAttributesContainer").find("table > tbody").append(
                    '<tr>'+
                    '<td class="num">'+response.last_id+'</td>'+
                    '<td>'+escapeHtml(data.name_str)+'</td>'+
                    '<td>'+sharedAttributeTypeLabel(data.type_str)+'</td>'+
                    '<td>'+sharedDescriptionPreview(data.desc_str)+'</td>'+
                    '</tr>'
                );
            };

            var complete = function(){
                $('#create_shared_attribute_modal').modal('hide');
            };

            doAjaxSync("shared_attribute_add", data, success, null, complete);
        }
    });
}

function delete_shared_attribute(){
    var $container = $("#sharedAttributesTable");
    var deleteContent =
        '<label for="delete_name">Name</label>'+
        '<p id="delete_name">'+$container.find('.selected td:first').next().text()+'</p>'+
        '<label for="delete_desc">Description</label>'+
        '<p id="delete_desc">'+$container.find('.selected td:last').text()+'</p>';

    $('#deleteContent').html(deleteContent);
    $('#deleteModal').modal('show');

    $(".confirmDelete").unbind("click").click(function() {
        var data = { shared_attribute_id : getActiveSharedAttributeId() };

        var success = function(){
            $container.find(".selected:first").remove();
            $("#delete_shared_attribute").attr("disabled", "disabled");
            $("#sharedAttributesEnumTable > tbody").empty();
            $("#create_shared_attribute_enum, #edit_shared_attribute_enum, #delete_shared_attribute_enum").attr("disabled", "disabled");
        };

        var complete = function(){
            $('#deleteModal').modal('hide');
        };

        var login = function(){
            delete_shared_attribute();
        };

        doAjaxSync("shared_attribute_delete", data, success, null, complete, null, login);
    });
}

function add_shared_attribute_enum(){
    $("#create_shared_attribute_enum_form").validate({
        rules: {
            create_shared_attribute_enum_value: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'administration_validation',
                        type: 'shared_attribute_enum',
                        id: getActiveSharedAttributeId(),
                        mode: 'create'
                    }
                }
            }
        },
        messages: {
            create_shared_attribute_enum_value: {
                required: "Shared attribute value must have a name.",
                remote: "This shared attribute value already exists"
            }
        }
    });

    $(".confirm_create_shared_attribute_enum").unbind("click").click(function() {
        if($('#create_shared_attribute_enum_form').valid()) {
            var data = {
                shared_attribute_id : getActiveSharedAttributeId(),
                value_str : $("#create_shared_attribute_enum_value").val(),
                desc_str : $("#create_shared_attribute_enum_description").val()
            };

            var success = function(){
                $("#sharedAttributesEnumTable > tbody").append(
                    '<tr>'+
                    '<td><i>new</i></td>'+
                    '<td>'+escapeHtml(data.value_str)+'</td>'+
                    '<td>'+sharedDescriptionPreview(data.desc_str)+'</td>'+
                    '</tr>'
                );
            };

            var complete = function(){
                $('#create_shared_attribute_enum_modal').modal('hide');
            };

            var login = function(){
                add_shared_attribute_enum();
            };

            doAjaxSync("shared_attribute_enum_add", data, success, null, complete, null, login);
        }
    });
}

function htmlLabelValue(id, label, value){
    return sprintf('<label for="%s">%s</label><p id="%s">%s</p>', id, label, id, value);
}

function delete_shared_attribute_enum(){
    var $container = $("#sharedAttributesEnumTable");
    var deleteContent = htmlLabelValue("delete_name", "Value", getActiveSharedAttributeEnumValue())
        + htmlLabelValue("delete_desc", "Description", getActiveSharedAttributeEnumDescription());

    $('#deleteContent').html(deleteContent);
    $('#deleteModal').modal('show');

    $(".confirmDelete").unbind("click").click(function() {
        var data = {
            shared_attribute_id : getActiveSharedAttributeId(),
            value_str : getActiveSharedAttributeEnumValue()
        };

        var success = function(){
            $container.find(".selected:first").remove();
            $("#delete_shared_attribute_enum, #edit_shared_attribute_enum").attr("disabled", "disabled");
        };

        var complete = function(){
            $('#deleteModal').modal('hide');
        };

        var login = function(){
            delete_shared_attribute_enum();
        };

        doAjaxSync("shared_attribute_enum_delete", data, success, null, complete, null, login);
    });
}
