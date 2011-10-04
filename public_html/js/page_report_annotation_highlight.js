$("#content span").live("mouseover", function(){
	$(this).addClass("hightlighted");
	return $(this).hasClass("token");
}).live("mouseout", function(){
	$("#content span.hightlighted").removeClass("hightlighted");	
});

// Podświetlanie elementów z tabeli
$(".an_row").live("mouseover", function(){
	var id = $(this).attr("label");
	$(this).addClass("hightlighted");
	$("#"+id).addClass("hightlighted");
	$("#"+id).prev("small").addClass("hightlighted");
}).live("mouseout", function(){
	var id = $(this).attr("label");
	$(this).removeClass("hightlighted");
	$("#"+id).removeClass("hightlighted");
	$("#"+id).prev("small").removeClass("hightlighted");
});;
