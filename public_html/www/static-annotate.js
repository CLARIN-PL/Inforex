/*
 * static-annotate.js
 *
 * Marginalia has been developed with funding and support from
 * BC Campus, Simon Fraser University, and the Government of
 * Canada, and units and individuals within those organizations.
 * Many thanks to all of them.  See CREDITS.html for details.
 * Copyright (C) 2005-2007 Geoffrey Glass www.geof.net
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
 */
 
/*
 * Initialize the REST annotation service
 */
function StaticAnnotationService( serviceUrl, filename )
{
	this.serviceUrl = serviceUrl;
	this.filename = filename;
	this.current_id = 1000; 
	return this;
}

/**
 * Fetch a list of annotated blocks
 */
StaticAnnotationService.prototype.listBlocks = function( url, f )
{
	if ( f )
		f( null );
}

/*
 * Fetch a list of annotations from the server
 */
StaticAnnotationService.prototype.listAnnotations = function ( url, userid, block, f )
{
	var serviceUrl = this.serviceUrl + '/' + this.filename;
	var xmlhttp = domutil.createAjaxRequest( );
	xmlhttp.open( 'GET', serviceUrl );
	xmlhttp.onreadystatechange = function( ) {
		if ( 4 == xmlhttp.readyState ) {
			if ( 200 == xmlhttp.status ) {
				if ( null != f )
				{
					f( xmlhttp.responseXML );
				}
			}
			else {
				logError( "AnnotationService.listAnnotations failed with code " + xmlhttp.status );
			}
			xmlhttp = null;
		}
	}
	trace( 'annotation-service', "AnnotationService.listAnnotations " + serviceUrl );
	xmlhttp.send( null );
}

/*
 * Create an annotation on the server
 */
StaticAnnotationService.prototype.createAnnotation = function( annotation, f )
{
	this.current_id += 1;
	if ( null != f )
		f( this.serviceUrl + '/' + this.filename + '/' + this.current_id );
}

/*
 * Update an annotation on the server
 */
StaticAnnotationService.prototype.updateAnnotation = function( annotation, f )
{
	if ( null != f )
		f( null );
}

/*
 * Delete an annotation on the server
 */
StaticAnnotationService.prototype.deleteAnnotation = function( annotationId, f )
{
	if ( null != f )
		f( null );
}

