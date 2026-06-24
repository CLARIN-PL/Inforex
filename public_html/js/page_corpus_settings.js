/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */
var url = $.url(window.location.href);
var corpus_id = url.param('corpus');
var max_metadata_enum_values = 55;
var assignedUsersPage = 1;
var assignedUsersPageSize = 10;

function changeDefaultValue(mode){
    var enums = getCurrentEnums(mode);
    var default_val_html;
    var metadata_select = (mode === "create") ? '#create_metadata_type' : '#edit_metadata_type';
    if($(metadata_select).val() === "text"){
        default_val_html = "<input type = 'text' class = 'form-control "+mode+"_text_default' placeholder='Default value'>";
    } else{
        default_val_html = "<select class = 'form-control select_"+mode+"_default'>";
        if(enums.length === 0){
            default_val_html += "<option value = '-'>-</option>";
        } else{
            for(var i = 0; i < enums.length; i++){
                default_val_html += "<option value = '"+enums[i]+"'>"+enums[i]+"</option>";
            }
        }
        default_val_html += "</select>";
    }
    $("#"+mode+"_default_options").html(default_val_html);
}

function getCurrentEnums(mode){
    var enum_input;
    if(mode === "create"){
        enum_input = ".enum_input";
    } else{
        enum_input = ".edit_enum_input";
    }

    var enum_values = [];
    $(enum_input).each(function(){
        var edit_value = $(this).val();
        if(edit_value !== ""){
            enum_values.push(edit_value);
        }
    });
    return enum_values;
}


