/*
 * marginalia.js
 *
 * Marginalia has been developed with funding and support from
 * BC Campus, Simon Fraser University, and the Government of
 * Canada, the UNDESA Africa i-Parliaments Action Plan, and  
 * units and individuals within those organizations.  Many 
 * thanks to all of them.  See CREDITS.html for details.
 * Copyright (C) 2005-2007 Geoffrey Glass; the United Nations
 * http://www.geof.net/code/annotation
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * $Id: marginalia.js 400 2008-12-20 00:14:00Z geof.glass $
 */

// Features that can be switched on and off
AN_BLOCKMARKER_FEAT = 'show block markers';
AN_ACCESS_FEAT = 'public/private access settings';
AN_LINKING_FEAT = 'allow external links';
AN_EXTLINKS_FEAT = 'allow external links';
AN_ACTIONS_FEAT = 'allow (edit) actions';

// The names of HTML/CSS classes used by the annotation code.
AN_HOVER_CLASS = 'hover';			// assigned to highlights and notes when the mouse is over the other
AN_ANNOTATED_CLASS = 'annotated';	// class added to fragment when annotation is on
AN_SELFANNOTATED_CLASS = 'self-annotated';  // annotations are by the current user (and therefore editable)
AN_EDITINGNOTE_CLASS = 'editing-note';		// (on body) indicates a note is being edited
AN_EDITINGLINK_CLASS = 'editing-link';
AN_LASTHIGHLIGHT_CLASS = 'last';	// used to flag the last highlighted regin for a single annotation
AN_ACTIONPREFIX_CLASS = 'action-';		// prefix for class names for actions (e.g. action-delete)

AN_RANGECARET_ID = 'range-caret';		// identifies caret used to show zero-length ranges

AN_ANNOTATION_FIELD = 'annotation';	// reference to Annotation object
AN_POST_FIELD = 'post';				// reference to PostMicro object

AN_ID_PREFIX = 'annot';				// prefix for annotation IDs in element classes and IDs

// Length limits
MAX_QUOTE_LENGTH = 1000;

// The timeout between coop multitasking calls.  Should be short so most time is spent doing
// something rather than timing out.
AN_COOP_TIMEOUT = 50;

// The maximum time to spend on one coop multitasking call.  Should be short enough to be
// fairly unnoticeable, but long enough to get some work done.
AN_COOP_MAXTIME = 200;

/* ************************ User Functions ************************ */

/**
 * Must be called before any other annotation functions
 * service - used to connect to the server side
 * loginUserId - the current user
 * displayUserId - the user whose annotations are to be shown (may differ from username)
 * urlBase - if null, annotation URLs are used as normal.  Otherwise, they are searched for this
 * string and anything preceeding it is chopped off.  This is necessary because IE lies about
 * hrefs:  it provides an absolute URL for that attribute, rather than the actual text.  In some
 * cases, absolute URLs aren't desirable (e.g. because the annotated resources might be moved
 * to another host, in which case the URLs would all break).
 */
function Marginalia( service, loginUserId, displayUserId, features )
{
	this.annotationService = service;
	this.loginUserId = loginUserId;
	this.displayUserId = displayUserId;
	this.editing = null;	// annotation currently being edited (if any)
	this.noteEditor = null;	// state for note currently being edited (if any) - should replace editing, above
	
	this.userInRequest = false;
	this.preferences = null;
	this.keywordService = null;
	this.urlBase = null;
	this.blockMarkers = false;
	this.access = false;
	this.actions = false;
	this.defaultAction = null;
	this.skipContent = function(node) {
		return _skipAnnotationLinks(node) || _skipAnnotationActions(node) || _skipCaret(node); };
	this.saveEditPrefs = Marginalia.saveEditPrefs;
	this.displayNote = Marginalia.defaultDisplayNote;
	this.allowAnyUserPatch = false;
	this.onMarginHeight = null;
		
	this.editors = {
		'default': Marginalia.newDefaultEditor,
		freeform: Marginalia.newEditorFunc( FreeformNoteEditor ),
		keyword: Marginalia.newEditorFunc( KeywordNoteEditor ),
		link: Marginalia.newEditorFunc( SimpleLinkUi )
	};
	
	for ( var feature in features )
	{
		var value = features[ feature ];
		switch ( feature )
		{
			// Set the default action for a new annotation ("edit" for track changes)
			case 'action':
				this.defaultAction = value;
			
			// Allow this user to submit patches for out-of-date annotations by any other user
			case 'allowAnyUserPatch':
				this.allowAnyUserUpdate = value;
				break;
			
			// The baseUrl should be stripped from annotation URLs.  The server must also do this.
			case 'baseUrl':
				this.baseUrl = value;
				break;
				
			// Override the function for displaying a note in the margin
			case 'displayNote':
				this.displayNote = value;
				break;
				
			// Override or add editors for editing margin notes
			case 'editors':
				for ( var name in value )
					this.editors[ name ] = value[ name ];
				break;
				
			// The keyword service to provide the drop-down list of keywords
			case 'keywordService':
				this.keywordService = value;
				break;
				
			// Toggle: Create an annotation when the user presses the Enter key
			case 'onkeyCreate':
				if ( value )
					addEvent( document, 'keyup', _keyupCreateAnnotation );
				break;
				
			// Callback for when margin height changes
			case 'onMarginHeight':
				this.onMarginHeight = value;
				break;
				
			// A Preferences object used to store/retrieve preferences on the server
			case 'preferences':
				this.preferences = value;
				break;
				
			// Toggle: Display the private/public access button for each margin note
			case 'showAccess':
				this.showAccess = value;
				break;
				
			case 'showActions':
				this.showActions = value;
				break;
				
			// Show a caret where the user clicks the mouse.  Do not use.
			case 'showCaret':
				if ( value )
				{
					document.addEventListener( 'mouseup', _caretUpHandler, true );
					document.addEventListener( 'mousedown', _caretDownHandler, false );
				}
				break;
				
			// Show block markers in the left margin, indicating how many users have annotated a block
			case 'showBlockMarkers':
				this.showBlockMarkers = value;
				break;
				
			// Function for ignoring elements embedded in annotatable content
			case 'skipContent':
				var oldSkipContent = this.skipContent;
				var customSkipContentFunc = value;
				this.skipContent = function(node) { return oldSkipContent(node) || customSkipContentFunc(node); };
				break;
				
			// Send the user ID in requests (for the demo - shouldn't use in actual systems)
			case 'userInRequest':
				this.userInRequest = value;
				break;
				
			// Toggle: Are you sure you want to delete this note? alert box
			case 'warnDelete':
				this.warnDelete = value;
				break;
			
			case 'saveEditPrefs':
				this.saveEditPrefs = value;
				break;
			
			default:
				if ( typeof( this[ feature ] ) != 'undefined' )
					throw 'Attempt to override feature: ' + feature;
				else
					this[ feature ] = value;
				break;
		}
	}
}

