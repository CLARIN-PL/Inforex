var url = $.url(window.location.href);
var corpus_id = url.param('corpus');
var publicCorporaCache = {};
var publicCorporaRequest = null;


$(function() {

    $(".show_public").click(function(event){
        event.preventDefault();
        event.stopPropagation();

        var annotation_set_id = $(this).closest('tr').attr('id');

        getPublicCorpora(annotation_set_id);
    });

    $(".tableContent").on("click", "tbody > tr", function (element) {


        if(!$(element.target).hasClass("public-corpora-button")){
            $(this).siblings().removeClass("hightlighted");
            $(this).addClass("hightlighted");
            var containerType = $(this).parents(".tableContainer:first").attr('id');
            if (containerType == "annotationSetsContainer") {

                $('#annotationSubsetsContainer').css('visibility', 'visible');
                $("#annotationTypesContainer").css('visibility', 'hidden');

                $("#annotationSetsCorporaContainer").css('visibility', 'visibile');
                $("#corpusContainer").css('visibility', 'visible');
                $("#annotationTypesContainer table > tbody").empty();
            }
            else if (containerType == "annotationSubsetsContainer") {
                $("#annotationTypesContainer").css('visibility', 'visible');
            }
            get($(this));
        }
    });
});

function getPublicCorpora(annotation_set_id){

    $("#browse_public_corpora_modal").modal("show");
    renderPublicCorporaLoading();

    if (publicCorporaCache[annotation_set_id]) {
        renderPublicCorpora(publicCorporaCache[annotation_set_id]);
        return;
    }

    if (publicCorporaRequest) {
        publicCorporaRequest.abort();
    }

    var _data = {
        annotation_set_id: annotation_set_id,
        ajax: "public_annotation_sets"
    };

    publicCorporaRequest = $.ajax({
        type: "POST",
        url: "index.php",
        data: _data,
        dataType: "json",
        success: function(data) {
            successWrapper(data, function(result) {
                publicCorporaCache[annotation_set_id] = result;
                renderPublicCorpora(result);
            }, function() {
                $("#public_corpora_table").html('<tr><td colspan="3" class="public-annotations-empty">Unable to load public corpora.</td></tr>');
            });
        },
        error: function(request, textStatus) {
            if (textStatus !== "abort") {
                $("#public_corpora_table").html('<tr><td colspan="3" class="public-annotations-empty">Unable to load public corpora.</td></tr>');
            }
        },
        complete: function() {
            publicCorporaRequest = null;
        }
    });
}

function renderPublicCorpora(data) {
    var tableHtml = "";

    if (!data || data.length === 0) {
        $("#public_corpora_table").html('<tr><td colspan="3" class="public-annotations-empty">No public corpora found.</td></tr>');
        return;
    }

    $.each(data, function(index, value){
        tableHtml += "<tr>" +
                        "<td class='public-annotation-name'>"+escapeHtml(value.name)+"</td>"+
                        "<td>"+buildDescription(value.description)+"</td>"+
                        "<td class='text-center'>"+buildCountBadge(value.count_uses, false)+"</td>" +
                    "</tr>";
    });
    $("#public_corpora_table").html(tableHtml);
}

function renderPublicCorporaLoading() {
    $("#public_corpora_table").html(
        '<tr><td colspan="3" class="public-annotations-loading">' +
            '<span class="public-annotations-loader"></span>' +
            '<span>Loading public corpora...</span>' +
        '</td></tr>'
    );
}

function get($element) {
    var $container = $element.parents(".tableContainer:first");
    var containerName = $container.attr("id");
    var childId = "";
    if (containerName == "annotationSetsContainer" || containerName == "annotationSubsetsContainer") {
        var _data = {
            parent_id: $element.attr('id')
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
                        '<tr id = "'+value.id+'">' +
                        '<td class="public-annotation-name">' + escapeHtml(value.name) + '</td>' +
                        '<td>' + buildDescription(value.description) + '</td>' +
                        '</tr>';
                }
                else if (_data.parent_type == "annotation_subset")
                    tableRows +=
                        '<tr id = '+value.id+'>' +
                        '<td><span class="public-annotation-type-name" style="' + escapeAttribute(value.css == null ? "" : value.css) + '">' + escapeHtml(value.name) + '</span></td>' +
                        '<td>' + buildDescription(value.description) + '</td>' +
                        '<td class = "text-center">'+ buildCountBadge(value.number_used, false) +'</td>'+
                        '<td style="display:none">' + (value.css == null ? "" : value.css) + '</td>' +
                        '</tr>';
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
        doAjaxSyncWithLogin("annotation_edit_get", _data, success, login);
    }
}

function buildDescription(description) {
    var text = description == null ? "" : description;

    return '<div class="annotation_description administration-description-preview" title="' + escapeAttribute(text) + '">' + escapeHtml(text) + '</div>';
}

function buildCountBadge(count, muted) {
    return '<span class="public-annotations-count-badge' + (muted ? ' public-annotations-count-badge-muted' : '') + '">' + escapeHtml(count) + '</span>';
}

function escapeHtml(value) {
    return $('<div>').text(value == null ? "" : value).html();
}

function escapeAttribute(value) {
    return escapeHtml(value).replace(/"/g, '&quot;');
}
