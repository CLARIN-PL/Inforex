/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */
var url = $.url(window.location.href);
var corpus_id = url.param('corpus');
var max_metadata_enum_values = 20;


$(function(){

    $(".metadata_type").change(function(){
        if($(this).attr('id') === "create_metadata_type"){
            if($("#create_metadata_type").val() === "enum"){
                $(".enum_values_edition").show();
            } else{
                $(".enum_values_edition").hide();
            }
        } else{
            if($("#edit_metadata_type").val() === "enum"){
                $(".edit_enum_values_edition").show();
            } else{
                $(".edit_enum_values_edition").hide();
            }
        }
    });

    $(".add_enum").click(function(){
        if($(this).val() === "add"){
            if($('.enum_input').length <= max_metadata_enum_values){
                var input = "<input class = 'form-control enum_input'>";
                $("#enum_values").append(input);
            }
        } else{
            if($('.edit_enum_input').length <= max_metadata_enum_values){
                var input = "<input class = 'form-control edit_enum_input'>";
                $("#edit_enum_values").append(input);
            }
        }
    });

    $(".remove_enum").click(function(){
        if($(this).val() === "add") {
            if ($('.enum_input').length > 1) {
                $("#enum_values").children().last().remove();
            }
        } else{
            if ($('.edit_enum_input').length > 1) {
                $("#edit_enum_values").children().last().remove();
            }
        }
    });

    $(".edit_metadata").click(function(){
        edit_metadata($(this));
    });



    $('.search_users').submit(false);

    $(".search_users").keyup(function () {
        var text = this.value.toLowerCase();
        if(text.length >= 3){
            console.log("Search now");
            var data = {
                'match_text': text,
                'corpus_id': corpus_id
            };

            var success = function(users){
                var rows = "";
                $.each(users, function (index, value) {
                    var button = "<button id = '"+value.user_id+"' class = 'add_user_button btn btn-primary'><i class='fa fa-arrow-left' aria-hidden='true'></i></button>";
                    rows += "<tr>" +
                        "<td>"+value.screename+"</td>" +
                        "<td>"+value.login+"</td>" +
                        "<td>"+value.email+"</td>" +
                        "<td class = 'text-center'>"+button+"</td>" +
                        "</tr>";
                } );

                $("#add_user_to_corpus_table").html(rows);
            };

            doAjaxSync("user_corpus_assign", data, success);
        } else{
            $("#add_user_to_corpus_table").html("");
        }
    });


    $("input[type=checkbox]:not(.annotationSet, .userReportPerspective, .relation_set_checkbox, .create_metadata_null, .edit_metadata_null)").click(function(){
		set($(this));
	});

    $("#corpus_set_corpus_perspective_roles").on("click", ".userReportPerspective", function(){
        set($(this));
    })

	$("input[type=checkbox]:not(.create_metadata_null):checked").parent().addClass("selected");

	$("#reportPerspectives").click(function(e){
		e.preventDefault();
		getReportPerspectives();
	});

	$("#corpusPerspectives").on('click', '.setReportPerspective', function(){
		setReportPerspective($(this));
	});

    $("#corpusPerspectives").on('change', '.updateReportPerspective', function(){
		updateReportPerspective($(this));
	});

    $(".delete_metadata").click(function(){
        deleteMetadata($(this));
    });

	$(".tablesorter, .table").on("click", "tbody > tr" ,function(){
		$(this).siblings().removeClass("hightlighted");
		$(this).addClass("hightlighted");
		$(".tableOptions .edit").show();
		$(".tableOptions .ext_edit").show();
        $(".tableOptions .edit_metadata").show();
        $(".tableOptions .delete_metadata").show();
        $(".tableOptions .delete").show();
		$(".tableOptions").show();
	});

	$(".create").click(function(){
		//add($(this));
	});

	$(".ext_edit").click(function(){
		ext_edit($(this));
	});

    $(".editBasicInfo").click(function(){
        var tr = $(this).parents("tr")
        tr.siblings().removeClass("hightlighted");
        tr.addClass("hightlighted");
    });

    $("#add_user_to_corpus_table").on('click', '.add_user_button', function(){
        add_user($(this));
    });

    $("#corpus_update").on('click', '.remove_user_button', function(){
        remove_user($(this));
    });

    $("#corpusElementsContainer").on("click", ".editBasicInfoName",function(){
        editBasicInfoName($(this));
    });

    $(".editBasicInfoOwner").click(function(){
        editBasicInfoOwner($(this));
    });

    $(".editBasicInfoAccess").click(function(){
        editBasicInfoAccess($(this));
    });

    $(".editBasicInfoDescription").click(function(){
        editBasicInfoDescription($(this));
    });

    $(".subcorporaEdit").click(function(){
        editSubcorpora($(this));
    });

    $(".subcorporaCreate").click(function(){
        createSubcorpora($(this));
    });

    $(".createFlag").click(function(){
        createFlag($(this));
    });

    $(".editFlag").click(function(){
        editFlag($(this));
    });

	$("#page_content").on("click", ".edit", function(){
		if ($(this).parent().attr("element") == "corpus_details"){
			var tr = $(this).parents("tr")
			tr.siblings().removeClass("hightlighted");
			tr.addClass("hightlighted");
		}
		//edit($(this));
	});

	$(".delete").click(function(){
		remove($(this));
	});

	$(".delete_corpora_button").click(function(){
		delete_corpus();
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


function deleteMetadata(element){

    var row = $("#page").find(".hightlighted");
    var field_name = $("#page").find('.hightlighted td:first').text();
    var field_type = $(row).find("td:eq(1)").text();

    var delete_html = '<table>'+
        '<label for="delete_name">Field:</label>'+
        '<p id = "delete_name">'+field_name+'</p>'+
        '<label for="delete_type">Type:</label>'+
        '<p id = "delete_type">'+field_type+'</p>';

    $('#deleteContent').html(delete_html);
    $('#deleteModal').modal('show');

    $( ".confirmDelete" ).unbind( "click" ).click(function() {

        var _data = {
            url: $.url(window.location.href).attr('query'),
            action: "delete",
            field: field_name
        };

        var success = function () {
            $(row).remove();
            $(".delete_metadata").hide();
        };

        var complete = function(){
            $('#deleteModal').modal('hide');
        };

        doAjaxSync("corpus_edit_ext", _data, success, null, complete);
    });

}

function edit_metadata(){
    $(".edit_metadata_error").hide();
    var page = $("#page");
    var field = $(page).find('.hightlighted td:first').text();
    var type = $(page).find('.hightlighted td:eq(1)').text();
    var is_null = $(page).find('.hightlighted td:eq(2)').text() === "Yes";

    $("#edit_metadata_field").val(field);
    $("#edit_metadata_type").val(type);
    $("#edit_metadata_null").prop("checked", is_null);

    if(type === "enum"){
        var select_options = $(page).find('.hightlighted td:last').find("select").children();
        var enum_values = [];
        var inputs = "";
        $.each(select_options, function(index, value){
            var enum_value = $(value).val();

            if(enum_value != "-values-"){
                inputs += '<input class = "form-control edit_enum_input" value = "'+enum_value+'">';
                enum_values.push(enum_value);
            }
        });
        $("#edit_enum_values").html(inputs);
        $(".edit_enum_values_edition").show();
    } else{
        $(".edit_enum_values_edition").hide();
        $("#edit_enum_values").html("<input class = 'form-control edit_enum_input'>");

    }

    $('#edit_metadata_modal').modal('show');

    $( ".confirm_edit_metadata" ).unbind( "click" ).click(function() {
            /*
             Gets enumeration values and converts them to a format ready for database insert
             */
            var display_enum_values = [];
            if($("#edit_metadata_type").val() === "enum"){
                var enum_values = '';
                $("#edit_enum_values").children().each(function(){
                    var edit_value = $(this).val();
                    if(edit_value !== ""){
                        enum_values += '"' + edit_value +'",';
                        display_enum_values.push(edit_value);
                    }
                });
                enum_values = enum_values.replace(/,\s*$/, '');
            }

            //If enum type is selected, at least one enum value needs to be specified.
            if(!($("#edit_metadata_type").val() === "enum" && display_enum_values.length === 0)) {
                var _data = {
                    url: $.url(window.location.href).attr('query'),
                    action: "edit",
                    enum_values: enum_values,
                    field: $("#edit_metadata_field").val(),
                    old_field: field,
                    type: $("#edit_metadata_type").val(),
                    is_null: $(".edit_metadata_null").is(':checked')
                };

                var success = function () {
                    var row = $(page).find('.hightlighted');

                    var tableRows = "";
                    tableRows +=
                        '<td>' + _data.field + '</td>' +
                        '<td>' + _data.type + '</td>' +
                        '<td>' + (_data.is_null ? "Yes" : "No") + '</td>';

                    if ($("#edit_metadata_type").val() === "enum") {
                        tableRows += '<td class = "text-center">' +
                            '<select class = "form-control">' +
                            '<option>-values-</option>';
                        $.each(display_enum_values, function (ind, val) {
                            tableRows += '<option>' + val + '</option>'
                        });
                        tableRows += '</select></td>';
                    } else {
                        tableRows += "<td class = 'text-center'>-</td>"
                    }
                    $(row).html(tableRows);

                    $('#edit_metadata_modal').modal('hide');
                };


                var complete = function (data) {
                    $('#create_metadata_modal').modal('hide');
                };

                doAjaxSync("corpus_edit_ext", _data, success, null, complete);
            } else{
                $(".edit_metadata_error").show();
            }

    });

}

function refresh_corpus_users(){
    console.log("Refreshing");
    var data = {
        'mode': 'get',
        'corpus_id': corpus_id
    }

    var success = function(users){
        console.log(users);
        var rows = "";
        $.each(users, function (index, value) {
            if(value.role != null){
                rows += "<tr>" +
                    "<td>"+value.screename+"</td>" +
                    "<td>"+value.login+"</td>" +
                    "<td>"+value.email+"</td>" +
                    "<td>"+value.last_activity+"</td>" +
                    "<td style='text-align: center'><button id = '"+value.user_id+"' class = 'remove_user_button btn btn-primary'><i class='fa fa-arrow-right' aria-hidden='true'></i></button></td>"+
                    "</tr>";
            }
        } );

        $("#users_assigned_table").html(rows);
    };

    doAjaxSync("user_corpus_assign", data, success);
}

function add_user(element){

    var data = {
        'element_type': 'users',
        'operation_type': 'add',
        'value' : $(element).attr('id'),
        'corpus_id': corpus_id
    }

    var success = function(){
        $(element).closest('tr').hide();
        refresh_corpus_users();
    };

    doAjaxSync("corpus_update", data, success);
}

function remove_user(element){
    if (confirm("Do you really want to remove this user?")) {
        var data = {
            'element_type': 'users',
            'operation_type': 'remove',
            'value': $(element).attr('id'),
            'corpus_id': corpus_id
        }

        var success = function () {
            $(element).closest('tr').hide();
        };

        doAjaxSync("corpus_update", data, success);
    }
}


function set($element){
	var attrs = $element[0].attributes;

	var _data = {
			url: $.url(window.location.href).attr("query"),
			operation_type : ($element.is(':checked') ? "add" : "remove")
	}

	for(var i=0;i<attrs.length;i++) {
		_data[attrs[i].nodeName] = attrs[i].nodeValue;
	}

	var ajax = $element.parents(".tablesorter").attr("id");

	var success = function(data){
		$element.parent().css('background',($element.is(':checked') ? '#9DD943' : '#FFFFFF'));
		$(".tablesorter").trigger("update");
	};

	var login = function(){
		set($element);
	};
	doAjaxSyncWithLogin(ajax, _data, success, login);
}


function getReportPerspectives(){

	var success = function(data){
		var modalHtml =
				'<table class="tablesorter table table-striped" cellspacing="1" style = "overflow: scroll;">'+
					'<thead>'+
						'<tr>'+
							'<th>active</th>'+
							'<th>title</th>'+
							'<th>description</th>'+
							'<th>access</th>'+
						'</tr>'+
					'</thead>'+
					'<tbody>';
		$.each(data,function(index,value){
			modalHtml +=
				'<tr'+(value.cid ? '' : ' class="inactive"')+'>'+
					'<td>'+'<input class="setReportPerspective" perspectivetitle="'+value.title+'" type="checkbox" perspectiveid="'+value.id+'" '+(value.cid ? 'checked="checked"' : '')+'/></td>'+
					'<td>'+value.title+'</td>'+
					'<td>'+value.description+'</td>'+
					'<td>'+
						'<select id = "select_'+value.id+'" '+(value.cid ? '' : ' disabled')+' perspectiveid="'+value.id+'" class="updateReportPerspective">'+
							'<option perspectiveid="'+value.id+'" value="loggedin" '+((value.access && value.access=="loggedin") ? 'selected="selected"' : '' )+'>loggedin</option>'+
							'<option perspectiveid="'+value.id+'" value="role" '+((value.access && value.access=="role") ? 'selected="selected"' : '' )+'>role</option>'+
							'<option perspectiveid="'+value.id+'" value="public" '+((value.access && value.access=="public") ? 'selected="selected"' : '' )+'>public</option>'+
						'</select>'+
					'</td>'+
				'</tr>';
		});
		modalHtml += '</tbody></table>';
        $("#corpusPerspectivesContent").html(modalHtml);
	};

	var login = function(data){
		getReportPerspectives();
	};

	var url = $.url(window.location.href);
	var corpus_id = url.param("corpus");
	doAjaxSyncWithLogin("corpus_get_report_perspectives", {url: "corpus="+corpus_id}, success, login);
}


function setReportPerspective($element){
    var perspective_id =$element.attr('perspectiveid');
    
	var _data = {
			url: $.url(window.location.href).attr('query'),
			perspective_id : perspective_id,
			access : $('option[perspectiveid="'+$element.attr('perspectiveid')+'"]:selected').val(),
			operation_type : ($element.prop('checked') ? "add" : "remove")
		};

	var success = function(){
	    var action = $element.prop('checked') ? "add" : "remove";

		$element.parent().parent().toggleClass("inactive");
		if(action == "add"){
            $("#select_"+perspective_id).removeAttr("disabled");

        } else{
            $("#select_"+perspective_id).attr("disabled", true);
        }
		updatePerspectiveTable($element,($element.prop('checked') ? "add" : "remove"));
	};

	var login = function(){
		setReportPerspective($element);
	};



	doAjaxSyncWithLogin("corpus_set_corpus_and_report_perspectives", _data, success, login);
}

function updateReportPerspective($element){
	if ($element.is("select")){
		var params = {
			url: $.url(window.location.href).attr('query'),
			perspective_id : $element.attr('perspectiveid'),
			access : $('option[perspectiveid="'+$element.attr('perspectiveid')+'"]:selected').val(),
			operation_type : "update"
		};

		var success = function(data){
			updatePerspectiveTable($element,"update");
		};

		var login = function(){
			updateReportPerspective($element);
		};


		doAjaxSyncWithLogin("corpus_set_corpus_and_report_perspectives", params, success, login);
	}
}

function updatePerspectiveTable($element,operation_type){
	var perspective_id = $element.attr('perspectiveid');

	if(operation_type == "remove"){
		$("#corpus_set_corpus_perspective_roles td[perspective_id="+perspective_id+"]").remove();
        $("#corpus_set_corpus_perspective_roles th[perspective_id="+perspective_id+"]").remove();

    }
	else if(operation_type == "add"){
		var access = $('option[perspectiveid="'+$element.attr('perspectiveid')+'"]:selected').val();
		var title = $element.attr('perspectivetitle');
		$("#corpus_set_corpus_perspective_roles thead tr").append("<th perspective_id='"+perspective_id+"' style='text-align: center'>"+title+"</th>");
		$("#corpus_set_corpus_perspective_roles tbody tr").each(function(){
			var html="";
            var user_id = $(this).attr('id');
			if( access == "role"){
				html += "<td perspective_id='"+perspective_id+"' style='text-align: center;'>";
				html += "<input class='userReportPerspective' type='checkbox' user_id="+user_id;
				html += " perspective_id='"+perspective_id+"' value='1' />";
				html += "</td>";
			}
			else{
				html += "<td perspective_id='"+perspective_id+"' style='text-align: center;'>";
				html += "<i>"+access+"</i>";
			}
				html += "</td>";
			$(this).append(html);
		});
	}
	else if(operation_type == "update"){
		var access = $('option[perspectiveid="'+$element.attr('perspectiveid')+'"]:selected').val();
		$("#corpus_set_corpus_perspective_roles tbody tr").each(function(){
			var html="";
			if( access == "role"){
				var user_id = $(this).attr('id');
                html += "<input class='userReportPerspective' type='checkbox' user_id='"+user_id+"' perspective_id='"+perspective_id+"' value='1' />";
			}
			else{
				html += "<i>"+access+"</i>";
			}
			$(this).find("td[perspective_id="+perspective_id+"]").html(html);
			$(this).find("td[perspective_id="+perspective_id+"]").css('background', '#FFFFFF');
		});
	}
}


function add($element){
	var elementType = $element.parent().attr("element");
	var parent = $element.parent().attr("parent");
	var $dialogBox =
		$('<div class="addDialog">'+
				'<table>'+
					'<tr><th style="text-align:right">Name</th><td><input id="elementName" type="text" /></td></tr>'+
					(elementType=='flag'
					?
					'<tr><th style="text-align:right">Short</th><td><input id="elementShort" type="text" /></td></tr>'+
					'<tr><th style="text-align:right">Description</th><td><textarea id="elementDescription" rows="4"></textarea></td></tr>'+
					'<tr><th style="text-align:right">Sort</th><td><input id="elementSort" type="text" /></td></tr>'
					:
					'<tr><th style="text-align:right">Description</th><td><textarea id="elementDescription" rows="4"></textarea></td></tr>'
					)+
				'</table>'+
		'</div>')
		.dialog({
			modal : true,
			title : 'Create '+elementType.replace(/_/g," "),
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					var _data = 	{
							url : (elementType=='corpus' ? "" : $.url(window.location.href).attr('query') ),
							name_str : $("#elementName").val(),
							short_str : $("#elementShort").val(),
							desc_str : $("#elementDescription").val(),
							element_type : elementType
						};
					if (elementType=='flag'){
						_data.element_sort = $("#elementSort").val();
					}

					var success = function(data){
						if ( elementType=='flag' ){
							$("#"+parent+" > tbody").append(
									'<tr>'+
										'<td>'+ data.last_id+'</td>'+
										'<td class="name">'+_data.name_str+'</td>'+
										'<td class="short">'+_data.short_str+'</td>'+
										'<td class="description">'+_data.desc_str+'</td>'+
										'<td class="sort">'+_data.element_sort+'</td>'+
									'</tr>'
								);
						}
						else{
							$("#"+parent+" > tbody").append(
									'<tr>'+
										'<td>'+data.last_id+'</td>'+
										'<td>'+_data.name_str+'</td>'+
										'<td>'+_data.desc_str+'</td>'+
									'</tr>'
								);
						}
					};

					var login = function(){
						add($element);
					};

					var complete = function(){
						$dialogBox.dialog("close");
					};

					var error = function(data){
                    }

					doAjaxSync($element.attr("action"), _data, success, error, complete, null, login);
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}
		});
}

//corpus_details, name
function editBasicInfoName($element){
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $("#"+parent);

    var corpusName = $container.find('.hightlighted td:first').text();
    $("#nameDescription").val(corpusName);

    console.log("Editiing");
    $("#basicInfoNameModal").modal("show");

    $( "#edit_corpus_name_form" ).validate({
        rules: {
            nameDescription: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'corpus_validation',
                        type: 'edit_corpus_name',
                        corpus_id: corpus_id
                    }
                }
            }
        },
        messages: {
            nameDescription: {
                required: "Corpus must have a name.",
                remote: "A corpus with this name already exists."
            }
        }
    });

    $( ".confirmName" ).unbind( "click" ).click(function() {
        if($('#edit_corpus_name_form').valid()) {
            var edit_id = $container.find('.hightlighted th:first').attr("id");
            var _data = {
                //ajax : "corpus_update",
                url: $.url(window.location.href).attr('query'),
                name_str: $("#elementName").val(),
                desc_str: $("#nameDescription").val(),
                element_type: elementType,
                element_id: edit_id
            };

            var success = function(){
                var html = '<th id="' + _data.element_id + '">' + $container.find('.hightlighted th:first').text() + '</th>';
                html += '<td>' + _data.desc_str + '</td>';
                html += '<td>' + $container.find('.hightlighted td:last').html() + '</td>';
                $container.find(".hightlighted").html(html);

                $("#basicInfoNameModal").modal("hide");
            };

            var login = function () {
                edit($element);
            };

            var complete = function () {
            };

            doAjaxSync("corpus_update", _data, success, null, complete, null, login);
        }
    });
}

