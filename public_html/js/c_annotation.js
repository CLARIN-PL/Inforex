/**
 * Klasa reprezentuje zaznaczoną adnotację (element span).
 * Umożliwia modyfikację zakresu adnotacji. 
 */
function Annotation(ann){
	this.ann = ann;
	this.left = $(ann).context.previousSibling;
	this.right = $(ann).context.nextSibling;
	
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
	// Rozszerz adnotację o przylegający ciąg liter i cyfr
	var data = this.left.data;
	if (data && data.length>0){
		// Początek tekstu do wycięcia z `data`
		var dataEndOffset = data.length-1;
		// Przeskocz sekwencję białych znaków
		while (dataEndOffset>0 && data[dataEndOffset]==' ') dataEndOffset--;
		// Przeskocz sekwencję liter i znaków
		while (dataEndOffset>0 && isAlphanumeric(data[dataEndOffset-1])) dataEndOffset--;
		// Jeżeli początek ustawiony jest na białym znaku, to adnotacja nie została rozszerzona o inne znaki niż białe
		if (data[dataEndOffset]!=' '){
			// Przenieś fragment z tekstu do adnotacji
			this.left.data = data.substring(0, dataEndOffset);
			$(this.ann).text(data.substring(dataEndOffset) + $(this.ann).text());
			return data.length-dataEndOffset;
		}
		else
			return 0;
	}	
}

/**
 * Zminiejsz adnotację z lewej strony o pełny element (ciągłą sekwencję liter i cyfr).
 */
Annotation.prototype.shrinkLeft = function(){
	var data = this.left.data;
	var text = $(this.ann).text();
	if ( text.length > 1 ){
		// Koniec tekstu do wycięcia z `text`
		var textOffset = 1;
		while (textOffset<text.length && isAlphanumeric(text[textOffset])) textOffset++;
		while (textOffset<text.length && text[textOffset]==' ') textOffset++;
		// Nie pozwul na zwinięcie adnotacji do pustego ciągu znaków
		if ( textOffset < text.length ){
			// Przenieś fragment
			this.left.data = data + text.substring(0,textOffset);
			$(this.ann).text(text.substring(textOffset));
			return -textOffset;
		}
		else
			return 0;
	}	
}

/**
 * Rozszerz adnotację z prawej strony o pełny element (ciągłą sekwencję liter i cyfr).
 */
Annotation.prototype.extendRight = function(){
 	var data = this.right.data;
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
 			$(this.ann).text($(this.ann).text() + data.substring(0, dataEndOffset) );
 			this.right.data = data.substring(dataEndOffset);
 			return dataEndOffset;
 		}
 		else
 			return 0;
 	}	
}

/**
* Zminiejsz adnotację z prawej strony o pełny element (ciągłą sekwencję liter i cyfr).
*/
Annotation.prototype.shrinkRight = function(){
	var data = this.right.data;
	var text = $(this.ann).text();
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
			$(this.ann).text(text.substring(0, textOffset));
			return textOffset - text.length;
		}
		else
			return 0;
	}	
}
/**
 * Zmień zakreś adnotacji.
 */
Annotation.prototype.change = function(leftOffset, rightOffset){
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
 
