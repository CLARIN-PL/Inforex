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

	$(".password_change_a").click(function() {		
		if ($(".password_change_form").hasClass("show")){
			$(".password_change_form").hide();
			$(".password_change_form").removeClass("show");
		}
		else{
			$(".password_change_form").show();
			$(".password_change_form").addClass("show");
		}
		return false;
	});
});


function validate_password(pass1, pass2){
	return pass1 == pass2 ? true : false
}
