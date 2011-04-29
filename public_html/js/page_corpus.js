$(function(){
	$("#annotationSets").click(function(e){
		e.preventDefault();
		getAnnotationSets();
	});
	
	$(".setAnnotationSet").live("click",function(){
		setAnnotationSet($(this));
	});
});

function getAnnotationSets(){
	$.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : {
			ajax : "corpus_get_annotation_sets",
			corpus_id : $("#corpusId").text()
		},				
		success : function(data){
			ajaxErrorHandler(data,
				function(){	
					var dialogHtml = 
						'<div class="annotationSetsDialog">'+
							'<table class="tablesorter">'+
								'<thead>'+
									'<tr>'+
										'<th>id</th>'+
										'<th>description</th>'+
										'<th>count</th>'+
										'<th>assign</th>'+
									'</tr>'+
								'</thead>'+
								'<tbody>';
					$.each(data,function(index,value){
						dialogHtml += 
							'<tr>'+
								'<td>'+value.id+'</td>'+
								'<td>'+value.description+'</td>'+
								'<td>'+value.count_ann+'</td>'+
								'<td><input class="setAnnotationSet" type="checkbox" setid="'+value.id+'" '+(value.cid ? 'checked="checked"' : '')+'/></td>'+
							'</tr>';
					});
					dialogHtml += '</tbody></table></div>';
					var $dialogBox = $(dialogHtml).dialog({
						modal : true,
						height : 500,
						title : 'Assign annotation sets to corpus',
						buttons : {
							Close: function() {
								$dialogBox.dialog("close");
							}
						},
						close: function(event, ui) {
							$dialogBox.dialog("destroy").remove();
							$dialogBox = null;
						}
					});	
				},
				function(){
					getAnnotationSets();
				}
			);								
		}
	});		
}

function setAnnotationSet($element){
	log($element.attr('checked') ? "add" : "remove");
	$.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : {
			ajax : "corpus_set_annotation_sets_corpora",
			corpus_id : $("#corpusId").text(),
			annotation_set_id : $element.attr('setid'),
			operation_type :  ($element.attr('checked') ? "add" : "remove")
		},				
		success : function(data){
			ajaxErrorHandler(data,
				function(){	
				
				},
				function(){
					setAnnotationSet($element);
				}
			);								
		}
	});	

}