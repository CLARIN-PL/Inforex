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

    $(".createRelationSet").click(function(){
        createRelationSet($(this));
    });

    $(".editRelationSet").click(function(){
        editRelationSet($(this));
    });

    $(".annotation_set_checkbox").click(function(){
        var annotation_set_id = $(this).closest('tr').attr('id');
        var relation_direction = $(this).attr('name');
        var relation_type_id = $("#relationTypesContainer").find('.hightlighted td:first').text();

        if($("#annotation_set_"+relation_direction+"_" + annotation_set_id).prop('checked')){
            $(this).val("set");
        } else{
            $(this).val("");
        }

        if($(this).prop('checked')){
            var action = 'create';
        } else{
            var action = 'delete';
        }

        var data = {
            'action': action,
            'mode': 'annotation_set_id',
            'annotation_set_id': annotation_set_id,
            'relation_type_id': relation_type_id,
            'direction': relation_direction
        };

        doAjaxSync("relations_groups_management", data, success);


    });

    $("#relation_group_annotation_subset").on("click", ".annotation_subset_checkbox", function(){


        var annotation_subset_id = $(this).closest('tr').attr('id');
        var relation_type_id = $("#relationTypesContainer").find('.hightlighted td:first').text();
        var relation_direction = $(this).attr('name');
        var annotation_set_id = $("#relation_group_annotation_set").find('.hightlighted').attr('id');

        if($(this).prop('checked')){
            var action = 'create';
        } else{
            var action = 'delete';
        }

        var data = {
            'action': action,
            'mode': 'annotation_subset',
            'annotation_set_id' : annotation_set_id,
            'annotation_subset_id': annotation_subset_id,
            'relation_type_id': relation_type_id,
            'direction': relation_direction
        }

        var success = function(){
            generateAnnotationSets();
            $("#relation_group_annotation_set #"+annotation_set_id).addClass("hightlighted");
            getAnnotationSubsets(annotation_set_id);
            $("#relation_group_annotation_subset #"+annotation_subset_id).addClass("hightlighted");
            getAnnotationTypes(annotation_subset_id);
            var relation_type_container = $("#relationTypesContainer").find('.hightlighted');
            getRelationGroups(relation_type_container);

        };

        doAjaxSync("relations_groups_management", data, success);
    });

    $("#relation_group_annotation_set").on("click", ".annotation_set_checkbox", function(){
        var annotation_set_id = $(this).closest('tr').attr('id');
        var relation_type_id = $("#relationTypesContainer").find('.hightlighted td:first').text();
        var relation_direction = $(this).attr('name');

        if($(this).prop('checked')){
            var action = 'create';
        } else{
            var action = 'delete';
        }

        var data = {
            'action': action,
            'mode': 'annotation_set',
            'annotation_set_id' : annotation_set_id,
            'relation_type_id': relation_type_id,
            'direction': relation_direction
        }

        var success = function(){
            generateAnnotationSets();
            $("#relation_group_annotation_set #"+annotation_set_id).addClass("hightlighted");
            getAnnotationSubsets(annotation_set_id);
            $("#relation_group_annotation_type").html("");

            var relation_type_container = $("#relationTypesContainer").find('.hightlighted');
            getRelationGroups(relation_type_container);
        };

        doAjaxSync("relations_groups_management", data, success);
    });


    $("#relation_group_annotation_type").on("click", ".annotation_type_checkbox", function(){

        var annotation_type_id = $(this).closest('tr').attr('id');
        var relation_direction = $(this).attr('name');
        var relation_type_id = $("#relationTypesContainer").find('.hightlighted td:first').text();
        var annotation_set_id = $("#relation_group_annotation_set").find('.hightlighted').attr('id');
        var annotation_subset_id = $("#relation_group_annotation_subset").find('.hightlighted').attr('id');

        if($(this).prop('checked')){
           var action = 'create';
        } else{
            var action = 'delete';
        }

        var data = {
            'action': action,
            'mode': 'annotation_type',
            'annotation_set_id' : annotation_set_id,
            'annotation_type_id': annotation_type_id,
            'annotation_subset_id': annotation_subset_id,
            'relation_type_id': relation_type_id,
            'direction': relation_direction
        }

        var success = function(){
            generateAnnotationSets();
            $("#relation_group_annotation_set #"+annotation_set_id).addClass("hightlighted");
            getAnnotationSubsets(annotation_set_id);
            $("#relation_group_annotation_subset #"+annotation_subset_id).addClass("hightlighted");
            getAnnotationTypes(annotation_subset_id);

            var relation_type_container = $("#relationTypesContainer").find('.hightlighted');
            getRelationGroups(relation_type_container);


        };

        doAjaxSync("relations_groups_management", data, success);
    });




    $(".createRelationGroup").click(function(){
        generateAnnotationSets();
        
        
        
        $("#relation_group_annotation_subset").html("");
        $("#relation_group_annotation_type").html("");
    });

    $(".relationGroupManagement").on("click", "tbody > tr", function(){
        $(this).siblings().removeClass("hightlighted");
        $(this).addClass("hightlighted");
        var container_type = $(this).parent().attr('id');
        var annotation_id = $(this).attr('id');

        if(container_type == "relation_group_annotation_set"){
            getAnnotationSubsets(annotation_id);
            $("#relation_group_annotation_type").html("");

        }
        else if(container_type == "relation_group_annotation_subset"){
            getAnnotationTypes(annotation_id);
        }
        else if(container_type == "relation_group_annotation_type"){

        }
    });

    $(".tableContent").on("click", "tbody > tr" ,function(){
		$(this).siblings().removeClass("hightlighted");
		$(this).addClass("hightlighted");
		var containerType = $(this).parents(".tableContainer:first").attr('id');

        if (containerType=="relationSetsContainer"){
            $("#relationSetsContainer .edit").show();
            $("#relationSetsContainer .delete").show();


            $("#relationTypesContainer .create").show();
            $("#relationGroupsContainer").hide();
            $("#relationTypesContainer").show();
			$("#relationTypesContainer .edit,#relationTypesContainer .delete").hide();
			$("#relationTypesContainer table > tbody").empty();
            get($(this));

        }
		else if (containerType=="relationTypesContainer"){
			$("#relationTypesContainer .edit,#relationTypesContainer .delete").show();
            $("#relationGroupsContainer .create").show();
            $("#relationGroupsContainer").show();
            $("#relationGroupsContainer .delete").hide();

            getRelationGroups($(this));

        } else if(containerType == "relationGroupsContainer"){
            $("#relationGroupsContainer .delete").show();
        }
	});
});


