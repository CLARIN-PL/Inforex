$(function(){

	$(".tests_items").hide();
	$("#tests_document_list").hide();
	/*
	Obsługa tabeli z typami testów (po kliknięciu typ testu wyświetlana jest lista dokumentów dla danego testu)
	*/
	$("tr.group").click(function(){
		$("#tests_document_list").show();
		var test_id = $(this).attr('id');
		$(".tests_items").hide();
		$("." + test_id).show();		
	});
	
});