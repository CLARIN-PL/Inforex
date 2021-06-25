/**
 * Part of the Inforex project
 * Copyright (c) 2020
 * Code licensed under the GNU LGPL http://www.gnu.org/licenses/
 * Wroclaw University of Science and Technology
 */
let user_id;

$(document).ready(function () {
    $('#usersTable').on("click", ".edit_user_button", function () {
        let tr = $(this).closest("tr");
        user_id = tr.find("td.id").text();
        Metro.dialog.open('#editUser')
        editUser(user_id);
    });
});

function passwordCheck(val){
    if(val ===""){
        return true;
    }else{
        if(val.length >=8){
            return true;
        }
    }
    return false;
}

function checkLoginExists(val) {
    let check = {
        mode: 'create',
        'login': val
    };
    let isGood = true;
    let success = function (resp) {
        isGood = !resp;
    }
    doAjaxSync("user_validation", check, success)
    return isGood;
}

function checkLoginWithIdExists(val){
    let check = {
        mode: 'edit',
        login: val,
        id: user_id
    };
    let isGood = true;
    let success = function (resp) {
        isGood = !resp;
    }
    doAjaxSync("user_validation", check, success)
    return isGood;
}

function onCreateUserSubmit() {
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
        let user_html = `<tr>
                    <td class="id">${_data.id}</td>
                    <td>${login}</td>
                    <td>${username}</td>
                    <td>${email}</td>
                    <td></td>
                    <td><a href="#" class="edit_user_button" data-toggle="modal" data-target="#edit_user_modal">
                            <button class="button"><span class="mif-pencil"></span></button>
                        </a>
                    </td>
                    </tr>`;
        $("#usersTableBody").prepend(user_html);
        $("#create_user_form").get(0).reset();
        Metro.dialog.close("#createNewUser");
    };

    doAjaxSync("user_add", data, success);
}

function onEditUserSubmit() {
        let user_id = $("#user_id").val();
        let login = $("#edit_user_login").val();
        let username = $("#edit_user_username").val();
        let email = $("#edit_user_email").val();
        let password = $("#edit_user_password").val();
        let roles = [];
        let roles_string = "";

        $.each($('[data-cls-checkbox="roles_checkbox"]'), function (index, value) {
            if ($(value).prop("checked")) {
                let role_value = $(value).val();
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
            $("#usersTable tbody tr td.id").each(
                function(){
                    if ($(this).text() === user_id){
                        $(this).parent().find(".login").html(login);
                        $(this).parent().find(".screename").html(username);
                        $(this).parent().find(".email").html(email);
                        $(this).parent().find(".user_roles").html(roles_string);
                    }
                }
            );
            Metro.dialog.close("#editUser");
        };
        doAjaxSync("user_edit", data, success);
}

function editUser(user_id) {
    let roles = null;
    doAjaxSync("roles_get", {}, function (data) {
        roles = data;
    });
    let success = function (data) {
        let user = data;

        let rolesForm = '';
        for (let i = 0; i < roles.length; i++) {
            let checked = $.inArray(roles[i].role, user.roles) > -1 ? "checked" : "";

            rolesForm += `<input data-cls-checkbox="roles_checkbox" type="checkbox"  
                                 data-role="checkbox" name="roles[]" 
                                 data-caption="${roles[i].description}"
                                 value="${roles[i].role}" ${checked} />`;
        }

        $(".roles").html(rolesForm);
        $("#edit_user_login").val(data.login);
        $("#user_id").val(data.user_id);
        $("#edit_user_username").val(data.screename);
        $("#edit_user_email").val(data.email);

    };
    let login = function () {
        editUser(user_id);
    };
    doAjaxSyncWithLogin("user_get", {user_id: user_id}, success, login);
}