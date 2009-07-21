var isCtrl = false; 

$(document)
	.keyup(function (e) { 
		if(e.which == 17) 
			isCtrl=false; 
	})
	.keydown(function (e) { 
		if(e.which == 17) 
			isCtrl=true; 
		if(e.which == 83 && isCtrl == true) { 
			//run code for CTRL+S -- ie, save! return false; 
		}
		if(e.which == 75){
			window.location = $("#article_prev").attr("href");
		} 
		if(e.which == 76){
			window.location = $("#article_next").attr("href");
		}
		if(e.which == 83){
			$("#formating").click();
		}
		if(e.which == 65){
			$("#accept").click();
		}
	});
	