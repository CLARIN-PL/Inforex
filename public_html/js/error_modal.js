function generateErrorModal(error_heading, error_msg, error_code, errorCallback){
    $("#ajax_error_heading").html(error_heading || 'Something went wrong');
    $("#ajax_error_message_code").html(error_msg || '');
    if (error_msg) {
        $("#ajax_error_message_code").show();
        $(".ajax-error-modal-details-label").show();
    } else {
        $("#ajax_error_message_code").hide();
        $(".ajax-error-modal-details-label").hide();
    }
    $("#ajax_error_message_code").scrollTop(0);
    $("#ajax_error_modal").modal()
    if (errorCallback != null ){
        errorCallback();
    }
}

function copyAjaxErrorToClipboard(){
    var heading = $("#ajax_error_heading").text() || '';
    var details = $("#ajax_error_message_code").text() || '';
    var text = heading;
    if (details) {
        text += "\n\n" + details;
    }

    var onSuccess = function(){
        $("#ajax_error_copy_button").html('<i class="fa fa-check" aria-hidden="true"></i> Copied');
        window.setTimeout(function(){
            $("#ajax_error_copy_button").html('<i class="fa fa-clipboard" aria-hidden="true"></i> Copy details');
        }, 1500);
    };

    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(onSuccess);
        return;
    }

    var textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.setAttribute('readonly', '');
    textarea.style.position = 'absolute';
    textarea.style.left = '-9999px';
    document.body.appendChild(textarea);
    textarea.select();
    try {
        document.execCommand('copy');
        onSuccess();
    } finally {
        document.body.removeChild(textarea);
    }
}

$(function(){
    $(document).on('click', '#ajax_error_copy_button', function(){
        copyAjaxErrorToClipboard();
    });
});

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