//corpus_details, owner
function editBasicInfoAccess($element){
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $("#"+parent);

    var select_text = '<label for="basicInfoAccess">Access:</label>'
    select_text += '<select id="basicInfoAccess" class = "form-control"><option value="0">restricted</option>'
    select_text += '<option value="1"'+($container.find('.hightlighted td:first').text() == 'public' ? " selected " : "" )+ '>public</option></select>'

    $("#basicInfoAccessSelect").html(select_text);

    $( ".confirmAccess" ).unbind( "click" ).click(function() {
        var edit_id = $container.find('.hightlighted th:first').attr("id");
        var _data = 	{
            //ajax : "corpus_update",
            url: $.url(window.location.href).attr('query'),
            name_str : $("#elementName").val(),
            desc_str : $("#basicInfoAccessSelect > #basicInfoAccess").val(),
            element_type : elementType,
            element_id : edit_id
        };

        var success = function(data){
                var html = '<th id="'+_data.element_id+'">'+$container.find('.hightlighted th:first').text()+'</th>';
                html += '<td>';
                if(_data.desc_str == "1"){
                    html += "public";
                } else{
                    html += "restricted";
                }

                html += '</td>';
                html += '<td>' +$container.find('.hightlighted td:last').html() + '</td>';

                $container.find(".hightlighted:first").html(html);

        };

        var login = function(){
            edit($element);
        };

        var complete = function(){
        };

        doAjaxSync("corpus_update", _data, success, null, complete, null, login);
    });
}

