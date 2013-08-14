/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

/**
 * Klasa reprezentująca panel z danym zaznaczonej adnotacji. 
 */
function WidgetAnnotation(){
	var _widget = this; 

	// Obiekt klasy Annotation.
	this._annotation = null;
	
	// Element SPAN do manipulacji przy pomocy jQuery.
	this._annotationSpan = null;
	
	/*this._leftOffset = 0;
	this._rightOffset = 0;
	this._redoText = "";
	this._redoType = "";*/
	
	$(".annotation_redo").click(function(){
		_widget.redo();
		set_current_annotation(null);
		cancel_relation();
	});
	
	$("#annotation_save").click(function(){
		_widget.save();
		set_current_annotation(null);
		cancel_relation();
	});

	$("#annotation_delete").click(function(e){
		//e.preventDefault();
		_widget.deleteAnnotation();
	});

	$("#annotation_type span[groupid]").live("click",function(){
		if ( _widget._annotation != null ){
			_widget._annotation.setType($(this).attr("class"));
			$(_widget._annotation.ann).attr("groupid",$(this).attr("groupid"));
			_widget.updateButtons();
			$("#annotation_redo_type").text($(this).attr("class"));
			$("#changeAnnotationType").trigger('click');
		}
	});
	
	$("#changeAnnotationType").click(function(){
		if ($(this).hasClass("closeChange")){
			$("#annotation_type").hide();
			$(this).removeClass("closeChange").text("(change)");
			$("#relationsPanel").show();
		}
		else {
			$("#annotation_type").show();
			$(this).addClass("closeChange").text("(close)");
			$("#relationsPanel").hide();
		}
	});	
	
	this.updateButtons();
}

WidgetAnnotation.prototype._annotation = null;

/**
 * Obsługa przycisków.
 */
WidgetAnnotation.prototype.keyDown = function(e, isCtrl, isShift){
	//log(e.which);
	if( (e.which == 37 || e.which == 39) && isCtrl && !isShift && this._annotationSpan != null ){
		if ( e.which == 37 )
			//this._leftOffset += 
			this._annotation.extendLeft();
		else
			//this._leftOffset += 
			this._annotation.shrinkLeft();
		this.setText(this._annotation.getText());
		//this.setLeftBorderOffset(this._leftOffset);
	}
	else
	if( (e.which == 37 || e.which == 39) && isCtrl && isShift && this._annotationSpan != null ){
		if( e.which == 39 )
			//this._rightOffset += 
			this._annotation.extendRight();
		else
			//this._rightOffset += 
			this._annotation.shrinkRight();
		this.setText(this._annotation.getText());
		//this.setRightBorderOffset(this._rightOffset);
	}

}

/**
 * Ustaw anotację do edycji.
 */
