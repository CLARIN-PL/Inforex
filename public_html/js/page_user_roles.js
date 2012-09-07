$(document).ready(function(){
	$('.password_change').keyup(function() {
		var empty = false;
		$('.password_change').each(function() {
			if ($(this).val() == '') {
				empty = true;
			}
		});
		if(!validate_password($('.password_change[name=new_pass1]').val(), $('.password_change[name=new_pass2]').val())){
			empty = true;
			$('.password_change[name=new_pass1]').css("border" , "3px red double");
			$('.password_change[name=new_pass2]').css("border" , "3px red double");
		}

		if (empty) {
			$('.password_change[type=submit]').attr('disabled', 'disabled');
		} else {
			$('.password_change[type=submit]').removeAttr('disabled');
			$('.password_change[name=new_pass1]').css("border" , "3px green double");
			$('.password_change[name=new_pass2]').css("border" , "3px green double");
		}
	});

	$('.add_user').keyup(function() {
		var empty = false;
		$('.add_user').each(function() {
			if ($(this).val() == '') {
				empty = true;
			}
		});

		if (empty) {
			$('.add_user[type=submit]').attr('disabled', 'disabled');
		} else {
			$('.add_user[type=submit]').removeAttr('disabled');
		}
	});

	$(".option").click(function() {
		var option_element = $(this).attr("id");
		if ($("."+option_element).hasClass("show")){
			$("."+option_element).hide();
			$("."+option_element).removeClass("show");
		}
		else{
			$("."+option_element).show();
			$("."+option_element).addClass("show");
		}
		return false;
	});


	$("select.edit_user").change(function(){
		var value = $(this).val();
		var login = $("select.edit_user option[value="+value+"]").attr("login");
		var user_name = $("select.edit_user option[value="+value+"]").text();

		$(".edit_user[name=login]").attr("value", login);
		$(".edit_user[name=name]").attr("value", user_name);
		$('.edit_user[type=submit]').removeAttr('disabled');
	});
});


function validate_password(pass1, pass2){
	return pass1 == pass2 ? true : false
}
