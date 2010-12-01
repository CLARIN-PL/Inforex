//Podświetla identyfikator i adnotację po najechaniu kursorem na adnotację.
$("#content span").live("mouseover", function(){
	$(this).addClass("hightlighted");
	$(this).children(".label_container").show();
	$(this).parents("span").children(".label_container").show();
});

$(".highlight span").live("mouseover", function(){
	$(this).addClass("hightlighted");
	$(this).children(".label_container").show();
	$(this).parents("span").children(".label_container").show();
});

//Usuwa podświetlenie identyfikator i adnotację po najechaniu kursorem na adnotację.
$("#content span").live("mouseout", function(){
	$(this).removeClass("hightlighted");	
	$(this).parents("span").children(".label_container").hide();
	$(this).children(".label_container").hide();
});

$("#.highlight span").live("mouseout", function(){
	$(this).removeClass("hightlighted");	
	$(this).parents("span").children(".label_container").hide();
	$(this).children(".label_container").hide();
});

//Podświetla identyfikator i adnotację po najechaniu kursorem na adnotację.
$("#content .annotation_label").live("mouseover", function(){
	var title = $(this).attr("title");
	$("[title='"+title+"']").addClass("hightlighted");
});
	
//Usuwa podświetlenie identyfikator i adnotację po najechaniu kursorem na adnotację.
$("#content .annotation_label").live("mouseout", function(){
	var title = $(this).attr("title");
	$("[title='"+title+"']").removeClass("hightlighted");
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

$(function(){
	$("#content span").each(function(index){
		if ($(this).parent().get(0).tagName != "SPAN"){
			//create_labels(this);
		}
	});
});

// Odtwarza strukturę labelek dla wskazanej anotacji, nawet zagnieżdżonej 
function recreate_labels(span){
//	if ($(span).parent().get(0).tagName != "SPAN"){
//		$(span).children(".label_container").remove();
//		create_labels(span);
//	}else
//		$(span).parents("span").each(function(index){
//			if ($(this).parent().get(0).tagName != "SPAN")
//			{
//				$(this).children(".label_container").remove();
//				create_labels(this);
//			}
//		});
}

function create_labels(top){
	var container = $('<div>').prependTo($(top));
	container.addClass("label_container");
	container.css("width", $(top).width() +"px" );
	container.css("top", $(top).height() + 2);
	height = create_sublabels(top, 0, 0, container);
	container.css("height", height +"px" );	
}

function create_sublabels(parent, left, level, container){
	var max_height = 0;

	var newDiv = $('<div>').prependTo($(container));
	newDiv.addClass("annotation_label");
	newDiv.append($(parent).attr("title").split(":")[1]);		
	newDiv.css("width", $(parent).width()-2 );
	newDiv.css("top", level * 12);
	newDiv.css("left", left);
	newDiv.attr("title", $(parent).attr("title"));
	

	$(parent).children("span").each(function(index){
		height = create_sublabels(this, left + $(this).position().left, level+1, container);
		max_height = Math.max(max_height, height);
	});
	
	return Math.max(max_height, (level+1)*12);
}