function getAnnotationSubsets(annotation_set_id){

    var relation_type_id = $("#relationTypesContainer").find('.hightlighted td:first').text();

    var _data = 	{
        annotation_set_id: annotation_set_id,
        relation_type_id: relation_type_id,
        mode: 'annotation_subsets'
    };

    var success = function(data){
        var tableRows = "";

        $.each(data,function(index, value){
            tableRows+=
                '<tr id = "'+value.annotation_subset_id+'">'+
                    '<td>'+value.name+'</td>'+
                    '<td>'+value.description+'</td>'+
                    '<td class = "text-center"><input '+ (value.source == 1 ? "checked" : "") +' type="checkbox" class="annotation_subset_checkbox" name = "source" id = "annotation_subset_source_'+value.annotation_subset_id+'"></td>'+
                    '<td class = "text-center"><input '+ (value.target == 1 ? "checked" : "") +' type="checkbox" class="annotation_subset_checkbox" name = "target" id = "annotation_subset_target_'+value.annotation_subset_id+'"></td>'+
                '</tr>';
        });

        $("#relation_group_annotation_subset").html(tableRows);
    };

    doAjaxSync("get_relation_type_groups", _data, success);
}

function getAnnotationTypes(annotation_subset_id){
    var relation_type_id = $("#relationTypesContainer").find('.hightlighted td:first').text();
    var annotation_set_id = $("#relation_group_annotation_set").find('.hightlighted').attr('id')

    var _data = 	{
        annotation_subset_id: annotation_subset_id,
        annotation_set_id: annotation_set_id,
        relation_type_id: relation_type_id,
        mode: 'annotation_types'
    };

    var success = function(data){
        var tableRows = "";

        $.each(data,function(index, value){
            tableRows+=
                '<tr id = "'+value.annotation_type_id+'">'+
                '<td>'+value.name+'</td>'+
                '<td>'+value.description+'</td>'+
                '<td class = "text-center"><input '+ (value.source == 1 ? "checked" : "") +' type="checkbox" class="annotation_type_checkbox" name = "source" id = "annotation_subset_source_'+value.annotation_subset_id+'"></td>'+
                '<td class = "text-center"><input '+ (value.target == 1 ? "checked" : "") +' type="checkbox" class="annotation_type_checkbox" name = "target" id = "annotation_subset_target_'+value.annotation_subset_id+'"></td>'+
                '</tr>';
        });


        $("#relation_group_annotation_type").html(tableRows);
    };

    doAjaxSync("get_relation_type_groups", _data, success);
}