//corpus_details, access
function editBasicInfoOwner($element){
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $("#"+parent);

    $("#basicInfoOwnerSelect").html(get_users($container.find('.hightlighted td:first').text()));
    $( ".confirmOwner" ).unbind( "click" ).click(function() {
        var edit_id = $container.find('.hightlighted th:first').attr("id");
        var _data = 	{
            //ajax : "corpus_update",
            url: $.url(window.location.href).attr('query'),
            name_str : $("#elementName").val(),
            desc_str : $("#selectedUser").val(),
            element_type : elementType,
            element_id : edit_id
        };

        var success = function(data){
            var html = '<th id="'+_data.element_id+'">'+$container.find('.hightlighted th:first').text()+'</th>';
            html += '<td>';
            html += $("#selectedUser option:selected").text();
            html += '</td>';
            html += '<td>' +$container.find('.hightlighted td:last').html() + '</td>';
            $container.find(".hightlighted:first").html(html);
        };

        var login = function(){
            edit($element);
        };

        var complete = function(){
        };

        doAjaxSync("corpus_update", _data, success, null, complete, null, login);
    });
}

function editBasicInfoDescription($element){
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $("#"+parent);

    var description = '<textarea id="corpusDescription" class = "form-control" rows="4">'+$container.find('.hightlighted td:first').text()+'</textarea>'
    $("#corpusDescriptionArea").html(description);

    $( ".confirmDescription" ).unbind( "click" ).click(function() {
        var edit_id = $container.find('.hightlighted th:first').attr("id");
        var _data = 	{
            //ajax : "corpus_update",
            url: $.url(window.location.href).attr('query'),
            name_str : $("#elementName").val(),
            desc_str : $("#corpusDescription").val(),
            element_type : elementType,
            element_id : edit_id
        };

        var success = function(data){
            var html = '<th id="'+_data.element_id+'">'+$container.find('.hightlighted th:first').text()+'</th>';
            html += '<td>';
            html += _data.desc_str;
            html += '</td>';
            html += '<td>' +$container.find('.hightlighted td:last').html() + '</td>';
            $container.find(".hightlighted:first").html(html);
        };

        var login = function(){
            edit($element);
        };

        var complete = function(){
        };

        doAjaxSync("corpus_update", _data, success, null, complete, null, login);
    });
}