// Recorded in every annotation created by this client
Marginalia.VERSION = 2;

Marginalia.prototype.newEditor = function( annotation, editorName )
{
	var f = editorName ? this.editors[ editorName ] : this.editors[ 'default' ];
	return f( this, annotation );
}

/**
 * Call this to create a function for constructing an editor with the given constructor
 * Avoids excessive context in the lambda
 */
Marginalia.newEditorFunc = function( constructor, params )
{
	var f = function( marginalia, annotation ) { return new constructor( params ); };
	return f;
}

/**
 * Figure out whether note editing should be in keywords or freeform mode
 * If the note text is a keyword, default to keywords.  Otherwise, check
 * preferences.
 */
Marginalia.newDefaultEditor = function( marginalia, annotation )
{
	if ( ! marginalia.keywordService )
		return new FreeformNoteEditor( );
	else if ( ! annotation || '' == annotation.getNote() )
	{
		var pref = marginalia.preferences.getPreference( AN_NOTEEDITMODE_PREF );
		if ( pref == AN_EDIT_NOTE_KEYWORDS )
			return new KeywordNoteEditor( );
		else
			return new FreeformNoteEditor( );
	}
	else if ( marginalia.keywordService.isKeyword( annotation.getNote() ) )
		return new KeywordNoteEditor( );
	else
		return new FreeformNoteEditor( );
}

Marginalia.saveEditPrefs = function( marginalia, annotation, editor )
{
	if ( editor.constructor == KeywordNoteEditor )
		marginalia.preferences.setPreference( AN_NOTEEDITMODE_PREF, AN_EDIT_NOTE_KEYWORDS );
	else if ( editor.constructor == FreeformNoteEditor )
		marginalia.preferences.setPreference( AN_NOTEEDITMODE_PREF, AN_EDIT_NOTE_FREEFORM );
}



/**
 * Could do this in the initializer, but by leaving it until now we can avoid
 * forcing clients to have an onload handler
 */
Marginalia.prototype.listPosts = function( )
{
	if ( ! this.posts )
		this.posts = PostPageInfo.getPostPageInfo( document );
	return this.posts;
}

Marginalia.prototype.createAnnotation = function( annotation, f )
{
	var f2 = null;
	if ( this.keywordService )
	{
		var keywordService = this.keywordService;
		f2 = function( url ) {
			f( url );
			keywordService.refresh( );
		};
	}
	this.annotationService.createAnnotation( annotation, f2 ? f2 : f );
}

Marginalia.prototype.updateAnnotation = function( annotation )
{
	if ( annotation.hasChanged() )
	{
		var marginalia = this;
		var f = function( xml )	{
			annotation.resetChanges();
			if ( marginalia.keywordService )
				marginalia.keywordService.refresh( );
		};
		this.annotationService.updateAnnotation( annotation, f );
	}
}

Marginalia.prototype.deleteAnnotation = function( annotationId )
{
	var f = null;
	if ( this.keywordService )
	{
		var keywordService = this.keywordService;
		f = function( xml ) {
			keywordService.refresh( );
		};
	}
	this.annotationService.deleteAnnotation( annotationId, f );
}


/**
 * Show all annotations on the page
 * Make sure to call showMarginalia too
 * There used to be showAnnotations and hideAnnotations functions which could
 * apply to individual posts on a page.  Unused, I removed them - they added
 * complexity because annotations needed to be stored but not displayed.  IMHO,
 * the best way to do this is with simple dynamic CSS (using display:none).
 * TODO: If the url is a wildcard matching multiple posts, per block user markers
 * won't show up as a result of calling this!
 */
Marginalia.prototype.showAnnotations = function( url, block )
{
	// Must set the class here so that annotations margins will expand so that,
	// in turn, any calculations done by the caller (e.g. to resize margin
	// buttons) will take the correct size into account.
	domutil.addClass( document.body, AN_ANNOTATED_CLASS );
	if ( this.loginUserId == this.displayUserId || '' == this.displayUserId )
		domutil.addClass( document.body, AN_SELFANNOTATED_CLASS );
	// marginalia.hideAnnotations( );
	var marginalia = this;
	this.annotationService.listAnnotations( url, this.displayUserId, block,
		function(xmldoc) { _showAnnotationsCallback( marginalia, url, xmldoc, true ) } );
}

