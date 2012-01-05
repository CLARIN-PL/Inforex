$(function(){
	/*
	Obsługa tabeli z typami testów (po kliknięciu typ testu)
	*/
	$("tr.setGroup").click(function(){
		if ($(this).hasClass("showItem"))
			$(this).removeClass("showItem").nextUntil(".setGroup").hide();
		else  
			$(this).addClass("showItem").nextUntil(".setGroup").filter(".subsetGroup").show();
	});
});