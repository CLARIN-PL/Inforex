var url = $.url(window.location.href);
var corpus_id = url.param('corpus');

function customAnnotationEscapeHtml(value) {
    return String(value == null ? "" : value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function customAnnotationAccessBadge(access) {
    var normalized = String(access || "private").trim().toLowerCase();
    var className = "corpus-settings-custom-annotation-set-access-private";

    if (normalized === "public") {
        className = "corpus-settings-custom-annotation-set-access-public";
    } else if (normalized === "edit") {
        className = "corpus-settings-custom-annotation-set-access-edit";
    }

    return "<span class='corpus-settings-custom-annotation-set-access " + className + "'>" + customAnnotationEscapeHtml(normalized) + "</span>";
}

function customAnnotationVisibilityBadge(visibility) {
    var normalized = String(visibility || "Visible").trim();
    var isHidden = normalized.toLowerCase() === "hidden";
    var icon = isHidden ? "fa-eye-slash" : "fa-eye";
    var className = isHidden ? "corpus-settings-custom-annotation-type-visibility-hidden" : "corpus-settings-custom-annotation-type-visibility-visible";

    return "<span class='corpus-settings-custom-annotation-type-visibility " + className + "' title='" + customAnnotationEscapeHtml(normalized) + "'>" +
        "<i class='fa " + icon + "' aria-hidden='true'></i>" +
        "<span class='sr-only'>" + customAnnotationEscapeHtml(normalized) + "</span>" +
        "</span>";
}

function customAnnotationOwnerInitials(ownerName) {
    var words = String(ownerName || "").trim().split(/\s+/);

    if (!words[0]) {
        return "";
    }

    return words.slice(0, 2).map(function (word) {
        return word.charAt(0).toUpperCase();
    }).join("");
}

function customAnnotationOwnerBadge(ownerName) {
    var safeOwner = customAnnotationEscapeHtml(ownerName);
    var initials = customAnnotationOwnerInitials(ownerName);

    if (!initials) {
        return "";
    }

    return "<span class='corpus-settings-custom-annotation-set-owner' title='" + safeOwner + "'>" + customAnnotationEscapeHtml(initials) + "</span>";
}

function customAnnotationSetRow(id, name, description, ownerId, ownerName, access, visibility, editAccess) {
    var rowClass = editAccess ? " class='edit_access'" : "";
    var safeOwner = customAnnotationEscapeHtml(ownerName);

    return "<tr visibility='" + customAnnotationEscapeHtml(visibility) + "'" + rowClass + ">" +
        "<td class='column_id td-right'><span class='corpus-settings-custom-annotation-set-id'>" + customAnnotationEscapeHtml(id) + "</span></td>" +
        "<td><span class='corpus-settings-custom-annotation-set-name'>" + customAnnotationEscapeHtml(name) + "</span></td>" +
        "<td><span class='annotation_description corpus-settings-custom-annotation-set-description' title='" + customAnnotationEscapeHtml(description) + "'>" + customAnnotationEscapeHtml(description) + "</span></td>" +
        "<td class='td-center set_owner' id='" + customAnnotationEscapeHtml(ownerId) + "' data-owner-name='" + safeOwner + "'>" +
        customAnnotationOwnerBadge(ownerName) +
        "</td>" +
        "<td class='td-center'>" + customAnnotationAccessBadge(access) + "</td>" +
        "</tr>";
}

function customAnnotationSubsetRow(id, name, description) {
    return "<tr>" +
        "<td class='column_id td-right'><span class='corpus-settings-custom-annotation-subset-id'>" + customAnnotationEscapeHtml(id) + "</span></td>" +
        "<td><span class='corpus-settings-custom-annotation-subset-name'>" + customAnnotationEscapeHtml(name) + "</span></td>" +
        "<td><span class='annotation_description corpus-settings-custom-annotation-subset-description' title='" + customAnnotationEscapeHtml(description) + "'>" + customAnnotationEscapeHtml(description) + "</span></td>" +
        "</tr>";
}

function customAnnotationTypeRow(id, name, shortName, description, visibility, css) {
    return "<tr id='" + customAnnotationEscapeHtml(id) + "'>" +
        "<td><span class='corpus-settings-custom-annotation-type-name' style='" + customAnnotationEscapeHtml(css) + "'>" + customAnnotationEscapeHtml(name) + "</span></td>" +
        "<td><span class='corpus-settings-custom-annotation-type-short'>" + customAnnotationEscapeHtml(shortName) + "</span></td>" +
        "<td><span class='annotation_description corpus-settings-custom-annotation-type-description' title='" + customAnnotationEscapeHtml(description) + "'>" + customAnnotationEscapeHtml(description) + "</span></td>" +
        "<td class='td-center'>" + customAnnotationVisibilityBadge(visibility) + "</td>" +
        "<td style='display:none'>" + customAnnotationEscapeHtml(css) + "</td>" +
        "</tr>";
}

$(function () {
    $('.search_input').submit(false);
    $("#annotationSetsTable .set_owner").each(function () {
        var $ownerCell = $(this);
        var ownerName = $.trim($ownerCell.attr("data-owner-name") || $ownerCell.text());

        $ownerCell.attr("data-owner-name", ownerName);
        $ownerCell.html(customAnnotationOwnerBadge(ownerName));
    });

    $(".search_input").keyup(function () {
        var data = this.value.toLowerCase();
        var table = $("#share_annotation_set_table");
        $(table).children().each(function (index, row) {
            var text = $(row).text().toLowerCase();
            if (text.indexOf(data) >= 0 || this.value == "") {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    $(".deleteAnnotations").click(function () {
        remove_annotation($(this));
    });


    $(".create_annotation_set").click(function(){
        addAnnotationSet($(this));
    });

    $(".edit_annotation_set").click(function(){
        editAnnotationSet($(this));
    });


    $(".create_annotation_subset").click(function(){
        addAnnotationSubset($(this));
    });

    $(".edit_annotation_subset").click(function(){
        editAnnotationSubset($(this));
    });

    $(".create_annotation_type").click(function(){
        addAnnotationType($(this));
    });

    $(".edit_annotation_type").click(function(){
        editAnnotationType($(this));
    });

    $(".shareAnnotationSet").click(function(){
       shareAnnotationSet();
    });

    $("#share_annotation_set_table").on("click", ".share_annotation_checkbox", function(){
        var user_id = $(this).attr('id');
        var checked = ($(this).prop('checked') === true ? "add" : "remove");
        var annotation_set_id = $("#annotationSetsTable .hightlighted > td:first").text();

        var data = {
            annotation_set_id: annotation_set_id,
            user_id: user_id,
            mode: checked
        };

        var success = function(users) {

        };

        doAjaxSync("annotation_set_share", data, success);
    });


    $(".tableContent").on("click", "tbody > tr", function () {
        $(this).siblings().removeClass("hightlighted");
        $(this).addClass("hightlighted");
        containerType = $(this).parents(".tableContainer:first").attr('id');
        if (containerType == "annotationSetsContainer") {
            $("#annotationSetsContainer .edit,#annotationSetsContainer .deleteAnnotations").show();

            if(!$(this).hasClass("edit_access")){
                $("#annotationSetsContainer .shareAnnotationSet").show();
            } else{
                $("#annotationSetsContainer .shareAnnotationSet").hide();
            }

            $("#annotationSubsetsContainer .create").show();
            $('#annotationSubsetsContainer').css('visibility', 'visible');
            $("#annotationTypesContainer").css('visibility', 'hidden');
            $("#annotationSetsCorporaContainer").css('visibility', 'visible');
            $("#corpusContainer").css('visibility', 'visible');
            $("#annotationSubsetsContainer .edit,#annotationSubsetsContainer .deleteAnnotations").hide();
            $("#annotationTypesContainer table > tbody").empty();
        }
        else if (containerType == "annotationSubsetsContainer") {
            $("#annotationSubsetsContainer .edit,#annotationSubsetsContainer .deleteAnnotations").show();
            $("#annotationTypesContainer .create").show();
            $("#annotationTypesContainer").css('visibility', 'visible');
            $("#annotationTypesContainer .edit,#annotationTypesContainer .deleteAnnotations").hide();
        }
        else if (containerType == "annotationTypesContainer") {
            $("#annotationTypesContainer .edit,#annotationTypesContainer .deleteAnnotations").show();
        }
        get($(this));
    });

});

function shareAnnotationSet(){
    var data = {
        annotation_set_id: $("#annotationSetsTable .hightlighted > td:first").text(),
        owner_id: $("#annotationSetsTable .hightlighted > .set_owner").attr('id'),
        mode: "get"
    };

    var success = function(users) {
        var rows = "";
        $.each(users, function (index, value) {
            var checkbox = "<label class='corpus-settings-custom-annotation-share-checkbox'>" +
                "<input class='share_annotation_checkbox' id='" + value.user_id + "' type='checkbox' " + (value.annotation_set_id !== null ? "checked" : "") + ">" +
                "<span aria-hidden='true'></span>" +
                "</label>";

            rows += "<tr>" +
                        "<td><span class='corpus-settings-custom-annotation-share-user'>"+customAnnotationEscapeHtml(value.screename)+"</span></td>" +
                        "<td><span class='corpus-settings-custom-annotation-share-login'>"+customAnnotationEscapeHtml(value.login)+"</span></td>" +
                        "<td class='text-center'>"+checkbox+"</td>" +
                    "</tr>";
        } );

        $("#share_annotation_set_table").html(rows);
    };

    doAjaxSync("annotation_set_share", data, success);
}

function addAnnotationSet($element){
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $element.parents(".tableContainer");

    $( "#create_annotation_sets_form" ).validate({
        rules: {
            create_annotation_set_name: {
              required: true
          }
        },
        messages: {
            create_annotation_set_name: {
                required: "Annotation set must have a name."
            }
        }
    });


    $( ".confirm_annotation_set" ).unbind( "click" ).click(function() {

        if($('#create_annotation_sets_form').valid()) {

            var accessType = $('#create_setAccess').val();

            if (accessType === "public") {
                var visibility = 1;
            } else {
                var visibility = 0;
            }

            var _data = {

                desc_str: $("#create_annotation_set_name").val(),
                description: $("#create_annotation_set_description").val(),
                setAccess_str: visibility,
                element_type: elementType,
                customAnnotation: true,
                corpus: corpus_id
            };

            var success = function (data) {
                $container.find("table > tbody").append(
                    customAnnotationSetRow(data.last_id, _data.desc_str, _data.description, data.user_id, data.user, accessType, visibility, false)
                );

                $('#create_annotation_set_modal').modal('hide');
            };

            doAjax("annotation_edit_add", _data, success, null);

        }

    });
}

function addAnnotationSubset($element){
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var parent_id = $("#annotationSetsTable .hightlighted > td:first").text();
    var $container = $element.parents(".tableContainer");

    $( "#create_annotation_subsets_form" ).validate({
        rules: {
            create_annotation_subset_name: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'corpus_custom_annotation_sets',
                        type: 'annotation_subset',
                        mode: 'create',
                        annotation_set:  function(){
                            return $("#annotationSetsTable .hightlighted > td:first").text()
                        },
                        corpus: corpus_id
                    }
                }
            }
        },
        messages: {
            create_annotation_subset_name: {
                required: "Annotation set must have a name.",
                remote: "This name is already in use."
            }
        }
    });


    $( ".confirm_annotation_subset" ).unbind( "click" ).click(function() {

        if($('#create_annotation_subsets_form').valid()) {
            var _data = {

                desc_str: $("#create_annotation_subset_name").val(),
                description: $("#create_annotation_subset_description").val(),
                element_type: elementType,
                parent_id: parent_id,
                corpus: corpus_id
            };

            var success = function (data) {

                $container.find("table > tbody").append(
                    customAnnotationSubsetRow(data.last_id, _data.desc_str, _data.description)
                );
            };

            doAjax("annotation_edit_add", _data, success, null);
            $('#create_annotation_subset_modal').modal('hide');
        }

    });
}

function addAnnotationType($element){
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $element.parents(".tableContainer");

    $("#create_annotation_type_css").on("change paste keyup", function() {
        var value = $(this).val();
        $("#create_annotation-style-preview").attr('style', value);
    });

    $( "#create_annotation_types_form" ).validate({
        rules: {
            create_annotation_type_name: {
                regex: "^[a-zA-Z0-9_]+$",
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'corpus_custom_annotation_sets',
                        type: 'annotation_type',
                        mode: 'create',
                        annotation_subset:  function(){
                            return $("#annotationSubsetsTable .hightlighted > td:first").text()
                        }
                    }
                }
            }
        },
        messages: {
            create_annotation_type_name: {
                required: "Annotation type must have a name.",
                remote: "This name is already in use."
            }
        }
    });


    $( ".confirm_annotation_type" ).unbind( "click" ).click(function() {

        if($('#create_annotation_types_form').valid()) {
            var _data = {
                element_type: elementType,
                parent_id: $("#annotationSubsetsTable .hightlighted > td:first").text(),
                name_str: $("#create_annotation_type_name").val(),
                short: $("#create_annotation_type_short").val(),
                desc_str: $("#create_annotation_type_desc").val(),
                visibility: $("#create_elementVisibility").val(),
                css: $("#create_annotation_type_css").val(),
                corpus: corpus_id,
                set_id: $("#annotationSetsTable .hightlighted > td:first").text()
            };

            var success = function (data) {

                $container.find("table > tbody").append(
                    customAnnotationTypeRow(data.last_id || _data.name_str, _data.name_str, _data.short, _data.desc_str, _data.visibility, _data.css)
                );
                $('#create_annotation_type_modal').modal('hide');
            };

            doAjax("annotation_edit_add", _data, success, null);
        }

    });
}


function editAnnotationSubset($element){
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $element.parents(".tableContainer");

    $( "#edit_annotation_subsets_form" ).validate({
        rules: {
            edit_annotation_subset_name: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'corpus_custom_annotation_sets',
                        type: 'annotation_subset',
                        mode: 'edit',
                        annotation_set:  function(){
                            return $("#annotationSetsTable .hightlighted > td:first").text()
                        },
                        id: function(){
                            return $("#annotationSubsetsTable .hightlighted > td:first").text()
                        }
                    }
                }
            }
        },
        messages: {
            edit_annotation_subset_name: {
                required: "Annotation set must have a name.",
                remote: "This name is already in use."
            }
        }
    });

    $("#edit_annotation_subset_name").val($container.find('.hightlighted td:first').next().text());
    $("#edit_annotation_subset_description").val($container.find('.hightlighted td:first').next().next().text());

    $( ".confirm_annotation_subset" ).unbind( "click" ).click(function() {
        if($("#edit_annotation_subsets_form").valid()) {

            var _data = {
                desc_str: $("#edit_annotation_subset_name").val(),
                description: $("#edit_annotation_subset_description").val(),
                element_id: $container.find('.hightlighted td:first').text(),
                element_type: elementType,
                parent_id: $("#annotationSubsetsTable .hightlighted > td:first").text()
            };

            var success = function (data) {
                $container.find(".hightlighted:first").html(
                    customAnnotationSubsetRow($container.find(".hightlighted td:first").text(), _data.desc_str, _data.description).replace(/^<tr>|<\/tr>$/g, "")
                );
                $('#edit_annotation_subset_modal').modal('hide');
            };

            var login = function () {
                edit($element);
            };

            doAjaxSyncWithLogin("annotation_edit_update", _data, success, login);
        }
    });
    if (elementType == "annotation_type") {
        $("#previewCssButton").click(function () {
            $("#previewCssSpan").attr('style', $("#elementCss").val());
        });
    }

}