Marginalia.prototype.showBlockAnnotations = function( url, block )
{
	// TODO: Push down calculations must be repaired where new annotations are added.
	// Ideally this would happen automatically.
	var marginalia = this;
	this.annotationService.listAnnotations( url, null, block,
		function(xmldoc) { _showAnnotationsCallback( marginalia, url, xmldoc, false, true ) } );
}

/**
 * This is the callback function called by listAnnotations when data first comes back
 * from the server.
 */
function _showAnnotationsCallback( marginalia, url, xmldoc, doBlockMarkers )
{
	domutil.addClass( document.body, AN_ANNOTATED_CLASS );
	if ( marginalia.loginUserId == marginalia.displayUserId || '' == marginalia.displayUserId )
		domutil.addClass( document.body, AN_SELFANNOTATED_CLASS );
	marginalia.annotationXmlCache = xmldoc;
	_annotationDisplayCallback( marginalia, url, doBlockMarkers );
}

/**
 * This callback is used to display annotations from the cache in the marginalia object.
 * It will spend a certain amount of time displaying annotations;  if it can't show them
 * all in that time, it will call setTimeout to trigger continued display later.  This
 * is basically a way to implement cooperative multitasking so that if many annotations
 * need to be displayed the browser won't lock up.
 *
 * It will also fetch and display block markers if that feature is set and the doBlockMarkers
 * flag is true.  Block markers must be displayed *after* the annotations so that their
 * height will be correct.
 *
 * - noCountIncrement: set this if the annotation is simlpy being redisplayed, and is not part
 *   of an attempt to fetch and display more annotations
 */
function _annotationDisplayCallback( marginalia, callbackUrl, doBlockMarkers, noCountIncrement )
{
	var startTime = new Date( );
	var curTime;
	
	// Parse the XML, if that hasn't been done already
	if ( marginalia.annotationXmlCache )
	{
		marginalia.annotationCache = parseAnnotationXml( marginalia.annotationXmlCache );
		delete marginalia.annotationXmlCache;
		curTime = new Date( );
		if ( curTime - startTime >= AN_COOP_MAXTIME )
		{
			setTimeout( function() { _annotationDisplayCallback( marginalia, callbackUrl, doBlockMarkers ) }, AN_COOP_TIMEOUT );
			return;
		}
	}
	
	// Display cached annotations
	// Do this by merging the new annotations with those already displayed
	// For this to work, annotations must be sorted by URL
	var annotations = marginalia.annotationCache;
	if ( annotations )
	{
		var url = null;			// there may be annotations for multiple URLs;  this is the current one
		var post = null;		// post for the current url
		var notes = null;		// current notes element
		var nextNode = null;
		for ( var annotation_i = 0;  annotation_i < annotations.length;  ++annotation_i )
		{
			// Don't want to fail completely just because one or more annotations are malformed
			if ( null != annotations[ annotation_i ] )
			{
				var annotation = annotations[ annotation_i ];
				
				// Determine whether we're moving on to a new post (hence a new note list)
				if ( annotation.getUrl( ) != url )
				{
					// Margin height callback
					if ( post && marginalia.onMarginHeight )
						marginalia.onMarginHeight( post );
					
					url = annotation.getUrl( );
					post = marginalia.listPosts( ).getPostByUrl( url, marginalia.baseUrl );
					
					// Find the first note in the list (if there is one)
					if ( post )
					{
						notes = post.getNotesElement( );
						nextNode = notes.firstCild;
					}
					else
						logError( 'Post not found for URL "' + url + "'" );
				}
				
				// The server shouldn't normally return URLs that not on this page, but it
				// could (e.g. if the target has been deleted).  In that case, don't crash!
				if ( post )
				{
					// Find the position of the annotation by walking through the note list
					// (binary search would be nice here, but not practical unless the list is
					// stored somewhere other than in the DOM - plus, since multiple annotations
					// are dealt with here at once, the speed hit shouldn't be too bad)
					while ( nextNode )
					{
						if ( ELEMENT_NODE == nextNode.nodeType && nextNode.annotation )
						{
							if ( annotation.compareRange( nextNode.annotation ) < 0 )
								break;
						}
						nextNode = nextNode.nextSibling;
					}
					
					// If the annotation is already present, increment its fetch count
					if ( ! noCountIncrement )
						annotation.fetchCount += 1;
					// Now insert before beforeNote
					var success = post.addAnnotation( marginalia, annotation, nextNode );
					
					if ( success && annotation.getUserId( ) == marginalia.loginUserId || marginalia.allowAnyUserPatch )
						marginalia.patchAnnotation( annotation, post );
				}
			}
			
			annotations[ annotation_i ] = null;
			if ( curTime - startTime >= AN_COOP_MAXTIME )
				break;
		}
		
		if ( annotations.length == annotation_i )
		{
			delete marginalia.annotationCache;
			if ( doBlockMarkers && marginalia.showBlockMarkers )
				marginalia.showPerBlockUserCounts( callbackUrl );
		}
		else
			setTimeout( function() { _annotationDisplayCallback( marginalia, callbackUrl, doBlockMarkers ) }, AN_COOP_TIMEOUT );
	}
	// Finally, reposition block markers, as the annotations may have altered paragraph lengths
	else if ( doBlockMarkers && marginalia.showBlockMarkers )
	{
		marginalia.showPerBlockUserCounts( callbackUrl );
	}
}


