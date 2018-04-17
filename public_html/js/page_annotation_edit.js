/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){

    $(".delete").click(function () {
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

    //Resets fields on the modal when it is closed
    $('.modal').on('hidden.bs.modal', function (e) {
        $(this)
            .find("input,textarea")
            .val('')
            .removeClass('error')
            .end()
            .find("input[type=checkbox], input[type=radio]")
            .prop("checked", "")
            .end()
            .find("#annotation_type_preview")
            .removeAttr("style")
            .end()
            .find("label.error")
            .remove()
            .end();
    })
	
	$(".move").click(function(e){
		e.preventDefault();		
		move($(this));
	});

	$(".tableContent").on("click", "tbody > tr" ,function(){
		$(this).siblings().removeClass("hightlighted");
		$(this).addClass("hightlighted");
		containerType = $(this).parents(".tableContainer:first").attr('id');
		if (containerType=="annotationSetsContainer"){
			$("#annotationSetsContainer .edit,#annotationSetsContainer .delete").show();
			$("#annotationSubsetsContainer .create").show();
            $('#annotationSubsetsContainer').css('visibility','visible');
            $("#annotationTypesContainer").css('visibility','hidden');
            $("#annotationSetsCorporaContainer").css('visibility','visible');
            $("#corpusContainer").css('visibility','visible');
			$("#annotationSubsetsContainer .edit,#annotationSubsetsContainer .delete").hide();
			$("#annotationTypesContainer span").hide();
			$("#annotationTypesContainer table > tbody").empty();
		}
		else if (containerType=="annotationSubsetsContainer"){
			$("#annotationSubsetsContainer .edit,#annotationSubsetsContainer .delete").show();
			$("#annotationTypesContainer .create").show();
            $("#annotationTypesContainer").css('visibility','visible');
			$("#annotationTypesContainer .edit,#annotationTypesContainer .delete").hide();
		}
		else if (containerType=="annotationTypesContainer"){
			$("#annotationTypesContainer .edit,#annotationTypesContainer .delete").show();
		}
		get($(this));
	});

    $("#create_predefined-styles span").click(function(){
        var css = $(this).attr("style");
        $("#create_annotation_type_css").val(css);
        $("#create_annotation-style-preview").attr("style", css);
    });

    $("#edit_predefined-styles span").click(function(){
        var css = $(this).attr("style");
        $("#edit_annotation_type_css").val(css);
        $("#edit_annotation-style-preview").attr("style", css);
    });

    $("#create_annotation_type_css").bind('input propertychange', function(){
        var css = $(this).val();
        $("#create_annotation-style-preview").attr("style", css);
    });


    $("#edit_annotation_type_css").bind('input propertychange', function(){
        var css = $(this).val();
        $("#edit_annotation-style-preview").attr("style", css);
    });
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
						'<td class = "column_id td-right">'+value.id+'</td>'+
						'<td>'+value.name+'</td>'+
					    '<td><div class = "annotation_description">'+(value.description==null ? "" : value.description)+'</div></td>'+
					'</tr>';
				}
				else if (_data.parent_type=="annotation_subset")
					tableRows+=
						'<tr id = "'+value.id+'">'+
							'<td><span style="'+(value.css==null ? "" : value.css)+'">'+value.name+'</span></td>'+
							'<td>'+(value.short==null ? "" : value.short)+'</td>'+
							'<td><div class = "annotation_description">'+(value.description==null ? "" : value.description)+'</div></td>'+
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
							'<td>'+(value.description==null ? "" : value.description)+'</td>'+
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
							'<td>'+(value.description==null ? "" : value.description)+'</td>'+
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
            console.log(accessType);

            var visibility;
            if (accessType === "public") {
                visibility = 1;
            } else {
                visibility = 0;
            }

            var _data = {
                desc_str: $("#create_annotation_set_name").val(),
                description: $("#create_annotation_set_description").val(),
                setAccess_str: visibility,
                element_type: elementType
            };

            var success = function (data) {
                $container.find("table > tbody").append(
                    '<tr visibility = ' + visibility + '>' +
                    '<td class = "column_id td-right">' + data.last_id + '</td>' +
                    '<td>' + _data.desc_str + '</td>' +
                    '<td><div class = "annotation_description">' + _data.description + '</div></td>' +
                    '<td class = "td-center">' + data.user + '</td>' +
                    '<td class = "td-center">' + accessType + '</td>' +
                    '</tr>'
                );

                $('#create_annotation_set_modal').modal('hide');
            };

            doAjaxSyncWithLogin("annotation_edit_add", _data, success, null);

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
                        }
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
                parent_id: parent_id
            };

            var success = function (data) {

                $container.find("table > tbody").append(
                    '<tr>' +
                    '<td class = "column_id td-right">' + data.last_id + '</td>' +
                    '<td>' + _data.desc_str + '</td>' +
                    '<td><div class = "annotation_description">' + _data.description + '</div></td>' +
                    '</tr>'
                );
            };

            doAjaxSyncWithLogin("annotation_edit_add", _data, success, null);
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
        $("#create_annotation_type_preview").attr('style', value);
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
                set_id: $("#annotationSetsTable .hightlighted > td:first").text(),
                corpus: corpus_id
            };

            var success = function (data) {

                $container.find("table > tbody").append(
                    '<tr>' +
                    '<td><span style="' + _data.css + '">' + _data.name_str + '</span></td>' +
                    '<td>' + _data.short + '</td>' +
                    '<td><div class = "annotation_description">' + _data.desc_str + '</div></td>' +
                    '<td>' + _data.visibility + '</td>' +
                    '<td style="display:none">' + _data.css + '</td>' +
                    '</tr>'
                );
                $('#create_annotation_type_modal').modal('hide');
            };

            doAjaxSyncWithLogin("annotation_edit_add", _data, success, null);
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
                    '<td class = "column_id td-right">' + $container.find(".hightlighted td:first").text() + '</td>' +
                    '<td>' + _data.desc_str + '</td>' +
                    '<td><div class = "annotation_description">' + _data.description + '</div></td>'
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
    var annotation_type_id = $container.find(".hightlighted").attr('id');
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
                element_id: $($vals[0]).text(),
                annotation_type_id: annotation_type_id,
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
                    '<td><span style="' + _data.css + '">' + _data.name_str + '</span></td>' +
                    '<td>' + _data.short + '</td>' +
                    '<td><div class = "annotation_description">' + _data.desc_str + '</div></td>' +
                    '<td>' + _data.shortlist + '</td>' +
                    '<td style="display:none">' + _data.css + '</td>');
                $('#edit_annotation_type_modal').modal('hide');

            };

            var login = function () {
                edit($element);
            };

            doAjaxSyncWithLogin("annotation_edit_update", _data, success, login);
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
    if(visibility === "1"){
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

            var success = function (data) {
                if (elementType == "annotation_set") {
                    $container.find(".hightlighted:first").html(
                        '<td class = "column_id td-right">' + $container.find(".hightlighted td:first").text() + '</td>' +
                        '<td>' + _data.desc_str + '</td>' +
                        '<td><div class = "annotation_description">' + _data.description + '</div></td>' +
                        '<td class = "td-center">' + $container.find(".hightlighted td:nth-child(4)").text() + '</td>' +
                        '<td class = "td-center" >' + $("#edit_setAccess").val() + '</td>'
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

            doAjaxSyncWithLogin("annotation_edit_update", _data, success, login);
        }
    });

}


