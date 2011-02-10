//alert("included!");
$(function(){
	$("tr.subsetGroup").click(function(){
		$subsetGroup = $(this);
		if ($subsetGroup.hasClass("showItem")){
			$subsetGroup.removeClass("showItem");
			$subsetGroup.nextUntil(".subsetGroup, .setGroup").hide();
			
		}
		else { 
			$subsetGroup.addClass("showItem");
			$subsetGroup.nextUntil(".subsetGroup, .setGroup").filter(".annotation_type").show();
		}
	});
	
	$("tr.setGroup").click(function(){
		$setGroup = $(this);
		if ($setGroup.hasClass("showItem")){
			$setGroup.removeClass("showItem");
			$setGroup.nextUntil(".setGroup").hide();
			
		}
		else { 
			$setGroup.addClass("showItem");
			$setGroup.nextUntil(".setGroup").filter(".subsetGroup").show();
		}
	});	
});