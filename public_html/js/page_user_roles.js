/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

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
});


function validate_password(pass1, pass2){
	return pass1 == pass2 ? true : false
}
