/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

/**
 * Klasa reprezentująca panel z danym zaznaczonej adnotacji.
 * @param selector
 */
function WidgetAnnotation(selector, callbackClose){
	var _widget = this;

	this.box = $(selector);

	// Callback dla zamknięcia edytora
	var callbackClose = callbackClose;

	// Obiekt klasy Annotation.
	this._annotation = null;
	
	// Element SPAN do manipulacji przy pomocy jQuery.
	this._annotationSpan = null;

	$(".annotation_redo").click(function(){
		_widget.redo();
		callbackClose();
	});

	$("#annotation_save").click(function(){
		_widget.save();
        callbackClose();
	});

	$("#annotation_delete").click(function(e){
		_widget.deleteAnnotation();
        callbackClose();
	});

	$("#annotation_type span[groupid]").on("click",function(){
		if ( _widget._annotation != null ){
			_widget._annotation.setType($(this).attr("class"));
			$(_widget._annotation.ann).attr("groupid",$(this).attr("groupid"));
			_widget.updateButtons();
			$("#annotation_redo_type").text($(this).attr("class"));
			$("#changeAnnotationType").trigger('click');
		}
	});

    $("#changeAnnotationType").popover({title: '<b>Change annotation type</b>',
        content: _widget.getAnnotationTypeTree(),
        html: true, placement : 'left'}).data("bs.popover").tip().addClass('annotation-type-tree annotations');

    $("#changeAnnotationType").on('shown.bs.popover', function(){
        $(".annotation-type-tree a.an").click(function(){
            var annotationType = $(this).attr("value");
			var annotationTypeId = $(this).attr("annotation_type_id");
            _widget.setType(annotationTypeId, annotationType);
            $("#changeAnnotationType").click();
        });
	});

	this.updateButtons();
}

WidgetAnnotation.prototype._annotation = null;

WidgetAnnotation.prototype.getAnnotationTypeTree = function() {
    var $annTypeClone = $("#annotation-types .tree").clone();
    $annTypeClone.find(".short_all").remove();
    $annTypeClone.find("div.icons").remove();
    return $annTypeClone.html();
}


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
 * Przechowuje oryginalny obiekt przed anotacją.
 */
_contentBackup = "";

/**
 * Ustaw anotację do edycji.
 */
