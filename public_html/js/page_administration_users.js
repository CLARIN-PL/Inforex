/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */
$(document).ready(function () {

    $("#create_user_form").validate({
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
                required: "User name is required."
            },
            email: {
                required: "Email is required."
            },
            password: {
                required: "Password is required."
            }
        }
    });

    $(".confirm_create_user").unbind("click").click(function () {
        if ($('#create_user_form').valid()) {
            let login = $("#create_user_login").val();
            let username = $("#create_user_username").val();
            let email = $("#create_user_email").val();
            let password = $("#create_user_password").val();

            let data = {
                'login': login,
                'name': username,
                'email': email,
                'password': password
            };

            let success = function (_data) {
                let button_html = '<button class="button"><span class="mif-pencil"></span></button>'
                let link_html = '<a href="#" class="edit_user_button" data-toggle="modal" data-target="#edit_user_modal">' + button_html + '</a>';
                let user_html = '<tr>' +
                    '<td class="id">' + _data.id + '</td>' +
                    '<td>' + login + '</td>' +
                    '<td>' + username + '</td>' +
                    '<td>' + email + '</td>' +
                    '<td></td>' +
                    '<td>' + link_html + '</td>' +
                    '</tr>';
                $("#usersTableBody").prepend(user_html);
                Metro.dialog.close("#createNewUser");
            };

            doAjaxSync("user_add", data, success);
        }
    });

    $('#usersTable').on("click", ".edit_user_button", function () {
        let tr = $(this).closest("tr");
        let id = tr.find("td.id").text();
        Metro.dialog.open('#editUser')
        user_edit(id, tr);
    });
});

/**
 * Otwiera okno do edycji danych użytkownika o wskazanym identyfikatorze.
 * @param user_id
 * @param tr
 */
function user_edit(user_id, tr) {
    let roles = null;
    doAjaxSync("roles_get", {}, function (data) {
        roles = data;
    });
    let success = function (data) {
        let user = data;


        let rolesForm = '';
        for (let i = 0; i < roles.length; i++) {
            let checked = $.inArray(roles[i].role, user.roles) > -1 ? ' checked="checked"' : "";
            rolesForm += '<input class = "roles_checkbox" type="checkbox" name="roles[]" value="' + roles[i].role + '"' + checked + '/> ' + roles[i].description + "<br/>";
        }

        $(".roles").html(rolesForm);
        $("#edit_user_login").val(data.login);
        $("#user_id").val(data.user_id);
        $("#edit_user_username").val(data.screename);
        $("#edit_user_email").val(data.email);

        $("#edit_user_form").validate({
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

        $(".confirm_edit_user").unbind("click").click(function () {
            if ($('#edit_user_form').valid()) {
                let login = $("#edit_user_login").val();
                let username = $("#edit_user_username").val();
                let email = $("#edit_user_email").val();
                let password = $("#edit_user_password").val();
                let roles = [];
                let roles_string = "";

                $.each($(".roles_checkbox"), function (index, value) {
                    if ($(value).prop("checked")) {
                        var role_value = $(value).val();
                        roles.push(role_value);
                        if (roles_string === "") {
                            roles_string += role_value;
                        } else {
                            roles_string += ", " + role_value;
                        }
                    }
                });

                let data = {
                    'user_id': user_id,
                    'login': login,
                    'name': username,
                    'email': email,
                    'password': password,
                    'roles': roles
                };

                let success = function () {
                    $(tr).find(".login").html(login);
                    $(tr).find(".screename").html(username);
                    $(tr).find(".email").html(email);
                    $(tr).find(".user_roles").html(roles_string);

                    $('#edit_user_modal').modal('hide');
                };

                doAjaxSync("user_edit", data, success);
                Metro.dialog.close("#editUser");
            }
        });

    };
    let login = function () {
        user_edit(user_id);
    };
    doAjaxSyncWithLogin("user_get", {user_id: user_id}, success, login);
}