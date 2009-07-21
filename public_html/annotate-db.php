<?PHP

/*
 * annotate-db.php
 *
 * THIS IS A DEMO.  It lacks important security checks for a running application - in
 * particular, it trusts the userid passed in from the request.
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
 *
 * $Id: annotate-db.php 399 2008-12-20 00:13:24Z geof.glass $
 */

// Yeah, gotta love the mess that is PHP
function unfix_quotes( $value )
{
	return get_magic_quotes_gpc( ) != 1 ? $value : stripslashes( $value );
}


class AnnotationDB
{
	function open( $dbhost, $dbuser, $password, $dbname )
	{
		global $CFG;
		// I'm connecting directly to mysql for simplicity here.  Don't forget to set password correctly in mysql:
		// SET PASSWORD FOR 'some_user'@'some_host' = OLD_PASSWORD('newpwd');
		mysql_connect( $dbhost, $dbuser, $password );
		if ( ! @mysql_select_db( $dbname ) )
			return false;
		$CFG->dbopen += 1;
		return true;
	}
		
	function release( )
	{
		global $CFG;
		$CFG->dbopen -= 1;
		if ( 0 == $CFG->dbopen )
			mysql_close( );
	}

	function createAnnotation( &$annotation )
	{
		global $CFG;

		$link = $annotation->getLink( );
		$sequenceRange = $annotation->getSequenceRange( );
		$xpathRange = $annotation->getXPathRange( );
		$sequenceStart = $sequenceRange->getStart( );
		$sequenceEnd = $sequenceRange->getEnd( );
		$xpathStart = $xpathRange->getStart( );
		$xpathEnd = $xpathRange->getEnd( );

		$sUser			= addslashes( $annotation->getUserId( ) );
		$sUrl			= addslashes( $annotation->getUrl( ) );
		$sNote			= addslashes( $annotation->getNote( ) );
		$sAccess		= addslashes( $annotation->getAccess( ) );
		$sAction		= addslashes( $annotation->getAction( ) );
		$sQuote			= addslashes( $annotation->getQuote( ) );
		$sQuote_title	= addslashes( $annotation->getQuoteTitle( ) );
		$sQuote_author	= addslashes( $annotation->getQuoteAuthorId( ) );
		$sLink			= null == $link ? 'null' : "'".addslashes( $link )."'";
		$sLinkTitle		= addslashes( $annotation->getLinkTitle( ) );
		
		// In a running application, all queries should be parameterized for security,
		// not concatenated together as I am doing here.
		$query = "insert into $CFG->dbannotation "
			. "(userid, url, note, access, action"
			. ", quote, quote_title, quote_author, link, link_title, created"
			. ", start_xpath, start_block, start_line, start_word, start_char"
			. ", end_xpath, end_block, end_line, end_word, end_char"
			. ") values ("
			. "'$sUser', '$sUrl', '$sNote', '$sAccess', '$sAction'"
			. ", '$sQuote', '$sQuote_title', '$sQuote_author', $sLink, '$sLinkTitle', now()"
			. ", '".$xpathStart->getPathStr()."', '".$sequenceStart->getPaddedPathStr()
			."', ".$sequenceStart->getLines().", ".$sequenceStart->getWords().", ".$sequenceStart->getChars()
			. ", '".$xpathEnd->getPathStr()."', '".$sequenceEnd->getPaddedPathStr()
			."', ".$sequenceEnd->getLines().", ".$sequenceEnd->getWords().", ".$sequenceEnd->getChars()
			. ")";
	//	echo "\nQUERY: $query\n\n";
		mysql_query( $query );
		$r = 1 == mysql_affected_rows( ) ? mysql_insert_id( ) : 0;
		return $r;
	}
	
	function deleteAnnotation( $id )
	{
		global $CFG;
		
		$sId = (int) $id;
		// In a running application, all queries should be parameterized for security,
		// not concatenated together as I am doing here.
		$query = "delete from $CFG->dbannotation where id=$sId";
		
		mysql_query( $query );
		$r = mysql_affected_rows( ) == 1 ? true : false;
		return $r;
	}
	
