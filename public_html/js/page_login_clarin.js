// https://stackoverflow.com/questions/19491336/get-url-parameter-jquery-or-how-to-get-query-string-values-in-js
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

    var showLoginErrorMsg = function () {
        var form = $('form#loginFormClarin');
        form.prepend('<h5 class="error text-center">Invalid login.</h5><hr>');
    };

    var showEmailDuplicateErrorMsg = function () {
        var form = $('form#newUserForm');
        form.prepend('<h5 class="error text-center">Email already exists.</h5><hr>');
    };

    var showEmailEmptyErrorMsg = function () {
        var form = $('form#newUserForm');
        form.prepend('<h5 class="error text-center">Email and username cannot be empty.</h5><hr>');
    };

    if(error){
        // alert(error);
        switch(error) {
            case 'login':
                showLoginErrorMsg();
                break;
            case 'email_duplicate':
                showEmailDuplicateErrorMsg();
                break;
            case 'email_empty':
                showEmailEmptyErrorMsg();
                break;
        }
    }
});