function editAnnotationType($element){

    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $element.parents(".tableContainer");

    $( "#edit_annotation_types_form" ).validate({
        rules: {
            edit_annotation_type_name: {
                regex: "^[a-zA-Z0-9_]+$",
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'corpus_custom_annotation_sets',
                        type: 'annotation_type',
                        mode: 'edit',
                        annotation_subset:  function(){
                            return $("#annotationSubsetsTable .hightlighted > td:first").text()
                        },
                        id: function(){
                            return $("#annotationTypesTable .hightlighted").attr('id')
                        }
                    }
                }
            }
        },
        messages: {
            edit_annotation_type_name: {
                required: "Annotation type must have a name.",
                remote: "This name is already in use."
            }
        }
    });

    $vals = $container.find('.hightlighted td');
    $("#edit_annotation_type_name").val($($vals[0]).text());
    $("#edit_annotation_type_short").val($($vals[1]).text());
    $("#edit_annotation_type_desc").val($($vals[2]).text());
    $("#edit_elementVisibility").val($($vals[3]).text());
    $("#edit_annotation_type_css").val($($vals[4]).text());
    $("#edit_annotation-style-preview").attr("style", $($vals[4]).text());


    $( ".confirm_annotation_type" ).unbind( "click" ).click(function() {
        if($("#edit_annotation_types_form").valid()) {
            var _data = {
                element_type: elementType,
                parent_id: $("#annotationSubsetsTable .hightlighted > td:first").text(),
                annotation_type_id: $($vals).parent().attr('id'),
                name_str: $("#edit_annotation_type_name").val(),
                short: $("#edit_annotation_type_short").val(),
                desc_str: $("#edit_annotation_type_desc").val(),
                visibility: $("#edit_elementVisibility").val(),
                css: $("#edit_annotation_type_css").val(),
                set_id: $("#annotationSetsTable .hightlighted > td:first").text(),
                shortlist: $("#edit_elementVisibility").val()
            };

            var success = function (data) {
                $container.find(".hightlighted:first").html(
                    customAnnotationTypeRow(_data.annotation_type_id, _data.name_str, _data.short, _data.desc_str, _data.shortlist, _data.css).replace(/^<tr[^>]*>|<\/tr>$/g, "")
                );
                $('#edit_annotation_type_modal').modal('hide');

            };

            var login = function () {
                edit($element);
            };

            doAjax("annotation_edit_update", _data, success, login);
        }
    });

    $("#previewCssButton").click(function (e) {
        $("#edit_annotation_type_name").attr('style', $("#edit_annotation_type_css").val());
        e.preventDefault();
    });

}


