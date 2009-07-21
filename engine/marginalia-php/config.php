<?php

// $Id$

// This is for testing purposes only:  a real implementation requires authentication.
$USER->username = 'anonymous';

unset( $CFG );

// Use Bungeni-style interface?
$CFG->bungeniStyle = false;

// Database connection info
$CFG->dbhost = 'localhost';
$CFG->db = 'annotation';
$CFG->dbuser = 'root';
$CFG->dbpass = 'krasnal';
$CFG->dbannotation = 'annotations';
$CFG->dbopen = 0;

// Installation URL
$CFG->host = 'localhost';
$CFG->wwwapp = '/gpw/www';
$CFG->wwwserver = 'http://' . $CFG->host . '/';
$CFG->wwwroot = 'http://' . $CFG->host . $CFG->wwwapp;

// The server-side scripts (annotate.php etc.) are assumed to live here:
$CFG->serviceURL = $CFG->wwwroot;

// This should be the URL to index.php - though for most operations it
// doesn't have to be (if it isn't, links in the Atom feed will be wrong):
$CFG->annotatedUrl = $CFG->wwwroot;

// Path to annotation service;  used in Atom feed
$CFG->annotate_servicePath = $CFG->wwwapp . '/annotate.php';

// Date of application installation
// Used for tag URIs in the Atom feed.  Don't change this unless you want to 
// mark all feed data as modified.
$CFG->installDate = strtotime( '1970-01-01 00:00' );

// Microformat CSS Classes
// These classes are needed by annotation to discover the title, author, etc. of a fragment
// for annotation.  These can be attached to the appropriate fields if they are displayed, otherwise
// they must be in hidden elements.  They must match the values used in annotation.js.
define( 'FRAGMENT_CLASS', 'xentry' );
define( 'CONTENT_CLASS', 'content' );
define( 'TITLE_CLASS', 'title' );
define( 'AUTHOR_CLASS', 'author' );
define( 'DATE_CLASS', 'posted' );
define( 'URL_CLASS', 'entrylink' );  // unlike the others, the value is in the href, not the text content

?>
