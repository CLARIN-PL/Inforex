/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

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
		
		/* Nawiguj wgłąb struktury DOM, aby ustawić wskaźnik końca zaznaczenia
		 * jak najbliżej zaznaczonego tekstu.
		 */ 		
		while ( this.sel.endContainer.lastChild != null 
					&& this.sel.endContainer.children.length > 0){
			var n = this.sel.endContainer.children.length -1;
			while ( n >= 0 && this.sel.endContainer.children[n].textContent == "" )
				n--;
			if ( n >= 0 ){
				var endNode = this.sel.endContainer.children[n].lastChild;
				if ( endNode.nodeType == 3 ) // TEXT_NODE
					this.sel.setEnd(endNode, endNode.length);
				else	
					this.sel.setEndAfter(endNode);
			}
		}
		
		/* Nawiguj wgłąb struktury DOM, aby ustawić wskaźnik początku zaznaczenia
		 * jak najbliżej zaznaczonego tekstu.
		 */ 		
		while ( this.sel.startContainer.firstChild != null 
					&& this.sel.startContainer.children.length > 0){
			var maxn = this.sel.startContainer.children.length;
			var n = 0;
			while (n < maxn && this.sel.startContainer.children[n].textContent == "" )
				n++;
			if ( n < maxn ){
				var startNode = this.sel.startContainer.children[n].firstChild;
				if ( startNode.nodeType == 3 ) // TEXT_NODE
					this.sel.setStart(startNode, startNode.length);
				else	
					this.sel.setStartBefore(startNode);
			}
		}		
		
		/* Wyrównaj poziomy zagnieżdzeń początku i końca zaznaczenia */
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
		
		/* Jeżeli mają tych samych rodziców, ale nie są tekstam, to przesuń na sąsiadujące elementy */
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
		this.isValid = false;
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

Selection.prototype.clear = function(){
	this.sel.setStart(this.sel.startContainer, 0);
	this.sel.setEnd(this.sel.endContainer, 0);
}

