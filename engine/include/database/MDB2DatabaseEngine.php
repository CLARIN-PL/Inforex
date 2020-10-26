<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

/*
 * Implementation of DatabaseEngine Interface with MDB2 library
 */

// for access to MDB2/Datatype.php
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(__DIR__.'/../../external/pear'));
require_once(__DIR__."/../../external/pear/MDB2.php");

class MDB2DatabaseEngine implements IDatabaseEngine {

	private $mdb2	= null;

	public function __construct($dsn) {
		$options = array('portability' => MDB2_PORTABILITY_NONE);
        $options['debug']=2;
		$options['result_buffering']=false;
		// to eliminate some problems with prepare statements
		$options['emulate_prepared']=true;
		$this->mdb2 = MDB2::connect($dsn, $options);
		if (PEAR::isError($this->mdb2)) {
			throw new Exception($this->mdb2->getMessage());
		}
		$this->mdb2->loadModule('Extended');
		$this->mdb2->query("SET SESSION query_cache_type = ON");        
	} // __construct()

	public function disconnect() {

		$this->mdb2->disconnect();

	} // disconnect()

	/**
     * Execute a manipulation query to the database and return the number 
	 * of affected rows
     *
     * @param   string  the SQL query
     *
     * @return  mixed   number of affected rows on success, a MDB2 error 
	 *					on failure
     */
	public function exec($query) {

		return $this->mdb2->exec($query);

	} // exec()

    /**
     * Send a query to the database and return any results
     *
     * @param   string  the SQL query
     *
     * @return mixed   an MDB2_Result handle on success, a MDB2 error on failure
     *
     */
	public function query($query) {

		return $this->mdb2->query($query);

	} // query()

	/**
     * Prepares a query for multiple execution with execute().
     * With some database backends, this is emulated.
     * prepare() requires a generic query as string like
     * 'INSERT INTO numbers VALUES(?,?)' or
     * 'INSERT INTO numbers VALUES(:foo,:bar)'.
     * The ? and :name and are placeholders which can be set using
     * bindParam() and the query can be sent off using the execute() method.
     * The allowed format for :name can be set with the 'bindname_format' option.
     *
     * @param   string  the query to prepare
	 *
     * @return  mixed   resource handle for the prepared query on success,
     *                  a MDB2 error on failure
     */
    public function prepare($query) {
 
		return $this->mdb2->prepare($query);

	} // prepare()

	/**
     * Returns the autoincrement ID if supported or $id or fetches the current
     * ID in a sequence called: $table.(empty($field) ? '' : '_'.$field)
	 *  In mysql driver implemetation is realized by send to base 
	 *  SQL query: "SELECT LAST_INSERT_ID()"
     *
     * @return  mixed   MDB2 Error Object or id
     *
     */
	public function lastInsertID() {

		return $this->mdb2->lastInsertID();

	} // lastInsertID

	 /**
     * Convert a text value into a DBMS specific format that is suitable to
     * compose query statements.
	 *   requires: Datatype module 
     *
     * @param   string  text string value that is intended to be converted.
     * @param   string  type to which the value should be converted to
     * @param   bool    quote
     * @param   bool    escape wildcards
     *
     * @return  string  text string that represents the given argument value in
     *       a DBMS specific format.
     */
    function quote($value, $type = null, $quote = true, $escape_wildcards = false) {

		// all calls from our application have: $type="text", $quote=true
		// and no $escape_wildcards is specified
		//  May be we should convert this to singleargs function

		return $this->mdb2->quote($value,$type,$quote,$escape_wildcards);

	} // quote()
 
    /**
     * This method is used to collect information about an error
     *
     * @param   mixed   error code or resource
     *
     * @return  array   with MDB2 errorcode, native error code, native message
     *
     */
    function errorInfo($error = null) {

		return $this->mdb2->errorInfo($error);

    }  // errorInfo()

} // of MDB2DatabaseEngine class


?>