$(function(){
    $("#create_metadata_form").on('change', '.enum_input', function(){
        changeDefaultValue("create");
    });

    $("#edit_metadata_form").on('change', '.edit_enum_input', function(){
        changeDefaultValue("edit");
    });

    $(".metadata_type").change(function(){
        if($(this).attr('id') === "create_metadata_type"){
            if($("#create_metadata_type").val() === "enum"){
                $(".enum_values_edition").show();
            } else{
                $(".enum_values_edition").hide();
            }
            changeDefaultValue("create");
        } else{
            if($("#edit_metadata_type").val() === "enum"){
                $(".edit_enum_values_edition").show();
            } else{
                $(".edit_enum_values_edition").hide();
            }
            changeDefaultValue("edit");
        }
    });

    $("#create_metadata_field").keyup(function(){
       var field_name = $(this).val();
       var column_id = field_name.toLowerCase();

       var i = 0;
       for(i; i < column_id.length; i++){
           if(!isAlphaNumeric(column_id[i])){
               column_id = replaceAt(column_id, i, "_");
           }
       }
       $("#create_metadata_column_id").val(column_id);
    });

    $(".add_enum").click(function(){
        if($(this).val() === "add"){
            if($('.enum_input').length <= max_metadata_enum_values){
                var input = "<input class = 'form-control enum_input'>";
                $("#enum_values").append(input);
            }
            changeDefaultValue("create");
        } else{
            if($('.edit_enum_input').length <= max_metadata_enum_values){
                var input = "<input class = 'form-control edit_enum_input'>";
                $("#edit_enum_values").append(input);
            }
            changeDefaultValue("edit");
        }
    });

    $(".remove_enum").click(function(){
        if($(this).val() === "add") {
            if ($('.enum_input').length > 1) {
                $("#enum_values").children().last().remove();
            }
            changeDefaultValue("create");
        } else{
            if ($('.edit_enum_input').length > 1) {
                $("#edit_enum_values").children().last().remove();
            }
            changeDefaultValue("edit");
        }
    });

    $(".edit_metadata").click(function(){
        edit_metadata($(this));
    });



    $('.search_users').submit(false);
    $(".corpus-settings-users .search-form").submit(false);

    $(".search_assigned_users").keyup(function () {
        assignedUsersPage = 1;
        renderAssignedUsersPagination();
    });

    renderAssignedUsersPagination();

    $(".search_users").keyup(function () {
        var text = this.value.toLowerCase();
        if(text.length >= 3){
            var data = {
                'match_text': text,
                'corpus_id': corpus_id
            };

            var success = function(users){
                var rows = "";
                $.each(users, function (index, value) {
                    var button = "<button id='"+value.user_id+"' class='add_user_button btn btn-primary corpus-settings-user-action-button' title='Assign user to corpus'><i class='fa fa-arrow-left' aria-hidden='true'></i><span class='sr-only'>Assign user</span></button>";
                    rows += "<tr>" +
                        "<td><span class='corpus-settings-user-name'>"+value.screename+"</span></td>" +
                        "<td><span class='corpus-settings-user-login'>"+value.login+"</span></td>" +
                        "<td><span class='corpus-settings-user-email'>"+value.email+"</span></td>" +
                        "<td class='corpus-settings-users-action-cell'>"+button+"</td>" +
                        "</tr>";
                } );

                $("#add_user_to_corpus_table").html(rows);
            };

            doAjaxSync("user_corpus_assign", data, success);
        } else{
            $("#add_user_to_corpus_table").html("");
        }
    });


    $("input[type=checkbox]:not(.annotationSet, .userReportPerspective, .relation_set_checkbox, .create_metadata_null, .edit_metadata_null)").click(function(e){
        e.stopPropagation();
		set($(this), $(window).scrollTop());
	});

    $("#corpus_set_corpus_perspective_roles").on("click", ".userReportPerspective", function(e){
        e.stopPropagation();
        set($(this), $(window).scrollTop());
    })

    $("#corpus_set_corpus_role").on("change", ".corpusRole", function(){
        $(this).closest("td").toggleClass("corpus-settings-roles-checkbox-cell-active", this.checked);
    });

	$("input[type=checkbox]:not(.create_metadata_null):checked").parent().addClass("selected");

	$("#reportPerspectives").click(function(e){
		e.preventDefault();
		getReportPerspectives();
	});

	$("#corpusPerspectives").on('change', '.setReportPerspective', function(){
		setReportPerspective($(this));
	});

    $("#corpusPerspectives").on('click mousedown', '.corpus-settings-perspectives-checkbox, .corpus-settings-perspectives-checkbox > span[aria-hidden=\"true\"]', function(e){
        e.stopPropagation();
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

    $(".editBasicInfoCss").click(function(){
        editBasicInfoCss($(this));
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
		if($(this).hasClass('deleteFlag')){
            deleteFlag($(this));
		} else if($(this).hasClass("deleteSubcorpus")){
            deleteSubcorpus($(this));
        }
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

function replaceAt(word, index, replacement) {
    return word.substr(0, index) + replacement+ word.substr(index + replacement.length);
}

function isAlphaNumeric(char){
    return char.match(/^[a-zA-Z0-9$]+$/i) !== null;
}


function deleteMetadata(element){

    var row = $("#page").find(".hightlighted");
    var field_name = $("#page").find('.hightlighted td:eq(1)').text();
    var field_type = $(row).find("td:eq(3)").text();

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
            document.body.style.cursor='default';
            $(".confirmDelete").prop('disabled', false);
            $('#deleteModal').modal('hide');
        };


        $(".confirmDelete").prop('disabled', true);
        document.body.style.cursor='wait';
        doAjax("corpus_edit_ext", _data, success, null, complete);
    });

}

function getSelectedDefaultValue(mode){
    var selected_value = $('input[name='+mode+'_metadata_default_value]:checked').val();
    var value;
    if(selected_value !== "null"){
        if($("#" + mode + "_metadata_type").val() === "enum"){
            if($('input[name='+mode+'_metadata_default_value]:checked').hasClass('enum_select')){
                value = $(".select_" + mode + "_default").val();
            } else{
                value = $('input[name='+mode+'_metadata_default_value]:checked').val();
            }
            if(value === "-" || value === "null"){
                value = null;
            }
        } else{
            value = $("." + mode + "_text_default").val();
            if(value === ""){
                value = null;
            }
        }
    } else{
        value = null;
    }
    return value;
}

function edit_metadata(){
    $(".edit_metadata_error").hide();
    var page = $("#page");
    var field = $(page).find('.hightlighted td:first').text();
    var column_id = $(page).find('.hightlighted td:eq(1)').text();
    var comment = $(page).find('.hightlighted td:eq(2)').text();
    var type = $(page).find('.hightlighted td:eq(3)').text();
    var is_null = $(page).find('.hightlighted td:eq(4)').text() === "Yes";

    $("#edit_metadata_field").val(field);
    $("#edit_metadata_column_id").val(column_id);
    $("#edit_metadata_comment").val(comment);
    $("#edit_metadata_type").val(type);
    $("#edit_metadata_null").prop("checked", is_null);
    var selected_default = $(".hightlighted > td:eq(4)").text();
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
    var enums = getCurrentEnums('edit');
    var default_val_html;
    if(type === "text"){
        default_val_html = "<input type = 'text' class = 'form-control edit_text_default' value = '"+(selected_default !== 'empty' ? selected_default : '')+"' placeholder='Default value'>";
    } else{
        //Gets the default value from the table
        default_val_html = "<select class = 'form-control select_edit_default'>";
        if(enums.length === 0){
            default_val_html += "<option value = '-'>-</option>";
        } else{
            for(var i = 0; i < enums.length; i++){
                default_val_html += "<option "+(selected_default === enums[i] ? 'selected' : '')+" value = '"+enums[i]+"'>"+enums[i]+"</option>";
            }
        }
        default_val_html += "</select>";
    }
    $("#edit_default_options").html(default_val_html);

    if(selected_default !== "empty"){
        $("#edit_metadata_default_select").prop("checked", true);
    } else{
        $("#edit_metadata_default_empty").prop("checked", true);
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
                    field: $("#edit_metadata_column_id").val(),
                    field_name: $("#edit_metadata_field").val(),
                    comment: $("#edit_metadata_comment").val(),
                    old_field: column_id,
                    type: $("#edit_metadata_type").val(),
                    default: getSelectedDefaultValue('edit')
                };

                var success = function () {
                    var row = $(page).find('.hightlighted');

                    var tableRows = "";
                    tableRows +=
                        '<td><span class="corpus-settings-metadata-field">' + _data.field_name + '</span></td>' +
                        '<td><span class="corpus-settings-metadata-column-id">' + _data.field + '</span></td>' +
                        '<td><span class="corpus-settings-metadata-comment">' + _data.comment + '</span></td>' +
                        '<td><span class="corpus-settings-metadata-type">' + _data.type + '</span></td>' +
                        '<td><span class="corpus-settings-metadata-default">' + (_data.default === null ? "empty" : _data.default) + '</span></td>';

                    if ($("#edit_metadata_type").val() === "enum") {
                        tableRows += '<td class = "text-center">' +
                            '<select class = "form-control select_edit_default corpus-settings-metadata-values-select">' +
                            '<option>-values-</option>';
                        $.each(display_enum_values, function (ind, val) {
                            tableRows += '<option>' + val + '</option>'
                        });
                        tableRows += '</select></td>';
                    } else {
                        tableRows += "<td class='text-center'><span class='corpus-settings-metadata-empty'>-</span></td>"
                    }
                    $(row).html(tableRows);

                    $(".confirm_edit_metadata").prop('disabled', false);
                    $('#edit_metadata_modal').modal('hide');
                };


                var complete = function() {
                    document.body.style.cursor='default';
                };

                $(".confirm_edit_metadata").prop('disabled', true);
                document.body.style.cursor='wait';
                doAjax("corpus_edit_ext", _data, success, null, complete);
            } else{
                $(".edit_metadata_error").show();
            }

    });

}

