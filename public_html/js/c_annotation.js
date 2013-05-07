/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

/**
 * Klasa reprezentuje zaznaczoną adnotację (element span).
 * Umożliwia modyfikację zakresu adnotacji. 
 * 
 * 
 * added by kotu:
 * tymczasowo nowe metody do rozszerzania/skracania anotacji nie korzystają z atrybutów left/right klasy Annotation
 * i nie update'ują ich (bo po co?)
 */
function Annotation(ann){
	this.ann = ann;	
	this.left = $(ann).context.previousSibling;
	this.right = $(ann).context.nextSibling;
	var context = $(ann).context.parentNode;
	jQuery(context).addClass("context");
	
	var match = $(ann).attr("title").match(/an#(.*):(.*)/);
	this.id = match[1];
	this.type = match[2];
}

Annotation.prototype.getText = function(){
	return $(this.ann).text();
}

/**
 * Rozszerz adnotację z lewej strony o pełny element (ciągłą sekwencję liter i cyfr).
 */
Annotation.prototype.extendLeft = function(){
	var left = $(this.ann).context.previousSibling;
	if (left && left.nodeType==3 && left.data.length>0){
		// Początek tekstu do wycięcia z `data`
		var dataEndOffset = left.data.length-1;
		// Przeskocz sekwencję białych znaków
		while (dataEndOffset>0 && left.data[dataEndOffset]==' ') dataEndOffset--;
		// Przeskocz sekwencję liter i znaków
		while (dataEndOffset>0 && isAlphanumeric(left.data[dataEndOffset-1])) dataEndOffset--;
		// Jeżeli początek ustawiony jest na białym znaku, to adnotacja nie została rozszerzona o inne znaki niż białe
		if (left.data[dataEndOffset]!=' '){
			// Przenieś fragment z tekstu do adnotacji
			$(this.ann).html(left.data.substring(dataEndOffset) + $(this.ann).html());
			left.data = left.data.substring(0, dataEndOffset);
			//if (left.data=="") left.data=" ";
			//return left.data.length-dataEndOffset;
		}
		else {
			$(left).prependTo(this.ann);
			this.extendLeft();
		}
	}	
 	else if (left && left.nodeType==3 && left.data.length==0){
 		$(left).remove();
 		this.extendLeft();
 	}	
	else if (left && ($(left).is("span") || $(left).is("div"))){
		$(left).prependTo(this.ann);
	}
}

/**
 * Zminiejsz adnotację z lewej strony o pełny element (ciągłą sekwencję liter i cyfr).
 */
Annotation.prototype.shrinkLeft = function(){
	var left = $(this.ann).context.previousSibling;
	if (left){
		var text = $(this.ann).html();
		var nodeText = "";
		$.each($(this.ann)[0].childNodes, function(index, value){
			if ($(value).is(":not(span)")) nodeText+=$(value).text();
		});	
		if ( nodeText.length > 1 ){
			var textOffset = 1;
			if (!text.match("^<span") && !text.match("^<div")){
				while (textOffset<text.length && isAlphanumeric(text[textOffset])) textOffset++;
				while (textOffset<text.length && text[textOffset]==' ') textOffset++;
				if ( textOffset < text.length ){
					if ($(left).is("span") || $(left).is("div"))
						left = $(document.createTextNode("")).insertAfter(left)[0];					
					left.data += text.substring(0,textOffset);
					$(this.ann).html(text.substring(textOffset));
				}
			}
			else if (text.match("^<span")){
				$(this.ann).children("span:first").insertBefore(this.ann);
				$(document.createTextNode(" ")).insertBefore(this.ann);
			}
			else if (text.match("^<div")){
				$(this.ann).children("div:first").insertBefore(this.ann);
				$(document.createTextNode(" ")).insertBefore(this.ann);
			}
		}	
	}
	else {
		$(document.createTextNode(" ")).insertBefore(this.ann);	
		this.shrinkLeft();
	}
}

/**
 * Rozszerz adnotację z prawej strony o pełny element (ciągłą sekwencję liter i cyfr).
 */
Annotation.prototype.extendRight = function(){
	var right = $(this.ann).context.nextSibling;
 	if (right && right.nodeType==3 && right.data.length>0){
 		// Początek tekstu do wycięcia z `data`
 		var dataEndOffset = 0;
 		// Przeskocz sekwencję białych znaków
 		while (dataEndOffset<right.data.length && right.data[dataEndOffset]==' ') dataEndOffset++;
 		// Przeskocz sekwencję liter i znaków
 		while (dataEndOffset<right.data.length && isAlphanumeric(right.data[dataEndOffset])) dataEndOffset++;
 		// Jeżeli nie przeskoczono żadnego znaku, a istnieją znaki do przeskoczenia to przesuń o jeden
 		if (dataEndOffset==0 && right.data.length>0) dataEndOffset++;
 		// Jeżeli początek ustawiony jest na białym znaku, to adnotacja nie została rozszerzona o inne znaki niż białe
 		if (right.data[dataEndOffset-1]!=' '){
 			// Przenieś fragment z tekstu do adnotacji
 			$(this.ann).html($(this.ann).html() + right.data.substring(0, dataEndOffset) );
 			right.data = right.data.substring(dataEndOffset);
			//if (right.data=="") right.data=" ";
 		}
		else {
			$(right).appendTo(this.ann);
			this.extendRight();
		}
 	}	
	else if (right && ($(right).is("span") || $(right).is("div"))){
		$(right).appendTo(this.ann);		
	} 	
	
}

/**
* Zminiejsz adnotację z prawej strony o pełny element (ciągłą sekwencję liter i cyfr).
*/
Annotation.prototype.shrinkRight = function(){
	var right = $(this.ann).context.nextSibling;
	if (right){
		var text = $(this.ann).html();
		var nodeText = "";
		$.each($(this.ann)[0].childNodes, function(index, value){
			if ($(value).is(":not(span)")) nodeText+=$(value).text();
		});	
		if ( nodeText.length > 1 ){
			var textOffset = text.length;
			if (text.substr(-5,5)!="span>" && text.substr(-4,4)!="div>"){
				if (textOffset>0 && isAlphanumeric(text[textOffset-1]))
					while (textOffset>0 && isAlphanumeric(text[textOffset-1])) textOffset--;
				else
					textOffset--;
				while (textOffset>0 && text[textOffset-1]==' ') textOffset--;
				// Nie pozwól na zwinięcie adnotacji do pustego ciągu znaków
				if ( textOffset > 0 ){
					if ($(right).is("span") || $(right).is("div"))
						right = $(document.createTextNode(" ")).insertBefore(right);
					// Przenieś fragment
					right.data =  text.substring(textOffset) + right.data;
					$(this.ann).html(text.substring(0, textOffset));
					//return textOffset - text.length;
				}
			}
			else if (text.substr(-5,5)=="span>"){
				$(this.ann).children("span:last").insertAfter(this.ann);
				$(document.createTextNode(" ")).insertAfter(this.ann);
			}
			else if (text.substr(-4,4)=="div>"){
				$(this.ann).children("div:last").insertAfter(this.ann);
				$(document.createTextNode(" ")).insertAfter(this.ann);
			}
		}
	}
	else {
		$(document.createTextNode(" ")).insertAfter(this.ann);	
		this.shrinkRight();
	}	
}

/**
 * Change annotation type.
 */
Annotation.prototype.setType = function(type){
	$(this.ann).removeClass(this.type);
	$(this.ann).addClass(type);
	$(this.ann).attr("title", "an#"+this.id+":"+type);
	this.type = type;
}
 
/**
 * Apply all changes made to the annotation.
 */
Annotation.prototype.commit = function(){
	
}
 
