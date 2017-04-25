var url = $.url(window.location.href);
var corpus_id = url.param('corpus');

$(function(){

    $(".createCustom").click(function(){
        add_annotation($(this));
    });

    $(".editCustom").click(function(){
        edit_annotation($(this));
    });

    $(".deleteCustom").click(function(){
        remove_annotation($(this));
    });

    $(".tableContent").on("click", "tbody > tr" ,function(){
        $(this).siblings().removeClass("hightlighted");
        $(this).addClass("hightlighted");
        containerType = $(this).parents(".tableContainer:first").attr('id');
        if (containerType=="annotationSetsContainer"){
            $("#annotationSetsContainer .editCustom,#annotationSetsContainer .deleteCustom").show();
            $("#annotationSubsetsContainer .create").show();
            $('#annotationSubsetsContainer').css('visibility','visible');
            $("#annotationTypesContainer").css('visibility','hidden');
            $("#annotationSetsCorporaContainer").css('visibility','visibile');
            $("#corpusContainer").css('visibility','visible');
            $("#annotationSubsetsContainer .edit,#annotationSubsetsContainer .delete").hide();
            $("#annotationTypesContainer span").hide();
            $("#annotationTypesContainer table > tbody").empty();
        }
        else if (containerType=="annotationSubsetsContainer"){
            $("#annotationSubsetsContainer .editCustom,#annotationSubsetsContainer .deleteCustom").show();
            $("#annotationTypesContainer .createCustom").show();
            $("#annotationTypesContainer").css('visibility','visible');
            $("#annotationTypesContainer .editCustom,#annotationTypesContainer .deleteCustom").hide();
        }
        else if (containerType=="annotationTypesContainer"){
            $("#annotationTypesContainer .editCustom,#annotationTypesContainer .deleteCustom").show();
        }
        get($(this));
    });

    //$("")
});


function get($element){
    var $container = $element.parents(".tableContainer:first");
    var containerName = $container.attr("id");
    var childId = "";
    if (containerName=="annotationSetsContainer" || containerName=="annotationSubsetsContainer"){
        var _data = 	{
            //ajax : "annotation_edit_get",
            parent_id : $element.children(":first").text()
        };
        if (containerName=="annotationSetsContainer"){
            childId = "annotationSubsetsContainer";
            _data.parent_type = 'annotation_set';
        }
        else {
            childId = "annotationTypesContainer";
            _data.parent_type = 'annotation_subset';
        }

        var success = function(data){
            var tableRows = "";
            $.each(data,function(index, value){
                //for annotation_set the last two objects contains data from annotation_sets_corpora and corpora
                if (_data.parent_type=="annotation_set" && index<data.length-2){
                    tableRows+=
                        '<tr>'+
                        '<td class = "column_id">'+value.id+'</td>'+
                        '<td>'+value.description+'</td>'+
                        '</tr>';
                }
                else if (_data.parent_type=="annotation_subset")
                    tableRows+=
                        '<tr>'+
                        '<td><span style="'+(value.css==null ? "" : value.css)+'">'+value.name+'</span></td>'+
                        '<td>'+(value.short==null ? "" : value.short)+'</td>'+
                        '<td>'+(value.description==null ? "" : value.description)+'</td>'+
                        '<td>'+(value.shortlist==0 ? "Visible" : "Hidden")+'</td>'+
                        '<td style="display:none">'+(value.css==null ? "" : value.css)+'</td>'+
                        '</tr>';
            });
            $("#"+childId+" table > tbody").html(tableRows);

            if (_data.parent_type=="annotation_set"){
                //annotation_sets_corpora:
                tableRows = "";
                $.each(data[data.length-2],function(index, value){
                    tableRows+=
                        '<tr>'+
                        '<td class = "column_id">'+value.id+'</td>'+
                        '<td>'+value.name+'</td>'+
                        '<td>'+value.description+'</td>'+
                        '</tr>';
                });
                $("#annotationSetsCorporaContainer table > tbody").html(tableRows);
                //corpora:
                tableRows = "";
                $.each(data[data.length-1],function(index, value){
                    tableRows+=
                        '<tr>'+
                        '<td class = "column_id">'+value.id+'</td>'+
                        '<td>'+value.name+'</td>'+
                        '<td>'+value.description+'</td>'+
                        '</tr>';
                });
                $("#corpusContainer table > tbody").html(tableRows);
            }
        };
        var login = function(data){
            get($element);
        };
        doAjaxSyncWithLogin("annotation_edit_get", _data, success, login);
    }

}

