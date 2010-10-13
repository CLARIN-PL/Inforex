function dom_path_length(e){
	var c = 0;
	while (e != null){
		c++;
		e = e.parentNode;
	}
	return c;	
}

function Selection(){
	var sel = window.getSelection();
	if (sel && sel.toString()!=""){
		this.sel = sel.getRangeAt( 0 );
		this.isValid = true;
		this.isSimple = (sel.startContainer == sel.endContainer);
		while (this.sel.startContainer.parentNode != this.sel.endContainer.parentNode){

			var path_start = dom_path_length(this.sel.startContainer);
			var path_end = dom_path_length(this.sel.endContainer);

			if (path_start == path_end){
				this.sel.setStartBefore(this.sel.startContainer);
				this.sel.setEndAfter(this.sel.endContainer);
			}else if (path_start < path_end){
				this.sel.setEndAfter(this.sel.endContainer);				
			}else{
				this.sel.setStartBefore(this.sel.startContainer);				
			}
		}
		
		// Jeżeli mają tych samych rodziców, ale nie są tekstam, to przesuń na sąsiadujące elementy
		if (this.sel.startContainer.nodeType != 3) {
			if ( this.sel.startContainer.previousSibling == null )
				this.sel.setStartBefore(this.sel.startContainer);				
			else
				this.sel.setStartAfter(this.sel.startContainer.previousSibling);
		}
		if (this.sel.endContainer.nodeType != 3 && this.sel.endContainer != null){
			this.sel.setEndAfter(this.sel.endContainer);
		}
	}
	else
	{
		this.isValis = false;
	}	
}

// Zaznaczenie
Selection.prototype._sel;

// Czy zostało utworzone zaznaczenie.
Selection.prototype._isValid;

// Proste zaznaczenie to takie, które zawiera się wewnątrz jednego elementu i nie zawiera elementów zagnieżdżonych.
Selection.prototype._isSimple;

/**
 * Pomiń białe znaki na począktu i końcu anotacji.
 */
Selection.prototype.trim = function(){
	// Usuń białe znaki przed i po zaznaczeniu
	var startOffset = this.sel.startOffset;
	var endOffset = this.sel.endOffset;
	// Sprawdź, czy jest co obciąć z lewej strony
	if (this.sel.startContainer.data){
		while (startOffset<endOffset && startOffset<this.sel.startContainer.data.length && isWhite(this.sel.startContainer.data[startOffset])) 
			startOffset++;
		this.sel.setStart(this.sel.startContainer, startOffset);
	}
	// Sprawdź, czy jest co obciąć z prawej strony	
	if (this.sel.endContainer.data){
		while (endOffset>1 && isWhite(this.sel.endContainer.data[endOffset-1])) 
			endOffset--;
		this.sel.setEnd(this.sel.endContainer, endOffset);
	}
}

/**
 * Rozszerza adnotację na lewo i prawo, aby obejmowała ciąg sąsiadujących liter i cyfr.
 */
Selection.prototype.fit = function(){
	// Usuń białe znaki przed i po zaznaczeniu
	var startOffset = this.sel.startOffset;
	var endOffset = this.sel.endOffset;
	
	if (this.sel.startContainer.data){
		var contextLeft = this.sel.startContainer.data;	
		if ( contextLeft[startOffset] != ' ' )
			while ( startOffset>0 && isAlphanumeric(contextLeft[startOffset-1]) )
				startOffset--;
		this.sel.setStart(this.sel.startContainer, startOffset);
	}

	if (this.sel.endContainer.data){
		var contextRight = this.sel.endContainer.data;
		if ( contextRight[endOffset] != ' ')
			while ( endOffset < contextRight.length && isAlphanumeric(contextRight[endOffset]) )
				endOffset++;
		this.sel.setEnd(this.sel.endContainer, endOffset);
	}
}