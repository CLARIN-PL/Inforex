function EditorTranscription(editor){
	this._editor = editor;
}

EditorTranscription.prototype.reindent = function(){
	var current = this._editor.cursorLine();
	this._editor.reindent();
	this._editor.jumpToLine(current);
}

EditorTranscription.prototype.insertLineStaringWith = function(startsWith, content){
	var n = 0, inserted = 0;
	var lines = this._editor.lineNumber(this._editor.lastLine());
	while ( inserted == 0 && n < lines ){
		alert(n);
		var lineContent = this._editor.lineContent(this._editor.nthLine(n+1)); 
		if ( lineContent.substring(0, startsWith.length) == startsWith ){
			this._editor.insertIntoLine(this._editor.nthLine(n+1), "end", "\n" + content);
			inserted = 1;
		}
		n++;
	}	
}

EditorTranscription.prototype.setCursor = function(lineNumber, charOffset){
	this._editor.selectLines(this._editor.nthLine(lineNumber), charOffset);
}

EditorTranscription.prototype.setCursorAfter = function(lineNumber, text){
	this._editor.selectLines(this._editor.nthLine(lineNumber), 0);
	var find = this._editor.getSearchCursor(text);
	if (find.findNext()){
		var position = find.position();
		this._editor.selectLines(this._editor.nthLine(lineNumber), position.character + text.length);		
	}
}

EditorTranscription.prototype.currentLineNumber = function(){
	return this._editor.lineNumber(this._editor.cursorLine());
}

/**
 * Zwraca indeks linii zawierającej podany tekst liczony od 1.
 * @return
 */
EditorTranscription.prototype.findLine = function(text){
	var n = 0, found = false;
	var lines = this._editor.lineNumber(this._editor.lastLine());
	while ( !found && n < lines ){
		var lineContent = this._editor.lineContent(this._editor.nthLine(n+1));
		found = lineContent.indexOf(text) != -1;
		n++;
	}
	return found ? n : -1;
}

/**
 * Przechodzi do następnej linii.
 * @return
 */
EditorTranscription.prototype.goToNextLine = function(){
	this._editor.jumpToLine(this._editor.nextLine());
}
/**
 * Wstawia nową linię przed linią, w które aktualnie znajduje się kursor.
 * @param content
 * @return
 */
EditorTranscription.prototype.insertLine = function(content){
	this._editor.insertIntoLine(this._editor.cursorLine(), 0, content + "\n");
}

/**
 * Wstawia nową linię, ale tylko jeżeli jest ona wewnątrz podanego znacznika
 * @param content
 * @return
 */
EditorTranscription.prototype.insertLineWithin = function(content, within){
	var starts = this.findLine("<"+within);
	var ends = this.findLine("</"+within);
	var current = this._editor.lineNumber(this._editor.cursorLine());
	if ( current > starts && current <= ends ){
		this.insertLine(content);
		return true;
	}
	else
		return false;
}

/**
 * Wstawia tekst pod warunkiem, że kursor znajduje się wewnątrz podanego znacznika.
 */
EditorTranscription.prototype.insertWithin = function(content, within){
	this._editor.replaceSelection(content);
	return true;
}

/**
 * 
 * @param before
 * @param after
 * @param within
 * @return
 */
EditorTranscription.prototype.insertAroundWithin = function(before, after, within){
	var position = this._editor.cursorPosition(true);	
	// Jeżeli jest obecny znak @ to ustaw w jego miejsce kursor, w przeciwnym wypadku na koniec
	var charOffset = before.indexOf("@") != -1 ? before.indexOf("@") : before.length;
	// Usuń znak @
	before = before.replace("@", "");
	var content = this._editor.selection();
	this._editor.replaceSelection(before + content + after);
	this._editor.selectLines(position.line, position.character + charOffset);
	return true;
};

/**
 * Wstawia wartość do atrybutu. Jeżeli atrybut nie istnieje to go tworzy. Jeżeli nie istnieje tag, to tworzy tag.
 * @param tag
 * @param attribute
 * @param value
 * @return
 */
EditorTranscription.prototype.insertValueCascade = function(tag, attribute, value){
	var content = this._editor.selection();
	this._editor.replaceSelection(value);
};