_contentBackup = "";
WidgetAnnotation.prototype.set = function(annotationSpan){
	_contentBackup = $("#content").html();
	_contentBackupLeft = $("#content > div").first().find("div.contentBox").html();
	_contentBackupRight = $("#content > div").first().next().find("div.contentBox").html();
	//log("set");
	// Wyczyść informacje potrzebne do cofnięcia zmian.11
	if ( annotationSpan == null ){
		this.setText("-");
		//this._leftOffset = 0;
		//this._rightOffset = 0;	 
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
			// Wczytaj dodatkowe atrybuty anotacji
			jQuery.ajax({
				async : false,
				url : "index.php",
				dataType : "json",
				type : "post",
				data : { ajax : "report_get_annotation_attributes", annotation_id : this._annotation.id },				
				success : function(data){
					$(".annotation_attribute").remove();					
					for (var i in data.attributes)
					{
						var attr = data.attributes[i];
						var input = "";
						if (attr.type == "enum"){
							for ( var v in attr.values )
								input = "<option><b>"+attr.values[v].value +"</b></option>";
							input = "<select>"+input+"</select>";
						}
						else if (attr.type == "radio"){
							for ( var v in attr.values )
							{
								var v = attr.values[v];
								if ( attr.value == v.value )
									input_op = "<input type='radio' name='"+attr.name+"' value='"+v.value+"' style='vertical-align: middle' checked='checked'><b>"+v.value +"</b> &mdash; "+v.description+"<br/>";
								else
									input_op = "<input type='radio' name='"+attr.name+"' value='"+v.value+"' style='vertical-align: middle'><b>"+v.value+"</b> &mdash; "+v.description+"<br/>";
								input = input + input_op;
							}
						}
						var row = "<tr class='annotation_attribute'><th style='text-align: right'>" + attr.name + ":</th><td>"+input+"</td></tr>";
						
						$("#widget_annotation_buttons").before(row);
					}
								
				}
			});
			
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

// TODO czy jeszcze potrzebne?
WidgetAnnotation.prototype.redo = function(){
	//$("#content").html(_contentBackup);
	$("#content > div").first().find("div.contentBox").html(_contentBackupLeft);
	$("#content > div").first().next().find("div.contentBox").html(_contentBackupRight);
	this.updateButtons();
}

WidgetAnnotation.prototype.save = function(){
	if ( this._annotation != null ){			
		var content_no_html = $.trim($("span.selected").parents("div.content").html());
		// Remove containers with labels
		jqhtml = $("<div>"+content_no_html+"</div>");
		$(".label_container", jqhtml).remove();
		$("span.selected", jqhtml).wrap("<xyz>");
		content_no_html = jqhtml.html();
		content_no_html = content_no_html.replace(/<sup.*?<\/sup>/gi, '');
		content_no_html = content_no_html.replace(/<xyz>(.*?)<\/xyz>/, fromDelimiter+"$1"+toDelimiter);						
		//content_no_html = html2txt(content_no_html);
		content_no_html = html_entity_decode(content_no_html);
		content_no_html = content_no_html.replace(/<\/?[^>]+>/gi, '');
		
		// Pobierz treść anotacji przed usunięciem białych znaków
		var from = content_no_html.indexOf(fromDelimiter) + fromDelimiter.length;
		var to = content_no_html.indexOf(toDelimiter);
		var text = content_no_html.substring(from, to);

		// Oblicz właściwe indeksy
		content_no_html = content_no_html.replace(/\s/g, '');
		from = content_no_html.indexOf(fromDelimiter);
		to = content_no_html.indexOf(toDelimiter) - fromDelimiter.length - 1;
		
		var report_id = $("#report_id").val();
		var annotation_id = this._annotation.id;
		var type = $("#annotation_redo_type").text();
		
		status_processing("zapisywanie zmian ...");
		
		attributes = '';
		$(".annotation_attribute :checked").each(function(i){
			attributes = attributes + $(this).attr("name") + "=" + $(this).attr("value") + "\n";
		});
		//set_sentences();
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
						type: type,
						attributes : attributes
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

WidgetAnnotation.prototype.deleteAnnotation = function(){
	deleteAnnotation(this._annotation.id);
};

function deleteAnnotation(annotationId){
	annid = annotationId;;
	var $annContainer = $("div.content #an"+annotationId).parents("div.content");
	$dialogBox = 
		$('<div class="deleteDialog annotations">Are you sure to delete the annotation?</div>')
		.dialog({
			modal : true,
			title : 'Delete annotation',
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					jQuery.ajax({
						async : false,
						url : "index.php",
						dataType : "json",
						type : "post",
						data : { 
							ajax : "report_delete_annotation", 
							annotation_id : annid
						},				
						success : function(data){
							ajaxErrorHandler(data,
								function(){
									deleteAnnotationsRels(annid);
									//var parent = jQuery("#an"+annid).parent("span");
									//var annotation_node = jQuery("#an"+annid); 					
									var annotation_node = $annContainer.find("#an"+annid);
									var parent = annotation_node.parent("span");
									annotation_node.replaceWith(annotation_node.html());
									//cancelEvent();
									//$('#eventTable a[eventid="'+eventId+'"]').parent().parent().remove();
									$("#annotationList td.deleteAnnotation[annotation_id='"+annid+"']").parent().remove();
									$dialogBox.dialog("close");
									set_current_annotation(null);									
									cancel_relation();									
								},
								function(){
									$dialogBox.dialog("close");
									this.deleteAnnotation();
									//deleteEvent();
								}
							);								
						}
					});	
				
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}
	});	
}

function deleteAnnotationsRels(annid){
	$.each($("#an" + annid).nextUntil("span"), function(){
		var rel_title = $(this).attr("title");
		var rel_target = $(this).attr("target");
		var old_relin_title = $("#an" + rel_target).prev().attr("title");
		var new_relin_title = old_relin_title ? old_relin_title.replace(rel_title, "") : ""; 
		if(new_relin_title && $.trim(new_relin_title) == ""){
			$("#an" + rel_target).prev().remove();
		}
		else{
			$("#an" + rel_target).prev().attr("title", new_relin_title);
		}
		$(this).remove();
		
	});
	if($("#an" + annid).prev("sup.relin")){
		$.each($("sup.rel"), function(){
			if($(this).attr("target") == annid){
				$(this).remove();
			}
		});
		$("#an" + annid).prev("sup.relin").remove();
	}
}

WidgetAnnotation.prototype.isChanged = function(){
	var isChange = false;
	if ( this._annotation ){
		isChange = isChange || $("#annotation_redo_text").text() != $("#annotation_text").text();		
		$("#annotation_redo_type").html(this._redoType);			
		isChange = true;
	}
	else{
		$("#annotation_redo_type").html("");		
	}
	return isChange;
};

WidgetAnnotation.prototype.updateButtons = function(){
				
	if ( this.isChanged() ){
		$("#annotation_save").removeAttr("disabled");
		$("#annotation_redo").removeAttr("disabled");
	}else{
		$("#annotation_save").attr("disabled", "true");
		$("#annotation_redo").attr("disabled", "true");		
	}
};