function remove_annotation($element) {
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $element.parents(".tableContainer");
    var element_id;
    if (elementType == "annotation_set" || elementType == "annotation_subset") {
        element_id = $container.find('.hightlighted td:first').text();
        var delete_html =
            '<label for="delName">Name:</label>' +
            '<p id = "delName">' + $container.find('.hightlighted td:first').next().text() + '</p>';
    }
    else if (elementType == "annotation_type") {
        $vals = $container.find('.hightlighted td');
        element_id = $container.find(".hightlighted").attr('id');
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
                $("#annotationSetsContainer .edit,#annotationSetsContainer .delete").hide();
                $("#annotationSubsetsContainer span").hide();
                $("#annotationTypesContainer span").hide();
                $("#annotationSubsetsContainer table > tbody").empty();
                $("#annotationTypesContainer table > tbody").empty();
                $("#annotationSetsCorporaTable > tbody").empty();
                $("#corpusTable > tbody").empty();
            }
            else if (elementType == "annotation_subset") {
                $("#annotationSubsetsContainer .create").show();
                $("#annotationSubsetsContainer .edit,#annotationSubsetsContainer .delete").hide();
                $("#annotationTypesContainer span").hide();
                $("#annotationTypesContainer table > tbody").empty();
            }
            else {
                $("#annotationTypesContainer .edit,#annotationTypesContainer .delete").hide();
            }

            $('#deleteModal').modal('hide');
        };


        var login = function () {
            remove($element);
        };

        doAjaxSyncWithLogin("annotation_edit_delete", _data, success, login);
    });

}

function move($element){
	var $moveElement = null;
	var $targetElement = null;
	var _data = {
		//ajax : "annotation_edit_move" 
	};
	var $setElement = $("#annotationSetsTable tr.hightlighted:first");
	if ($element.hasClass("assign")){
		$moveElement =  $("#corpusTable tr.hightlighted:first").removeClass("hightlighted");
		$targetTable = $("#annotationSetsCorporaTable > tbody");
		_data.move_type = 'assign';
	}
	else if ($element.hasClass("unassign")){
		$moveElement =  $("#annotationSetsCorporaTable tr.hightlighted:first").removeClass("hightlighted");
		$targetTable = $("#corpusTable > tbody");
		_data.move_type = 'unassign';
	}
	if ($moveElement.length>0){
		_data.set_id = $setElement.children("td:first").text();
		_data.corpora_id = $moveElement.children("td:first").text();
		
		var success = function(data){
			$targetTable.append($moveElement);
		};
		var login = function(data){
			move($element);
		};
		
		doAjaxSyncWithLogin("annotation_edit_move", _data, success, login);
	}

}