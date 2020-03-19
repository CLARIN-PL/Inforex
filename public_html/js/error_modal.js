function generateErrorModal(error_heading, error_msg, error_code, errorCallback){
    $("#ajax_error_heading").html(error_heading);
    $("#ajax_error_message_code").html(error_msg);
    if (error_msg) {
        $("#ajax_error_message_code").show();
    } else {
        $("#ajax_error_message_code").hide();
    }
    $("#ajax_error_modal").modal()
    if (errorCallback != null ){
        errorCallback();
    }
}

function generateAccessErrorModal(error_data){
    var error_message = error_data.error_msg.message;
    var rolesGranted = error_data.error_msg.rolesGranted;
    var rolesRequired = error_data.error_msg.rolesRequired;

    $("#ajax_error_message").html(error_message);

    var rolesGrantedHtml = "";
    rolesGranted.forEach(function(value){
        rolesGrantedHtml += '<button type="button" class="btn btn-success btn-xs" style="margin: 3px">'+value+'</button>';
    });

    var rolesRequiredHtml = "";
    rolesRequired.forEach(function(value){
        rolesRequiredHtml += '<button type="button" class="btn btn-danger btn-xs" style="margin: 3px">'+value+'</button>';
    });

    $("#ajax_roles_granted").html(rolesGrantedHtml);
    $("#ajax_roles_required").html(rolesRequiredHtml);

    $("#ajax_access_error_modal").modal()
}