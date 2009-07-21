/*
 * marginalia-direct.js
 *
 * Call directly in to Marginalia for debugging purposes.
 * Activate MarginaliaDirect with Shift-Ctrl-S
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
 * $Id: marginalia-direct.js 340 2008-11-30 06:50:46Z geof.glass $
 */
 
function MarginaliaDirect( marginaliaService )
{
	this.marginaliaService = marginaliaService;
	window.marginaliaDirect = this;
	return this;
}

MarginaliaDirect.prototype.init = function( )
{
	addEvent( window, 'keyup', _marginaliaDirectKeypressHandler );
}

function _marginaliaDirectKeypressHandler( event )
{
	var direct = window.marginaliaDirect;
	var character = String.fromCharCode( event.which );
	if ( ( 'm' == character || 'M' == character ) && event.shiftKey && event.ctrlKey )
	{
		var box = document.getElementById( 'marginalia-direct' );
		if ( null != box )
			direct.hide( );
		else
			direct.show( );
	}
}

MarginaliaDirect.prototype.show = function( )
{
	var direct = this;
	
	document.body.appendChild( domutil.element( 'div', {
		id: 'marginalia-direct'
		}, [
			domutil.element( 'h1', null, 'Marginalia Direct Console' ),
			domutil.element( 'fieldset', null, [
				domutil.element( 'legend', null, 'Find Annotations' ),
				this.newInputField( 'md-annotation-user', null, 'User', window.marginalia.displayUserId, true ),
				this.newInputField( 'md-annotation-url', null, 'URL', window.location, true ),
				domutil.element( 'button', {
					id: 'md-find',
					onclick: function() { direct.listAnnotations( ) }
				}, 'Find' )
			] ),
			domutil.element( 'div', {
				id: 'md-annotation-list' } ),
			domutil.element( 'button', {
				id: 'md-close',
				onclick: function() { direct.hide() }
			}, 'Close' )
		]
	) );
}

MarginaliaDirect.prototype.listAnnotations = function( )
{
	var user = document.getElementById( 'md-annotation-user' );
	var url = document.getElementById( 'md-annotation-url' );
	
	var direct = this;
		
	// Clear out any existing list items
	var annotationList = document.getElementById( 'md-annotation-list' );
	while ( annotationList.firstChild )
		annotationList.removeChild( annotationList.firstChild );

	this.marginaliaService.listAnnotations( url.value, user.value, null, function( xml ) { direct.showAnnotations( xml ); } );
}

MarginaliaDirect.prototype.deleteAnnotation = function( annotation )
{
	var direct = this;
	this.marginaliaService.deleteAnnotation( annotation.id, function() { direct.annotationDeleted( annotation.id ); } );
}

MarginaliaDirect.prototype.annotationDeleted = function( id )
{
	var annotationList = document.getElementById( 'md-annotation-list' );
	for ( var item = annotationList.firstChild;  item;  item = item.nextSibling )
	{
		if ( item.annotation && item.annotation.getId() == id )
		{
			item.annotation = null;
			annotationList.removeChild( item );
		}
	}
}

MarginaliaDirect.prototype.updateAnnotation = function( listItem )
{
	var direct = this;
	var annotation = listItem.annotation;
	annotation.setUrl( this.getFieldInput( listItem, 'md-annotation-url' ).value );
	annotation.setSequenceRange( SequenceRange.fromString( this.getFieldInput( listItem, 'md-annotation-sequence-range' ).value ) );
	annotation.setXPathRange( XPathRange.fromString( this.getFieldInput( listItem, 'md-annotation-xpath-range' ).value ) );
	annotation.setQuote( this.getFieldInput( listItem, 'md-annotation-quote' ).value );
	annotation.setNote( this.getFieldInput( listItem, 'md-annotation-note' ).value );
	annotation.setLink( this.getFieldInput( listItem, 'md-annotation-link' ).value );
	this.marginaliaService.updateAnnotation( annotation, null );
}

MarginaliaDirect.prototype.getFieldInput = function( listItem, fieldName )
{
	var field = domutil.childByTagClass( listItem, null, fieldName );
	field = domutil.childByTagClass( field, 'input', null );
	return field;
}
	

MarginaliaDirect.prototype.showAnnotations = function( xml )
{
	var annotations = parseAnnotationXml( xml );
	for ( var i = 0;  i < annotations.length;  ++i )
		this.showAnnotation( annotations[ i ] );
	return 0;
}

MarginaliaDirect.prototype.showAnnotation = function( annotation )
{
	var direct = this;
	var annotationList = document.getElementById( 'md-annotation-list' );

	var xpathRange = annotation.getXPathRange( );
	var sequenceRange = annotation.getSequenceRange( );
	
	var listItem = domutil.element( 'fieldset', {
		annotation: annotation
		}, domutil.element( 'legend', null, '#' + annotation.getId() + ' by ' + annotation.getUserId() ) );
	annotationList.appendChild( listItem );
	
	domutil.addContent( listItem, null, [
		// URL, Range, Access
		this.newInputField( null, 'md-annotation-url', 'URL', annotation.getUrl(), true ),
		this.newInputField( null, 'md-annotation-sequence-range', 'Sequence Range', sequenceRange.toString(), true ),
		this.newInputField( null, 'md-annotation-xpath-range', 'XPath Range', 
			xpathRange ? xpathRange.toString() : '', true ),
		this.newInputField( null, 'md-annotation-access', 'Access', annotation.getAccess(), true ),

		// Quote, Note, Link
		this.newInputField( null, 'md-annotation-quote', 'Quote', annotation.getQuote(), true ),
		this.newInputField( null, 'md-annotation-note', 'Note', annotation.getNote(), true ),
		this.newInputField( null, 'md-annotation-link', 'Link', annotation.getLink(), true ),

		// Last updated
		domutil.element( 'p', {
			className: 'updated'
			}, 'Last updated ' + annotation.updated ),
		
		domutil.element( 'button', {
			id: 'md-annotation-update',
			onclick: function() { direct.updateAnnotation( listItem ); }
			}, 'Update' ),
		domutil.element( 'button', {
			id: 'md-annotation-delete',
			onclick: function() { direct.deleteAnnotation( annotation ); }
		}, 'Delete' )
	] );
}



MarginaliaDirect.prototype.newInputField = function( id, className, text, value, enabled )
{
	var input = domutil.element( 'input', {
		id: id,
		value: value } );
	if ( ! enabled )
		input.setAttribute( 'disabled', 'disabled' );

	return domutil.element ( 'div', {
		className: 'field ' + className
		}, [
			domutil.element( 'label', {
				id: id + 'label',
				'for': id
				}, text ),
			input
		]
	);
}

MarginaliaDirect.prototype.hide = function( )
{
	var box = document.getElementById( 'marginalia-direct' );
	box.parentNode.removeChild( box );
}