/**
 * Older annotations may be using old path formats or other saved data which is
 * incorrect or inconsistent with current formatting.  This function checks
 * for the possibility, makes the changes, and submits the any updates to the
 * server.  This only applies to annotations owned by the current user.
 *
 * Ranges can be out of date for the following reasons:
 * - because there is no XPath range stored (older version lacked this)
 * - because the sequence range is in word.char format (e.g. 215.0 215.3)
 * - because the sequence range lacks a second block (e.g. /3/4/15.0 15.3)
 * - because the sequence range lacks a line number (e.g. /3/4/15.0;/3/4/15.3)
 * Current sequence range format looks like 3.4/1.15.0;3.4/1.15.3
 */
Marginalia.prototype.patchAnnotation = function( annotation, post )
{
	var root = post.getContentElement( );
	var sequenceRange = annotation.getSequenceRange( );
	var xpathRange = annotation.getXPathRange( );
	
	// Don't even try fixing xpath ranges if they can't be resolved on this browser
	if ( sequenceRange.needsUpdate( ) || (
			XPathRange.canResolve( root ) && ( null == xpathRange || xpathRange.needsUpdate( ) ) ) )
	{
		// Determine the physical word range in the document
		var wordRange = post.wordRangeFromAnnotation( this, annotation );
		var textRange = TextRange.fromWordRange( wordRange, this.skipContent );
		wordRange = WordRange.fromTextRange( textRange, root, this.skipContent );
		
		// Update the annotation
		var quote;
		if ( sequenceRange.needsUpdate( ) )
		{
			var newSequenceRange = wordRange.toSequenceRange( root );
			// Verify that the new sequence range is correct
			// Don't want to break existing data because of bad resolution or a code bug
			wordRange = WordRange.fromSequenceRange( newSequenceRange, root, this.skipContent );
			textRange = TextRange.fromWordRange( wordRange, this.skipContent );
			quote = getTextRangeContent( textRange, this.skipContent );
			quote = quote.replace( /(\s|\u00a0)+/g, ' ' );
			if ( annotation.getQuote( ) == quote )
				annotation.setSequenceRange( newSequenceRange );
		}
		if ( XPathRange.canResolve( root ) && ( null == xpathRange || xpathRange.needsUpdate( ) ) )
		{
			var newXPathRange = wordRange.toXPathRange( root );
			// Verify that the new xpath range is correct
			// Don't want to break existing data because of bad resolution or a code bug
			wordRange = WordRange.fromXPathRange( newSequenceRange, root, this.skipContent );
			textRange = TextRange.fromWordRange( wordRange, this.skipContent );
			quote = getTextRangeContent( textRange, this.skipContent );
			quote = quote.replace( /(\s|\u00a0)+/g, ' ' );
			if ( annotation.getQuote( ) == quote )
				annotation.setXPathRange( newXPathRange );
		}
		marginalia.updateAnnotation( annotation, null );

		// Replace the editable note display
		post.removeNote( this, annotation );
		var nextNode = post.getAnnotationNextNote( this, annotation );
		noteElement = post.showNote( this, annotation, nextNode );
		post.repositionNotes( this, noteElement.nextSibling );
	
		// Reposition block markers
		post.repositionBlockMarkers( this );
	}
}

/**
 * Hide all annotations on the page
 */
Marginalia.prototype.hideAnnotations = function( )
{
	domutil.removeClass( document.body, AN_ANNOTATED_CLASS );
	domutil.removeClass( document.body, AN_SELFANNOTATED_CLASS );
	
	var posts = this.listPosts( ).getAllPosts( );
	for ( var i = 0;  i < posts.length;  ++i )
	{
		var post = posts[ i ];
		// Should also destruct each annotation
		var annotations = post.removeAnnotations( marginalia );
		for ( var j = 0;  j < annotations.length;  ++j )
			annotations[ j ].destruct( );
		// normalizeSpace( post.element );
	}
}

/* *****************************
 * Additions to Annotation class
 */
 
/**
 * Convenience method for getting the note element for a given annotation
 */
Annotation.prototype.getNoteElement = function( )
{
	return document.getElementById( AN_ID_PREFIX + this.getId() );
}


/* ************************ Add/Show Functions ************************ */
/* These are for adding an annotation to the post and display.
 * addAnnotation calls the other three in order:
 * showNote, highlightRange, positionNote
 * None of these do anything with the server, but they do create interface
 * elements which when activated call server functions.
 *
 * In order to achieve a single point of truth, the only annotation list
 * is the list of annotation notes attached to each post in the DOM.
 * On the one hand, the two can't vary independently.  But I can't think
 * why they would need to.  This way, they can't get out of sync.
 */

/**
 * Add an annotation to the local annotation list and display.
 * Returns true if the annotation highlight was located successfully
 */
PostMicro.prototype.addAnnotation = function( marginalia, annotation, nextNode, editor )
{
	if ( ! nextNode )
		nextNode = this.getAnnotationNextNote( marginalia, annotation );
	// If the annotation is already displayed, remove the existing display
	var existing = annotation.getNoteElement( );
	if ( existing )
		this.removeAnnotation( marginalia, annotation );
	var quoteFound = this.showHighlight( marginalia, annotation );
	// Go ahead and show the note even if the quote wasn't found
	var r;
	if ( editor )
		r = this.showNoteEditor( marginalia, annotation, editor, nextNode );
	else
		r = this.showNote( marginalia, annotation, nextNode );
	// Reposition any following notes that need it
	this.repositionSubsequentNotes( marginalia, nextNode );
	return quoteFound;
}

