/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){

	$(".editRelation").click(function(){
        editRelation($(this));
	});

	$(".delete").click(function(){
		remove($(this));
	});

    $(".createRelation").click(function(){
        createRelation($(this));
    });

    $(".tableContent").on("click", "tbody > tr" ,function(){
		$(this).siblings().removeClass("hightlighted");
		$(this).addClass("hightlighted");
		containerType = $(this).parents(".tableContainer:first").attr('id');
		if (containerType=="annotationSetsContainer"){
			$("#relationTypesContainer .create").show();
            $("#relationTypesContainer").show();
			$("#relationTypesContainer .edit,#relationTypesContainer .delete").hide();
			$("#relationTypesContainer table > tbody").empty();
		}
		else if (containerType=="relationTypesContainer"){
			$("#relationTypesContainer .edit,#relationTypesContainer .delete").show();
		}
		get($(this));
	});
}); 


function get($element){
	var $container = $element.parents(".tableContainer:first");
	var containerName = $container.attr("id");
	var childId = "";
	if (containerName!="relationTypesContainer"){
		var _data = 	{ 
				parent_id : $element.children(":first").text()
			};
		if (containerName=="annotationSetsContainer"){
			childId = "relationTypesContainer";
			_data.parent_type = 'annotation_set';
		}

		var success = function(data){
			var tableRows = "";
			$.each(data,function(index, value){

                if(value.description === null){
                    value.description = "";
                }

				tableRows+=
				'<tr>'+
					'<td class = "column_id">'+value.id+'</td>'+
					'<td>'+value.name+'</td>'+
					'<td>'+value.description+'</td>'+
				'</tr>';
			});
			$("#"+childId+" table > tbody").html(tableRows);
		};
		
		var login = function(){
			get($element);
		};
		
		doAjaxSyncWithLogin("relation_type_get", _data, success, login);			
	}
}

function createRelation($element){
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $element.parents(".tableContainer");

    $( "#create_relation_form" ).validate({
        rules: {
            create_relation_name: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'administration_validation',
                        type: 'relation_edit',
                        mode: 'create'
                    }
                }
            }
        },
        messages: {
            create_relation_name: {
                required: "Relation must have a name.",
				remote: "This relation type already exists"
            }
        }
    });

    $( ".confirm_relation_create" ).unbind( "click" ).click(function() {

        if ($('#create_relation_form').valid()) {
            var _data = 	{
                //ajax : "relation_type_add",
                name_str : $("#create_relation_name").val(),
                desc_str : $("#create_relation_description").val(),
                element_type : elementType
            };

            if (elementType=='relation_type'){
                _data.parent_id = $("#annotationSetsTable .hightlighted > td:first").text();
            }

            var success = function(data){
                $container.find("table > tbody").append(
                    '<tr>'+
                    '<td class = "column_id">'+data.last_id+'</td>'+
                    '<td>'+_data.name_str+'</td>'+
                    '<td>'+_data.desc_str+'</td>'+
                    '</tr>'
                );

                $('#create_relation_modal').modal('hide');
            };

            doAjaxSync("relation_type_add", _data, success);
        }
    });
}

function editRelation($element){
	var elementType = $element.parent().attr("element");
	var parent = $element.parent().attr("parent");
	var $container = $element.parents(".tableContainer");

	$("#edit_relation_name").val($container.find('.hightlighted td:first').next().text());
    $("#edit_relation_description").val($container.find('.hightlighted td:last').text());


    $( "#edit_relation_form" ).validate({
        rules: {
            edit_relation_name: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'administration_validation',
                        type: 'relation_edit',
                        id: function(){
                            return $container.find('.hightlighted td:first').text();
                        },
                        mode: 'edit'
                    }
                }
            }
        },
        messages: {
            edit_relation_name: {
                required: "Relation must have a name.",
                remote: "This relation type already exists"
            }
        }
    });


    $( ".confirm_relation_edit" ).unbind( "click" ).click(function() {

		if ($('#edit_relation_form').valid()) {
			var _data = 	{
					name_str : $("#edit_relation_name").val(),
					desc_str : $("#edit_relation_description").val(),
					element_type : elementType,

					element_id : $container.find('.hightlighted td:first').text()
				};

			var success = function(data){
				$container.find(".hightlighted:first").html(
						'<td class = "column_id">'+$container.find(".hightlighted td:first").text()+'</td>'+
						'<td>'+_data.name_str+'</td>'+
						'<td>'+_data.desc_str+'</td>'
				);

				$('#edit_relation_modal').modal('hide');
			};

			doAjaxSync("relation_type_update", _data, success);

		}
	});
}

function remove($element){	
	var elementType = $element.parent().attr("element");
	var parent = $element.parent().attr("parent");
	var $container = $element.parents(".tableContainer");
	var deleteContent =
						'<label for = "delete_name">Name</label>'+
						'<p id = "delete_name">'+$container.find('.hightlighted td:first').next().text()+'</p>'+
						'<label for = "delete_desc">Description</label>'+
						'<p id = "delete_desc">'+$container.find('.hightlighted td:last').text()+'</p>';

    $('#deleteContent').html(deleteContent);
    $('#deleteModal').modal('show');

    $( ".confirmDelete" ).unbind( "click" ).click(function() {
        var _data =
		{
			ajax : "relation_type_delete",
			element_type : elementType,
			element_id : $container.find('.hightlighted td:first').text()
		};

		var success = function(data){
			$container.find(".hightlighted:first").remove();
			if (elementType=="relation_type"){
				$("#relationTypesContainer .create").show();
				$("#relationTypesContainer .edit,#relationTypesContainer .delete").hide();
			}
            $('#deleteModal').modal('hide');

        };

		doAjaxSync("relation_type_delete", _data, success);
	});
	
}