/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(document).ready(function(){
    $('form.search-form').submit(false);

    $("input[name=search]").keyup(function () {
		var data = this.value.toLowerCase();
		var table = $("#usersTable");
		$(table).find("tbody tr").each(function (index, row) {
			var text = $(row).text().toLowerCase();
			if (text.indexOf(data) >= 0 || this.value == "") {
				$(this).show();
			} else {
				$(this).hide();
			}
		});
	});

	$('.add_user_button').click(function() {
		user_add();
	});

	$('#usersTable').on("click", ".edit_user_button", function() {
		var tr = $(this).closest("tr");
		var id = tr.find("td.id").text();
		user_edit(id, tr);
	});
});

function user_add(){

    $( "#create_user_form" ).validate({
        rules: {
            login: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'user_validation',
                        mode: 'create'
                    }
                }
            },
            name: {
                required: true
            },
            email: {
                required: true,
				email: true
            },
            password: {
                required: true
            }
        },
        messages: {
            login: {
                required: "Login is required.",
				remote: "This username is taken"
            },
            name: {
                required: "Name is required."
            },
            email: {
                required: "Email is required."
            },
            password: {
                required: "Password is required."
            }
        }
    });


    $( ".confirm_create_user" ).unbind( "click" ).click(function() {
        if($('#create_user_form').valid()) {
            var login = $("#create_user_login").val();
            var username = $("#create_user_username").val();
            var email = $("#create_user_email").val();
            var password = $("#create_user_password").val();

            var data = {
                'login': login,
                'name': username,
                'email' : email,
                'password': password
            };

            var success = function(_data){
                var user_html = '<tr>'+
                                    '<td style="color: grey; text-align: right" class="id">'+ _data.id +'</td>'+
                                    '<td class="login">'+login+'</td>' +
                                    '<td class="screename">'+username+'</td>' +
                                    '<td class="email">'+email+'</td>' +
                                    '<td class="email"></td>' +
                                    '<td></td>' +
                                    '<td><a href="#" class="edit_user_button" data-toggle="modal" data-target="#edit_user_modal"><button class = "btn btn-primary">Edit</button></a></td>'+
                                '</tr>';

                $("#usersTableBody").prepend(user_html);
                $('#create_user_modal').modal('hide');
            };

            doAjaxSync("user_add", data, success);
        }
    });
}


/**
 * Otwiera okno do edycji danych użytkownika o wskazanym identyfikatorze.
 * @param id
 */
function user_edit(user_id, tr){

    var roles = null;
    doAjaxSync("roles_get", {}, function(data){
        roles = data;
    });
    var success = function(data){
        var user = data;


        var rolesForm = '';
        for (var i = 0; i < roles.length; i++) {
            var checked = $.inArray(roles[i].role, user.roles) > -1 ? ' checked="checked"' : "";
            rolesForm += '<input class = "roles_checkbox" type="checkbox" name="roles[]" value="'+roles[i].role+'"'+checked+'/> ' + roles[i].description + "<br/>";
        }

        $(".roles").html(rolesForm);
        $("#edit_user_login").val(data.login);
        $("#user_id").val(data.user_id);
        $("#edit_user_username").val(data.screename);
        $("#edit_user_email").val(data.email);

        $("#edit_user_form" ).validate({
            rules: {
                login: {
                    required: true,
                    remote: {
                        url: "index.php",
                        type: "post",
                        data: {
                            ajax: 'user_validation',
                            mode: 'edit',
                            id: data.user_id
                        }
                    }
                },
                name: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                login: {
                    required: "Login is required.",
                    remote: "This username is taken"
                },
                name: {
                    required: "Name is required."
                },
                email: {
                    required: "Email is required."
                }
            }
        });

        $( ".confirm_edit_user" ).unbind( "click" ).click(function() {
            if ($('#edit_user_form').valid()) {
                var login = $("#edit_user_login").val();
                var username = $("#edit_user_username").val();
                var email = $("#edit_user_email").val();
                var password = $("#edit_user_password").val();
                var roles = [];
                var roles_string = "";

                $.each($(".roles_checkbox"), function(index, value){
                    if($(value).prop("checked")){
                        var role_value = $(value).val();
                        roles.push(role_value);
                        if(roles_string === ""){
                            roles_string += role_value;
                        } else{
                            roles_string += ", " + role_value;
                        }
                    }
                });

                var data = {
                    'user_id': user_id,
                    'login': login,
                    'name': username,
                    'email' : email,
                    'password': password,
                    'roles': roles
                };

                var success = function(){
                    $(tr).find(".login").html(login);
                    $(tr).find(".screename").html(username);
                    $(tr).find(".email").html(email);
                    $(tr).find(".user_roles").html(roles_string);

                    $('#edit_user_modal').modal('hide');
                };

                doAjaxSync("user_edit", data, success);
            }
        });

    };
    var login = function(){
        user_edit(user_id);
    };
    doAjaxSyncWithLogin("user_get", {user_id: user_id}, success, login);
}