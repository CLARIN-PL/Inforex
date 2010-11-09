function console_add(text){
	$("#console").show();
	var n = $("#console dt").length;
	$("#console dl").prepend("<dt>"+n+":</dt><dd>"+text+"</dd>");
}