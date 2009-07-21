
function bungeniMarginaliaInit( username, url, serviceRoot ) 
{
	var annotationService = new RestAnnotationService( serviceRoot + '/annotate', false );
	window.marginalia = new Marginalia( annotationService, username, username, {
		preferences: new Preferences( new RestPreferenceService( serviceRoot + '/preference', true ) ),
		baseUrl:  null,
		showAccess:  true,
		showBlockMarkers:  true,
		showActions:  true,
		onkeyCreate:  true,
		warnDelete: false,
		displayNote: bungeni.displayNote,
		editors: {
			'default':  Marginalia.newEditorFunc( BungeniNoteEditor ),
			freeform:  Marginalia.newEditorFunc( BungeniNoteEditor )
		}
	} );

	trackchanges.addEditShortcuts( );
//	var marginaliaDirect = new MarginaliaDirect( annotationService );
//	marginaliaDirect.init( );
	initLogging();

	window.marginalia.showAnnotations( url );
}

function bungeniClickCreateEdit( )
{
}

bungeni = {
	editType:  function( annotation, isEditing )
	{
		if ( annotation.getAction() == 'edit' )
		{
			if ( isEditing || annotation.getNote() )
			{
				if ( annotation.getQuote() )
					return getLocalized( 'note replace label' );
				else
					return getLocalized( 'note insert label' );
			}
			else
				return getLocalized( 'note delete label' );
		}
		else
			return getLocalized( 'note note label' );	
	},
	
	standardNoteDisplay: function( marginalia, annotation, noteElement, params, isEditing )
	{

		noteElement.appendChild( domutil.element( 'span', {
			className: 'note-type',
			content: bungeni.editType( annotation, isEditing )
		} ) );
		
		if ( params.isCurrentUser )
		{
			var controls = domutil.element( 'div', { className: 'controls' } );
			noteElement.appendChild( controls );

			// add the link button
			if ( params.linkingEnabled )
			{
				controls.appendChild( domutil.button( {
					className:  AN_LINKBUTTON_CLASS,
					title:  getLocalized( 'annotation link button' ),
					content:  AN_LINK_EDIT_ICON
				} ) );
			}
	
			// add the access button
			if ( marginalia.showAccess )
			{
				controls.appendChild( domutil.button( {
					className:  AN_ACCESSBUTTON_CLASS,
					title:  getLocalized( annotation.getAccess() == AN_PUBLIC_ACCESS ? 'public annotation' : 'private annotation' ),
					content:  annotation.getAccess() == AN_PUBLIC_ACCESS ? AN_SUN_SYMBOL : AN_MOON_SYMBOL
				} ) );
			}
			
			// add the delete button
			controls.appendChild( domutil.button( {
				className:  AN_DELETEBUTTON_CLASS,
				title:  getLocalized( 'delete annotation button' ),
				content:  'x'
			} ) );
	
			marginalia.bindNoteBehaviors( annotation, controls, [
				[ 'button.annotation-link', { click: 'edit link' } ],
				[ 'button.annotation-access', { click: 'access' } ],
				[ 'button.annotation-delete', { click: 'delete' } ]
			] );
		}
	},
	
	displayNote: function( marginalia, annotation, noteElement, params )
	{
		bungeni.standardNoteDisplay( marginalia, annotation, noteElement, params, false );
		
		// add the text content
		var noteText = document.createElement( 'p' );
		var titleText = null;
	
		if ( ! params.quoteFound || ! annotation.getSequenceRange( ) )
			titleText = getLocalized( 'quote not found' ) + ': \n"' + annotation.getQuote() + '"';
		else if ( params.keyword )
			titleText = params.keyword.description;
		
		if ( titleText )
			noteText.setAttribute( 'title', titleText );
		
		// If this doesn't belong to the current user, add the name of the owning user
		if ( ! params.isCurrentUser )
		{
			domutil.addClass( noteElement, 'other-user' );
			noteText.appendChild( domutil.element( 'span', {
				className:  'username',
				content:  annotation.getUserId( ) + ': ' } ) );
		}
		noteText.appendChild( document.createTextNode( annotation.getNote() ) );
		noteElement.appendChild( noteText );
		
		// Return behavior mappings
		if ( params.isCurrentUser )
		{
			marginalia.bindNoteBehaviors( annotation, noteElement, [
				[ 'p', { click: 'edit' } ]
			] );
		}
	}
}
	

function BungeniNoteEditor( )
{
	this.editNode = null;
}

BungeniNoteEditor.prototype.bind = FreeformNoteEditor.prototype.bind;
BungeniNoteEditor.prototype.clear = FreeformNoteEditor.prototype.clear;
BungeniNoteEditor.prototype.save = FreeformNoteEditor.prototype.save;
BungeniNoteEditor.prototype.focus = FreeformNoteEditor.prototype.focus;

BungeniNoteEditor.prototype.show = function( )
{
	var postMicro = this.postMicro;
	var marginalia = this.marginalia;
	var annotation = this.annotation;
	var noteElement = this.noteElement;
	
	bungeni.standardNoteDisplay( marginalia, annotation, noteElement, {
		isCurrentUser: true,
		linkingEnabled: true,
	}, true );
	
	// Create the edit box
	this.editNode = document.createElement( "textarea" );
	this.editNode.rows = 3;
	this.editNode.appendChild( document.createTextNode( annotation.getNote() ) );

	// Set focus after making visible later (IE requirement; it would be OK to do it here for Gecko)
	this.editNode.annotationId = this.annotation.getId();
	addEvent( this.editNode, 'keypress', _editNoteKeypress );
	addEvent( this.editNode, 'keyup', _editChangedKeyup );
	
	this.noteElement.appendChild( this.editNode );
}