function editAnnotationSet($element){
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $element.parents(".tableContainer");

    var visibility = $container.find('.hightlighted').attr("visibility");
    var visibilityStr = "private";
    if(visibility === '1'){
        visibilityStr = "public";
    }

    $( "#edit_annotation_sets_form" ).validate({
        rules: {
            edit_annotation_set_name: {
                required: true
            }
        },
        messages: {
            edit_annotation_set_name: {
                required: "Annotation set must have a name."
            }
        }
    });


    $("#edit_annotation_set_name").val($container.find('.hightlighted td:first').next().text());
    $("#edit_annotation_set_description").val($container.find('.hightlighted td:first').next().next().text());
    $("#edit_setAccess").val(visibilityStr);



    $( ".confirm_annotation_set" ).unbind( "click" ).click(function() {
        if($('#edit_annotation_sets_form').valid()) {
            var _data = {
                desc_str: $("#edit_annotation_set_name").val(),
                description: $("#edit_annotation_set_description").val(),
                set_access: $("#edit_setAccess").val(),
                element_id: $container.find('.hightlighted td:first').text(),
                element_type: elementType,
                parent_id: $("#annotationSetsTable .hightlighted > td:first").text()
            };

            var owner_id = $container.find(".hightlighted td:nth-child(4)").attr('id');
            var owner_name = $container.find(".hightlighted td:nth-child(4)").attr("data-owner-name") || $container.find(".hightlighted td:nth-child(4)").text();

            var success = function (data) {
                if (elementType == "annotation_set") {
                    $container.find(".hightlighted:first").html(
                        customAnnotationSetRow($container.find(".hightlighted td:first").text(), _data.desc_str, _data.description, owner_id, owner_name, $("#edit_setAccess").val(), visibility, false).replace(/^<tr[^>]*>|<\/tr>$/g, "")
                    );
                }

                if(_data.set_access === "public"){
                    visibility = 1;
                } else{
                    visibility = 0;
                }

                $('#edit_annotation_set_modal').modal('hide');
                $container.find(".hightlighted").attr('visibility', visibility);
            };

            var login = function () {
                edit($element);
            };

            doAjax("annotation_edit_update", _data, success, login);
        }
    });

}