function getFilteredAssignedUserRows(){
    var text = $(".search_assigned_users").val() || "";
    text = text.toLowerCase();

    return $("#users_assigned_table tr").filter(function () {
        return $(this).text().toLowerCase().indexOf(text) !== -1;
    });
}

function renderAssignedUsersPagination(){
    var $allRows = $("#users_assigned_table tr");
    var $filteredRows = getFilteredAssignedUserRows();
    var totalRows = $filteredRows.length;
    var totalPages = Math.max(1, Math.ceil(totalRows / assignedUsersPageSize));
    var startIndex;
    var endIndex;
    var $info = $("#assigned_users_pagination_info");
    var $controls = $("#assigned_users_pagination_controls");

    assignedUsersPage = Math.min(Math.max(assignedUsersPage, 1), totalPages);
    startIndex = (assignedUsersPage - 1) * assignedUsersPageSize;
    endIndex = startIndex + assignedUsersPageSize;

    $allRows.hide();
    $filteredRows.slice(startIndex, endIndex).show();

    if (totalRows === 0) {
        $info.text("No assigned users found.");
        $controls.empty();
        return;
    }

    $info.text("Showing " + (startIndex + 1) + " to " + Math.min(endIndex, totalRows) + " of " + totalRows + " assigned users");
    renderAssignedUsersPaginationControls(totalPages);
}

