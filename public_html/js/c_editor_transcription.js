function EditorTranscription(editor){
	this._editor = editor;
}

EditorTranscription.prototype.reindent = function(){
	var position = this._editor.cursorPosition();
	var offset = position.charOffset;
	var currentLineLength = this._editor.lineContent(position.line).length;	
	var lineNumber = this._editor.lineNumber(position.line);
	this._editor.reindent();
	var newLineLength = this._editor.lineContent(position.line).length;
	this._editor.selectLines(position.line, newLineLength - ( currentLineLength - position.character ) );
};

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
};

EditorTranscription.prototype.setCursor = function(lineNumber, charOffset){
	this._editor.selectLines(this._editor.nthLine(lineNumber), charOffset);
};

EditorTranscription.prototype.setCursorAfter = function(lineNumber, text){
	this._editor.selectLines(this._editor.nthLine(lineNumber), 0);
	var find = this._editor.getSearchCursor(text);
	if (find.findNext()){
		var position = find.position();
		this._editor.selectLines(this._editor.nthLine(lineNumber), position.character + text.length);		
	}
};

EditorTranscription.prototype.currentLineNumber = function(){
	return this._editor.lineNumber(this._editor.cursorLine());
};

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
};

/**
 * Przechodzi do następnej linii.
 * @return
 */
EditorTranscription.prototype.goToNextLine = function(){
	this._editor.jumpToLine(this._editor.nextLine());
};

/**
 * Wstawia nową linię przed linią, w której aktualnie znajduje się kursor.
 * Jeżeli w `text` znajduje się znak `@`, to w jego miejsce zostanie wstawiony kursor.
 * @param text --- tekst do wstawienia
 * @return
 */
EditorTranscription.prototype.insertLine = function(text){
	// Indeks znaku @
	var charOffset = text.indexOf("@") != -1 ? text.indexOf("@") : text.length;
	// Usuń znak @
	text = text.replace("@", "");
	// Zapamiętaj numer bieżącej linii i znaku
	var position = this._editor.cursorPosition(true);	
	// Wstaw na początku bieżącej linii
	this._editor.insertIntoLine(this._editor.cursorLine(), 0, text + "\n");
	// Ustawienie pozycji kursora
	this._editor.selectLines(position.line, charOffset);	
};

/**
 * Wstawia tekst w miejsce aktualnego zaznaczenia.
 * Jeżeli w `text` jest znak `@`, to w jego miejsce zostanie wstawiony kursor.
 * Jeżeli w `text` jest `##`, to w jego miejsce zostanie wstawiony aktualnie zaznaczony tekst.
 * @param text --- tekst do wstawienia
 */
EditorTranscription.prototype.insertText = function(text){
	var position = this._editor.cursorPosition();
	// Jeżeli jest ##, to wstaw w to miejsce aktualnie zaznaczony tekst
	if ( text.indexOf("##") != -1 )
		text = text.replace("##", this._editor.selection());		
	// Indeks znaku @
	var charOffset = text.indexOf("@") != -1 ? text.indexOf("@") + position.character : text.length;
	// Usuń znak @
	text = text.replace("@", "");
	// Zapamiętaj numer bieżącej linii i znaku
	var position = this._editor.cursorPosition(true);	
	// Wstaw tekst
	this._editor.replaceSelection(text);
	// Ustaw pozycję kursora
	this._editor.selectLines(position.line, charOffset);	
};

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
};

/**
 * Wstawia tekst pod warunkiem, że kursor znajduje się wewnątrz podanego znacznika.
 */
EditorTranscription.prototype.insertWithin = function(content, within){
	this._editor.replaceSelection(content);
	return true;
};

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