WidgetAnnotation.prototype.set = function(annotationSpan){
	_contentBackup = $("#content").html();
	_contentBackupLeft = $("#content > div").first().find("div.contentBox").html();
	_contentBackupRight = $("#content > div").first().next().find("div.contentBox").html();
	widget = this;
	var parent = this;

    if ( $("#changeAnnotationType").next('div.popover').length > 0 ){
        $("#changeAnnotationType").click();
	}

	/* Ustawienie zaznaczenia aktualnej anotacji */
    $("#content span.selected").removeClass("selected");
    $(annotationSpan).addClass("selected");

    // Wyczyść informacje potrzebne do cofnięcia zmian.
	if ( annotationSpan == null ){
		this.setText("-");
		this._annotationSpan = null;
	}
	else if ( this._annotationSpan != annotationSpan ){
        parent.box.LoadingOverlay("show");
        parent.box.LoadingOverlay("show");
        parent.box.LoadingOverlay("show");
		parent.box.LoadingOverlay("show");
		this._annotationSpan = annotationSpan;
		
		if ( this._annotationSpan != null ){
			// Uaktualnij zaznaczenie w tabeli adnotacji.
			$("#annotations tr[label="+$(this._annotationSpan).attr("id")+"]").toggleClass("selected");
            var tt = $(this._annotationSpan);
            tt.find("sup").remove();

			// Zapamiętaj treść
			this._annotation = new Annotation(annotationSpan);
			this.setText(tt.text());
			this.setId(this._annotation.id);
			this.setType(this._annotation.type);
			this._redoType = this._annotation.type;
			// Wczytaj dodatkowe atrybuty anotacji
			var params = {
				annotation_id : this._annotation.id
			};
			var success = function(data){
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
                parent.box.LoadingOverlay("hide");
			};
			doAjax("report_get_annotation_attributes", params, success);
		
			// Pobierz i ustaw lemat anotacji
			doAjax("annotation_lemma_get", {annotation_id: this._annotation.id}, function(data){
				widget.setLemma(data.lemma);
                parent.box.LoadingOverlay("hide");
			});

			// Pobierz i ustaw lemat anotacji
			doAjax("annotation_type_get", {annotation_id: this._annotation.id}, function(data){
				widget.setType(data.id, data.type);
				widget.setTypeOrg(data.type);
				parent.box.LoadingOverlay("hide");
			});

			var params2 = {
				annotation_id : wAnnotationDetails._annotation.id
			};
			
			var success2 = function(data){
				$("#shared_attribute").empty();
				html = "<table>";
				for (var shared_attribute_id in data){
					shared_attribute = data[shared_attribute_id];
					html += "<tr>";
					html += "<td>" + shared_attribute.name + " : </td>";
					if (shared_attribute.type == "enum"){
						html += '<td><select class="shared_attribute" name="shared_' + shared_attribute_id + '">';
						html += '<option></option>';
						for (var val_id in shared_attribute.possible_values){							
							pos_val = shared_attribute.possible_values[val_id];
							if (pos_val == shared_attribute.value)
								html += '<option value="' + pos_val + '" selected="selected">';
							else 
								html += '<option value="' + pos_val + '">';
							html += pos_val + '</option>';
						}
						html += '</select></td>';
					}
					else {
						html += '<td><input type="text" class="shared_attribute" name="shared_' + shared_attribute_id + '" value="' + (shared_attribute.value ? shared_attribute.value : "") + '"></td>';
					}
					html += "</tr>";
				}
				html += "</table>";
				$("#shared_attribute").html(html);
                parent.box.LoadingOverlay("hide");
			};
			
			doAjax("annotation_get_shared_attribute_types_values", params2, success2);
		}
	}
	
	if ( this._annotationSpan != null ){
		$("#annotation_type option").removeAttr("selected");
		//$("#annotation_type option[value="+this._annotation.type+"]").attr("selected",true);
		$("#annotation_type").removeAttr("disabled");
	}else{
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

WidgetAnnotation.prototype.setType = function(id, type){
    $("#annotation-details #annotation_redo_type").text(type);
	$("#annotation-details #annotation_redo_type_id").val(id);
    this.updateButtons();
}

WidgetAnnotation.prototype.setTypeOrg = function(type){
	this._orgAnnotationType = type;
}

WidgetAnnotation.prototype.getTypeOrg = function(){
	return this._orgAnnotationType;
}

WidgetAnnotation.prototype.setId = function(annotation_id){
	$("#annotation_id").text(annotation_id);
}

WidgetAnnotation.prototype.setLemma = function(lemma){
	$("#annotation_lemma").val(lemma);
}

// TODO czy jeszcze potrzebne?
WidgetAnnotation.prototype.redo = function(){
	$(this._annotationSpan).removeClass("selected");
	$(this._annotationSpan).removeClass("hightlight");
	this.updateButtons();
}

/**
 * Zapisuje w systemie stan anotacji na podstawie danych w formularzu.
 */
WidgetAnnotation.prototype.save = function(){
	if ( this._annotation != null ){
		var parent = this;
		var content_no_html = $.trim($("span.selected").parents("div.content").html());
		// Remove containers with labels
		jqhtml = $("<div>"+content_no_html+"</div>");
		$(".label_container", jqhtml).remove();
		$("span.selected", jqhtml).wrap("<xyz>");
		content_no_html = jqhtml.html();
		content_no_html = content_no_html.replace(/<sup.*?<\/sup>/gi, '');
		content_no_html = content_no_html.replace(/<xyz>(.*?)<\/xyz>/, fromDelimiter+"$1"+toDelimiter);
        content_no_html = content_no_html.replace(/<\/?[^>]+>/gi, '');
		content_no_html = html_entity_decode(content_no_html);

		// Pobierz treść anotacji przed usunięciem białych znaków
		var from = content_no_html.indexOf(fromDelimiter) + fromDelimiter.length;
		var to = content_no_html.indexOf(toDelimiter);
		var text = content_no_html.substring(from, to);

		// Oblicz właściwe indeksy
		content_no_html = content_no_html.replace(/\s/g, '');
		from = content_no_html.indexOf(fromDelimiter);
		to = content_no_html.indexOf(toDelimiter) - fromDelimiter.length - 1;

        var annotation = this._annotation;
		var report_id = $("#report_id").val();
		var annotation_id = this._annotation.id;
		var type_id = $("#annotation_redo_type_id").val();
		var type = $("#annotation_redo_type").text();
		var lemma = $("#annotation_lemma").val();
		var orgType = this.getTypeOrg();
		
		status_processing("zapisywanie zmian ...");
		
		attributes = '';
		$(".annotation_attribute :checked").each(function(i){
			attributes = attributes + $(this).attr("name") + "=" + $(this).attr("value") + "\n";
		});
		
		shared_attributes = {};
		
		$(".shared_attribute").each(function(i){
			shared_attributes[$(this).attr("name").substring(7)] = $(this).val();
		});
		
		var params = { 	
			annotation_id: annotation_id,
			report_id: report_id,						
			from: from,
			to: to,
			text: text,
			type_id: type_id,
			attributes : attributes,
			shared_attributes : shared_attributes,
			lemma : lemma
		};
		
		var success = function(data){
			console_add("Annotation <b>" + "[an#" + annotation_id + "]:" + text + " </b> was saved");
			$(parent._annotationSpan).removeClass(orgType);
			$(parent._annotationSpan).removeClass("selected");
			$(parent._annotationSpan).removeClass("hightlight");
            $(parent._annotationSpan).addClass(type);
            parent._annotationSpan = null;
			status_fade();
		};
		
		doAjax('report_update_annotation',params,success,status_fade);
	}						
}

WidgetAnnotation.prototype.deleteAnnotation = function(){
	deleteAnnotation(this._annotation.id);
};

function deleteAnnotation(annotationId){
	annid = annotationId;
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
					var params = {
						annotation_id : annid
					};
					var success = function(data){
						if ( data['error'] ){
							$dialogBoxError = $('<div>' + data['error'] + '</div>').dialog({
								title : 'Error',
								buttons : {
									Ok : function(){
										$dialogBox.dialog("close");
										$dialogBoxError.dialog("close");
									}
								}
							});
						}
						else{
							deleteAnnotationsRels(annid);
							var annotation_node = $annContainer.find("#an"+annid);
							var parent = annotation_node.parent("span");
							annotation_node.replaceWith(annotation_node.html());
							$("#annotationList td.deleteAnnotation[annotation_id='"+annid+"']").parent().remove();
							$dialogBox.dialog("close");
							//set_current_annotation(null);
							//cancel_relation();
						}
					};
					var login = function(){
						this.deleteAnnotation();
					};
					doAjaxSyncWithLogin("report_delete_annotation", params, success, login);
					
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
