function Selection(){
	var sel = window.getSelection();
	if (sel && sel.toString()!=""){
		this.sel = sel.getRangeAt( 0 );
		this.isValid = true;
		this.isSimple = sel.startContainer == sel.endContainer;
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

/*
 * Rozszerza adnotację na lewo i prawo, aby obejmowała ciąg sąsiadujących liter i cyfr.
 */
Selection.prototype.trim = function(){
	// Usuń białe znaki przed i po zaznaczeniu
	var startOffset = this.sel.startOffset;
	var endOffset = this.sel.endOffset;
	while (startOffset<endOffset && startOffset<this.sel.startContainer.data.length && this.sel.startContainer.data[startOffset]==' ') 
		startOffset++;
	this.sel.setStart(this.sel.startContainer, startOffset);
	if (this.sel.endContainer.data){
		while (endOffset>1 && this.sel.endContainer.data[endOffset-1]==' ') 
			endOffset--;
		this.sel.setEnd(this.sel.endContainer, endOffset);
	}
}

/*
* Rozszerza adnotację na lewo i prawo, aby obejmowała ciąg sąsiadujących liter i cyfr.
*/
Selection.prototype.fit = function(){
	// Usuń białe znaki przed i po zaznaczeniu
	var startOffset = this.sel.startOffset;
	var endOffset = this.sel.endOffset;
	
	var contextLeft = this.sel.startContainer.data;	
	if ( contextLeft[startOffset] != ' ' )
		while ( startOffset>0 && isAlphanumeric(contextLeft[startOffset-1]) )
			startOffset--;
	this.sel.setStart(this.sel.startContainer, startOffset);

	if (this.sel.endContainer.data){
		var contextRight = this.sel.endContainer.data;
		if ( contextRight[endOffset] != ' ')
			while ( endOffset < contextRight.length && isAlphanumeric(contextRight[endOffset]) )
				endOffset++;
		this.sel.setEnd(this.sel.endContainer, endOffset);
	}
}