function renderAssignedUsersPaginationControls(totalPages){
    var $controls = $("#assigned_users_pagination_controls");
    var buttons = "";
    var page;

    if (totalPages <= 1) {
        $controls.empty();
        return;
    }

    buttons += "<button type='button' class='btn btn-default corpus-settings-users-page-button' data-page='" + (assignedUsersPage - 1) + "'" + (assignedUsersPage === 1 ? " disabled" : "") + ">Previous</button>";

    for (page = 1; page <= totalPages; page++) {
        buttons += "<button type='button' class='btn btn-default corpus-settings-users-page-button" + (page === assignedUsersPage ? " active" : "") + "' data-page='" + page + "'>" + page + "</button>";
    }

    buttons += "<button type='button' class='btn btn-default corpus-settings-users-page-button' data-page='" + (assignedUsersPage + 1) + "'" + (assignedUsersPage === totalPages ? " disabled" : "") + ">Next</button>";
    $controls.html(buttons);
}

function corpusSettingsEscapeHtml(value) {
    return String(value == null ? "" : value)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function corpusSettingsActivityTimestampCell(timestamp){
    var safeTimestamp = corpusSettingsEscapeHtml(timestamp || "");
    var parts;
    var date;
    var time;

    if (!timestamp) {
        return "<span class='administration-activities-time-empty corpus-settings-user-date-empty'>No activity</span>";
    }

    parts = String(timestamp).split(" ");
    date = parts[0] || timestamp;
    time = parts[1] ? parts[1].substr(0, 5) : "";

    return "<span class='administration-activities-time corpus-settings-user-date' title='" + safeTimestamp + "'>" +
        "<i class='fa fa-clock-o' aria-hidden='true'></i>" +
        "<span>" + corpusSettingsEscapeHtml(date) + "</span>" +
        (time ? "<small>" + corpusSettingsEscapeHtml(time) + "</small>" : "") +
        "</span>";
}

$(document).on("click", ".corpus-settings-users-page-button", function () {
    if ($(this).prop("disabled")) {
        return;
    }

    assignedUsersPage = parseInt($(this).attr("data-page"), 10);
    renderAssignedUsersPagination();
});

function refresh_corpus_users(){
    var data = {
        'mode': 'get',
        'corpus_id': corpus_id
    };

	    var success = function(users){
	        var rows = "";
	        $.each(users, function (index, value) {
	            rows += "<tr>" +
	                "<td><span class='corpus-settings-user-name'>"+value.screename+"</span></td>" +
	                "<td><span class='corpus-settings-user-login'>"+value.login+"</span></td>" +
	                "<td><span class='corpus-settings-user-email'>"+value.email+"</span></td>" +
	                "<td>"+corpusSettingsActivityTimestampCell(value.last_activity)+"</td>" +
	                "<td class='corpus-settings-users-action-cell'><button id='"+value.user_id+"' class='remove_user_button btn btn-primary corpus-settings-user-action-button' title='Remove user from corpus'><i class='fa fa-arrow-right' aria-hidden='true'></i><span class='sr-only'>Remove user</span></button></td>"+
	                "</tr>";
	        } );

        $("#users_assigned_table").html(rows);
        renderAssignedUsersPagination();
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
            $(element).closest('tr').remove();
            renderAssignedUsersPagination();
        };

        doAjaxSync("corpus_update", data, success);
    }
}


function restoreCorpusSettingsScroll(scrollTop){
    if (typeof scrollTop === "undefined" || scrollTop === null) {
        return;
    }

    $(window).scrollTop(scrollTop);
    setTimeout(function(){
        $(window).scrollTop(scrollTop);
    }, 0);
}