function generateAnnotationSets(){
    var relation_type_id = $("#relationTypesContainer").find('.hightlighted td:first').text();
    var _data = 	{
        relation_type_id: relation_type_id,
        mode: 'annotation_set'
    };

    var success = function(data){
        var table_html = "";
        $.each(data,function(index, value){
            table_html +=
                '<tr id = '+value.annotation_set_id+' >' +
                    '<td>'+value.name+'</td>'+
                    '<td>'+value.description+'</td>'+
                    '<td class = "text-center">'+
                        '<input '+ (value.source == 1 ? "checked" : "") +' type="checkbox" class="annotation_set_checkbox" name = "source" id = "annotation_set_source_'+value.annotation_set_id+'" value="">'+
                    '</td>'+
                    '<td class = "text-center">' +
                        '<input '+ (value.target == 1 ? "checked" : "") +' type="checkbox" class="annotation_set_checkbox" name = "target" id = "annotation_set_target_'+value.annotation_set_id+'" value="">'+
                    '</td>'+
                '</tr>';
        });

        $("#relation_group_annotation_set").html(table_html);
    };

    doAjaxSync("get_relation_type_groups", _data, success);
}


function get($element){
	var $container = $element.parents(".tableContainer:first");
	var containerName = $container.attr("id");
	var childId = "";
	if (containerName!="relationTypesContainer"){
		var _data = 	{ 
				parent_id : $element.children(":first").text()
			};
		if (containerName=="relationSetsContainer"){
			childId = "relationTypesContainer";
			_data.parent_type = 'relation_set';
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
                _data.parent_id = $("#relationSetsTable .hightlighted > td:first").text();
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

function createRelationSet($element){
    var parent = $element.parent().attr("parent");
    var $container = $element.parents(".tableContainer");

    $( "#create_relation_set_form" ).validate({
        rules: {
            create_relation_set_name: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'administration_validation',
                        type: 'relation_set_edit',
                        mode: 'create'
                    }
                }
            }
        },
        messages: {
            create_relation_set_name: {
                required: "Relation set must have a name.",
                remote: "This relation set already exists"
            }
        }
    });

    $( ".confirm_relation_set_create" ).unbind( "click" ).click(function() {

        if ($('#create_relation_set_form').valid()) {

            var accessType = $('#create_setAccess').val();

            if (accessType) {
                var visibility = 1;
            } else {
                var visibility = 0;
            }


            var _data = 	{
                //ajax : "relation_type_add",
                name_str : $("#create_relation_set_name").val(),
                desc_str : $("#create_relation_set_description").val(),
                setAccess_str: visibility
            };

            var success = function(data){
                $container.find("table > tbody").append(
                    '<tr>'+
                    '<td class = "column_id">'+data.last_id+'</td>'+
                    '<td>'+_data.name_str+'</td>'+
                    '<td>'+_data.desc_str+'</td>'+
                    '<td>' + data.user + '</td>' +
                    '<td>' + accessType + '</td>' +
                    '</tr>'
                );

                $('#create_relation_set_modal').modal('hide');
            };

            doAjaxSync("relation_set_add", _data, success);
        }
    });
}