function add_annotation($element){
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $element.parents(".tableContainer");
    var $dialogBox = null;
    if (elementType=="annotation_set")
        $dialogBox =
            $('<div class="addDialogCustom">'+
                '<table>'+
                    '<tr>'+
                        '<th style="text-align:right">Description</th>'+
                        '<td><textarea id="elementDescriptionCustom" rows="4"></textarea></td>'+
                    '</tr>'+
                    '<tr>'+
                        '<th style="text-align:right">Access</th>'+
                        '<td>   <select id="setAccessCustom">' +
                        '<option value = "public">Public</option>' +
                        '<option value = "private">Private</option>' +
                        '</select>' +
                        '</td>'+
                    '</tr>'+
                '</table>'+
                '</div>');
    else if(elementType == "annotation_subset")
        $dialogBox =
            $('<div class="addDialogCustom">'+
                '<table>'+
                '<tr>'+
                '<th style="text-align:right">Description</th>'+
                '<td><textarea id="elementDescriptionCustom" rows="4"></textarea></td>'+
                '</tr>' +
                '</table>'+
                '</div>');
    else if (elementType=="annotation_type")
        $dialogBox =
            $('<div class="addDialogCustom">'+
                '<table>'+
                '<tr>'+
                '<th style="text-align:right">Name</th>'+
                '<td><input id="elementNameCustom" type="text" /></td>'+
                '</tr>'+
                '<tr>'+
                '<th style="text-align:right">Short</th>'+
                '<td><input id="elementShortCustom" type="text" /></td>'+
                '</tr>'+
                '<tr>'+
                '<th style="text-align:right">Description</th>'+
                '<td><textarea id="elementDescriptionCustom" rows="4"></textarea></td>'+
                '</tr>'+
                '<tr>'+
                '<th style="text-align:right">Visibility</th>'+
                '<td>' +
                '<select id="elementVisibility">' +
                '<option value = "Hidden">Hidden</option>' +
                '<option value = "Visible">Visibile</option>' +
                '</select>' +
                '</td>'+
                '</tr>'+
                '<tr>'+
                '<th style="text-align:right">Css</th>'+
                '<td><textarea id="elementCssCustom" rows="4"></textarea><br/>(<a href="#" id="previewCssButton">refresh preview</a>)</td>'+
                '</tr>'+
                '<tr>'+
                '<th style="text-align:right">Preview</th>'+
                '<td><span id="previewCssSpanCustom">sample</span></td>'+
                '</tr>'+
                '</table>'+
                '</div>');
    $dialogBox.dialog({
        modal : true,
        title : 'Create '+elementType.replace(/_/g," "),
        buttons : {
            Cancel: function() {
                $dialogBox.dialog("close");
            },
            Ok : function(){
                var _data = 	{
                    //ajax : "annotation_edit_add",
                    desc_str : $("#elementDescriptionCustom").val(),
                    setAccess_str : $('#setAccessCustom').val(),
                    element_type : elementType
                };
                if (elementType == 'annotation_set'){
                    _data.customAnnotation = true;
                    _data.corpus = corpus_id;
                }
                else if (elementType=='annotation_subset'){
                    _data.parent_id = $("#annotationSetsTable .hightlighted > td:first").text();
                }
                else if (elementType=='annotation_type'){
                    _data.parent_id = $("#annotationSubsetsTable .hightlighted > td:first").text();
                    _data.name_str = $("#elementNameCustom").val();
                    _data.short = $("#elementShortCustom").val();
                    _data.description = $("#elementDescriptionCustom").val();
                    _data.visibility = $("#elementVisibility").val();
                    console.log(_data.visibility);
                    _data.css = $("#elementCss").val();
                    _data.set_id = $("#annotationSetsTable .hightlighted > td:first").text();
                }

                var success = function(data){
                    console.log(data);
                    if (elementType=="annotation_set"){
                        if(_data.setAccess_str == "public"){
                            visibility = 1;
                        } else{
                            visibility = 0;
                        }
                        $container.find("table > tbody").append(
                            '<tr visibility = '+visibility+'>'+
                            '<td class = "column_id">'+data.last_id+'</td>'+
                            '<td>'+_data.desc_str+'</td>'+
                            '<td>'+ data.user+'</td>'+
                            '<td>'+ _data.setAccess_str+'</td>'+
                            '</tr>'
                        );
                    }
                    else if(elementType=="annotation_subset"){
                        $container.find("table > tbody").append(
                            '<tr>'+
                            '<td class = "column_id">'+data.last_id+'</td>'+
                            '<td>'+_data.desc_str+'</td>'+
                            '</tr>'
                        );
                    }

                    else if (elementType=="annotation_type")
                        $container.find("table > tbody").append(
                            '<tr>'+
                            '<td><span style="'+_data.css+'">'+_data.name_str+'</span></td>'+
                            '<td>'+_data.short+'</td>'+
                            '<td>'+_data.desc_str+'</td>'+
                            '<td>'+_data.visibility+'</td>'+
                            '<td style="display:none">'+_data.css+'</td>'+
                            '</tr>'
                        );
                    $dialogBox.dialog("close");
                };
                var login = function(){
                    $dialogBox.dialog("close");
                    add($element);
                };

                doAjaxSyncWithLogin("annotation_edit_add", _data, success, login);
            }
        },
        close: function(event, ui) {
            $dialogBox.dialog("destroy").remove();
            $dialogBox = null;
        }
    });
    if (elementType=="annotation_type"){
        $("#previewCssButton").click(function(){
            $("#previewCssSpan").attr('style',$("#elementCss").val());
        });
    }


}

