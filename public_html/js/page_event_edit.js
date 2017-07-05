/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
	$(".createEventGroup").click(function(){
		addEventGroup($(this));
	});
	
	$(".editEventGroup").click(function(){
		editEventGroup($(this));
	});

    $(".createEventTypeSlot").click(function(){
        addEventTypeSlot($(this));
    });

    $(".editEventTypeSlot").click(function(){
        editEventTypeSlot($(this));
    });

    $(".createEventType").click(function(){
        addEventType($(this));
    });

    $(".editEventType").click(function(){
        editEventType($(this));
    });

	$(".delete").click(function(){
		remove($(this));
	});

    $( "#event_form" ).validate({
        rules: {
            event_name: {
                required: true
            }
        },
        messages: {
            event_name: {
                required: "This field is required."
            }
        }
    });

	
	$(".tableContent").on("click", "tbody > tr" ,function(){
		$(this).siblings().removeClass("hightlighted");
		$(this).addClass("hightlighted");
		containerType = $(this).parents(".tableContainer:first").attr('id');
		if (containerType=="eventGroupsContainer"){
            $("#eventTypesContainer").show();
            $("#eventTypeSlotsContainer").hide();
			$("#eventGroupsContainer .edit,#eventGroupsContainer .delete").show();
			$("#eventTypesContainer .create").show();
			$("#eventTypesContainer .edit,#eventTypesContainer .delete").hide();
			$("#eventTypeSlotsContainer span").hide();
			$("#eventTypeSlotsContainer table > tbody").empty();
		}
		else if (containerType=="eventTypesContainer"){
			$("#eventTypesContainer .edit,#eventTypesContainer .delete").show();
			$("#eventTypeSlotsContainer .create").show();
            $("#eventTypeSlotsContainer").show();
			$("#eventTypeSlotsContainer .edit,#eventTypeSlotsContainer .delete").hide();
		}
		else {
			$("#eventTypeSlotsContainer .edit,#eventTypeSlotsContainer .delete").show();
		}
		get($(this));
	});
}); 


function get($element){
	var $container = $element.parents(".tableContainer:first");
	var containerName = $container.attr("id");
	var childId = "";
	if (containerName!="eventTypeSlotsContainer"){
		var _data = 	{ 
				parent_id : $element.children(":first").text()
			};
		if (containerName=="eventGroupsContainer"){
			childId = "eventTypesContainer";
			_data.parent_type = 'event_group';
		}
		else {
			childId = "eventTypeSlotsContainer";
			_data.parent_type = 'event_type';
		}
		
		var success = function(data){
			var tableRows = "";
			$.each(data,function(index, value){
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
		
		doAjaxSyncWithLogin("event_edit_get", _data, success, login);
	}
}

function addEventGroup($element){
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $element.parents(".tableContainer");

    $( "#create_event_form" ).validate({
        rules: {
            create_event_name: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'administration_validation',
                        type: 'event_group',
                        mode: 'create'
                    }
                }
            }
        },
        messages: {
            create_event_name: {
                required: "This field is required.",
				remote: "This event group already exists"
            }
        }
    });

    $( ".confirm_create_event" ).unbind( "click" ).click(function() {

        if ($('#create_event_form').valid()) {
            var _data = 	{
                ajax : "event_edit_add",
                name_str : $("#create_event_name").val(),
                desc_str : $("#create_event_description").val(),
                element_type : elementType
            };

            var success = function(data){
                $container.find("table > tbody").append(
                    '<tr>'+
                    '<td class = "column_id">'+data.last_id+'</td>'+
                    '<td>'+_data.name_str+'</td>'+
                    '<td>'+_data.desc_str+'</td>'+
                    '</tr>'
                );
                $('#create_event_modal').modal('hide');
            };

            doAjaxSync("event_edit_add", _data, success);
        }
    });
}

function editEventGroup($element){
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $element.parents(".tableContainer");

    $( "#edit_event_form" ).validate({
        rules: {
            edit_event_name: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'administration_validation',
                        type: 'event_group',
                        id: function(){
                            return $container.find('.hightlighted td:first').text();
                        },
                        mode: 'edit'
                    }
                }
            }
        },
        messages: {
            edit_event_name: {
                required: "This field is required.",
                remote: "This event group already exists"
            }
        }
    });

    $('#edit_event_name').val($container.find('.hightlighted td:first').next().text());
    $('#edit_event_description').val($container.find('.hightlighted td:first').next().next().text());

    $( ".confirm_edit_event" ).unbind( "click" ).click(function() {

        if ($('#edit_event_form').valid()) {
            var _data = {
                name_str: $("#edit_event_name").val(),
                desc_str: $("#edit_event_description").val(),
                element_type: elementType,
                element_id: $container.find('.hightlighted td:first').text()
            };

            var success = function (data) {
                $container.find(".hightlighted:first").html(
                    '<td class = "column_id">' + $container.find(".hightlighted td:first").text() + '</td>' +
                    '<td>' + _data.name_str + '</td>' +
                    '<td>' + _data.desc_str + '</td>'
                );
                $('#edit_event_modal').modal('hide');
            };

            doAjaxSync("event_edit_update", _data, success);
        }
    });

}