function set($element, scrollTop){
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
		var $cell = $element.closest("td");
		if ($cell.hasClass("corpus-settings-event-group-use-cell")) {
			$cell.toggleClass("corpus-settings-event-group-use-cell-active", $element.is(":checked"));
		} else if ($cell.hasClass("corpus-settings-perspectives-access-cell")) {
			$cell.toggleClass("corpus-settings-perspectives-access-cell-active", $element.is(":checked"));
		} else {
			$element.parent().css('background',($element.is(':checked') ? '#9DD943' : '#FFFFFF'));
		}
		$(".tablesorter").trigger("update");
        restoreCorpusSettingsScroll(scrollTop);
	};

	var login = function(){
		set($element, scrollTop);
	};
	doAjaxSyncWithLogin(ajax, _data, success, login);
}

function corpusSettingsPerspectiveCheckbox(className, attributes, checked) {
    return "<label class='corpus-settings-perspectives-checkbox'>" +
        "<input class='" + className + "' type='checkbox' " + attributes + (checked ? " checked='checked'" : "") + ">" +
        "<span aria-hidden='true'></span>" +
        "</label>";
}

function corpusSettingsPerspectiveAccessBadge(access) {
    return "<span class='corpus-settings-perspectives-access-badge'>" + access + "</span>";
}



function getCorpusPerspectivesScrollState(){
    var $wrapper = $("#corpusPerspectivesContent .corpus-settings-perspectives-modal-table-wrapper");
    var $modalBody = $("#corpusPerspectivesContent");
    return {
        wrapper: $wrapper,
        modalBody: $modalBody,
        top: $wrapper.length ? $wrapper.scrollTop() : 0,
        modalTop: $modalBody.length ? $modalBody.scrollTop() : 0
    };
}

function restoreCorpusPerspectivesScrollState(state){
    if (!state) {
        return;
    }

    if (document.activeElement && typeof document.activeElement.blur === "function") {
        document.activeElement.blur();
    }

    window.requestAnimationFrame(function(){
        if (state.modalBody && state.modalBody.length) {
            state.modalBody.scrollTop(state.modalTop || 0);
        }
        if (state.wrapper && state.wrapper.length) {
            state.wrapper.scrollTop(state.top || 0);
        }

        window.requestAnimationFrame(function(){
            if (state.modalBody && state.modalBody.length) {
                state.modalBody.scrollTop(state.modalTop || 0);
            }
            if (state.wrapper && state.wrapper.length) {
                state.wrapper.scrollTop(state.top || 0);
            }
        });
    });
}

