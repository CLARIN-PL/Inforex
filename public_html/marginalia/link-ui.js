/*
 * link-ui.js
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
 * $Id: link-ui.js 327 2008-11-26 23:31:50Z geof.glass $
 */

AN_LINK_CLASS = 'annotation-link';	// class given to a nodes for link annotations
AN_LINKEDIT_ID = 'editing-link';	// ID assigned to note element whose link is being edited
AN_LINKDELETEBUTTON_CLASS = 'delete-link';
MAX_LINK_LENGTH = 255;


/**
 * Display a link icon for an annotation link
 * Requires that the highlighting for the annotation already be displayed
 * (this is how it figures out just where to put the link icon)
 */
PostMicro.prototype.showLink = function( marginalia, annotation )
{
	this.hideLink( marginalia, annotation );
	
	if ( null != annotation.link && '' != annotation.link )
	{
		var highlights = domutil.childrenByTagClass( this.getContentElement( ), 'em', AN_ID_PREFIX + annotation.getId(), null, null );
		for ( var i = 0;  i < highlights.length;  ++i )
		{
			if ( domutil.hasClass( highlights[ i ], AN_LASTHIGHLIGHT_CLASS ) )
			{
				// TODO: should check whether a link is valid in this location;  if not,
				// either refuse to show or insert a clickable Javascript object instead
				var lastHighlight = highlights[ i ];

				var linkTitle = '';
				if ( null != annotation.getNote() && '' != annotation.getNote() )
				{
					if ( marginalia.keywordService )
					{
						var keyword = marginalia.keywordService.getKeyword( annotation.getNote() );
						if ( keyword )
							linkTitle = keyword.name + ': ' + keyword.description;
					}
					if ( ! linkTitle )
					{
						linkTitle = annotation.getNote().length > MAX_NOTEHOVER_LENGTH
							? annotation.getNote().substr( 0, MAX_NOTEHOVER_LENGTH ) + '...'
							: annotation.getNote();
					}
				}
				
				lastHighlight.appendChild( domutil.element( 'sup', {
					content:  domutil.element( 'a', {
						className:  AN_LINK_CLASS + ' ' + AN_ID_PREFIX + annotation.getId(),
						title:  linkTitle,
						href:  annotation.getLink(),
						content:  AN_LINK_ICON } )
					} ) );
			}
		}
	}
}


/**
 * Remove the link icon (if present)
 */
PostMicro.prototype.hideLink = function( marginalia, annotation )
{
	var existingLink = domutil.childByTagClass( this.getContentElement( ), 'a', AN_ID_PREFIX + annotation.getId(), null );
	if ( existingLink )
		existingLink.parentNode.removeChild( existingLink );	
}

/**
 * Don't call the linkUI implementation's methods directly - go through here instead so that
 * nested linkUI's will have a chance to execute
 */
PostMicro.prototype.showLinkEdit = function( marginalia, annotation, noteElement )
{
	marginalia.linkUI.showLinkEdit( marginalia, this, annotation, noteElement );
}

PostMicro.prototype.showLinkEditComplete = function( marginalia, annotation, noteElement )
{
	marginalia.linkUI.showLinkEditComplete( marginalia, this, annotation, noteElement );
}


/**
 * Edit an annotation link.  Calls custom behavior in marginalia.linkUI
 */
PostMicro.prototype.editAnnotationLink = function( marginalia, annotation )
{
	var post;
	if ( domutil.instanceOf( marginalia, Marginalia ) )
		post = this;
	else
	{
		var event = marginalia;
		var target = domutil.getEventTarget( event );
		annotation = domutil.nestedFieldValue( target, AN_ANNOTATION_FIELD );
		post = domutil.nestedFieldValue( target, AN_POST_FIELD );
		marginalia = window.marginalia;
	}
	
	annotation.editing = AN_EDIT_LINK;
	marginalia.editing = annotation;
	var noteElement = annotation.getNoteElement( );
	domutil.addClass( noteElement, AN_EDITINGLINK_CLASS );
	marginalia.linkUi.showLinkEdit( marginalia, post, annotation, noteElement );
}


/**
 * Save an annotation link after editing
 * Called by the UI implementation which has already update the annotation object,
 * and which will update the edit part of the display.
 * The caller should validate the link (for length at least) before setting it in the
 * annotation.
 */
PostMicro.prototype.saveAnnotationLink = function( marginalia, annotation, noteElement )
{
	// Update edit status
	delete annotation.editing;
	marginalia.editing = null;
	domutil.removeClass( noteElement, AN_EDITINGLINK_CLASS );

	this.flagAnnotation( marginalia, annotation, AN_HOVER_CLASS, false );
	marginalia.updateAnnotation( annotation, null );

	// Update the link display
	this.showLink( marginalia, annotation );
	
	return true;
}


/**
 * Delete a link
 * Can be called as:
 * - post.deleteLink( marginalia, annotation )
 * - deleteLink( event ) 
 */
PostMicro.prototype.deleteLink = function( marginalia, annotation )
{
	// Delete the link on the server
	annotation.setLink( '' );
	marginalia.updateAnnotation( marginalia, annotation, null );

	// Update display
	if ( annotation.editing )
		this.linkUI.showLinkEditComplete( marginalia, this, annotation );
	this.showLink( marginalia, annotation );

	// Update edit status
	delete annotation.editing;
	marginalia.editing = null;
	var noteElement = annotation.getNoteElement( );
	domutil.removeClass( noteElement, AN_EDITINGLINK_CLASS );
}



/**
 * Skip embedded links created by Marginalia
 */
function _skipAnnotationLinks( node )
{
	return ELEMENT_NODE == node.nodeType && domutil.hasClass( node, AN_LINK_CLASS );
//		&& node.parentNode
//		&& 'a' == domutil.getLocalName( node )
//		&& domutil.hasClass( node.parentNode, AN_HIGHLIGHT_CLASS );
}