function edit_annotation($element){
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $element.parents(".tableContainer");
    var $dialogBox = null;
    if (elementType=="annotation_set")
        $dialogBox =
            $('<div class="editDialog">'+
                '<table>'+
                '<tr>'+
                '<th style="text-align:right">Description</th>'+
                '<td><textarea id="elementDescription" rows="4">'+$container.find('.hightlighted td:first').next().text()+'</textarea></td>'+
                '</tr>'+
                '<tr>'+
                '<th style="text-align:right">Access</th>'+
                '<td>   ' +
                '<select id="setAccess">' +
                '<option ' + ($container.find('.hightlighted').attr("visibility") == 1 && 'selected = "selected"') + ' value = "public">Public</option>' +
                '<option ' + (  $container.find('.hightlighted').attr("visibility") == 0 && 'selected = "selected"') + ' value = "private">Private</option>' +
                '</select>' +
                '</td>'+
                '</tr>'+
                '</table>'+
                '</div>');
    else if (elementType=="annotation_subset"){
        $dialogBox =
            $('<div class="editDialog">'+
                '<table>'+
                '<tr>'+
                '<th style="text-align:right">Description</th>'+
                '<td><textarea id="elementDescription" rows="4">'+$container.find('.hightlighted td:first').next().text()+'</textarea></td>'+
                '</tr>' +
                '</table>'+
                '</div>');
    }
    else if (elementType=="annotation_type"){
        $vals = $container.find('.hightlighted td');
        $dialogBox =
            $('<div class="addDialog">'+
                '<table>'+
                '<tr>'+
                '<th style="text-align:right">Name</th>'+
                '<td style="padding-top: 4px"><span id="previewCssSpan" style="'+$($vals[4]).text()+'">'+ $($vals[0]).text()+'</span></td>'+
                '</tr>'+
                '<tr>'+
                '<th style="text-align:right">Short</th>'+
                '<td><input id="elementShort" type="text" value="'+$($vals[1]).text()+'"/></td>'+
                '</tr>'+
                '<tr>'+
                '<th style="text-align:right">Description</th>'+
                '<td><textarea id="elementDescription" rows="4">'+$($vals[2]).text()+'</textarea></td>'+
                '</tr>'+
                '<tr>'+
                '<th style="text-align:right">Visibility</th>'+
                '<td><select id="elementVisibility">' +
                '<option value = "Visible" ' + ($($vals[3]).text()=="Visible" ? "selected='selected'" : "") + ' >Visible</option>' +
                '<option value = "Hidden" ' + ($($vals[3]).text()=="Hidden" ? "selected='selected'" : "") + ' >Hidden</option>' +
                '</select></td>'+
                '</tr>'+
                '<tr>'+
                '<th style="text-align:right">Css</th>'+
                '<td><textarea id="elementCss">'+$($vals[4]).text()+'</textarea><br/>(<a href="#" id="previewCssButton">refresh preview</a>)</td>'+
                '</tr>'+
                '</table>'+
                '</div>');
    }

    $dialogBox.dialog({
        modal : true,
        title : 'Edit '+elementType.replace(/_/g," "),
        buttons : {
            Cancel: function() {
                $dialogBox.dialog("close");
            },
            Ok : function(){
                var _data = 	{
                    //ajax : "annotation_edit_update",
                    desc_str : $("#elementDescription").val(),
                    set_access: $("#setAccess").val(),
                    element_id : $container.find('.hightlighted td:first').text(),
                    element_type : elementType
                };
                if (elementType=='annotation_subset'){
                    _data.parent_id = $("#annotationSetsTable .hightlighted > td:first").text();
                }
                else if (elementType=='annotation_type'){
                    _data.parent_id = $("#annotationSubsetsTable .hightlighted > td:first").text();
                    _data.short = $("#elementShort").val();
                    _data.shortlist = $("#elementVisibility").val();
                    _data.css = $("#elementCss").val();
                    _data.set_id = $("#annotationSetsTable .hightlighted > td:first").text();
                }

                var success = function(data){
                    if (elementType=="annotation_set")
                        $container.find(".hightlighted:first").html(
                            '<td >'+$container.find(".hightlighted td:first").text()+'</td>'+
                            '<td>'+_data.desc_str+'</td>' +
                            '<td >'+$container.find(".hightlighted td:nth-child(3)").text()+'</td>'+
                            '<td >'+$("#setAccess").val()+'</td>'
                        );
                    else if (elementType=="annotation_subset")
                        $container.find(".hightlighted:first").html(
                            '<td >'+$container.find(".hightlighted td:first").text()+'</td>'+
                            '<td>'+_data.desc_str+'</td>' +
                            '<td >'+$container.find(".hightlighted td:nth-child(3)").text()+'</td>'
                        );
                    else if (elementType=="annotation_type")
                        $container.find(".hightlighted:first").html(
                            '<td><span style="'+_data.css+'">'+_data.element_id+'</span></td>'+
                            '<td>'+_data.short+'</td>'+
                            '<td>'+_data.desc_str+'</td>'+
                            '<td>'+_data.shortlist+'</td>'+
                            '<td style="display:none">'+_data.css+'</td>'
                        );
                    if(_data.set_access == "public"){
                        visibility = 1
                    } else{
                        visibility = 0
                    }

                    $container.find(".hightlighted").attr('visibility', visibility);
                    $dialogBox.dialog("close");
                };
                var login = function(){
                    $dialogBox.dialog("close");
                    edit($element);
                };

                doAjaxSyncWithLogin("annotation_edit_update", _data, success, login);
            }
        },
        close: function(event, ui) {
            $dialogBox.dialog("destroy").remove();
            $dialogBox = null;
        }
    });
    if (elementType=="annotation_type"){
        $("#previewCssButton").click(function(){
            $("#previewCssSpan").attr('style',$("#elementCss").val());
        });
    }

}


