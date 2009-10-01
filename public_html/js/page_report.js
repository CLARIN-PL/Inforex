var isCtrl = false; 

$(document)
	.keyup(function (e) { 
		if(e.which == 17) 
			isCtrl=false; 
	})
	.keydown(function (e) { 
		if(e.which == 17) 
			isCtrl=false; 
		if(e.which == 83 && isCtrl == true) { 
			//run code for CTRL+S -- ie, save! return false; 
		}
		if(e.which == 37 && isCtrl == true && $("#article_prev")){
			window.location = $("#article_prev").attr("href");
		} 
		if(e.which == 39 && isCtrl == true && $("#article_next")){
			window.location = $("#article_next").attr("href");
		}
//		if(e.which == 83 && isCtrl == true){
//			$("#formating").click();
//		}
//		if(e.which == 65){
//			$("#accept").click();
//		}
	});

//alert('a');

$(document).ready(function(){
	var input = $("#report_type");
	if (input){
		input.change(function(){
			var report_id = $("#report_id").val();
			var report_type = $("#report_type").val();
			$("#report_type").after("<img src='gfx/ajax.gif'/>");
			$.post("index.php", { ajax: "report_set_type", id: report_id, type: report_type },
			  function(data){
				$("#report_type + img").remove();
				$("#report_type").after("<span class='ajax_success'>zapisano</span>");
				$("#report_type + span").fadeOut("1000", function(){$("#report_type + span").remove()});				
			  }, "json");			
		});
	}
});

function insert_annotation(type){
	var obj = $("#edit").getSelection();
	var report_id = $("#report_id").val();	
    var currentScrollPosition = $("#edit").scrollTop();
	$("#edit").replaceSelection("<an:" + type + ">" + obj.text + "</an>");
    $("#edit").scrollTop(currentScrollPosition);
    var content = $("#edit").val();
    
//	$("#add_annotation_status").append("<img src='gfx/ajax.gif'/>");
//	$.post("index.php", { ajax: "report_add_annotation", id: report_id, content: content },
//	  function(data){
//		$("#add_annotation_status img").remove();
//		$("#add_annotation_status").append("<span class='ajax_success'>zapisano</span>");
//		$("#add_annotation_status span").fadeOut("1000", function(){$("#report_type + span").remove()});				
//	  }, "json");			
    
}

$(document).ready(function(){
	$(".autogrow").autogrow();
});