function editSubcorpora($element){
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $("#"+parent);
    var editElement = $container.find('.hightlighted td:first').next().text();
    var attrName = $container.find('.hightlighted th:first').text();
    var edit_id = $container.find('.hightlighted td:first').text();

    $("#subcorporaEditName").val(editElement);
    $("#subcorporaEditDescription").text($container.find('.hightlighted td:last').text());
    $("#subcorporaEdit").modal("show");

    $("#edit_subcorpora_form").validate({
        rules: {
            subcorporaEditName: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'corpus_validation',
                        type: 'subcorpora',
                        mode: 'edit',
                        corpus_id: corpus_id,
                        subcorpus_id: edit_id
                    }
                }
            }
        },
        messages: {
            subcorporaEditName: {
                required: "Subcorpus must have a name.",
                remote: "This name is already in use."
            }
        }
    });

    $( ".confirmSubcorporaEdit" ).unbind( "click" ).click(function() {
        if($('#edit_subcorpora_form').valid()) {

            var _data = {
                //ajax : "corpus_update",
                url: $.url(window.location.href).attr('query'),
                name_str: $("#subcorporaEditName").val(),
                desc_str: $("#subcorporaEditDescription").val(),
                element_type: elementType,
                element_id: edit_id
            };
            if (elementType == "flag") {
                _data.sort_str = $("#elementSort").val();
                _data.short_str = $("#elementShort").val();
            } else if (elementType == "user_id") {

            }

            var success = function (data) {
                /* TODO zmiana poprze podmianę całego wiersza zostaje zastąpiona podmianą konkrentych komórek -- na razie tylko dla flag */
                if (elementType == "flag") {
                    $container.find(".hightlighted:first td.name").text(_data.name_str);
                    $container.find(".hightlighted:first td.short").text(_data.short_str);
                    $container.find(".hightlighted:first td.description").text(_data.desc_str);
                    $container.find(".hightlighted:first td.sort").text(_data.sort_str);
                }
                else {
                    var html = "";
                    html += '<td>' + _data.element_id + '</td><td id="' + _data.element_id + '">' + _data.name_str + '</td>';

                    html += '<td>';
                    html += _data.desc_str;
                    html += '</td>';
                    $container.find(".hightlighted:first").html(html);
                }

                $("#subcorporaEdit").modal("hide");
            };

            var login = function () {
                edit($element);
            };

            var complete = function () {
            };

            doAjaxSync("corpus_update", _data, success, null, complete, null, login);
        }
    });
}

