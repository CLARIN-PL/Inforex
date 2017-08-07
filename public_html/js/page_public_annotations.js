var url = $.url(window.location.href);
var corpus_id = url.param('corpus');


$(function() {

    $(".tableContent").on("click", "tbody > tr", function () {
        $(this).siblings().removeClass("hightlighted");
        $(this).addClass("hightlighted");
        containerType = $(this).parents(".tableContainer:first").attr('id');
        if (containerType == "annotationSetsContainer") {

            $('#annotationSubsetsContainer').css('visibility', 'visible');
            $("#annotationTypesContainer").css('visibility', 'hidden');

            $("#annotationSetsCorporaContainer").css('visibility', 'visibile');
            $("#corpusContainer").css('visibility', 'visible');
            $("#annotationTypesContainer span").hide();
            $("#annotationTypesContainer table > tbody").empty();
        }
        else if (containerType == "annotationSubsetsContainer") {
            $("#annotationTypesContainer").css('visibility', 'visible');
        }
        get($(this));
    });
});

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
                        '<td>' + value.name + '</td>' +
                        '<td>' + (value.description == null ? "" : value.description) + '</td>' +
                        '</tr>';
                }
                else if (_data.parent_type == "annotation_subset")
                    tableRows +=
                        '<tr id = '+value.id+'>' +
                        '<td><span style="' + (value.css == null ? "" : value.css) + '">' + value.name + '</span></td>' +
                        '<td>' + (value.description == null ? "" : value.description) + '</td>' +
                        '<td class = "text-center"><span class="badge">'+ value.number_used +'</span></td>'+
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
