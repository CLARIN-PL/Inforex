/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(document).ready(function(){
    $.fn.jquery

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
		user_add("", "", "", "");
	});

	$('.edit_user_button').on("click", function() {
		var tr = $(this).closest("tr");
		var id = tr.find("td.id").text();		
		user_edit(id);
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
            $("#create_user_form").submit();
            $('#create_user_modal').modal('hide');
        }
    });
}


/**
 * Otwiera okno do edycji danych użytkownika o wskazanym identyfikatorze.
 * @param id
 */
function user_edit(user_id){

    var roles = null;
    doAjaxSync("roles_get", {}, function(data){
        roles = data;
    });
    var success = function(data){
        var user = data;


        var rolesForm = '';
        for (var i = 0; i < roles.length; i++) {
            var checked = $.inArray(roles[i].role, user.roles) > -1 ? ' checked="checked"' : "";
            rolesForm += '<input type="checkbox" name="roles[]" value="'+roles[i].role+'"'+checked+'/> ' + roles[i].description + "<br/>";
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
                $("#edit_user_form").submit();
                $('#edit_user_modal').modal('hide');

            }
        });

    };
    var login = function(){
        user_edit(user_id);
    };
    doAjaxSyncWithLogin("user_get", {user_id: user_id}, success, login);
}