function createSubcorpora($element) {
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");

    $("#subcorporaCreate").modal("show");

    $("#create_subcorpora_form").validate({
        rules: {
            subcorporaCreateName: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'corpus_validation',
                        type: 'subcorpora',
                        mode: 'create',
                        corpus_id: corpus_id
                    }
                }
            }
        },
        messages: {
            subcorporaCreateName: {
                required: "Subcorpus must have a name.",
                remote: "This name is already in use."
            }
        }
    });

    $(".confirmSubcorporaCreate").unbind("click").click(function () {
        if ($('#create_subcorpora_form').valid()) {

            var _data = {
                url: (elementType == 'corpus' ? "" : $.url(window.location.href).attr('query') ),
                name_str: $("#subcorporaCreateName").val(),
                desc_str: $("#subcorporaCreateDescription").val(),
                element_type: elementType
            };

            var success = function (data) {
                $("#" + parent + " > tbody").append(
                    '<tr>' +
                    '<td>' + data.last_id + '</td>' +
                    '<td>' + _data.name_str + '</td>' +
                    '<td>' + _data.desc_str + '</td>' +
                    '</tr>'
                );

                $("#subcorporaCreate").modal("hide");
            };

            var login = function () {
                createSubcorpora($element);
            };

            doAjaxSync($element.attr("action"), _data, success, null, null, null, login);
        }
    })
}

