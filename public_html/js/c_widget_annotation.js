/**
 * Klasa reprezentująca panel z danym zaznaczonej adnotacji. 
 */
function WidgetAnnotation(){
	var _widget = this; 

	// Obiekt klasy Annotation.
	this._annotation = null;
	
	// Element SPAN do manipulacji przy pomocy jQuery.
	this._annotationSpan = null;
	
	this._leftOffset = 0;
	this._rightOffset = 0;
	this._redoText = "";
	this._redoType = "";
	
	$("#annotation_redo").click(function(){
		_widget.redo();
		set_current_annotation(null);
	});
	
	$("#annotation_save").click(function(){
		_widget.save();
		set_current_annotation(null);
	});

	$("#annotation_delete").click(function(){
		_widget.delete();
		set_current_annotation(null);
	});

	$("#annotation_type").change(function(){
		if ( _widget._annotation != null ){
			_widget._annotation.setType($(this).val());
			_widget.updateButtons();
		}
	});
	
	this.updateButtons();
}

WidgetAnnotation.prototype._annotation = null;

/**
 * Obsługa przycisków.
 */
WidgetAnnotation.prototype.keyDown = function(e, isCtrl){
	
	if( (e.which == 37 || e.which == 39) && !isCtrl && this._annotation != null ){
		if ( e.which == 37 )
			this._leftOffset += this._annotation.extendLeft();
		else
			this._leftOffset += this._annotation.shrinkLeft();
		this.setText(this._annotation.getText());
		this.setLeftBorderOffset(this._leftOffset)
	}
	else
	if( (e.which == 37 || e.which == 39) && isCtrl && this._annotation != null ){
		if( e.which == 39 )
			this._rightOffset += this._annotation.extendRight();
		else
			this._rightOffset += this._annotation.shrinkRight();
		this.setText(this._annotation.getText());
		this.setRightBorderOffset(this._rightOffset)
	}

}

/**
 * Ustaw anotację do edycji.
 */
WidgetAnnotation.prototype.set = function(annotationSpan){

	// Wyczyść informacje potrzebne do cofnięcia zmian.
	if ( annotationSpan == null ){
		this.setText("-");
		this._leftOffset = 0;
		this._rightOffset = 0;	 
		this._annotationSpan = null;
	}
	else if ( this._annotationSpan != annotationSpan ){
		if ( this._annotationSpan != null ){
			//$(this._annotationSpan).toggleClass("selected");
			// Uaktualnij zaznaczenie w tabeli adnotacji.
			//$("#annotations tr[label="+$(this._annotationSpan).attr("id")+"]").toggleClass("selected");
		}
		
		this._annotationSpan = annotationSpan;
		
		if ( this._annotationSpan != null ){
			$(this._annotationSpan).toggleClass("selected");
			// Uaktualnij zaznaczenie w tabeli adnotacji.
			$("#annotations tr[label="+$(this._annotationSpan).attr("id")+"]").toggleClass("selected");
			// Zapamiętaj treść 
			this.setText($(this._annotationSpan).text());
			this._annotation = new Annotation(annotationSpan);
			this._redoType = this._annotation.type;
		}
	}
	
	if ( this._annotationSpan != null ){
		blockInsertion("zakończ edycję adnotacji");
		$("#annotation_type option").removeAttr("selected");
		$("#annotation_type option[value="+this._annotation.type+"]").attr("selected",true);
		$("#annotation_type").removeAttr("disabled");		
	}else{
		unblockInsertion();
		$("#annotation_type").attr("disabled", "true");
	}
	
	this.updateButtons();
}
 
WidgetAnnotation.prototype.get = function(){
	return this._annotationSpan;
}
 
WidgetAnnotation.prototype.setLeftBorderOffset =  function(val){
	$("#annotation_left").text((val>0 ? "+" :"") + val);	
}
 
WidgetAnnotation.prototype.setRightBorderOffset = function(val){
	$("#annotation_right").text((val>0 ? "+" :"") + val);	
}

WidgetAnnotation.prototype.setText = function(text){
	$("#annotation_text").text(text);
	this.updateButtons();
}