/**
 * Get all annotations on a post by looking at HTML (no check with server)
 */
PostMicro.prototype.listAnnotations = function( marginalia )
{
	var notesElement = this.getNotesElement( marginalia );
	var child = notesElement.firstChild;
	var annotations = new Array( );
	while ( null != child )
	{
		if ( child.annotation )
			annotations[ annotations.length ] = child.annotation;
		child = child.nextSibling;
	}
	return annotations;
}

/* ************************ Remove/Hide Functions ************************ */
/* These are counterparts to the add/show functions above */

/*
 * Remove all annotations from a post
 * Returns an array of removed annotations so the caller can destruct them if necessary
 */
PostMicro.prototype.removeAnnotations = function( marginalia )
{
	var notesElement = this.getNotesElement( marginalia );
	var child = notesElement.firstChild;
	var annotations = new Array( );
	while ( null != child )
	{
		if ( child.annotation )
		{
			annotations[ annotations.length ] = child.annotation;
			child.annotation = null;
		}
		notesElement.removeChild( child );
		child = notesElement.firstChild;
	}
	var micro = this;
	var stripTest = function( tnode )
		{ return micro.highlightStripTest( tnode, null ); };
	domutil.stripMarkup( this.getContentElement( ), stripTest, true );
	//portableNormalize( this.getContentElement( ) );
	domutil.removeClass( this.getElement( ), AN_ANNOTATED_CLASS );
	return annotations;
}

/**
 * Remove an individual annotation from a post
 */
PostMicro.prototype.removeAnnotation = function( marginalia, annotation )
{
	var next = this.removeNote( marginalia, annotation );
	this.removeHighlight( marginalia, annotation );

	// Reposition markers if necessary
	if ( 'edit' == annotation.action )
		this.repositionBlockMarkers( marginalia );
	
	return null == next ? null : next.annotation;
}

/* ************************ Display Actions ************************ */
/* These are called by event handlers.  Unlike the handlers, they are
 * not specific to controls or events (they should make no assumptions
 * about the event that triggered them). */

/**
 * Indicate an annotation is under the mouse cursor by lighting up the note and the highlight
 * If flag is false, this will remove the lit-up indication instead.
 * Works even if the highlight region is missing.
 */
PostMicro.prototype.flagAnnotation = function( marginalia, annotation, className, flag )
{
	// Activate the note
	var noteNode = document.getElementById( AN_ID_PREFIX + annotation.getId() );
	if ( flag )
		domutil.addClass( noteNode, className );
	else
		domutil.removeClass( noteNode, className );

	// Activate the highlighted areas
	var highlights = domutil.childrenByTagClass( this.getContentElement( ), null, AN_HIGHLIGHT_CLASS, null, null );
	for ( var i = 0;  i < highlights.length;  ++i )
	{
		var node = highlights[ i ];
		// Need to change to upper case in case this is HTML rather than XHTML
		if ( node.tagName.toUpperCase( ) == 'EM' && node.annotation == annotation )
		{
			if ( flag )
				domutil.addClass( node, className );
			else
				domutil.removeClass( node, className );
		}
	}
}


/**
 * Called to start editing a new annotation
 * the annotation isn't saved to the db until edit completes
 */
PostMicro.prototype.createAnnotation = function( marginalia, annotation, editor )
{
	// Ensure the window doesn't scroll by saving and restoring scroll position
	var scrollY = domutil.getWindowYScroll( );
	var scrollX = domutil.getWindowXScroll( );

	annotation.isLocal = true;
	
	// Show the annotation and highlight
	var nextNode = this.getAnnotationNextNote( marginalia, annotation );
	this.addAnnotation( marginalia, annotation, nextNode, editor );
	
	// HACK: Some editors may perform an action, then revert to the regular annotation
	// display by calling _saveAnnotation or _cancelAnnotationEdit.  If this is the
	// case, edit mode will be cancelled and there's no more to do display-wise.
	// I say this is a hack, because there should be a better way to indicate that
	// outcome (or to trigger it - a show() function that doesn't actually show is
	// very confusing).
	if ( marginalia.noteEditor )
	{
		// Focus on the text edit
		var noteElement = document.getElementById( AN_ID_PREFIX + annotation.getId() );
		// Sequencing here (with focus last) is important
		this.repositionNotes( marginalia, noteElement.nextSibling );
		marginalia.noteEditor.focus( );
		
		window.scrollTo( scrollX, scrollY );
		
		if ( marginalia.onMarginHeight )
			marginalia.onMarginHeight( this );
	}
}


/**
 * Cancel an annotation edit in progress
 * This restores the annotation's previous values or removes one that wasn't
 * yet created on the server.
 */
PostMicro.prototype.cancelAnnotationEdit = function( marginalia, annotation )
{
	if ( ! marginalia.noteEditor )
		return false;
	
	this.removeAnnotation( marginalia, annotation );
	if ( ! annotation.isLocal )
	{
		copy( marginalia.noteEditor.annotationOrig, annotation );
		this.addAnnotation( marginalia, annotation );
	}
	
	this.flagAnnotation( marginalia, annotation, AN_EDITINGNOTE_CLASS, false );
	domutil.removeClass( document.body, AN_EDITINGNOTE_CLASS );
}


/**
 * Save an annotation after editing
 */
