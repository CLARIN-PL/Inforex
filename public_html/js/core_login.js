/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

/**
 * Set of functions used to handle dynamic log in.
 */

function login(){
	loginForm(true, null);	
}

/**
 * Display a login form.
 * @param reload -- if set true, after successful login the page will be reloaded
 * @param loginCallback(loggedin) -- function that will be executed after closing login form;
 *                                   if user has logged in the `loggedin` will be set true.
 * @return
 */
function loginForm(reload, loginCallback){
	$("body").append(''+
			'<div id="dialog-form-login" title="Login to Inforex" style="">'+
			'	<form>'+
			'	<fieldset style="border-width: 0px">'+
			'		<label for="username">Login:</label>'+
			'		<input type="text" name="username" id="username" class="text ui-widget-content ui-corner-all"/>'+
			'		<label for="password">Password:</label>'+
			'		<input type="password" name="password" id="password" value="" class="text ui-widget-content ui-corner-all"/>'+
			'	</fieldset>'+
			'	</form>'+
			'   <span style="color: red; margin-left: 70px" id="dialog-form-login-error"></span>'+	
			'</div>');

	$("#dialog-form-login").dialog({
		autoOpen: true,
		width: 280,
		modal: true,
		buttons: {
			'Login': function() {
				login_callback($(this), reload, loginCallback);
			},
			'Cancel': function() {
				if ( loginCallback != null )
					loginCallback(false);
				$(this).dialog('close');
			}
		},
		close: function() {
			$("#dialog-form-login").remove();
			
		}
	});	
	
	$("#password").keypress(function(event){
		if (event.keyCode==13)
			login_callback($(this), reload, loginCallback);
	});

	$("#dialog-form-login input[name=username]").focus();
}

/**
 * Obiekt okna dialogowego jQuery UI.
 * @param dialog
 * @param reload
 * @param loginCallback
 * @return
 */
function login_callback(dialog, reload, loginCallback){

	var username = $("#username").val();
	var password = $("#password").val();
	
	var params = {
		username: username,
		password: password		
	};
	
	var success = function(data){
		if (loginCallback != null)
			loginCallback(true);
		if (reload)
			window.location.reload();
		else{
			dialog.dialog('destroy');
			$("#dialog-form-login").remove();
		}
	};
	
	var error = function(error_code){
		if (error_code == "ERROR_AUTHORIZATION"){
			$("#dialog-form-login-error").html("Niepoprawny login i/lub hasło");
		}
	};
	
	doAjax("user_login", params, success, error);
}

$(function(){
	$("#login_link, .login_link").click(function(){
		login();
		return false;
	});
	$("#logout_link").click(function(){
		$.post("index.php", {logout: 1}, function(){ window.location = window.location; });
		return false;
	});
});