function createFlag($element){
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");

    $("#createFlag").modal("show");

    $( "#create_flag_form" ).validate({
        rules: {
            flagNameCreate: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'corpus_validation',
                        type: 'flag',
                        mode: 'create',
                        corpus_id: corpus_id

                    }
                }
            }
        },
        messages: {
            flagNameCreate: {
                required: "Flag must have a name.",
                remote: "This name is already in use."
            }
        }
    });

    $( ".confirmFlagAdd" ).unbind( "click" ).click(function() {
        if($('#create_flag_form').valid()) {
            var _data = {
                url: (elementType == 'corpus' ? "" : $.url(window.location.href).attr('query') ),
                name_str: $("#flagNameCreate").val(),
                short_str: $("#flagShortCreate").val(),
                desc_str: $("#flagDescCreate").val(),
                element_sort: $("#flagSortCreate").val(),
                element_type: elementType
            };

            var success = function (data) {
                $("#" + parent + " > tbody").append(
                    '<tr>' +
                    '<td>' + data.last_id + '</td>' +
                    '<td class="name">' + _data.name_str + '</td>' +
                    '<td class="short">' + _data.short_str + '</td>' +
                    '<td class="description">' + _data.desc_str + '</td>' +
                    '<td class="sort">' + _data.element_sort + '</td>' +
                    '</tr>'
                );
                $("#createFlag").modal("hide");
            };

            var login = function () {
                createFlag($element);
            };

            var error = function (data) {
                console.log(data);
            };

            doAjaxSync("corpus_add_flag", _data, success, error, null, null, login);
        }
    });
}

function editFlag($element){
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $("#"+parent);
    var edit_id = $container.find('.hightlighted td:first').text();

    $("#flagNameEdit").val($container.find(".hightlighted:first td.name").text());
    $("#flagShortEdit").val($container.find(".hightlighted:first td.short").text());
    $("#flagDescEdit").text($container.find(".hightlighted:first td.description").text());
    $("#flagSortEdit").val($container.find(".hightlighted:first td.sort").text());

    $("#editFlag").modal("show");

    $( "#edit_flag_form" ).validate({
        rules: {
            flagNameEdit: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'corpus_validation',
                        type: 'flag',
                        mode: 'edit',
                        corpus_id: corpus_id,
                        flag_id: edit_id

                    }
                }
            }
        },
        messages: {
            flagNameEdit: {
                required: "Flag must have a name.",
                remote: "This name is already in use."
            }
        }
    });

    $( ".confirmFlagEdit" ).unbind( "click" ).click(function() {
            if($('#edit_flag_form').valid()) {
                var _data = {
                    //ajax : "corpus_update",
                    url: $.url(window.location.href).attr('query'),
                    name_str: $("#flagNameEdit").val(),
                    desc_str: $("#flagDescEdit").val(),
                    sort_str: $("#flagSortEdit").val(),
                    short_str: $("#flagShortEdit").val(),
                    element_type: elementType,
                    element_id: edit_id
                };


                var success = function (data) {
                    $container.find(".hightlighted:first td.name").text(_data.name_str);
                    $container.find(".hightlighted:first td.short").text(_data.short_str);
                    $container.find(".hightlighted:first td.description").text(_data.desc_str);
                    $container.find(".hightlighted:first td.sort").text(_data.sort_str);

                    $("#editFlag").modal("hide");

                };

                var login = function () {
                    edit($element);
                };

                var complete = function () {
                };

                doAjaxSync("corpus_update", _data, success, null, complete, null, login);
            }
    });
}


