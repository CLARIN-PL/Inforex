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
	
	$("#sharedAttributesTable").on("click", "tbody > tr", function(){
		$(this).siblings().removeClass("hightlighted");
		$(this).addClass("hightlighted");
		$("#create_shared_attribute, #delete_shared_attribute").show();
		if ($(this).find("td").eq(2).text() == "enum"){
			$("#create_shared_attribute_enum").show();
			$("#delete_shared_attribute_enum").hide();
			$("#move_detach").hide();	
			$("#move_attach").hide();
			get_shared_attributes_enum();
			get_annotation_types();
		}
		else { 
			$("#sharedAttributesEnumTable > tbody").empty();
			$("#create_shared_attribute_enum").hide();
			$("#delete_shared_attribute_enum").hide();
			$("#annotationTypesDetachedTable > tbody").empty();
			$("#annotationTypesAttachedTable > tbody").empty();
			$("#move_detach").hide();	
			$("#move_attach").hide();
			get_annotation_types();
		}
	});
	
	$("#create_shared_attribute_enum").click(function(){
		add_shared_attribute_enum();
        $("#create_shared_attribute_enum_modal").modal('show');

    });
	
	
	$("#sharedAttributesEnumTable").on("click", "tbody > tr", function(){
		$(this).siblings().removeClass("hightlighted");
		$(this).addClass("hightlighted");
		$("#create_shared_attribute_enum,#delete_shared_attribute_enum").show();		
	});	
	
	$("#delete_shared_attribute_enum").click(function(){
		delete_shared_attribute_enum();
	});	
	
	$("#annotationTypesAttachedTable").on("click", "tbody > tr", function(){
		$(this).siblings().removeClass("hightlighted");
		$(this).addClass("hightlighted");
		$("#move_detach").show();		
	});	
	
	$("#annotationTypesDetachedTable").on("click", "tbody > tr", function(){
		$(this).siblings().removeClass("hightlighted");
		$(this).addClass("hightlighted");
		$("#move_attach").show();		
	});		
	
	$("#move_attach").click(function(){
		add_annotation_type();
		$("#move_attach").hide();
	});	
	
	$("#move_detach").click(function(){
		delete_annotation_type();
		$("#move_detach").hide();
	});	
	
}); 


function get_shared_attributes_enum(){
	var _data = { 
		shared_attribute_id : $("#sharedAttributesTable .hightlighted td:first").text()
	};

	var success = function(data){
		var tableRows = "";
		$.each(data,function(index, value){
			tableRows+=
			'<tr>'+
				'<td>'+value.value+'</td>'+
				'<td>'+value.description+'</td>'+
			'</tr>';
		});
		$("#sharedAttributesEnumTable > tbody").html(tableRows);
	};
	
	var login = function(){
		get_shared_attributes_enum();
	};
	
	doAjaxSyncWithLogin("shared_attribute_enum_get", _data, success, login);
}

function get_annotation_types(){
	var _data = { 
		shared_attribute_id : $("#sharedAttributesTable .hightlighted td:first").text()
	};

	var success = function(data){
		var tableRowsAttached = "";
		var tableRowsDetached = "";
		$.each(data,function(index, value){
			if (value.shared_attribute_id)
				tableRowsAttached += 
				'<tr>' +
					'<td>' + value.annotation_type_id + '</td>' +
					'<td>' + value.name + '</td>' +
				'</tr>';
			else
				tableRowsDetached += 
					'<tr>' +
						'<td>' + value.annotation_type_id + '</td>' +
						'<td>' + value.name + '</td>' +
					'</tr>';
				
		});
		$("#annotationTypesAttachedTable > tbody").html(tableRowsAttached);
		$("#annotationTypesDetachedTable > tbody").html(tableRowsDetached);
	};
	
	var login = function(){
		get_annotation_types();
	};
	
	doAjaxSyncWithLogin("shared_attribute_annotation_types_get", _data, success, login);
}

function add_annotation_type(){
	var _data = 	{ 
			shared_attribute_id : $("#sharedAttributesTable .hightlighted td:first").text(),
			annotation_type_id : $("#annotationTypesDetachedTable .hightlighted td:first").text(),
			name : $("#annotationTypesDetachedTable .hightlighted td:last").text()
		};
	var success = function(data){
		$("#annotationTypesAttachedTable > tbody").append(
				'<tr>'+
					'<td>'+_data.annotation_type_id+'</td>'+
					'<td>'+_data.name+'</td>'+
				'</tr>'
		);	
		$("#annotationTypesDetachedTable .hightlighted").remove();
	};
	
	var login = function(){
		add_annotation_type();
	};
	
	doAjaxSyncWithLogin("annotation_type_shared_attribute_add", _data, success, login);	
}

