$(function(){
	$("#content").html("<img src='gfx/ajax.gif'/>");
	var content = $("#report_content").val();
	$.post("index.php", { ajax: "report_takipi", content: content},
	  function(data){
		$("#content").html(data['tagged']);
	  }, "json");			

});

$("#content span").live("mouseover", function(){
	$("#token code").html($(this).attr("label"));
});