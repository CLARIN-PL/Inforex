<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

/*
 * Implementation of DatabaseEngine Interface with PDO library
 */

class PDODatabaseEngine implements IDatabaseEngine {

	private $pdo	= null;

	public function __construct($dsn) {
        if(!is_array($dsn)) {
            $dsn = parse_database_uri($dsn);
        }
        $pdodriver = "mysql";
        $host = isset($dsn["hostspec"]) ? $dsn["hostspec"] : 'localhost';
        $dbname = isset($dsn["database"]) ? $dsn["database"] : 'inforex';
        $username = isset($dsn["username"]) ? $dsn["username"] : 'inforex';
        $password = isset($dsn["password"]) ? $dsn["password"] : '';
        $port = isset($dsn["port"]) ? ";port=".$dsn["port"] : '';
            // "mysql:host=localhost;dbname=DB;charset=UTF8"
            // new pdo('mysql:host=127.0.0.1;port=3306;dbname=mysql;charset=utf8mb4','user','password',array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,))
        $options = array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,);
        try {
		    $this->pdo = new PDO($pdodriver.":host=".$host.$port.";dbname=".$dbname,$username,$password,$options);
            //$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            throw new DatabaseException($e->getMessage(),$this->pdo);
        }

	} // __construct()

    public function prepareAndExecute($sql,$args=null) {

        if ($args == null){
            try {
                $result = $this->pdo->query($sql);
            } catch(PDOException $e) {
                throw new DatabaseException($e->getMessage(),$this->pdo);
            }
        } else {
            try {
                // returns PDOStatement object, false or Exception
                $sth = $this->pdo->prepare($sql);
            } catch(PDOException $e) {
                throw new DatabaseException($e->getMessage(),$this->pdo);
            }
            if ($sth instanceOf PDOStatement) {
                try {
                    // returns bool: true or false
                    if($sth->execute($args)) {
                        $result = $sth; // Object of PDOStatement class
                    } else {
                        $result = false;
                    }
                } catch(PDOException $e) {
                    throw new DatabaseException($e->getMessage(),$this->pdo);
                }
            } 
        }
        // $result is PDOStatement object or false
        return ($result == false) ? null : $result;

    } // prepareAndExecute

    /**
     * Returns the autoincrement ID if supported or $id or fetches the current
     * ID in a sequence called: $table.(empty($field) ? '' : '_'.$field)
     *  In mysql driver implemetation is realized by send to base 
     *  SQL query: "SELECT LAST_INSERT_ID()"
     *
     * @return  string id
     *
     */
	public function lastInsertID() {

		$result = $this->pdo->lastInsertID();
        	// $result is string 
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

		return $this->pdo->quote($text);

	} // escape()


    /**
     * This method is used to collect information about last database error
     *
     * @return  array   with PDO errorcode, native error code, native message
     *
     */
    public function errorInfo() {

               $result = array( null, 0, "" ); // default no error
               $errorInfo =  $this->pdo->errorInfo();
               if( $errorInfo[0] != 0 ){
                    $result = $errorInfo;
               }
               return $result;

    }  // errorInfo()

} // of PDODatabaseEngine class


?>