function addEventType($element){
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $element.parents(".tableContainer");

    $( "#create_event_type_form" ).validate({
        rules: {
            create_event_type_name: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'administration_validation',
                        type: 'event_type',
						event_group: function(){
        					return $("#eventGroupsTable .hightlighted > td:first").text()
    					},
                        mode: 'create'
                    }
                }
            }
        },
        messages: {
            create_event_type_name: {
                required: "This field is required.",
                remote: "This event type already exists"
            }
        }
    });


    $( ".confirm_create_event_type" ).unbind( "click" ).click(function() {

        if ($('#create_event_type_form').valid()) {
            var _data = 	{
                ajax : "event_edit_add",
                name_str : $("#create_event_type_name").val(),
                desc_str : $("#create_event_type_description").val(),
                element_type : elementType,
				parent_id: $("#eventGroupsTable .hightlighted > td:first").text()
            };

            var success = function(data){
                $container.find("table > tbody").append(
                    '<tr>'+
                    '<td class = "column_id">'+data.last_id+'</td>'+
                    '<td>'+_data.name_str+'</td>'+
                    '<td>'+_data.desc_str+'</td>'+
                    '</tr>'
                );
                $('#create_event_type_modal').modal('hide');
            };

            doAjaxSync("event_edit_add", _data, success);
        }
    });
}


function editEventType($element){
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $element.parents(".tableContainer");

    $( "#edit_event_type_form" ).validate({
        rules: {
            edit_event_type_name: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'administration_validation',
                        type: 'event_type',
                        event_group: function(){
                            return $("#eventGroupsTable .hightlighted > td:first").text()
                        },
                        id: function(){
                            return $container.find('.hightlighted td:first').text();
                        },
                        mode: 'edit'
                    }
                }
            }
        },
        messages: {
            edit_event_type_name: {
                required: "This field is required.",
                remote: "This event type already exists"
            }
        }
    });


    $('#edit_event_type_name').val($container.find('.hightlighted td:first').next().text());
    $('#edit_event_type_description').val($container.find('.hightlighted td:first').next().next().text());

    $( ".confirm_event" ).unbind( "click" ).click(function() {
        var _data = 	{
            name_str : $("#edit_event_type_name").val(),
            desc_str : $("#edit_event_type_description").val(),
            element_type : elementType,

            element_id : $container.find('.hightlighted td:first').text()
        };

        var success = function(data){
            $container.find(".hightlighted:first").html(
                '<td class = "column_id">'+$container.find(".hightlighted td:first").text()+'</td>'+
                '<td>'+_data.name_str+'</td>'+
                '<td>'+_data.desc_str+'</td>'
            );
            $('#edit_event_type_modal').modal('hide');
        };

        doAjaxSync("event_edit_update", _data, success);
    });

}

function addEventTypeSlot($element){
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $element.parents(".tableContainer");

    console.log("Here");

    $( "#create_event_type_slot_form" ).validate({
        rules: {
            create_event_type_slot_name: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'administration_validation',
                        type: 'event_type_slot',
                        event_type: function(){
                            return $("#eventTypesTable .hightlighted > td:first").text()
                        },
                        mode: 'create'
                    }
                }
            }
        },
        messages: {
            create_event_type_slot_name: {
                required: "This field is required.",
                remote: "This event type slot already exists"
            }
        }
    });

    $( ".confirm_create_event_type_slot" ).unbind( "click" ).click(function() {

        if ($('#create_event_type_slot_form').valid()) {
            var _data = 	{
                ajax : "event_edit_add",
                name_str : $("#create_event_type_slot_name").val(),
                desc_str : $("#create_event_type_slot_description").val(),
                parent_id: $("#eventTypesTable .hightlighted > td:first").text(),
                element_type : elementType
            };

            var success = function(data){
                $container.find("table > tbody").append(
                    '<tr>'+
                    '<td class = "column_id">'+data.last_id+'</td>'+
                    '<td>'+_data.name_str+'</td>'+
                    '<td>'+_data.desc_str+'</td>'+
                    '</tr>'
                );
                $('#create_event_type_slot_modal').modal('hide');
            };

            doAjaxSync("event_edit_add", _data, success);
        }
    });
}