WidgetAnnotation.prototype.redo = function(){
	this._annotation.change(this._leftOffset, this._rightOffset);	
	this.setText(this._annotation.getText());
	this.setLeftBorderOffset(0);
	this.setRightBorderOffset(0);
	this._leftOffset = 0;
	this._rightOffset = 0;
	
	$("#annotation_type option[value="+this._redoType+"]").attr("selected",true);
	$("#annotation_redo_type").html("");
	this._annotation.setType(this._redoType);
	
	this.updateButtons();
}

WidgetAnnotation.prototype.save = function(){
	if ( this._annotation != null ){			
		
		var content_no_html = $("#content").html();

		var content_no_html = content_no_html = $.trim($("#content").html());
		// Remove containers with labels
		jqhtml = $(content_no_html);
		$(".label_container", jqhtml).remove();
		content_no_html = jqhtml.html();
		content_no_html = content_no_html.replace(/<span[^>]*class="[^"]*selected[^"]*"[^>]*>/i, "<span>");
		content_no_html = content_no_html.replace(/<span>(.*?)<\/span>/, fromDelimiter+"$1"+toDelimiter);
		content_no_html = html2txt(content_no_html);

		var from = content_no_html.indexOf(fromDelimiter);
		var to = content_no_html.indexOf(toDelimiter) - 3;

		content_no_html = content_no_html.replace(fromDelimiter, "");
		content_no_html = content_no_html.replace(toDelimiter, "");
				
		var text = content_no_html.substring(from, to+1);

		var report_id = $("#report_id").val();
		var annotation_id = this._annotation.id;
		var type = $("#annotation_type").val();
		
		status_processing("zapisywanie zmian ...");
		
		$.ajax({
			type: 	'POST',
			url: 	"index.php",
			data:	{ 	
						ajax: "report_update_annotation",
						annotation_id: annotation_id,
						report_id: report_id,						
						from: from,
						to: to,
						text: text,
						type: type
					},
			success:function(data){
						var type = "later";
						if (data['success']){
							console_add("anotacja <b> "+"an#"+annotation_id+":"+type+" </b> została zapisana");
						}else{
						    dialog_error(data['error']);
						    $("span#new").after($("span#new").html());
						    $("span#new").remove();
						}			
						//$("input.an").removeAttr("disabled"); // Odblokuj przyciski
						status_fade();
					  },
			error: function(request, textStatus, errorThrown){
					  dialog_error(request['responseText']);
					  status_fade();
					  },
			dataType:"json"
		});			
	}						
}

WidgetAnnotation.prototype.delete = function(){
	var annid = this._annotation.id;
	$.post("index.php", { ajax : "report_delete_annotation", annotation_id : this._annotation.id},
			function (data){						
				if (data['success']){
					var parent = jQuery("#an"+annid).parent("span");
					var annotation_node = jQuery("#an"+annid); 					
					annotation_node.replaceWith(annotation_node.html());
					if (parent)
						recreate_labels(parent);
					// Zapis się powiódł.
				}else{
					// Wystąpił problem podczas zapisu.			
					$("#dialog .message").html(data['error']);						
					$("#dialog").dialog( {
						bgiframe: true, 
						modal: true,
						width: data['wide'] ? "90%" : "300",
						buttons: {
							Ok: function() {
								$(this).dialog('close');
							}
						}
					} );
				}
			}, "json");
}

WidgetAnnotation.prototype.isChanged = function(){
	var isChange = false;
	if ( this._annotation ){
		isChange = isChange || $("#annotation_redo_text").text() != $("#annotation_text").text();
		
		if ($("#annotation_type").val() != this._redoType){
			$("#annotation_redo_type").html("<br/>Było: <i>"+this._redoType+"</i>");			
			isChange = true;
		}
		else
			$("#annotation_redo_type").html("");
	}
	else{
		$("#annotation_redo_type").html("");		
	}
	return isChange;
}

WidgetAnnotation.prototype.updateButtons = function(){
				
	if ( this.isChanged() ){
		$("#annotation_save").removeAttr("disabled");
		$("#annotation_redo").removeAttr("disabled");
	}else{
		$("#annotation_save").attr("disabled", "true");
		$("#annotation_redo").attr("disabled", "true");		
	}

	if (this._annotation ){
		$("#annotation_delete").removeAttr("disabled");		
	}else{
		$("#annotation_delete").attr("disabled", "true");		
	}

}