/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(document).ready(function(){
	$('.password_change').keyup(function() {
		var empty = false;
		var $form = $(this).closest(".password_change_form");
		var $passwordFields = $form.find('input.password_change[type="password"]');
		var $newPass1 = $form.find('input.password_change[name=new_pass1]');
		var $newPass2 = $form.find('input.password_change[name=new_pass2]');
		var $submit = $form.find('input.password_change[type=submit]');

		$passwordFields.each(function() {
			if ($(this).val() == '') {
				empty = true;
			}
		});
		if(!validate_password($newPass1.val(), $newPass2.val())){
			empty = true;
			$newPass1.removeClass("is-valid").addClass("is-invalid");
			$newPass2.removeClass("is-valid").addClass("is-invalid");
		}

		if (empty) {
			$submit.attr('disabled', 'disabled');
		} else {
			$submit.removeAttr('disabled');
			$newPass1.removeClass("is-invalid").addClass("is-valid");
			$newPass2.removeClass("is-invalid").addClass("is-valid");
		}
	});

	$("#password_change_modal").on("hidden.bs.modal", function() {
		var $form = $(this).find(".password_change_form");
		$form.find('input.password_change[type="password"]').val("").removeClass("is-valid is-invalid");
		$form.find('input.password_change[type=submit]').attr('disabled', 'disabled');
	});

    $("#corpora_filter").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#corpora_table tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});


function validate_password(pass1, pass2){
	return pass1 == pass2 ? true : false
}