PostMicro.prototype.saveAnnotation = function( marginalia, annotation )
{
	// Don't allow this to happen more than once
	if ( ! marginalia.noteEditor )
		return false;
	
	// Save any changes to the annotation
	if ( marginalia.noteEditor.save )
		marginalia.noteEditor.save( );
	
	// ---- Validate the annotation ----

	// Check the length of the note.  If it's too long, do nothing, but restore focus to the note
	// (which is awkward, but we can't save a note that's too long, we can't allow the note
	// to appear saved, and truncating it automatically strikes me as an even worse solution.) 
	if ( marginalia.noteEditor.annotation.getNote().length > MAX_NOTE_LENGTH )
	{
		alert( getLocalized( 'note too long' ) );
		marginalia.noteEditor.focus( );
		return false;
	}
	
	// Similarly for the length of a link
	if ( marginalia.noteEditor.annotation.getLink().length > MAX_LINK_LENGTH )
	{
		alert( getLocalized( 'link too long' ) );
		marginalia.noteEditor.focus( );
		return false;
	}
	
	// Note and quote length cannot both be zero
	var sequenceRange = annotation.getSequenceRange( );
	if ( sequenceRange.start.compare( sequenceRange.end ) == 0 && annotation.getNote( ).length == 0 )
	{
		alert( getLocalized( 'blank quote and note' ) );
		marginalia.noteEditor.focus( );
		return false;
	}
	
	// Now that validation's complete, start storing things
	Marginalia.saveEditPrefs( marginalia, annotation, marginalia.noteEditor );

	// Ensure the window doesn't scroll by saving and restoring scroll position
	var scrollY = domutil.getWindowYScroll( );
	var scrollX = domutil.getWindowXScroll( );
	
	// Remove events and editor display
	this.stopEditing( marginalia, annotation );

	// TODO: listItem is an alias for noteElement
	var listItem = document.getElementById( AN_ID_PREFIX + annotation.getId() );
	
	// For annotations with links; insert, or substitute actions, must update highlight also
	if ( 'edit' == annotation.action && annotation.hasChanged( 'note' ) || annotation.hasChanged( 'link' ) )
	{
		this.removeHighlight( marginalia, annotation );
		this.showHighlight( marginalia, annotation );
	}
	
	// ---- Update the annotation contents ----
	
	// The annotation is local and needs to be created in the DB
	if ( annotation.isLocal )
	{
		var postMicro = this;
		var f = function( url ) {
			// update the annotation with the created ID
			var id = url.substring( url.lastIndexOf( '/' ) + 1 );
			annotation.setId( id );
			annotation.resetChanges( );
			annotation.isLocal = false;
			var noteElement = document.getElementById( AN_ID_PREFIX + '0' );
			noteElement.id = AN_ID_PREFIX + annotation.getId();
			var highlightElements = domutil.childrenByTagClass( postMicro.getContentElement( ), 'em', AN_ID_PREFIX + '0', null, null );
			for ( var i = 0;  i < highlightElements.length;  ++i )
			{
				domutil.removeClass( highlightElements[ i ], AN_ID_PREFIX + '0' );
				domutil.addClass( highlightElements[ i ], AN_ID_PREFIX + annotation.getId() );
			}
		};
		annotation.setUrl( this.getUrl( ) );
		
		// IE may have made a relative URL absolute, which could cause problems
		if ( null != marginalia.baseUrl
			&& annotation.url.substring( 0, marginalia.baseUrl.length ) == marginalia.baseUrl )
		{
			annotation.setUrl( annotation.getUrl().substring( marginalia.baseUrl.length ) );
		}

		annotation.setQuoteTitle( this.getTitle( ) );
		annotation.setQuoteAuthorId( this.getAuthorId( ) );
		annotation.setQuoteAuthorName( this.getAuthorName( ) );
		// ^ author name is usually ignored, as the server will know from the ID
		//   but conceivably there might be systems where this is not so
		marginalia.createAnnotation( annotation, f );
	}
	// The annotation already exists and needs to be updated
	else
		marginalia.updateAnnotation( annotation, null );
	
	// ---- Redraw the note ----
	// Update the link hover (if present)
	this.showLink( marginalia, annotation );
	
	// Replace the editable note display
	var nextNode = this.removeNote( marginalia, annotation );
	noteElement = this.showNote( marginalia, annotation, nextNode );
	this.repositionNotes( marginalia, noteElement.nextSibling );

	// May need to reposition block markers
	if ( annotation.action == 'edit' && annotation.hasChanged( 'note' ) || annotation.hasChanged( 'link' ) )
		this.repositionBlockMarkers( marginalia );
	
	window.scrollTo( scrollX, scrollY );
	
	// Margin height callback
	if ( marginalia.onMarginHeight )
		marginalia.onMarginHeight( this );
	
	return true;
}


/**
 * Stop edit mode
 * This removes the editor display, but doesn't replace it with anything
 */
PostMicro.prototype.stopEditing = function( marginalia, annotation )
{
	// Remove events
	removeEvent( document.documentElement, 'click', _saveAnnotation );
	var noteElement = document.getElementById( AN_ID_PREFIX + annotation.getId() );
	removeEvent( noteElement, 'click', domutil.stopPropagation );
	
	// Clear the editor
	marginalia.noteEditor.clear();
	marginalia.noteEditor = null;
	while ( noteElement.firstChild )
		noteElement.removeChild( noteElement.firstChild );

	this.flagAnnotation( marginalia, annotation, AN_EDITINGNOTE_CLASS, false );
	domutil.removeClass( document.body, AN_EDITINGNOTE_CLASS );
}


/**
 * Delete an annotation
 */