	function updateAnnotation( &$annotation )
	{
		global $CFG;
		
		$sId = (int) $annotation->getAnnotationId( );
		
		$query = '';
		
		$rangeForWords = null;
		
		// Sequence Range
		$sequenceRange = $annotation->getSequenceRange( );
		if ( null !== $sequenceRange )
		{
			$sStartBlock = addslashes( $sequenceRange->start->getPaddedPathStr( ) );
			$sEndBlock = addslashes( $sequenceRange->end->getPaddedPathStr( ) );
			$query = AnnotationDB::appendToUpdateStr( $query, "start_block='$sStartBlock'" );
			$query = AnnotationDB::appendToUpdateStr( $query, "end_block='$sEndBlock'" );
			$rangeForWords = $sequenceRange;
		}
		
		// XPath Range
		$xpathRange = $annotation->getXPathRange( );
		if ( ! $rangeForWords && null !== $xpathRange )
		{
			// did not used to test rangeForWords above, so if both sequence and xpath ranges
			// were present the xpath range was used.  This causes problems for the front-end
			// range update code.  Why was this the choice?
			$sStartXPath = addslashes( $xpathRange->start->getPathStr( ) );
			$sEndXPath = addslashes( $xpathRange->end->getPathStr( ) );
			$query = AnnotationDB::appendToUpdateStr( $query, "start_xpath='$sStartXPath'" );
			$query = AnnotationDB::appendToUpdateStr( $query, "end_xpath='$sEndXPath'" );
			$rangeForWords = $xpathRange;
		}
		
		// Set start and end words and chars if appropriate from block or xpath range
		// Must do it only once, even if both block and xpath range are set
		if ( null !== $rangeForWords )
		{
			$sStartLines = null === $rangeForWords->start->lines ? 'null' : (int) $rangeForWords->start->lines;
			$sStartWords = null === $rangeForWords->start->words ? 'null' : (int) $rangeForWords->start->words;
			$sStartChars = null === $rangeForWords->start->chars ? 'null' : (int) $rangeForWords->start->chars;
			$sEndLines = null === $rangeForWords->start->lines ? 'null' : (int) $rangeForWords->end->lines; 
			$sEndWords = null === $rangeForWords->start->words ? 'null' : (int) $rangeForWords->end->words;
			$sEndChars = null === $rangeForWords->start->chars ? 'null' : (int) $rangeForWords->end->chars;
			$query = AnnotationDB::appendToUpdateStr( $query, "start_line=$sStartLines" );
			$query = AnnotationDB::appendToUpdateStr( $query, "start_word=$sStartWords" );
			$query = AnnotationDB::appendToUpdateStr( $query, "start_char=$sStartChars" );
			$query = AnnotationDB::appendToUpdateStr( $query, "end_line=$sEndLines" );
			$query = AnnotationDB::appendToUpdateStr( $query, "end_word=$sEndWords" );
			$query = AnnotationDB::appendToUpdateStr( $query, "end_char=$sEndChars" );
		}
		
		// Note
		$note = $annotation->getNote( );
		if ( null !== $note )
		{
			$sNote = addslashes( $note );
			$query = AnnotationDB::appendToUpdateStr( $query, "note='$sNote'" );
		}
		
		// Quote
		$quote = $annotation->getQuote( );
		if ( null !== $note )
		{
			$sQuote = addslashes( $quote );
			$query = AnnotationDB::appendToUpdateStr( $query, "quote='$sQuote'" );
		}
		
		// Access
		$access = $annotation->getAccess( );
		if ( null !== $access )
		{
			$sAccess = addslashes( $access );
			$query = AnnotationDB::appendToUpdateStr( $query, "access='$sAccess'" );
		}
		
		// Action
		$action = $annotation->getAction( );
		if ( null != $action )
		{
			$sAction = addslashes( $action );
			$query = AnnotationDB::appendToUpdateStr( $query, "action='$sAction'" );
		}
			
		// Link
		$link = $annotation->getLink( );
		if ( null !== $link )
		{
			// TODO: Add extra security check on URL here
			$sLink = addslashes( $link );
			$query = AnnotationDB::appendToUpdateStr( $query, "link='$sLink'" );
		}
		
		// Link Title
		$linkTitle = $annotation->getLinkTitle( );
		if ( null !== $linkTitle )
		{
			$sLinkTitle = addslashes( $linkTitle );
			$query = AnnotationDB::appendToUpdateStr( $query, "link_title='$sLinkTitle'" );
		}
		
		// TODO: In a running application, all queries should be parameterized for security,
		// not concatenated together as I am doing here.
		$query = "update $CFG->dbannotation set $query where id=$sId";
		
		mysql_query( $query );
		
		// (Somewhat) perversely, if fields are set to the values they already have (i.e. there is no actual change),
		// the number of affected rows is zero.
		$r = mysql_affected_rows( );
		$r = $r == 0 || $r == 1 ? true : false;
		return $r;
	}
	
