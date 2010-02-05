function status_set(text){
	$("#status_icon").hide();
	$("#status_text").html(text);
	$("#status").show();	
}

function status_processing(text){
	$("#status_icon").show();
	$("#status_text").html(text);
	$("#status").show();		
}

function status_hide(text){
	$("#status").hide();	
}

function status_fade(){
	$("#status").fadeOut("10000", function(){$("#status").hide()});
}