PostMicro.prototype.deleteAnnotation = function( marginalia, annotation )
{
	// Pop up a warning (if configured)
	if ( marginalia.warnDelete )
	{
		if ( ! confirm( getLocalized( 'warn delete' ) ) )
			return;
	}
	
	// Ensure the window doesn't scroll by saving and restoring scroll position
	var scrollY = domutil.getWindowYScroll( );
	var scrollX = domutil.getWindowXScroll( );

	// Check whether this annotation is being edited - if so, cancel the edit
	if ( marginalia.noteEditor && annotation == marginalia.noteEditor.annotation )
		this.stopEditing( marginalia, annotation );
	
	// Delete it on the server
	if ( ! annotation.isLocal )
		marginalia.deleteAnnotation( annotation.getId(), null );
	
	// Find the annotation
	var next = this.removeAnnotation( marginalia, annotation );
	if ( null != next )
	{
		var nextElement = document.getElementById( AN_ID_PREFIX + next.id );
		this.repositionNotes( marginalia, nextElement );
	}
	annotation.destruct( );
	
	window.scrollTo( scrollX, scrollY );
	
	// Margin height callback
	if ( marginalia.onMarginHeight )
		marginalia.onMarginHeight( this );
}


/* ************************ Event Handlers ************************ */
/* Each of these should capture an event, obtain the necessary information
 * to execute it, and dispatch it to something else to do the work */

/**
 * Mouse hovers over an annotation note or highlight
 */
function _hoverAnnotation( event )
{
	var post = domutil.nestedFieldValue( this, AN_POST_FIELD );
	var annotation = domutil.nestedFieldValue( this, AN_ANNOTATION_FIELD );
	post.flagAnnotation( window.marginalia, annotation, AN_HOVER_CLASS, true );
}

/**
 * Mouse hovers off an annotation note or highlight
 */
function _unhoverAnnotation( event )
{
	// IE doesn't have a source node for the event, so use this
	var post = domutil.nestedFieldValue( this, AN_POST_FIELD );
	var annotation = domutil.nestedFieldValue( this, AN_ANNOTATION_FIELD );
	post.flagAnnotation( window.marginalia, annotation, AN_HOVER_CLASS, false );
}

/**
 * Hit any key in document
 */
function _keyupCreateAnnotation( event )
{
	var marginalia = window.marginalia;
	if ( null != marginalia.loginUserId && ( marginalia.loginUserId == marginalia.displayUserId || '' == marginalia.displayUserId ) )
	{
		// Enter to create a regular note
		if ( 13 == event.keyCode )
		{
			if ( createAnnotation( null, false, marginalia.newEditor() ) )
				event.stopPropagation( );
		}
	}
}

/**
 * When the user creates a zero-length text range, show the position with a marker
 */
function _caretUpHandler( event )
{
	// Verify W3C range support
	if ( ! window.getSelection )
		return;
	
	// Strip any existing caret (in case it wasn't already caught)
	_caretDownHandler( );
	
	var selection = window.getSelection();
	if ( selection.rangeCount == null )
		return;
	if ( selection.rangeCount == 0 )
		return;
	var textRange = selection.getRangeAt( 0 );

	if ( null != textRange )
	{
		// Save selection position
		var container = textRange.startContainer;
		var offset = textRange.startOffset;

		if ( domutil.parentByTagClass( container, null, PM_CONTENT_CLASS ) )
		{
			// Only show the caret if it's a point (i.e. the range has zero length)
			if ( container == textRange.endContainer && offset == textRange.endOffset )
			{
				// Insert the caret
				var caret = document.createElement( 'span' );
				caret.appendChild( document.createTextNode( '>' ) );
				caret.setAttribute( 'id', AN_RANGECARET_ID );
				textRange.insertNode( caret );
/*
				
				if ( ELEMENT_NODE == container.nodeType )
				{
					return false;
				}
				else if ( TEXT_NODE == container.nodeType )
				{
					var textBefore = container.nodeValue.substring( 0, offset );
					var textAfter = container.nodeValue.substring( offset );
					
					container.nodeValue = textBefore;
					container.parentNode.insertBefore( caret, container.nextSibling );
					var afterNode = document.createTextNode( textAfter );
					container.parentNode.insertBefore( afterNode, caret.nextSibling );
					
					if ( selection.removeAllRanges && selection.addRange )
					{
						selection.removeAllRanges( );
						textRange.setStart( afterNode, 0 );
						textRange.setEnd( afterNode, 0 );
						selection.addRange( textRange );
					}
					else
					{
						textRange.setStart( container, offset );
						textRange.setEnd( container, offset );
					}
				}
				*/
			}
		}
	}
}

// Remove any carets when the mouse button goes down
function _caretDownHandler( event )
{
	// Verify W3C range support
	if ( ! window.getSelection )
		return;
	
	var caret = document.getElementById( AN_RANGECARET_ID );
	if ( caret )
	{
		var selection = window.getSelection();
		
		var remove = false;
		if ( selection.rangeCount == null )
			remove = true;
		else if ( selection.rangeCount == 0 )
			remove = true;
		else
		{
			var textRange = selection.getRangeAt( 0 );
			if ( textRange.prevSibling != caret )
			{
				var before = caret.prevSibling;
				caret.parentNode.removeChild( caret );
				domutil.normalizeNodePair( before );
			}
		}
	}
}

function _skipCaret( node )
{
	return node.id == AN_RANGECARET_ID;
}

/**
 * Skip embedded action text created by Marginalia
 * Not needed - ins and del nodes are instead highlight em
 */