	function appendToUpdateStr( $query, $assignment )
	{
		if ( null == $query || '' == $query )
			return $assignment;
		else
			return $query . ', ' . $assignment;
	}

	function getQueryCondition( $url, $userid, $block )
	{
		// determine the filter for the select statement
		$sUrl = addslashes( $url );
		$cond = strchr( $sUrl, '*' ) === false ? "where url='$sUrl' " : "where url like '" . str_replace( '*', '%', $sUrl ) . "'";
		if ( $userid != null )
		{
			$sUser = addslashes( $userid );
			$cond .= " and userid='$sUser'";
		}
		else
			$cond .= " and access='public'";
		
		if ( $block != null )
		{
			$sBlockStr = addslashes( $block->getPaddedPathStr( ) );
			$cond .= " and start_block <= '$sBlockStr' and end_block >= '$sBlockStr'";
		}
		return $cond;
	}
	
	// Get the last updated time for a particular annotation query
	function getFeedLastModified( $url, $userid )
	{
		global $CFG;
		
		// In a running application, all queries should be parameterized for security,
		// not concatenated together as I am doing here.
		$cond = AnnotationDB::getQueryCondition( $url, $userid );
		$query = "select max(modified) modified from $CFG->dbannotation $cond";
		$result = mysql_query( $query );
		if ( $result )
		{
			$row = mysql_fetch_assoc( $result );
			return $row ? strtotime( $row[ 'modified' ] ) : $CFG->installDate;
		}
		else
			return strtotime( $CFG->installDate );
	}
	
	function getAnnotation( $annotationId )
	{
		global $CFG;
		
		$annotationId = (int) $annotationId;
		$query = "select * from $CFG->dbannotation where id=$annotationId";
		$result = mysql_query( $query );
		if ( $result )
		{
			$row = mysql_fetch_assoc( $result );
			$annotation = $this->rowToAnnotation( $row );
			return $annotation;
		}
		else
			return null;
	}
	
	function listAnnotations( $url, $userid, $block )
	{
		global $CFG;
		
		$cond = AnnotationDB::getQueryCondition( $url, $userid, $block );
		
		// Get the data rows
		$query = "select * from $CFG->dbannotation $cond";
		$query .= " order by url, start_block, start_line, start_word, start_char, end_block, end_line, end_word, end_char";
		//echo "Query: " . htmlspecialchars( $query ) ."<br/>";
		$result = mysql_query( $query );

		$annotations = array( );
		if ( $result )
		{
			// Individual entries ----
			while( $row = mysql_fetch_assoc( $result ) )
				$annotations[ ] = $this->rowToAnnotation( $row );
		}
		return $annotations;
	}
	
	function rowToAnnotation( $row )
	{
		$annotation = new Annotation( );
		$error = $annotation->fromArray( $row );
		if ( $error )
		{
			echo "[error: $error]";
			return null;
		}
		else
		{
			if ( $row[ 'start_block' ] !== null && $row[ 'end_block' ] !== null )
			{
				$sequenceRange = new SequenceRange(
					new SequencePoint( $row[ 'start_block' ], $row[ 'start_line' ], $row[ 'start_word' ], $row[ 'start_char' ] ),
					new SequencePoint( $row[ 'end_block' ], $row[ 'end_line' ], $row[ 'end_word' ], $row[ 'end_char' ] ) );
				$annotation->setSequenceRange( $sequenceRange );
			}
			if ( $row[ 'start_xpath' ] !== null )
			{
				$xpathRange = new XPathRange(
					new XPathPoint( $row[ 'start_xpath' ], $row[ 'start_line' ], $row[ 'start_word' ], $row[ 'start_char' ] ),
					new XPathPoint( $row[ 'end_xpath' ], $row[ 'end_line' ], $row[ 'end_word' ], $row[ 'end_char' ] ) );
				$annotation->setXPathRange( $xpathRange );
			}
			return $annotation;
		}
	}
	
}

?>
