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
    $("#ajax_access_error_modal").modal('hide');
    $("#ajax_error_modal").modal();
    if (errorCallback != null ){
        errorCallback();
    }
}

function copyAjaxErrorToClipboard(button){
    var $button = button ? $(button) : $('.ajax_error_modal.in:visible .ajax-error-copy-button').first();
    var $modal = $button.closest('.ajax_error_modal');
    // Read the dialog that was clicked, including roles for an access error.
    var text = $modal.find('.ajax-error-modal-card').text().trim();

    var onSuccess = function(){
        $button.html('<i class="fa fa-check" aria-hidden="true"></i> Copied');
        window.setTimeout(function(){
            $button.html('<i class="fa fa-clipboard" aria-hidden="true"></i> Copy details');
        }, 1500);
    };

    var fallback = function(){
        var textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.setAttribute('readonly', '');
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        // Bootstrap traps focus inside the modal; do not append outside it.
        $modal[0].appendChild(textarea);
        textarea.addEventListener('copy', function(event){ event.stopPropagation(); });
        textarea.focus();
        textarea.select();
        try {
            if (document.execCommand('copy')) {
                onSuccess();
            } else {
                $button.text('Copy failed');
            }
        } catch (error) {
            $button.text('Copy failed');
        } finally {
            textarea.parentNode.removeChild(textarea);
            $button.trigger('focus');
        }
    };
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(onSuccess, fallback);
    } else {
        fallback();
    }
}

$(function(){
    $(document).on('click', '.ajax-error-copy-button', function(event){
        event.preventDefault();
        event.stopPropagation();
        copyAjaxErrorToClipboard(this);
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

    $("#ajax_error_modal").modal('hide');
    $("#ajax_access_error_modal").modal();
}
