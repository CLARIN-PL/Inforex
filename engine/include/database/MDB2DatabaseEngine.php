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
		$this->mdb2 = MDB2::connect($dsn, $options);
        if (PEAR::isError($this->mdb2)) {
            throw new DatabaseException($this->mdb2->getMessage(),$this->mdb2);
        }
		// to eliminate some problems with prepare statements
		// which is specific for mysql driver only
		if($this->mdb2->phptype==='mysql') {
			$this->mdb2->setOption('emulate_prepared',True);
		}
		$this->mdb2->loadModule('Extended');
		$this->mdb2->query("SET SESSION query_cache_type = ON");    
		// hint for default using '0000-00-00' datetime field in tasks table :o(
        $this->mdb2->query("SET @old_sql_mode := @@sql_mode");
        $this->mdb2->query("SET @new_sql_mode := @old_sql_mode");
        $this->mdb2->query("SET @new_sql_mode := TRIM(BOTH ',' FROM REPLACE(CONCAT(',',@new_sql_mode,','),',NO_ZERO_DATE,' ,','))");
        $this->mdb2->query("SET @new_sql_mode := TRIM(BOTH ',' FROM REPLACE(CONCAT(',',@new_sql_mode,','),',NO_ZERO_IN_DATE,',','))");
        $this->mdb2->query("SET @@sql_mode := @new_sql_mode;");
    
	} // __construct()

    public function prepareAndExecute($sql,$args=null) {

        if ($args == null){
            $result = $this->mdb2->query($sql);
        } else {
            if (MDB2::isError($sth = $this->mdb2->prepare($sql))){
                $result = $sth; // MDB2_Error object
            } else {
                $result = $sth->execute($args); // MDB2_Result_xxx
                $sth->free();
            }
        }
        // $result is MDB2_Result_xxx or MDB2_Error object
        // all MDB2::Error errors converts to exception
        if (MDB2::isError($result)){
            throw new DatabaseException($result->getMessage() . "\n" . $result->getUserInfo(), $result);
        }
		// now we have MDB_Result_xxx 
		// encapsulate it in MDB2_Result wrapper
		$result = new MDB2DatabaseResult($result);

        return $result;

    } // prepareAndExecute

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

		$result = $this->mdb2->lastInsertID();
        	// $result is string or MDB2_Error object
        	// all MDB2::Error errors converts to exception
        	if (MDB2::isError($result)){
            		throw new DatabaseException($result->getMessage() . "\n" . $result->getUserInfo(), $result);
        	}
 		return $result;

	} // lastInsertID

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
    public function escape($text) {

		return $this->mdb2->escape($text);

	} // escape()


    /**
     * This method is used to collect information about last database error
     *
     * @return  array   with MDB2 errorcode, native error code, native message
     *
     */
    public function errorInfo() {

               $result = array( null, 0, "" ); // default no error
               $errorMDB2 =  $this->mdb2->errorInfo();
               if( is_array($errorMDB2) ){
                       if($errorMDB2[0]) {
                               $result = $errorMDB2;
                       }
               }
               return $result;

    }  // errorInfo()

} // of MDB2DatabaseEngine class


?>
