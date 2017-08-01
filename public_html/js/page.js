/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
	//Bootstrap-style errors for jQuery Validation plugin
    $.validator.setDefaults({
        errorElement: "span",
        errorClass: "help-block" +
        "",
        highlight: function (element, errorClass, validClass) {
            $(element).closest('.form-group').addClass('has-error');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        errorPlacement: function (error, element) {
            if (element.parent('.input-group').length || element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });

    //Changes the number of pages available in Datatables pagination
    // e.g. 1 ... 10 instead of 1,2,3,4,5 ... 10 when numbers_length = 3;
    //$.fn.DataTable.ext.pager.numbers_length = 5;

    //Resets fields on the bootstrap modals when they are closed
    $('.modal').on('hidden.bs.modal', function (e) {
        $(this)
            .find("input,textarea")
            .val('')
            .removeClass('error')
            .removeAttr('aria-invalid')
            .removeAttr('aria-describedby')
            .end()
            .find("input[type=checkbox], input[type=radio]")
            .prop("checked", "")
            .end()
            .find("aria-invalid.false")
            .attr("aria-invalid","false")
            .end()
            .find("#annotation_type_preview")
            .removeAttr("style")
            .end()
            .find("label.error")
            .remove()
            .end()
            .find(".has-error")
            .removeClass("has-error")
            .end()
            .find("span.help-block")
            .remove()
            .end();
    })


    $("#menu_page li").hover(function(){
		if (!$(this).hasClass("expanded")){
			$(this).addClass("expanded");
			$("#menu_page li").show();			
		}	
	});
	
	$("#menu_page").mouseleave(function(){
		$("#menu_page .expanded").removeClass("expanded");
		$("#menu_page li").hide();
		$("#menu_page li.active").show();					
	});

    $(".nav_corpus_pages > a").html($(".nav_corpus_pages li.active").text() + '<span class="caret"></span>');

    $("#compact-mode").click(function(){
		$("#page").toggleClass("compact");
		$.cookie("compact_mode", $("#page").hasClass("compact") ? "1" : "0");
		if ( autoreizeFitToScreen && typeof autoreizeFitToScreen === 'function' ) {
            autoreizeFitToScreen();
        }
	});
});