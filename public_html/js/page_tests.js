$(function(){

	$(".tests_items").hide();
	$("#tests_document_list").hide();
	/*
	Obsługa tabeli z typami testów (po kliknięciu typ testu wyświetlana jest lista dokumentów dla danego testu)
	*/
	$("tr.group").click(function(){
		$("#tests_document_list").show();
		var test_id = $(this).attr('id');
		var test_name = $(this).children(".test_name").text()
		$(".tests_items").hide();
		$("." + test_id).show();
		$(".tests_errors").hide();		
		$(".result_test_name").html(test_name + ":");
	});
	
	/*
	Obsługa tabeli z wynikami testów (po kliknięciu w pole testu wyświetlana jest lista naruszeń dla danego testu)
	*/
	$("tr.tests_items").click(function(){
		if ($(this).hasClass("showItem"))
			$(this).removeClass("showItem").nextUntil(".tests_items").hide();
		else  
			$(this).addClass("showItem").nextUntil(".tests_items").filter(".tests_errors").show();				
	});
	
});