function getReportPerspectives(){

	var success = function(data){
		var modalHtml =
				'<div class="administration-table-wrapper corpus-settings-perspectives-modal-table-wrapper">'+
				'<table class="tablesorter table table-striped table-hover administration-table corpus-settings-perspectives-modal-table" cellspacing="1">'+
					'<thead>'+
						'<tr>'+
							'<th>Active</th>'+
							'<th>Title</th>'+
							'<th>Description</th>'+
							'<th>Access</th>'+
						'</tr>'+
					'</thead>'+
					'<tbody>';
		$.each(data,function(index,value){
			modalHtml +=
				'<tr'+(value.cid ? '' : ' class="inactive"')+'>'+
					'<td class="corpus-settings-perspectives-modal-active-cell">'+corpusSettingsPerspectiveCheckbox("setReportPerspective", 'perspectivetitle="'+value.title+'" perspectiveid="'+value.id+'"', value.cid)+'</td>'+
					'<td><span class="corpus-settings-perspectives-modal-title" title="'+value.title+'">'+value.title+'</span></td>'+
					'<td><span class="corpus-settings-perspectives-modal-description" title="'+value.description+'">'+value.description+'</span></td>'+
					'<td>'+
						'<select id="select_'+value.id+'" '+(value.cid ? '' : ' disabled')+' perspectiveid="'+value.id+'" class="form-control updateReportPerspective">'+
							'<option perspectiveid="'+value.id+'" value="loggedin" '+((value.access && value.access=="loggedin") ? 'selected="selected"' : '' )+'>loggedin</option>'+
							'<option perspectiveid="'+value.id+'" value="role" '+((value.access && value.access=="role") ? 'selected="selected"' : '' )+'>role</option>'+
							'<option perspectiveid="'+value.id+'" value="public" '+((value.access && value.access=="public") ? 'selected="selected"' : '' )+'>public</option>'+
						'</select>'+
					'</td>'+
				'</tr>';
		});
		modalHtml += '</tbody></table></div>';
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
    var scrollState = getCorpusPerspectivesScrollState();
    
	var _data = {
			url: $.url(window.location.href).attr('query'),
			perspective_id : perspective_id,
			access : $('option[perspectiveid="'+$element.attr('perspectiveid')+'"]:selected').val(),
			operation_type : ($element.prop('checked') ? "add" : "remove")
		};

	var success = function(){
	    var action = $element.prop('checked') ? "add" : "remove";

		$element.closest("tr").toggleClass("inactive");
		if(action == "add"){
            $("#select_"+perspective_id).removeAttr("disabled");

        } else{
            $("#select_"+perspective_id).attr("disabled", true);
        }
		updatePerspectiveTable($element,($element.prop('checked') ? "add" : "remove"));
        restoreCorpusPerspectivesScrollState(scrollState);
	};

	var login = function(){
		setReportPerspective($element);
	};



	doAjaxSyncWithLogin("corpus_set_corpus_and_report_perspectives", _data, success, login);
}

function updateReportPerspective($element){
	if ($element.is("select")){
        var scrollState = getCorpusPerspectivesScrollState();
		var params = {
			url: $.url(window.location.href).attr('query'),
			perspective_id : $element.attr('perspectiveid'),
			access : $('option[perspectiveid="'+$element.attr('perspectiveid')+'"]:selected').val(),
			operation_type : "update"
		};

		var success = function(data){
			updatePerspectiveTable($element,"update");
            restoreCorpusPerspectivesScrollState(scrollState);
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
		$("#corpus_set_corpus_perspective_roles thead tr").append("<th perspective_id='"+perspective_id+"' title='"+title+"'>"+title+"</th>");
		$("#corpus_set_corpus_perspective_roles tbody tr").each(function(){
			var html="";
            var user_id = $(this).attr('id');
			if( access == "role"){
				html += "<td perspective_id='"+perspective_id+"' class='corpus-settings-perspectives-access-cell'>";
				html += corpusSettingsPerspectiveCheckbox("userReportPerspective", "user_id='"+user_id+"' perspective_id='"+perspective_id+"' value='1'", false);
				html += "</td>";
			}
			else{
				html += "<td perspective_id='"+perspective_id+"' class='corpus-settings-perspectives-access-cell'>";
				html += corpusSettingsPerspectiveAccessBadge(access);
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
                html += corpusSettingsPerspectiveCheckbox("userReportPerspective", "user_id='"+user_id+"' perspective_id='"+perspective_id+"' value='1'", false);
			}
			else{
				html += corpusSettingsPerspectiveAccessBadge(access);
			}
			$(this).find("td[perspective_id="+perspective_id+"]").html(html);
			$(this).find("td[perspective_id="+perspective_id+"]").removeClass("corpus-settings-perspectives-access-cell-active").addClass("corpus-settings-perspectives-access-cell");
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
                html += '<td><span class="corpus-settings-value corpus-settings-name">' + _data.desc_str + '</span></td>';
                html += '<td class="corpus-settings-actions-cell">' + $container.find('.hightlighted td:last').html() + '</td>';
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
    var currentAccess = $.trim($container.find('.hightlighted td:first').text());

    var select_text = '<label for="basicInfoAccess">Access:</label>'
    select_text += '<select id="basicInfoAccess" class = "form-control"><option value="0">restricted</option>'
    select_text += '<option value="1"'+(currentAccess == 'public' ? " selected " : "" )+ '>public</option></select>'

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
                    html += '<span class="corpus-settings-access corpus-settings-access-public">public</span>';
                } else{
                    html += '<span class="corpus-settings-access corpus-settings-access-restricted">restricted</span>';
                }

                html += '</td>';
                html += '<td class="corpus-settings-actions-cell">' +$container.find('.hightlighted td:last').html() + '</td>';

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

    $("#basicInfoOwnerSelect").html(get_users($.trim($container.find('.hightlighted td:first').text())));
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
            html += '<span class="corpus-settings-value corpus-settings-owner">' + $("#selectedUser option:selected").text() + '</span>';
            html += '</td>';
            html += '<td class="corpus-settings-actions-cell">' +$container.find('.hightlighted td:last').html() + '</td>';
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

    $( ".confirmDescription").unbind( "click" ).click(function() {
        var edit_id = $container.find('.hightlighted th:first').attr("id");
        var _data = 	{
            url: $.url(window.location.href).attr('query'),
            name_str : $("#elementName").val(),
            desc_str : $("#corpusDescription").val(),
            element_type : elementType,
            element_id : edit_id
        };

        var success = function(data){
            var html = '<th id="'+_data.element_id+'">'+$container.find('.hightlighted th:first').text()+'</th>';
            html += '<td>';
            html += '<span class="corpus-settings-value corpus-settings-description">' + _data.desc_str + '</span>';
            html += '</td>';
            html += '<td class="corpus-settings-actions-cell">' +$container.find('.hightlighted td:last').html() + '</td>';
            $container.find(".hightlighted:first").html(html);
        };

        var login = function(){
            edit($element);
        };
        var complete = function(){};

        doAjaxSync("corpus_update", _data, success, null, complete, null, login);
    });
}

function editBasicInfoCss($element){
    var $valueContainer = $("#cssValue");
    var elementType = $element.parent().attr("element");
    var parent = $element.parent().attr("parent");
    var $container = $("#"+parent);

    var css = $valueContainer.text();
    var input = '<textarea id="corpusCss" class="form-control" rows="4">' + css + '</textarea>';
    $("#corpusCssArea").html(input);

    $(".confirmCss").unbind( "click" ).click(function() {
        var edit_id = $container.find('.hightlighted th:first').attr("id");
        var _data = 	{
            url: $.url(window.location.href).attr('query'),
            name_str : $("#elementName").val(),
            desc_str : $("#corpusCss").val(),
            element_type : elementType,
            element_id : edit_id
        };

        var success = function(data){
            $valueContainer.html(_data.desc_str);
        };

        var login = function(){
            edit($element);
        };

        doAjaxSync("corpus_update", _data, success, null, null, null, login);
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
                    html += '<td class="corpus-settings-subcorpora-id">' + _data.element_id + '</td><td id="' + _data.element_id + '"><span class="corpus-settings-subcorpora-name">' + _data.name_str + '</span></td>';

                    html += '<td>';
                    html += '<span class="corpus-settings-subcorpora-description" title="' + _data.desc_str + '">' + _data.desc_str + '</span>';
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
                    '<td class="corpus-settings-subcorpora-id">' + data.last_id + '</td>' +
                    '<td id="' + data.last_id + '"><span class="corpus-settings-subcorpora-name">' + _data.name_str + '</span></td>' +
                    '<td><span class="corpus-settings-subcorpora-description" title="' + _data.desc_str + '">' + _data.desc_str + '</span></td>' +
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
                    '<td class="corpus-settings-flags-id">' + data.last_id + '</td>' +
                    '<td class="name"><span class="corpus-settings-flags-name">' + _data.name_str + '</span></td>' +
                    '<td class="short"><span class="corpus-settings-flags-short">' + _data.short_str + '</span></td>' +
                    '<td class="description"><span class="corpus-settings-flags-description" title="' + _data.desc_str + '">' + _data.desc_str + '</span></td>' +
                    '<td class="sort corpus-settings-flags-sort">' + _data.element_sort + '</td>' +
                    '</tr>'
                );
                $("#createFlag").modal("hide");
            };

            var login = function () {
                createFlag($element);
            };

            doAjaxSync("corpus_add_flag", _data, success, null, null, null, login);
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
                    $container.find(".hightlighted:first td.name").html('<span class="corpus-settings-flags-name">' + _data.name_str + '</span>');
                    $container.find(".hightlighted:first td.short").html('<span class="corpus-settings-flags-short">' + _data.short_str + '</span>');
                    $container.find(".hightlighted:first td.description").html('<span class="corpus-settings-flags-description" title="' + _data.desc_str + '">' + _data.desc_str + '</span>');
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


function deleteFlag(element){
    var parent = element.parent().attr("parent");
    var $container = $("#"+parent);

    var delete_html = '<table>'+
        '<label for="delete_name">Name:</label>'+
        '<p id = "delete_name">'+$container.find('.hightlighted td:first').next().text()+'</p>';

    $('#deleteContent').html(delete_html);
    $('#deleteModal').modal('show');

    $( ".confirmDelete" ).unbind( "click" ).click(function() {

        var _data = 	{
            url: $.url(window.location.href).attr('query'),
            element_id : $container.find('.hightlighted td:first').text()
        };

        var success = function(data){
            $container.find(".hightlighted:first").remove();
            $(".delete").hide();
            $(".edit").hide();
        };

        var login = function(){
            deleteFlag(element);
        };

        var complete = function(){
            $('#deleteModal').modal('hide');
        };

        doAjaxSync("flag_delete", _data, success, null, complete, null, login);
    });
}

function deleteSubcorpus(element){
    var parent = element.parent().attr("parent");
    var $container = $("#"+parent);

    var delete_html = '<table>'+
        '<label for="delete_name">Name:</label>'+
        '<p id = "delete_name">'+$container.find('.hightlighted td:first').next().text()+'</p>'+
        '<label for="delete_description">Description:</label>'+
        '<p id = "delete_description">'+$container.find('.hightlighted td:last').text()+'</p>';

    $('#deleteContent').html(delete_html);
    $('#deleteModal').modal('show');

    $( ".confirmDelete" ).unbind( "click" ).click(function() {

        var _data = 	{
            url: $.url(window.location.href).attr('query'),
            element_id : $container.find('.hightlighted td:first').text()
        };

        var success = function(data){
            $container.find(".hightlighted:first").remove();
            $(".delete").hide();
            $(".edit").hide();
        };

        var login = function(){
            deleteSubcorpus(element);
        };

        var complete = function(){
            $('#deleteModal').modal('hide');
        };

        doAjaxSync("subcorpus_delete", _data, success, null, complete, null, login);
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
                required: true
            },
            create_metadata_column_id: {
                required: true,
                regex: "^[a-zA-Z0-9$_]+$",
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
            },
            create_metadata_column_id: {
                required: "Field is required",
                remote: "This field name is already in use.",
                regex: "This field can only contain numbers, letters, $ or _"
            }
        }
    });

    var default_val_html;
    if($(".metadata_type").val() === "text"){
        default_val_html = "<input type = 'text' class = 'form-control create_text_default' placeholder='Default value'>"
    } else{
        default_val_html = "<select class = 'form-control select_create_default'>" +
                           "<option>-</option>" +
                           "</select>";
    }
    $("#create_default_options").html(default_val_html);

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
                    field: $("#create_metadata_column_id").val(),
                    comment: $("#create_metadata_comment").val(),
                    field_name: $("#create_metadata_field").val(),
                    type: $("#create_metadata_type").val(),
                    default: getSelectedDefaultValue('create')
                };

                var success = function (data) {
                    var tableRows = "";
                    tableRows +=
                        '<tr>' +
                        '<td><span class="corpus-settings-metadata-field">' + _data.field_name + '</span></td>' +
                        '<td><span class="corpus-settings-metadata-column-id">' + _data.field + '</span></td>' +
                        '<td><span class="corpus-settings-metadata-comment">' + ( _data.comment === "" ? "-" : _data.comment) + '</span></td>' +
                        '<td><span class="corpus-settings-metadata-type">' + _data.type + '</span></td>' +
                        '<td><span class="corpus-settings-metadata-default">' + ((_data.default === null || _data.default === "") ? "empty" : _data.default) + '</span></td>';

                    if ($("#create_metadata_type").val() === "enum") {
                        tableRows += '<td class = "text-center">' +
                            '<select class = "form-control select_create_default corpus-settings-metadata-values-select">' +
                            '<option>-values-</option>';
                        $.each(display_enum_values, function (ind, val) {
                            tableRows += '<option>' + val + '</option>'
                        });
                        tableRows += '</select></td></tr>';
                    } else {
                        tableRows += "<td class='text-center'><span class='corpus-settings-metadata-empty'>-</span></td></tr>"
                    }
                    $("#extListContainer > tbody").append(tableRows);
                };


                var complete = function (data) {
                    $('#create_metadata_modal').modal('hide');
                    $(".confirm_create_metadata").prop('disabled', false);
                    document.body.style.cursor='default';
                };

                $(".confirm_create_metadata").prop('disabled', true);
                document.body.style.cursor='wait';
                doAjax("corpus_edit_ext", _data, success, null, complete);
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
