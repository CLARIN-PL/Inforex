var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

$(document).ready(function(){
    var error = getUrlParameter('error');

    var showError = function (selector, message) {
        var form = $(selector);
        form.prepend('<h5 class="error text-center">' + message + '</h5><hr>');
    };

    if(error){
        switch(error) {
            case 'login':
                showError('form#loginFormOidc', 'Invalid legacy login.');
                break;
            case 'email_duplicate':
                showError('form#newUserForm', 'Unable to create or link the selected account.');
                break;
            case 'email_empty':
                showError('form#newUserForm', 'Email and username cannot be empty.');
                break;
        }
    }
});