function edit($element){
	var elementType = $element.parent().attr("element");
	var parent = $element.parent().attr("parent");
	var $container = $("#"+parent);
	var editElement = (elementType == 'corpus_details' ? $container.find('.hightlighted th:first').attr("id") : $container.find('.hightlighted td:first').next().text());
	var attrName = $container.find('.hightlighted th:first').text();

	var $dialogBox =
		$('<div class="editDialog">'+
            '<table>'+
                (elementType == 'corpus_details'
                ?
                '<tr><th style="text-align:right">' + attrName + '</th><td>'+
                    (editElement == "user_id"
                    ? get_users($container.find('.hightlighted td:first').text())
                    : (  editElement == "public"
                        ? '<select id="elementDescription"><option value="0">restricted</option><option value="1"'+($container.find('.hightlighted td:first').text() == 'public' ? " selected " : "" )+'>public</option></select>'
                        : '<textarea id="elementDescription" rows="4">'+$container.find('.hightlighted td:first').text()+'</textarea>')
                    ) +'</td></tr>'
                :
                '<tr><th style="text-align:right">Name</th><td><input id="elementName" type="text" value="'+editElement+'"/></td></tr>'+
                    (elementType == "flag"
                    ?
                    '<tr><th style="text-align:right">Short</th><td><input id="elementShort" type="text" value="'+$container.find('.hightlighted td.short').text()+'" /></td></tr>'+
                    '<tr><th style="text-align:right">Description</th><td><textarea id="elementDescription" rows="4">'+$container.find('.hightlighted td.description').text()+'</textarea></td></tr>'+
                    '<tr><th style="text-align:right">Sort</th><td><input id="elementSort" type="text" value="'+$container.find('.hightlighted td:last').text()+'" /></td></tr>'
                    :
                    '<tr><th style="text-align:right">Description</th><td><textarea id="elementDescription" rows="4">'+$container.find('.hightlighted td:last').text()+'</textarea></td></tr>'
                )) +
            '</table>'+
		'</div>')
		.dialog({
			modal : true,
			title : 'Edit '+elementType.replace(/_/g," ")+ (elementType == 'corpus_details' ? '' : ' #'+$container.find('.hightlighted td:first').text()),
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					var edit_id = (elementType == 'corpus_details' ? $container.find('.hightlighted th:first').attr("id") : $container.find('.hightlighted td:first').text());
					var _data = 	{
							//ajax : "corpus_update",
							url: $.url(window.location.href).attr('query'),
							name_str : $("#elementName").val(),
							desc_str : $("#elementDescription").val(),
							element_type : elementType,
							element_id : edit_id
						};
					if (elementType == "flag"){
						_data.sort_str = $("#elementSort").val();
						_data.short_str = $("#elementShort").val();
					}


					var success = function(data){
						/* TODO zmiana poprze podmianę całego wiersza zostaje zastąpiona podmianą konkrentych komórek -- na razie tylko dla flag */
						if ( elementType == "flag"){
							$container.find(".hightlighted:first td.name").text(_data.name_str);
							$container.find(".hightlighted:first td.short").text(_data.short_str);
							$container.find(".hightlighted:first td.description").text(_data.desc_str);
							$container.find(".hightlighted:first td.sort").text(_data.sort_str);
						}
						else{
						    /*
                            var html = (
                                    elementType == 'corpus_details'
                                        ? '<th id="'+_data.element_id+'">'+$container.find('.hightlighted th:first').text()+'</th>'
                                        : '<td>'+_data.element_id+'</td><td id="'+_data.element_id+'">'+_data.name_str+'</td>' ) +'<td>'+
                                (_data.name_str == "user_id"
                                    ? $("#elementDescription option:selected").text()
                                    : (_data.name_str == "public"
                                        ? (_data.desc_str == "1"
                                            ? "public"
                                            : "restricted" )
                                        : _data.desc_str))
                                + '</td>'+
                                (elementType == 'flag'
                                    ? '<td>'+_data.sort_str+'</td>'
                                    : '');*/

						    var html = "";
						    if(elementType == 'corpus_details'){
                               html += '<th id="'+_data.element_id+'">'+$container.find('.hightlighted th:first').text()+'</th>';
                            } else{
                                html += '<td>'+_data.element_id+'</td><td id="'+_data.element_id+'">'+_data.name_str+'</td>';
                            }
                            html += '<td>';
                            if(edit_id == "user_id"){
                                html += $("#elementDescription option:selected").text();
                            } else{
                                if(edit_id == "public"){
                                    if(_data.desc_str == "1"){
                                        html += "public";
                                    } else{
                                        html += "restricted";
                                    }var edit_id = (elementType == 'corpus_details' ? $container.find('.hightlighted th:first').attr("id") : $container.find('.hightlighted td:first').text());
					var _data = 	{
							//ajax : "corpus_update",
							url: $.url(window.location.href).attr('query'),
							name_str : $("#elementName").val(),
							desc_str : $("#elementDescription").val(),
							element_type : elementType,
							element_id : edit_id
						};
					if (elementType == "flag"){
						_data.sort_str = $("#elementSort").val();
						_data.short_str = $("#elementShort").val();
					}


					var success = function(data){
						/* TODO zmiana poprze podmianę całego wiersza zostaje zastąpiona podmianą konkrentych komórek -- na razie tylko dla flag */
						if ( elementType == "flag"){
							$container.find(".hightlighted:first td.name").text(_data.name_str);
							$container.find(".hightlighted:first td.short").text(_data.short_str);
							$container.find(".hightlighted:first td.description").text(_data.desc_str);
							$container.find(".hightlighted:first td.sort").text(_data.sort_str);
						}
						else{
						    /*
                            var html = (
                                    elementType == 'corpus_details'
                                        ? '<th id="'+_data.element_id+'">'+$container.find('.hightlighted th:first').text()+'</th>'
                                        : '<td>'+_data.element_id+'</td><td id="'+_data.element_id+'">'+_data.name_str+'</td>' ) +'<td>'+
                                (_data.name_str == "user_id"
                                    ? $("#elementDescription option:selected").text()
                                    : (_data.name_str == "public"
                                        ? (_data.desc_str == "1"
                                            ? "public"
                                            : "restricted" )
                                        : _data.desc_str))
                                + '</td>'+
                                (elementType == 'flag'
                                    ? '<td>'+_data.sort_str+'</td>'
                                    : '');*/

						    var html = "";
						    if(elementType == 'corpus_details'){
                               html += '<th id="'+_data.element_id+'">'+$container.find('.hightlighted th:first').text()+'</th>';
                            } else{
                                html += '<td>'+_data.element_id+'</td><td id="'+_data.element_id+'">'+_data.name_str+'</td>';
                            }
                            html += '<td>';
                            if(edit_id == "user_id"){
                                html += $("#elementDescription option:selected").text();
                            } else{
                                if(edit_id == "public"){
                                    if(_data.desc_str == "1"){
                                        html += "public";
                                    } else{
                                        html += "restricted";
                                    }
                                } else{
                                    html += _data.desc_str;
                                }
                            }
                            html += '</td>';
                            if(elementType == 'flag'){
                                html += '<td>'+_data.sort_str+'</td>';
                            }

							if (elementType == 'corpus_details'){
								html += '<td>' +$container.find('.hightlighted td:last').html() + '</td>';
							}
							$container.find(".hightlighted:first").html(html);
						}
					};

					var login = function(){
						edit($element);
					};

					var complete = function(){
						$dialogBox.dialog("close");
					};

					doAjaxSync("corpus_update", _data, success, null, complete, null, login);
                                } else{
                                    html += _data.desc_str;
                                }
                            }
                            html += '</td>';
                            if(elementType == 'flag'){
                                html += '<td>'+_data.sort_str+'</td>';
                            }

							if (elementType == 'corpus_details'){
								html += '<td>' +$container.find('.hightlighted td:last').html() + '</td>';
							}
							$container.find(".hightlighted:first").html(html);
						}
					};

					var login = function(){
						edit($element);
					};

					var complete = function(){
						$dialogBox.dialog("close");
					};

					doAjaxSync("corpus_update", _data, success, null, complete, null, login);

				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}
		});
}


