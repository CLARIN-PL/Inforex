<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

/**
 * Abstract Database Engine interface.
 */


interface IDatabaseEngine {

	function __construct($dsn);
	function prepareAndExecute($sql,$args=null); 

    /**
     * Returns the last autoincrement ID as string
     *  if supported SQL query: "SELECT LAST_INSERT_ID()"
     *
     * @return  mixed id as string or exception thrown on error
     *
     */
 
	function lastInsertID(); 
 
    /**
     * Quotes a string so it can be safely used in a query. It will quote
     * the text so it can safely be used within a query.
     *
     * @param   string  the input string to quote
     *
     * @return  string  quoted string
     *
     * @access  public
     */
    function escape($text);

	function errorInfo();

}

?>
