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
	else if (left && $(left).is("span")){
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
			if (!text.match("^<span")){
				while (textOffset<text.length && isAlphanumeric(text[textOffset])) textOffset++;
				while (textOffset<text.length && text[textOffset]==' ') textOffset++;
				if ( textOffset < text.length ){
					if ($(left).is("span"))
						left = $(document.createTextNode("")).insertAfter(left)[0];					
					left.data += text.substring(0,textOffset);
					$(this.ann).html(text.substring(textOffset));
				}
			}
			else {
				$(this.ann).children("span:first").insertBefore(this.ann);
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
 			//console.log(right.data.substring(0, dataEndOffset));
 			right.data = right.data.substring(dataEndOffset);
 			//console.log(right.data.substring(dataEndOffset-1));
			//if (right.data=="") right.data=" ";
 		}
		else {
			$(right).appendTo(this.ann);
			this.extendRight();
		}
 	}	
	else if (right && $(right).is("span")){
		$(right).appendTo(this.ann);		
	} 	
	
 	/*var data = this.right.data;
 	if (data && data.length>0){
 		// Początek tekstu do wycięcia z `data`
 		var dataEndOffset = 0;
 		// Przeskocz sekwencję białych znaków
 		while (dataEndOffset<data.length && data[dataEndOffset]==' ') dataEndOffset++;
 		// Przeskocz sekwencję liter i znaków
 		while (dataEndOffset<data.length && isAlphanumeric(data[dataEndOffset])) dataEndOffset++;
 		// Jeżeli nie przeskoczono żadnego znaku, a istnieją znaki do przeskoczenia to przesuń o jeden
 		if (dataEndOffset==0 && data.length>0) dataEndOffset++;
 		// Jeżeli początek ustawiony jest na białym znaku, to adnotacja nie została rozszerzona o inne znaki niż białe
 		if (data[dataEndOffset-1]!=' '){
 			// Przenieś fragment z tekstu do adnotacji
 			$(this.ann).html($(this.ann).html() + data.substring(0, dataEndOffset) );
 			this.right.data = data.substring(dataEndOffset);
 			return dataEndOffset;
 		}
 		else
 			return 0;
 	}	*/
}

/**
* Zminiejsz adnotację z prawej strony o pełny element (ciągłą sekwencję liter i cyfr).
*/
Annotation.prototype.shrinkRight = function(){
	/*var left = $(this.ann).context.previousSibling;
	if (left){
		var text = $(this.ann).html();
		var nodeText = "";
		$.each($(this.ann)[0].childNodes, function(index, value){
			if ($(value).is(":not(span)")) nodeText+=$(value).text();
		});	
		if ( nodeText.length > 1 ){
			var textOffset = 1;
			if (!text.match("^<span")){
				while (textOffset<text.length && isAlphanumeric(text[textOffset])) textOffset++;
				while (textOffset<text.length && text[textOffset]==' ') textOffset++;
				if ( textOffset < text.length ){
					if ($(left).is("span"))
						left = $(document.createTextNode(" ")).insertAfter(left)[0];					
					left.data += text.substring(0,textOffset);
					$(this.ann).html(text.substring(textOffset));
				}
			}
			else 
				$(this.ann).children("span:first").insertAfter(left);
		}	
	}
	else {
		$(document.createTextNode(" ")).insertBefore(this.ann);	
		this.shrinkLeft();
	}*/	
	var right = $(this.ann).context.nextSibling;
	if (right){
		var text = $(this.ann).html();
		var nodeText = "";
		$.each($(this.ann)[0].childNodes, function(index, value){
			if ($(value).is(":not(span)")) nodeText+=$(value).text();
		});	
		if ( nodeText.length > 1 ){
			var textOffset = text.length;
			if (text.substr(-5,5)!="span>"){
				if (textOffset>0 && isAlphanumeric(text[textOffset-1]))
					while (textOffset>0 && isAlphanumeric(text[textOffset-1])) textOffset--;
				else
					textOffset--;
				while (textOffset>0 && text[textOffset-1]==' ') textOffset--;
				// Nie pozwól na zwinięcie adnotacji do pustego ciągu znaków
				if ( textOffset > 0 ){
					if ($(right).is("span"))
						right = $(document.createTextNode(" ")).insertBefore(right);
					// Przenieś fragment
					right.data =  text.substring(textOffset) + right.data;
					$(this.ann).html(text.substring(0, textOffset));
					return textOffset - text.length;
				}
			}
			else {
				$(this.ann).children("span:last").insertAfter(this.ann);
				$(document.createTextNode(" ")).insertAfter(this.ann);
			}
				
		}
	}
	else {
		$(document.createTextNode(" ")).insertAfter(this.ann);	
		this.shrinkRight();
	}	
	/*var data = this.right.data;
	var text = $(this.ann).html();
	if ( text.length > 1 ){
		// Koniec tekstu do wycięcia z `text`
		var textOffset = text.length;
		if (textOffset>0 && isAlphanumeric(text[textOffset-1]))
			while (textOffset>0 && isAlphanumeric(text[textOffset-1])) textOffset--;
		else
			textOffset--;
		while (textOffset>0 && text[textOffset-1]==' ') textOffset--;
		// Nie pozwól na zwinięcie adnotacji do pustego ciągu znaków
		if ( textOffset > 0 ){
			// Przenieś fragment
			this.right.data =  text.substring(textOffset) + data;
			$(this.ann).html(text.substring(0, textOffset));
			return textOffset - text.length;
		}
		else
			return 0;
	}	*/
}
/**
 * Zmień zakreś adnotacji.
 */
Annotation.prototype.change = function(leftOffset, rightOffset){
	alert("aaa");
	// Czy rozszerzamy lewą stronę
	if ( leftOffset < 0 ){
		var index = this.left.data.length + leftOffset;
		$(this.ann).text( this.left.data.substring(index) + $(this.ann).text().substring(leftOffset) );
		this.left.data = this.left.data.substring(0, index); 
	}
	// Czy rozszerzamy prawą stronę
	if ( rightOffset < 0 ){
		var index = this.left.data.length + leftOffset;
		$(this.ann).text( $(this.ann).text() + this.right.data.substring(0, -rightOffset));
		this.right.data = this.right.data.substring(-rightOffset); 
	}	
	// Czy zwężamy lewą stronę
	if (leftOffset>0){
		this.left.data += $(this.ann).text().substring(0, leftOffset);
		$(this.ann).text($(this.ann).text().substring(leftOffset));
	}
	// Czy zwężamy prawą stronę
	if (rightOffset>0){
		var index = $(this.ann).text().length - rightOffset;
		this.right.data = $(this.ann).text().substring(index) + this.right.data;
		$(this.ann).text($(this.ann).text().substring(0, index));
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
 
