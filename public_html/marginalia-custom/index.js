/*
	The preferences object is required for load and storing user preferences, such as
	whether the user last created an annotation using a keywords drop-down or a text
	entry field.  The current implementation is a static dummy.
	  
	Keywords initialization is needed for the keywords drop-down.  It is called now to
	fetch the list of keywords so they will be ready when needed.
*/

// Needed when creating annotations:
ANNOTATION_ACCESS_DEFAULT = 'private';	// default access

function myClickCreateAnnotation( event, id )
{
	if ( 'bungeni' == window.marginaliaUiStyle )
		return clickCreateAnnotation( event, id, new SelectActionNoteEditor() );
	else
		return clickCreateAnnotation( event, id );
}

function demoOnLoad( userid, queryUrl, serviceRoot, uiStyle )
{
//	initLogging( );
	window.marginaliaUiStyle = uiStyle;
	
	// Uncomment RestAnnotationService if you have set up a database, or
	// StaticAnnotationService for a static demo.
	var annotationService = new RestAnnotationService( serviceRoot + '/annotate.php', { } );
	// var annotationService = new StaticAnnotationService(serviceRoot, 'example-annotations.xml' );
	
	var keywordService = new RestKeywordService( serviceRoot + '/keywords.txt');
	keywordService.init( );
	var preferences = new Preferences( new StaticPreferenceService( ) );
	
	marginaliaArgs = {
		preferences: preferences,
		keywordService: keywordService,
		baseUrl:  null,
		showAccess:  true,
		showBlockMarkers:  true,
		onkeyCreate:  true,
		warnDelete: false,
		showCaret: false,
		userInRequest: true
	};
	
	if ( 'bungeni' == uiStyle )
	{
		marginaliaArgs.showActions = true;
		marginaliaArgs.displayNote = bungeni.displayNote;
		marginaliaArgs.editors = {
			'default':  Marginalia.newEditorFunc( BungeniNoteEditor ),
			freeform:  Marginalia.newEditorFunc( BungeniNoteEditor )
		};
	}		
	
	window.marginalia = new Marginalia( annotationService, userid, userid, marginaliaArgs );
	
	if ( 'bungeni' == uiStyle )
		trackchanges.addEditShortcuts( );
	
	var marginaliaDirect = new MarginaliaDirect( annotationService );
	marginaliaDirect.init( );
	window.marginaliaQueryUrl = queryUrl;
	window.marginalia.showAnnotations( queryUrl );
}


function initLogging( )
{
	var log = window.log = new ErrorLogger( false, true );

	// Set these to true to view certain kinds of events
	// Most of these are only useful for debugging specific areas of code.
	// annotation-service, however, is particularly useful for most debugging
	log.setTrace( 'annotation-service', true );	// XMLHttp calls to the annotation service
	log.setTrace( 'word-range', false );			// Word Range calculations (e.g. converting from Text Range)
	log.setTrace( 'xpath-range', false );			// Trace XPath ranges
	log.setTrace( 'find-quote', false );			// Check if quote matches current state of document
	log.setTrace( 'node-walk', false );			// Used for going through nodes in document order
	log.setTrace( 'show-highlight', false );		// Text highlighting calculations
	log.setTrace( 'align-notes', false );			// Aligning margin notes with highlighting
	log.setTrace( 'range-compare', false );		// Compare range positions
	log.setTrace( 'range-string', false );			// Show conversions of word ranges to/from string
	log.setTrace( 'list-annotations-xml', false );// Show the full Atom XML coming back from listAnnotations
	log.setTrace( 'WordPointWalker', false );		// Show return values from WordPointWalker
	log.setTrace( 'prefs', false );				// List fetched preferences
	log.setTrace( 'keywords', false );				// List fetched keywords
	log.setTrace( 'BlockPoint.compare', false );	// Compare two BlockPoints
	log.setTrace( 'range-timing', false );			// Calculate the speed of range calculations
	log.setTrace( 'highlight-timing', false );	// Calculate the speed of highlight display
	log.setTrace( 'actions', false );				// Insertion of action text
	log.setTrace( 'behavior', true );				// Behavior mappings
}