function editRelationSet($element){
    var parent = $element.parent().attr("parent");
    var $container = $element.parents(".tableContainer");
    var visibility = $container.find('.hightlighted').attr("visibility");
    var visibilityStr = "private";
    if(visibility == 1){
        visibilityStr = "public";
    }

    $("#edit_relation_set_name").val($container.find('.hightlighted td:first').next().text());
    $("#edit_relation_set_description").val($container.find('.hightlighted td:eq(2)').text());
    $("#edit_setAccess").val(visibilityStr);

    $( "#edit_relation_set_form" ).validate({
        rules: {
            edit_relation_set_name: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'administration_validation',
                        type: 'relation_set_edit',
                        id: function(){
                            return $container.find('.hightlighted td:first').text();
                        },
                        mode: 'edit'
                    }
                }
            }
        },
        messages: {
            edit_relation_set_name: {
                required: "Relation set must have a name.",
                remote: "This relation set already exists"
            }
        }
    });


    $( ".confirm_relation_set_edit" ).unbind( "click" ).click(function() {

        if ($('#edit_relation_set_form').valid()) {
            var newVisibility = $("#edit_setAccess").val();

            var _data = 	{
                name_str : $("#edit_relation_set_name").val(),
                desc_str : $("#edit_relation_set_description").val(),
                set_access: newVisibility,
                element_id : $container.find('.hightlighted td:first').text()
            };

            var success = function(){
                $container.find(".hightlighted:first").html(
                    '<td class = "column_id">'+$container.find(".hightlighted td:first").text()+'</td>'+
                    '<td>'+_data.name_str+'</td>'+
                    '<td>'+_data.desc_str+'</td>' +
                    '<td>' + $container.find(".hightlighted td:nth-child(4)").text() + '</td>' +
                    '<td>' + $("#edit_setAccess").val() + '</td>'
                );

                $container.find(".hightlighted").attr('visibility', newVisibility === "public" ? 1 : 0);
                $('#edit_relation_set_modal').modal('hide');
            };

            doAjaxSync("relation_set_edit", _data, success);

        }
    });
}



function getRelationGroups($element){
    var $container = $element.parents(".tableContainer:first");
    var relation_type_id = $container.find('.hightlighted td:first').text();
    var _data = 	{
        relation_type_id: relation_type_id,
        mode: 'all'
    };

    var success = function(data){
        var tableRows = "";
        $.each(data,function(index, value){

            var annotation;
            var annotation_name;
            if(value.annotation_set_id === null){
                if(value.annotation_subset_id === null){
                    annotation = value.annotation_type_id;
                    annotation_name = value.type_name;
                } else{
                    annotation = value.annotation_subset_id;
                    annotation_name = value.subset_name;

                }
            } else{
                annotation = value.annotation_set_id;
                annotation_name = value.set_name;
            }

            tableRows+=
                '<tr>'+
                    '<td>'+value.part+'</td>'+
                    '<td>'+annotation_name+'</td>'
                '</tr>';
        });

        $("#relations_groups_content").html(tableRows);
    };

    doAjaxSync("get_relation_type_groups", _data, success);
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

        if(elementType === "relation_set"){
            var _data =
                {
                    element_id : $container.find('.hightlighted td:first').text()
                };

            var success = function(){
                $container.find(".hightlighted:first").remove();
                $("#relationSetsContainer .edit,#relationSetsContainer .delete").hide();
                $('#deleteModal').modal('hide');

            };

            doAjaxSync("relation_set_delete", _data, success);
        }
        else if(elementType === "relation_type"){
            var _data =
                {
                    element_type : elementType,
                    element_id : $container.find('.hightlighted td:first').text()
                };

            var success = function(){
                $container.find(".hightlighted:first").remove();
                $("#relationTypesContainer .create").show();
                $("#relationTypesContainer .edit,#relationTypesContainer .delete").hide();
                $('#deleteModal').modal('hide');

            };

            doAjaxSync("relation_type_delete", _data, success);
        }
	});
	
}