function get($element) {
    var $container = $element.parents(".tableContainer:first");
    var containerName = $container.attr("id");
    var childId = "";
    if (containerName == "annotationSetsContainer" || containerName == "annotationSubsetsContainer") {
        var _data = {
            parent_id: $element.children(":first").text()
        };
        if (containerName == "annotationSetsContainer") {
            childId = "annotationSubsetsContainer";
            _data.parent_type = 'annotation_set';
        }
        else {
            childId = "annotationTypesContainer";
            _data.parent_type = 'annotation_subset';
        }

        var success = function (data) {
            var tableRows = "";
            $.each(data, function (index, value) {
                //for annotation_set the last two objects contains data from annotation_sets_corpora and corpora
                if (_data.parent_type == "annotation_set" && index < data.length - 2) {
                    tableRows +=
                        customAnnotationSubsetRow(value.id, value.name, value.description == null ? "" : value.description);
                }
                else if (_data.parent_type == "annotation_subset")
                    tableRows +=
                        customAnnotationTypeRow(value.id, value.name, value.short == null ? "" : value.short, value.description == null ? "" : value.description, value.shortlist == 0 ? "Visible" : "Hidden", value.css == null ? "" : value.css);
            });
            $("#" + childId + " table > tbody").html(tableRows);

            if (_data.parent_type == "annotation_set") {
                //annotation_sets_corpora:
                tableRows = "";
                $.each(data[data.length - 2], function (index, value) {
                    tableRows +=
                        '<tr>' +
                        '<td class = "column_id">' + value.id + '</td>' +
                        '<td>' + value.name + '</td>' +
                        '<td>' + (value.description == null ? "" : value.description) + '</td>' +
                        '</tr>';
                });
                $("#annotationSetsCorporaContainer table > tbody").html(tableRows);
                //corpora:
                tableRows = "";
                $.each(data[data.length - 1], function (index, value) {
                    tableRows +=
                        '<tr>' +
                        '<td class = "column_id">' + value.id + '</td>' +
                        '<td>' + value.name + '</td>' +
                        '<td>' + (value.description == null ? "" : value.description) + '</td>' +
                        '</tr>';
                });
                $("#corpusContainer table > tbody").html(tableRows);
            }
        };
        var login = function (data) {
            get($element);
        };
        doAjax("annotation_edit_get", _data, success, login);
    }
}


