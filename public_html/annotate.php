<?php

/*
 * annotate.php
 * handles annotation http requests
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
 * $Id: annotate.php 405 2008-12-22 01:56:03Z geof.glass $
 */

require_once( "../engine/marginalia-php/MarginaliaHelper.php" );
require_once( "../engine/marginalia-php/Annotation.php" );
require_once( "../engine/marginalia-php/AnnotationService.php" );
require_once( "../engine/marginalia-php/config.php" );
require_once( "annotate-db.php" );

class DemoAnnotationService extends AnnotationService
{
	var $db;
	
	function DemoAnnotationService( )
	{
		global $CFG;
	
		$curuser = array_key_exists( 'curuser', $_GET ) ? $_GET[ 'curuser' ] : 'anonymous';		

		AnnotationService::AnnotationService( $CFG->host, $CFG->annotate_servicePath, $CFG->installDate, $curuser );
	}

	function beginRequest( )
	{
		global $CFG;
		$this->db = new AnnotationDB( );
		if ( ! $this->db->open( $CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->db ) )
		{
			$this->httpError( 500, 'Internal Service Error', 'Unable to connect to database' );
			return False;
		}
		return True;
	}
	
	function endRequest( )
	{
		$this->db->release( );
	}
		
	function doListAnnotations( $url, $username, $block )
	{
		file_put_contents("/home/czuk/nlp/workspace/GPWKorpusWeb/dump.txt", "url:".$url."\nuser:".$username."\nblock:".$block);

		$annotations = $this->db->listAnnotations( $url, $username, $block );
		if ( $annotations )
		{
			foreach ( $annotations as $annotation )
			{
				// In a real application, the username should be human-readable while the userid must be unique.
				// But since the demo doesn't have a lookup table to map from userid to username, just use userid
				// as username.
				$annotation->setUserName( $annotation->getUserId( ) );
			}
		}
		return $annotations;
	}
	
	function doGetAnnotation( $id )
	{
		$annotation = $this->db->getAnnotation( $id );
		// In a real application, the username should be human-readable while the userid must be unique.
		// But since the demo doesn't have a lookup table to map from userid to username, just use userid
		// as username.
		$annotation->setUserName( $annotation->getUserId( ) );
		return $annotation;
	}
	
	function doCreateAnnotation( &$annotation )
	{
		// This is a hack to allow testing of multiuser features:
		$annotation->setUserId( array_key_exists( 'userid', $_POST ) ? $_POST[ 'userid' ] : 'anonymous' );
		return $this->db->createAnnotation( $annotation );
	}
	
	function doUpdateAnnotation( $annotation )
	{
		return $this->db->updateAnnotation( $annotation );
	}
	
	function doDeleteAnnotation( $id )
	{
		$this->db->deleteAnnotation( $id );
		return True;
	}
}

$annotationService = new DemoAnnotationService( );
$annotationService->dispatch( );


?>
