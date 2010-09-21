function login(){
	$("body").append(''+
			'<div id="dialog-form-login" title="Login to Inforex" style="">'+
			'	<form>'+
			'	<fieldset style="border-width: 0px">'+
			'		<label for="username" style="float: left; width: 60px; text-align: right;margin-bottom: 5px; line-height: 1em">Login:</label>'+
			'		<input type="text" name="username" id="username" class="text ui-widget-content ui-corner-all" style="margin-bottom: 5px; background: #eee" />'+
			'		<label for="password" style="float: left; width: 60px; text-align: right; clear: left">Password:</label>'+
			'		<input type="password" name="password" id="password" value="" class="text ui-widget-content ui-corner-all" style="background: #eee" />'+
			'	</fieldset>'+
			'	</form>'+
			'   <span style="color: red; margin-left: 70px" id="dialog-form-login-error"></span>'+	
			'</div>');

	$("#dialog-form-login").dialog({
		autoOpen: true,
		width: 260,
		modal: true,
		buttons: {
			'Login': function() {
				login_callback();
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		},
		close: function() {
			$("#dialog-form-login").remove();
			
		}
	});	
	
	$("#password").keypress(function(event){
		if (event.keyCode==13)
			login_callback();
	});
}

function login_callback(){

	var username = $("#username").val();
	var password = $("#password").val();

	$.ajax({
			type: 	'POST',
			url: 	"index.php",
			data:	{ 	
						ajax: "user_login", 
						username: username,
						password: password
					},						
			success: function(data){
						if (data['success'])
							window.location.reload();
						else{
							var errorMsg = "Wprowadź login i hasło";
							if (data['error'] == -3) errorMsg = "Niepoprawny login i/lub hasło";
							$("#dialog-form-login-error").html(errorMsg);
						}
					},
			error: function(request, textStatus, errorThrown){						
						dialog_error("<b>HTML result:</b><br/>" + request.responseText);		
					},
			dataType:"json"						
	});
}

$(function(){
	$("#login_link").click(function(){
		login();
		return false;
	});
	$("#logout_link").click(function(){
		$.post("index.php", {logout: 1}, function(){ window.location = window.location; });
		return false;
	});
});