function remove($element){
	var elementType = $element.parent().attr("element");
	var parent = $element.parent().attr("parent");
	var $container = $("#"+parent);

	var delete_html = '<table>'+
        '<label for="delete_name">Name:</label>'+
        '<p id = "delete_name">'+$container.find('.hightlighted td:first').next().text()+'</p>'+
        (elementType == "subcorpus" ?
            '<label for="delete_description">Description:</label>'+
            '<p id = "delete_description">'+$container.find('.hightlighted td:last').text()+'</p>' : "")

	$('#deleteContent').html(delete_html);
    $('#deleteModal').modal('show');

    $( ".confirmDelete" ).unbind( "click" ).click(function() {

        var _data = 	{
            url: $.url(window.location.href).attr('query'),
            element_type : elementType,
            element_id : $container.find('.hightlighted td:first').text()
        };

        var success = function(data){
            $container.find(".hightlighted:first").remove();
            $(".delete").hide();
            $(".edit").hide();
        };

        var login = function(){
            remove($element);
        };

        var complete = function(){
            $('#deleteModal').modal('hide');
        };

        doAjaxSync("corpus_delete", _data, success, null, complete, null, login);
    });
}


function delete_corpus(){
	$("#deleteCorpusHeader").text('Delete corpora #'+ $('#corpus_id').val() + "?");
	$("#deleteCorpusName").text($('#corpus_name').val());
	$("#deleteCorpusDesc").text($('#corpus_description').val());

    $( ".confirmDeleteCorpus" ).unbind( "click" ).click(function() {
		var _data = 	{
				url: $.url(window.location.href).attr("query"),
				element_type : "corpus",
				element_id : $('#corpus_id').val()
			};

		var success = function(data){
			var href = document.location.origin + document.location.pathname + '?page=home';
			document.location = href;
		};

		var login = function(){
			remove($element);
		};

		var complete = function(){
			$dialogBox.dialog("close");
		};

		doAjaxSync("corpus_delete", _data, success, null, complete, null, login);
	});
}


function get_users(userName){
	var select = "<select class = 'form-control' id=\"selectedUser\">";

	var success = function(data){
		$.each(data,function(index, value){
			select += '<option value="'+value.user_id+'" '+(value.screename == userName ? " selected " : "")+'>'+value.screename+'</option>';
		});
	};

	var login = function(){
		get_users(userName);
	} ;

	doAjaxSyncWithLogin("users_get", {}, success, login);

	return select + "</select>";
}


function ext_edit($element){
	var parent = $element.parent().attr("parent");
    $(".create_metadata_error").hide();

    $( "#create_metadata_form" ).validate({
        rules: {
            create_metadata_field: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'corpus_validate_metadata',
                        mode: 'create',
                        corpus_id: corpus_id
                    }
                }
            }
        },
        messages: {
            create_metadata_field: {
                required: "Field is required.",
                remote: "This field name is already in use."
            }
        }
    });


    $( ".confirm_create_metadata" ).unbind( "click" ).click(function() {

        if($('#create_metadata_form').valid()) {

            /*
             Gets enumeration values and converts them to a format ready for database insert
             */
            var display_enum_values = [];
            if($("#create_metadata_type").val() === "enum"){
                var enum_values = '';
                $("#enum_values").children().each(function(){
                    var edit_value = $(this).val();
                    if(edit_value !== ""){
                        enum_values += '"' + edit_value +'",';
                        display_enum_values.push(edit_value);
                    }
                });
                enum_values = enum_values.replace(/,\s*$/, '');
            }

            if(!($("#create_metadata_type").val() === "enum" && display_enum_values.length === 0)) {
                var _data = {
                    url: $.url(window.location.href).attr('query'),
                    action: "add",
                    enum_values: enum_values,
                    field: $("#create_metadata_field").val(),
                    type: $("#create_metadata_type").val(),
                    is_null: $(".create_metadata_null").is(':checked')
                };

                var success = function (data) {
                    var tableRows = "";
                    tableRows +=
                        '<tr>' +
                        '<td>' + _data.field + '</td>' +
                        '<td>' + _data.type + '</td>' +
                        '<td>' + _data.is_null + '</td>';

                    if ($("#create_metadata_type").val() === "enum") {
                        tableRows += '<td class = "text-center">' +
                            '<select class = "form-control">' +
                            '<option>-values-</option>';
                        $.each(display_enum_values, function (ind, val) {
                            tableRows += '<option>' + val + '</option>'
                        });
                        tableRows += '</select></td></tr>';
                    } else {
                        tableRows += "<td class = 'text-center'>-</td></tr>"
                    }
                    $("#extListContainer > tbody").append(tableRows);
                };


                var complete = function (data) {
                    $('#create_metadata_modal').modal('hide');
                };

                doAjaxSync("corpus_edit_ext", _data, success, null, complete);
            } else{
                $(".create_metadata_error").show();
            }
        }

    });

}


function get_corpus_ext_elements(){
	var params = {
		url: $.url(window.location.href).attr('query'),
		action : "get"
	}

	var success = function(data){
		var tableRows = "";
		$.each(data,function(index, value){
			tableRows +=
			'<tr>'+
			'<td>'+value.field+'</td>'+
			'<td>'+value.type+'</td>'+
			'<td>'+value.null+'</td>'+
			'</tr>';
		});
		$("#extListContainer > tbody").html(tableRows);
		$("#extListContainer .create").show();
		$("#extListContainer").show();
		$(".tablesorter").trigger("update");
	};

	var login = function(data){
		get_corpus_ext_elements();
	};

	var error = function(){
		$("#extListContainer").hide();
	};

	doAjaxSync("corpus_edit_ext", params, success, error, null, null, login);
}