function remove_annotation($element){
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $element.parents(".tableContainer");
    var $dialogBox = null;
    if (elementType=="annotation_set" || elementType=="annotation_subset")
        $dialogBox =
            $('<div class="deleteDialog">'+
                '<table>'+
                '<tr>'+
                '<th style="text-align:right">Description</th>'+
                '<td>'+$container.find('.hightlighted td:first').next().text()+'</td>'+
                '</tr>'+
                '</table>'+
                '</div>');
    else if (elementType=="annotation_type"){
        $vals = $container.find('.hightlighted td');
        $dialogBox =
            $('<div class="deleteDialog">'+
                '<table>'+
                '<tr>'+
                '<th style="text-align:right">Short desc.</th>'+
                '<td>'+$($vals[1]).text()+'</td>'+
                '</tr>'+
                '<tr>'+
                '<th style="text-align:right">Description</th>'+
                '<td>'+$($vals[2]).text()+'</td>'+
                '</tr>'+
                '<tr>'+
                '<th style="text-align:right">Visibility</th>'+
                '<td>'+$($vals[3]).text()+'</td>'+
                '</tr>'+
                '<tr>'+
                '<th style="text-align:right">Css</th>'+
                '<td>'+$($vals[4]).text()+'</td>'+
                '</tr>'+
                '</table>'+
                '</div>');
    }
    $dialogBox.dialog({
        modal : true,
        title : 'Delete '+elementType.replace(/_/g," ")+ ' #'+$container.find('.hightlighted td:first').text()+"?",
        buttons : {
            Cancel: function() {
                $dialogBox.dialog("close");
            },
            Ok : function(){
                var _data = 	{
                    //ajax : "annotation_edit_delete",
                    element_type : elementType,
                    element_id : $container.find('.hightlighted td:first').text()
                };

                var success = function(data){
                    $container.find(".hightlighted:first").remove();
                    if (elementType=="annotation_set"){
                        $("#annotationSetsContainer .edit,#annotationSetsContainer .delete").hide();
                        $("#annotationSubsetsContainer span").hide();
                        $("#annotationTypesContainer span").hide();
                        $("#annotationSubsetsContainer table > tbody").empty();
                        $("#annotationTypesContainer table > tbody").empty();
                        $("#annotationSetsCorporaTable > tbody").empty();
                        $("#corpusTable > tbody").empty();
                    }
                    else if (elementType=="annotation_subset"){
                        $("#annotationSubsetsContainer .create").show();
                        $("#annotationSubsetsContainer .edit,#annotationSubsetsContainer .delete").hide();
                        $("#annotationTypesContainer span").hide();
                        $("#annotationTypesContainer table > tbody").empty();
                    }
                    else {
                        $("#annotationTypesContainer .edit,#annotationTypesContainer .delete").hide();
                    }
                    $dialogBox.dialog("close");
                };
                var login = function(){
                    $dialogBox.dialog("close");
                    remove($element);
                };

                doAjaxSyncWithLogin("annotation_edit_delete", _data, success, login);
            }
        },
        close: function(event, ui) {
            $dialogBox.dialog("destroy").remove();
            $dialogBox = null;
        }
    });

}