function delete_annotation_type(){
	var _data = 	{ 
			shared_attribute_id : $("#sharedAttributesTable .hightlighted td:first").text(),
			annotation_type_id : $("#annotationTypesAttachedTable .hightlighted td:first").text(),
			name : $("#annotationTypesAttachedTable .hightlighted td:last").text()
		};
	var success = function(data){
		$("#annotationTypesDetachedTable > tbody").append(
				'<tr>'+
					'<td>'+_data.annotation_type_id+'</td>'+
					'<td>'+_data.name+'</td>'+
				'</tr>'
		);	
		$("#annotationTypesAttachedTable .hightlighted").remove();
	};
	
	var login = function(){
		delete_annotation_type();
	};
	
	doAjaxSyncWithLogin("annotation_type_shared_attribute_delete", _data, success, login);	
}

function add_shared_attribute(){

    $( "#create_shared_attribute_form" ).validate({
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

    $( ".confirm_create_shared_attribute" ).unbind( "click" ).click(function() {
        if($('#create_shared_attribute_form').valid()) {

            var _data = {
                name_str : $("#create_shared_attribute_name").val(),
                type_str : $("#create_shared_attribute_type").val(),
                desc_str : $("#create_shared_attribute_description").val(),
            }

            var success = function(data){
                $("#sharedAttributesContainer").find("table > tbody").append(
                    '<tr>'+
                    '<td>'+data.last_id+'</td>'+
                    '<td>'+_data.name_str+'</td>'+
                    '<td>'+_data.type_str+'</td>'+
                    '<td>'+_data.desc_str+'</td>'+
                    '</tr>'
                );
            };

            var complete = function(){
                $('#create_shared_attribute_modal').modal('hide');
            };


            doAjaxSync("shared_attribute_add", _data, success, null, complete);
        }
    });
}


function delete_shared_attribute(){	
	var $container = $("#sharedAttributesTable");
    var deleteContent =
        '<label for = "delete_name">Name</label>'+
        '<p id = "delete_name">'+$container.find('.hightlighted td:first').next().text()+'</p>'+
        '<label for = "delete_desc">Description</label>'+
        '<p id = "delete_desc">'+$container.find('.hightlighted td:last').text()+'</p>';

    $('#deleteContent').html(deleteContent);
    $('#deleteModal').modal('show');

    $( ".confirmDelete" ).unbind( "click" ).click(function() {
        var _data = 	{
                shared_attribute_id : $container.find('.hightlighted td:first').text()
            };

        var success = function(data){
            $container.find(".hightlighted:first").remove();
            $("#delete_shared_attribute").hide();
            $("#sharedAttributesEnumTable > tbody").empty();
            $("#create_shared_attribute_enum").hide();
            $("#delete_shared_attribute_enum").hide();
            $("#annotationTypesAttachedTable > tbody").empty();
            $("#annotationTypesDetachedTable > tbody").empty();
        };

        var complete = function(){
            $('#deleteModal').modal('hide');
        };

        var login = function(){
            delete_shared_attribute();
        };

        doAjaxSync("shared_attribute_delete", _data, success, null, complete, null, login);
    });


	
}

function add_shared_attribute_enum(){
    $( "#create_shared_attribute_enum_form" ).validate({
        rules: {
            create_shared_attribute_enum_value: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'administration_validation',
                        type: 'shared_attribute_enum',
                        id: $("#sharedAttributesTable .hightlighted td:first").text(),
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

    $( ".confirm_create_shared_attribute_enum" ).unbind( "click" ).click(function() {
        if($('#create_shared_attribute_enum_form').valid()) {
            var _data = 	{
                    shared_attribute_id : $("#sharedAttributesTable .hightlighted td:first").text(),
                    value_str : $("#create_shared_attribute_enum_value").val(),
                    desc_str : $("#create_shared_attribute_enum_description").val()
                };
            var success = function(data){
                $("#sharedAttributesEnumTable > tbody").append(
                        '<tr>'+
                            '<td>'+_data.value_str+'</td>'+
                            '<td>'+_data.desc_str+'</td>'+
                        '</tr>'
                    );
            };

            var complete = function(){
                $('#create_shared_attribute_enum_modal').modal('hide');
            };

            var login = function(){
                add_shared_attribute_enum();
            };

            doAjaxSync("shared_attribute_enum_add", _data, success, null, complete, null, login);
        }

    });
}

function delete_shared_attribute_enum(){	
	var $container = $("#sharedAttributesEnumTable");
    var deleteContent =
        '<label for = "delete_name">Value</label>'+
        '<p id = "delete_name">'+$container.find('.hightlighted td:first').next().text()+'</p>'+
        '<label for = "delete_desc">Description</label>'+
        '<p id = "delete_desc">'+$container.find('.hightlighted td:last').text()+'</p>';

    $('#deleteContent').html(deleteContent);
    $('#deleteModal').modal('show');

    $( ".confirmDelete" ).unbind( "click" ).click(function() {
            var _data = 	{
                    shared_attribute_id : $("#sharedAttributesTable .hightlighted td:first").text(),
                    value_str : $container.find('.hightlighted td:first').text()
                };

            var success = function(data){
                $container.find(".hightlighted:first").remove();
                $("#delete_shared_attribute_enum").hide();
            };

            var complete = function(){
                $('#deleteModal').modal('hide');
            };

            var login = function(){
                delete_shared_attribute();
            };

            doAjaxSync("shared_attribute_enum_delete", _data, success, null, complete, null, login);
    });
	
}