function remove_annotation($element) {
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $element.parents(".tableContainer");
    if (elementType == "annotation_set" || elementType == "annotation_subset") {
        var element_id = $container.find('.hightlighted td:first').text();
        var delete_html =
            '<label for="delName">Name:</label>' +
            '<p id = "delName">' + $container.find('.hightlighted td:first').next().text() + '</p>';
    }
    else if (elementType == "annotation_type") {
        var element_id = $container.find('.hightlighted').attr('id');
        $vals = $container.find('.hightlighted td');
        var delete_html =
            '<label for="delShort">Short description:</label>' +
            '<p id = "delShort">' + $($vals[1]).text() + '</p>' +
            '<label for="delDesc">Description:</label>' +
            '<p id = "delDesc">' + $($vals[2]).text() + '</p>' +
            '<label for="delVisibility">Visibility:</label>' +
            '<p id = "delVisibility">' + $($vals[3]).text() + '</p>' +
            '<label for="delCss">Css:</label>' +
            '<p id = "delCss">' + $($vals[4]).text() + '</p>';
    }


    $('#deleteContent').html(delete_html);
    $('#deleteModal').modal('show');

    $(".confirmDelete").unbind("click").click(function () {
        var _data = {
            //ajax : "annotation_edit_delete",
            element_type: elementType,
            element_id: element_id
        };

        var success = function (data) {
            $container.find(".hightlighted:first").remove();
            if (elementType == "annotation_set") {
                $("#annotationSetsContainer .edit,#annotationSetsContainer .deleteAnnotations").hide();
                $("#annotationSubsetsContainer table > tbody").empty();
                $("#annotationTypesContainer table > tbody").empty();
                $("#annotationSetsCorporaTable > tbody").empty();
                $("#corpusTable > tbody").empty();
            }
            else if (elementType == "annotation_subset") {
                $("#annotationSubsetsContainer .create").show();
                $("#annotationSubsetsContainer .edit,#annotationSubsetsContainer .deleteAnnotations").hide();
                $("#annotationTypesContainer table > tbody").empty();
            }
            else {
                $("#annotationTypesContainer .edit,#annotationTypesContainer .deleteAnnotations").hide();
            }

            $('#deleteModal').modal('hide');
        };


        var login = function () {
            remove_annotation($element);
        };

        doAjax("annotation_edit_delete", _data, success, login);
    });

}