function editEventTypeSlot($element){
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $element.parents(".tableContainer");

    console.log("Edit")

    $( "#edit_event_type_slot_form" ).validate({
        rules: {
            edit_event_type_slot_name: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'administration_validation',
                        type: 'event_type_slot',
                        event_type: function(){
                            return $("#eventTypesTable .hightlighted > td:first").text()
                        },
                        id: function(){
                            return $container.find('.hightlighted td:first').text();
                        },
                        mode: 'edit'
                    }
                }
            }
        },
        messages: {
            edit_event_type_slot_name: {
                required: "This field is required.",
                remote: "This event type slot already exists"
            }
        }
    });

    $('#edit_event_type_slot_name').val($container.find('.hightlighted td:first').next().text());
    $('#edit_event_type_slot_description').val($container.find('.hightlighted td:first').next().next().text());

    $( ".confirm_edit_event_type_slot" ).unbind( "click" ).click(function() {
        if ($('#edit_event_type_slot_form').valid()) {
            var _data = 	{
                name_str : $("#edit_event_type_slot_name").val(),
                desc_str : $("#edit_event_type_slot_description").val(),
                element_type : elementType,
                element_id : $container.find('.hightlighted td:first').text()
            };

            var success = function(data){
                $container.find(".hightlighted:first").html(
                    '<td class = "column_id">'+$container.find(".hightlighted td:first").text()+'</td>'+
                    '<td>'+_data.name_str+'</td>'+
                    '<td>'+_data.desc_str+'</td>'
                );
                $('#edit_event_type_slot_modal').modal('hide');
            };

            doAjaxSync("event_edit_update", _data, success);
        }
    });

}



function add($element){
	var elementType = $element.parent().attr("element");
	var parent = $element.parent().attr("parent");
	var $container = $element.parents(".tableContainer");

    if (elementType=='event_group'){
		$("#event_header").text("Create event group");
    }
    else if (elementType=='event_type_slot'){
        $("#event_header").text("Create event type slot");
    } else{
        $("#event_header").text("Create relation type");
    }

    $('#events_modal').modal('show');

    $( ".confirm_event" ).unbind( "click" ).click(function() {

            if ($('#event_form').valid()) {
				var _data = 	{
						ajax : "event_edit_add",
						name_str : $("#event_name").val(),
						desc_str : $("#event_description").val(),
						element_type : elementType
					};
				if (elementType=='event_type'){
					_data.parent_id = $("#eventGroupsTable .hightlighted > td:first").text();
				}
				else if (elementType=='event_type_slot'){
					_data.parent_id = $("#eventTypesTable .hightlighted > td:first").text();
				}

				var success = function(data){
					$container.find("table > tbody").append(
							'<tr>'+
								'<td class = "column_id">'+data.last_id+'</td>'+
								'<td>'+_data.name_str+'</td>'+
								'<td>'+_data.desc_str+'</td>'+
							'</tr>'
						);
					$('#events_modal').modal('hide');
				};

				doAjaxSync("event_edit_add", _data, success);
			}
	});
}

function edit($element){	
	var elementType = $element.parent().attr("element");
	var parent = $element.parent().attr("parent");
	var $container = $element.parents(".tableContainer");

	console.log($container);

    if (elementType=='event_group'){
        $("#event_header").text("Edit event group");
    }
    else if (elementType=='event_type_slot'){
        $("#event_header").text("Edit event type slot");
    } else{
        $("#event_header").text("Edit relation type");
    }

	$('#event_name').val($container.find('.hightlighted td:first').next().text());
    $('#event_description').val($container.find('.hightlighted td:first').next().next().text());
    $('#events_modal').modal('show');

    $( ".confirm_event" ).unbind( "click" ).click(function() {
			var _data = 	{
					name_str : $("#event_name").val(),
					desc_str : $("#event_description").val(),
					element_type : elementType,

					element_id : $container.find('.hightlighted td:first').text()
				};

			var success = function(data){
				$container.find(".hightlighted:first").html(
						'<td class = "column_id">'+$container.find(".hightlighted td:first").text()+'</td>'+
						'<td>'+_data.name_str+'</td>'+
						'<td>'+_data.desc_str+'</td>'
				);
                $('#events_modal').modal('hide');
            };

			doAjaxSync("event_edit_update", _data, success);
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
		var _data = 	{
				element_type : elementType,
				element_id : $container.find('.hightlighted td:first').text()
			};

		var success = function(data){
			$container.find(".hightlighted:first").remove();
			if (elementType=="event_group"){
				$("#eventGroupsContainer .edit,#eventGroupsContainer .delete").hide();
				$("#eventTypesContainer span").hide();
				$("#eventTypeSlotsContainer span").hide();
				$("#eventTypesContainer table > tbody").empty();
				$("#eventTypeSlotsContainer table > tbody").empty();
			}
			else if (elementType=="event_type"){
				$("#eventTypesContainer .create").show();
				$("#eventTypesContainer .edit,#eventTypesContainer .delete").hide();
				$("#eventTypeSlotsContainer span").hide();
				$("#eventTypeSlotsContainer table > tbody").empty();
			}
			else {
				$("#eventTypeSlotsContainer .edit,#eventTypeSlotsContainer .delete").hide();
			}

            $('#deleteModal').modal('hide');

        };
		doAjaxSync("event_edit_delete", _data, success);
	});
	
}