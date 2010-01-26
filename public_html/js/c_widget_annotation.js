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
	});
	
	$("#annotation_save").click(function(){
		_widget.save();
		_widget.set(null);
	});
	

	$("#annotation_type").change(function(){
		if ( _widget._annotation != null ){
			_widget._annotation.setType($(this).val());
			_widget.updateButtons();
		}
	});
	
	this.updateButtons();
}

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
 * Ustawia zaznaczoną adnotację.
 */
WidgetAnnotation.prototype.set = function(annotationSpan){

	// Wyczyść informacje potrzebne do cofnięcia zmian.
	this.setRedoText("-");
	this.setText("-");
	this._leftOffset = 0;
	this._rightOffset = 0;	 
	
	if ( this._annotationSpan == annotationSpan ){
		$(this._annotationSpan).toggleClass("selected")
		$("#annotations tr[label="+$(this._annotationSpan).attr("id")+"]").toggleClass("selected");
		this._annotation = null;
		this._annotationSpan = null;
		this._redoType = "";
	}else{
		if ( this._annotationSpan != null ){
			$(this._annotationSpan).toggleClass("selected");
			// Uaktualnij zaznaczenie w tabeli adnotacji.
			$("#annotations tr[label="+$(this._annotationSpan).attr("id")+"]").toggleClass("selected");
		}
		
		this._annotationSpan = annotationSpan;
		
		if ( this._annotationSpan != null ){
			$(this._annotationSpan).toggleClass("selected");
			// Uaktualnij zaznaczenie w tabeli adnotacji.
			$("#annotations tr[label="+$(this._annotationSpan).attr("id")+"]").toggleClass("selected");
			// Zapamiętaj treść 
			this.setRedoText($(this._annotationSpan).text());
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

}
 
WidgetAnnotation.prototype.setLeftBorderOffset =  function(val){
	$("#annotation_left").text((val>0 ? "+" :"") + val);	
}
 
WidgetAnnotation.prototype.setRightBorderOffset = function(val){
	$("#annotation_right").text((val>0 ? "+" :"") + val);	
}

WidgetAnnotation.prototype.setRedoText = function(text){
	this._redoText = text;
	$("#annotation_redo_text").text(text);
	this.setLeftBorderOffset(text == "" ? "-" : 0);
	this.setRightBorderOffset(text == "" ? "-" : 0);
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
		
		var content = $("#content").html();			
		content = content.replace(/<small[^<]*<\/small>/gi, "");
		content = content.replace(/<\/span>/gi, "</an>");
		content = content.replace(/<span id="an[0-9]+" class="[^>]*" title="an#([0-9]+):([a-z_]+)">/gi, "<an#$1:$2>");
		content = content.replace(/<span title="an#([0-9]+):([a-z_]+)" class="[^>]*" id="an[0-9]+">/gi, "<an#$1:$2>");
		content = $.trim(content);
		
		var report_id = $("#report_id").val();
		
		var _widget = this;
		
		$.post("index.php", { ajax : "report_update_annotation", annotation_id : this._annotation.id, content : content, report_id : report_id},
				function (data){						
					if (data['success']){
						// Zapis się powiódł.
						_widget._leftOffset = 0;
						_widget._rightOffset = 0;
						_widget._redoType = _widget._annotation.type;
						_widget.setRedoText(_widget._annotation.getText());
						_widget.setText(_widget._annotation.getText());
						_widget.updateButtons();
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

}