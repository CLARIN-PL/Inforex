/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

/**
 * Konstruktor edytora
 * @param editor
 * @returns {EditorTranscription}
 */
function EditorTranscription(editor){
	this._editor = editor;
}

/**
 * Wykonuje reindent z zachowaniem aktualnej pozycji kursora.
 */
EditorTranscription.prototype.reindent = function(){
	var position = this._editor.cursorPosition();
	var offset = position.charOffset;
	var currentLineLength = this._editor.lineContent(position.line).length;	
	var lineNumber = this._editor.lineNumber(position.line);
	this._editor.reindent();
	var newLineLength = this._editor.lineContent(position.line).length;
	this._editor.selectLines(position.line, newLineLength - ( currentLineLength - position.character ) );
};

EditorTranscription.prototype.setCursor = function(lineNumber, charOffset){
	this._editor.selectLines(this._editor.nthLine(lineNumber), charOffset);
};

/**
 * Zwraca tekst względem aktualnej pozycji kursora.
 * @param offset
 * @returns
 */
EditorTranscription.prototype.substr = function(offset){
	var position = this._editor.cursorPosition(true);
	var line = this._editor.lineContent(this._editor.cursorLine());
	var text = "";
	if (offset < 0)
		text = line.substring(position.character+offset, position.character);
	else
		text = line.substring(position.character, position.character + offset);
	return text;
};


/**
 * 
 */
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
EditorTranscription.prototype.insertText = function(text, offset){
	// Ustaw domyślną wartość offsetu
	offset = typeof(offset) == 'undefined' ? 0 : offset;
	var position = this._editor.cursorPosition();
	// Jeżeli jest ##, to wstaw w to miejsce aktualnie zaznaczony tekst
	if ( text.indexOf("##") != -1 )
		text = text.replace("##", this._editor.selection());		
	// Indeks znaku @
	var charOffset = position.character + (text.indexOf("@") != -1 ? text.indexOf("@") : text.length);
	// Usuń znak @
	text = text.replace("@", "");
	// Zapamiętaj numer bieżącej linii i znaku
	var position = this._editor.cursorPosition(true);
	// Jeżeli ustawiono offset to przesuń kursor
	if (offset != 0){
		this._editor.selectLines(position.line, position.character + offset);			
	}
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
