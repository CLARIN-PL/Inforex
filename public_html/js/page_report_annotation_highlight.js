//Podświetla identyfikator i adnotację po najechaniu kursorem na adnotację.
$("#content span").live("mouseover", function(){
	$(this).addClass("hightlighted");	
});
	
//Usuwa podświetlenie identyfikator i adnotację po najechaniu kursorem na adnotację.
$("#content span").live("mouseout", function(){
	$(this).removeClass("hightlighted");	
});

// Podświetlanie elementów z tabeli
$(".an_row").live("mouseover", function(){
	var id = $(this).attr("label");
	$(this).addClass("hightlighted");
	$("#"+id).addClass("hightlighted");
	$("#"+id).prev("small").addClass("hightlighted");
});

$(".an_row").live("mouseout", function(){
	var id = $(this).attr("label");
	$(this).removeClass("hightlighted");
	$("#"+id).removeClass("hightlighted");
	$("#"+id).prev("small").removeClass("hightlighted");
});