function _skipAnnotationActions( node )
{
	if ( ELEMENT_NODE == node.nodeType && 'ins' == domutil.getLocalName( node ).toLowerCase() )
	{
		if ( node.parentNode && domutil.hasClass( node.parentNode, AN_HIGHLIGHT_CLASS ) )
			return true;
	}
	return false;
}


/*
 * Handler for standard createAnnotation button
 * Application may choose to do things otherwise (e.g. for edit actions)
 */
function clickCreateAnnotation( event, id, editor )
{
	// This might be called from a handler not set up by addEvent,
	// so use the clumsy functions.
	event = domutil.getEvent( event );
	domutil.stopPropagation( event );
	createAnnotation( id, true, editor );
}


/**
 * Create a highlight range based on user selection.
 *
 * This is not in the event handler section above because it's up to the calling
 * application to decide what control creates an annotation.  Deletes and edits,
 * on the other hand, are built-in to the note display.
 *
 * That said, the standard interface calls this from clickCreateAnnotation
 */
function createAnnotation( postId, warn, editor )
{
	var marginalia = window.marginalia;

	// Can't create an annotation while one is being edited
	if ( marginalia.noteEditor )
		return false;
	
	// Test for selection support (W3C or IE)
	if ( ( ! window.getSelection || null == window.getSelection().rangeCount )
		&& null == document.selection )
	{
		if ( warn )
			alert( getLocalized( 'browser support of W3C range required for annotation creation' ) );
		return false;
	}
		
	var textRange0 = getPortableSelectionRange();
	if ( null == textRange0 )
	{
		if ( warn )
			alert( getLocalized( 'select text to annotate' ) );
		return false;
	}
	
	// Strip off leading and trailing whitespace and preprocess so that
	// conversion to WordRange will go smoothly.
	var textRange = TextRange.fromW3C( textRange0 );
	textRange = textRange.shrinkwrap( marginalia.skipContent );
	if ( ! textRange )
	{
		// this happens if the shrinkwrapped range has no non-whitespace text in it
		if ( warn )
			alert( getLocalized( 'x select text to annotate' ) );
		return false;
	}
	
	// Check for an annotation with id 0.  If one exists, we can't send another request
	// because the code would get confused by the two ID values coming back.  In that
	// case (hopefully very rare), silently fail.  (I figure the user doesn't want to
	// see an alert pop up, and the natural human instinct would be to try again).
	if ( null != document.getElementById( AN_ID_PREFIX + '0' ) )
		return;
	

	if ( null == postId )
	{
		var contentElement = domutil.parentByTagClass( textRange.startContainer, null, PM_CONTENT_CLASS, false, null );
		if ( null == contentElement )
			return false;
		postId = domutil.parentByTagClass( contentElement, null, PM_POST_CLASS, true, PostMicro.skipPostContent ).id;
	}
	
	var post = marginalia.listPosts( ).getPostById( postId );
	
	// Confirm that the selection is within the post
	var contentElement = post.getContentElement( );
	if ( ! ( ( domutil.isElementDescendant( textRange.startContainer,contentElement )
		|| textRange.startContainer == contentElement )
		&& ( domutil.isElementDescendant( textRange.endContainer, contentElement )
		|| textRange.endContainer == contentElement ) ) )
	{
		if ( warn )
			alert( getLocalized( 'invalid selection' ) );
		return false;
	}
	
	var annotation = new Annotation( {
		url: post.getUrl( ),
		userid: marginalia.loginUserId,
		quoteAuthorId: post.getAuthorId( ),
		quoteAuthorName: post.getAuthorName( )
	} );
	
	// Must strip smartcopy as it contains a <br> element which will confuse
	// the range engine.  It's safe to do this because stripsubtree only
	// removes elements - it doesn't remove, normalize, or otherwise modify
	// the text nodes used by the textRange for reference.
	domutil.stripSubtree( post.getContentElement( ), null, 'smart-copy' );

	var wordRange = WordRange.fromTextRange( textRange, post.getContentElement( ), marginalia.skipContent );
	var sequenceRange = wordRange.toSequenceRange( post.getContentElement( ) );
	var xpathRange = wordRange.toXPathRange( post.getContentElement( ) );
	
	// Compress whitespace in quote down to a single space
	var quote = getTextRangeContent( textRange, marginalia.skipContent );
	quote = quote.replace( /(\s|\u00a0)+/g, ' ' );
	annotation.setQuote( quote );
	
	if ( 0 == annotation.getQuote().length )
	{
		annotation.destruct( );
		if ( warn )
			alert( getLocalized( 'zero length quote' ) );
		trace( null, "zero length quote '" + annotation.getQuote() + "'" );
		return false;
	}
	
	annotation.setSequenceRange( sequenceRange );
	annotation.setXPathRange( xpathRange );

	// TODO: test selection properly
	if ( null == annotation )
	{
		if ( warn )
			alert( getLocalized( 'invalid selection' ) );
		return false;
	}
	
	// Check to see whether the quote is too long (don't do this based on the raw text 
	// range because the quote strips leading and trailing spaces)
	if ( annotation.getQuote().length > MAX_QUOTE_LENGTH )
	{
		annotation.destruct( );
		if ( warn )
			alert( getLocalized( 'quote too long' ) );
		return false;
	}
	
	// If no editor is specified, use the default
	if ( ! editor )
		editor = marginalia.newEditor( annotation );
	
	post.createAnnotation( marginalia, annotation, editor );
	
	return true;
}
