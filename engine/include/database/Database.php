<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

// dla dostępności MDB2::MDB2_PORTABILITY_NONE przy inicjowaniu $db tylko
require_once(__DIR__."/../../../engine/external/pear/MDB2.php");

/**
 * Database gateway. 
 */
class Database{
	
	private $mdb2 = null; // instance of IDatabaseEngine class
	var $log = false;

	private $_encoding = "utf8mb4";

	/**
	 * @param dsn {array}
	 * @param log {boolean} -- print logs (default: false)
	 * @param log_output {String} -- where to print logs: fb (use fb function), print (use print),
	 */
	public function __construct($dsn, $log=false, $log_output="chrome_php", $encoding="utf8mb4"){
        // mysql library driver is not supported in PHP 7.x at all
        if(array_key_exists('phptype',$dsn) 
           && ($dsn['phptype']=='mysql')
           && version_compare(phpversion(),'7.0.0','>=')
          ) {
            throw new DatabaseException("Driver 'mysql' is not supported in PHP7, check your configuration 'phptype' setting, try using 'mysqli'."); 
        }
		$this->mdb2 = new MDB2DatabaseEngine($dsn);
		$this->set_encoding($encoding);
		$this->log = $log;
		$this->log_output = $log_output;
	}
	
	/**
	 * reset encoding to comunicate with database 
	 */
	public function set_encoding($encoding) {
		$this->_encoding = $encoding;
		// SET CHARACTER SET sets only subset of SET NAMES params
                //$this->execute("SET CHARACTER SET '$encoding'");
                $this->execute("SET NAMES '$encoding'");
	} // set_encoding()

	/**
	 * Log message using Database internal logger.
	 */
	private function log_message($message){
		if ( $this->log ){
			if ($this->log_output == "print"){
				print '<pre>\n'.$message.'</pre>\n';
			}
			elseif ($this->log_output == "chrome_php"){
				ChromePhp::log($message);
			}
		}
	}
	
	/**
	 * Log SQL statement with backtrace of its execution.
	 * @param $sql {String} SQL query.
	 * @param $args {Array} Query arguments
	 */
	private function log_sql($sql, $args){
		if ( $this->log ){
			$backtrace = array();
			foreach (debug_backtrace() as $d){
// When use register_shutdown_function, and the function called when shutting down, there are no line number nor filename information about this function, only function, class(if possible), type(if possible) and args are provided. We must provides service for potentialy missing index
				$backtrace[] = sprintf("File %s, line %d, %s%s%s(...)", 
					(isset($d['file']) ? $d['file'] : __FILE__), 
					(isset($d['line']) ? $d['line'] : ""), 
					(isset($d['class']) ? $d['class'] : ""), 
					(isset($d['type']) ? $d['type'] : ""), 
					(isset($d['function']) ? $d['function'] : "")
				);
			}

			if ($this->log_output == "print"){
				$msg = "SQL LOG\n";
				$msg .= $sql . "\n";
				$msg .= implode("\n", $backtrace);
				$msg .= print_r($args, true);
				print '<pre>\n'.$msg.'</pre>\n';
			}
			elseif ($this->log_output == "chrome_php"){
				ChromePhp::log($sql);
				ChromePhp::log($args);
				ChromePhp::log($backtrace);
			}
            // FB class included only in dev environment
			elseif (    ($this->log_output == "fb")
                        && (class_exists('FB'))
                   ) {
				FB::info($sql, "SQL LOG");
				fb($args, "Args");
				fb($backtrace, "Backtrace");
			}
			else {
				throw new DatabaseException("Unknown log mode ".$this->log_output.". Expected one of the following: print, chrome_php, fb");
			}
		}
	}

	/**
	 * Execute query with optional argument and return result of the execution.
	 * @param $sql {String} SQL query.
	 * @param $args {Array} Query argumnets.
	 *
	 * @returns MDB2_Result_Common object 
	 * any error is converted to DatabaseException, which should be catched
     * 
	 */
	function execute($sql, $args=null){
		$time_start = microtime(TRUE);
		//$sth = null;
		$result = null;
		try{
			$this->log_sql($sql, $args);
			if ($this->log){
				$this->log_message($args, "SQL DATA");
			}
			$result=$this->mdb2->prepareAndExecute($sql,$args);
			if ($this->log)
	        	$this->log_message('Execute time: '.number_format(microtime(TRUE)-$time_start, 6).' s.', "SQL");
		}		
		catch(DatabaseException $ex){
			// re-throw it as-is
			throw new DatabaseException($ex->getMessage(),$ex->getDetails());
		}
		catch(Exception $ex){
			// rethrow all other exception as DatabaseExceptions
			throw new DatabaseException($ex->getMessage());
		}

        return $result;

	} // execute
	
	/**
	 * Execute query and return result as an array of assoc arrays.
	 * @param $sql {String} SQL query.
	 * @param $args {Array} Query arguments.
	 * @return {Array} Array of arrays (rows) 
	 *         or DatabaseException on error
	 */
	function fetch_rows($sql, $args = null){
		return $this->execute($sql, $args)->fetchAll();
	}

	/**
	 * Return one-dimensional array of values for given column for each row
	 * returned by the query.
	 * @param $sql {String} SQL query.
	 * @param $column {String} Column name.
	 * @param $args {Array} Query arguments.
	 * @return {Array} An array of strings, i.e. array("one", "two", "three") 
	 */
	function fetch_ones($sql, $column, $args = null){
		$rows = $this->fetch_rows($sql, $args);
		$vals = array();
		foreach ($rows as $row){
			if(array_key_exists($column,$row)) {
				$vals[] = $row[$column];
			} else { // error
				throw new DatabaseException(
					"Column $column doesn't exists in results of $sql query.",
					array( 	"sql"=>$sql,
						"column" => $column,
						"args" => $args
					)
				);
			}
		}
		return $vals;
	}
	
	/**
	 * Return a one-dimensional array of values representing a single row
	 * returned by the query.
	 * @param $sql {String} SQL query.
	 * @param $args {Array} Query arguments.
	 * @return {Array} An assoc array of strings ( may be empty ) 
	 *         DatabaseException thrown on error
	 */
	function fetch($sql, $args=null){
		$result = $this->fetch_rows($sql,$args);
		return is_array($result) && (count($result)>0) ? $result[0] : array() ;
						
	}

	/**
	 * Return a single value for the first row.
         * @param $sql {String} SQL query.
         * @param $args {Array} Query arguments.
         * @return one scalar value or null if result is empty
         *         DatabaseException thrown on error
	 */
	function fetch_one($sql, $args=null){

		$result = $this->fetch($sql,$args);
		// select list of values from assoc array, and get first one
		// or null if empty
		return is_array($result) && (count($result)>0) ? array_values($result)[0] : null ;
		
	}

	/**
	 * 
	 * returns string type because of type of autoincrement field may
	 * be BIGINT
	 */
	function last_id(){
		return $this->mdb2->lastInsertID();
	}
	
	/**
	 * Update row values from a table for given key.
	 * @param table Name of a table.
	 * @param values Assoc array with values to update, i.e. array("column"=>"value")
	 * @param keys Assoc array with keys, i.e. array("key"=>"value")
         *         DatabaseException thrown on error
	 */
	function update($table, $values, $keys){
		$value = array();
		if(is_array($values)){
			foreach ($values as $k=>$v)
				$value[] = "`$k`=?";
		} else {
			throw new DatabaseException("2-nd argument of Database->update() must be an array.",$values);
		}
		if(!is_array($value)) {
			// followed implode() fails....
			throw new DatabaseException("2-nd argument of Database->update() must be non empty array.",$values);
		}
		$key = array();
		if(is_array($keys)){
			foreach ($keys as $k=>$v)
				$key[] = "`$k`=?";
		} else {
			throw new DatabaseException("3-rd argument of Database->update() must be an array.",$keys);
		}
		if(!is_array($key)) {
                        // followed implode() fails....
                        throw new DatabaseException("3-rd argument of Database->update() must be non empty array.",$keys);
                }
		$sql = "UPDATE $table SET ".implode(", ", $value)." WHERE ".implode(" AND ", $key);
		$args = array_merge(array_values($values), array_values($keys));
		$this->execute($sql, $args);
	}
	
	/**
	 * Inserts a row with values to given table.
	 * @param $table Name of a table
	 * @param $values Assoc table with columns and values, i.e. array("column"=>"value")
         *         DatabaseException thrown on error
	 */
	function insert($table, $values){
		$cols = array();
		$vals = array();
		if(is_array($values)){
			foreach ($values as $k=>$v){
				$cols[] = "`$k`";
				$vals[] = "?"; 
			}
                } else {
                        throw new DatabaseException("2-nd argument of Database->insert() must be an array.",$values);
                }
                if((!is_array($cols)) or (!is_array($vals))) {
                        // followed implode() fails....
                        throw new DatabaseException("2-nd argument of Database->insert() must be non empty array.",$values);
                }
		$sql = "INSERT INTO `$table` (".implode(",", $cols).") VALUES(".implode(",", $vals).")";
		$this->execute($sql, array_values($values));
	}	

	/**
	 * Inserts multiple rows to a single table.
	 * @param $table Name of a table
	 * @param $columns Array with column names.
	 * @param $values Array of array of column values.
         *         DatabaseException thrown on error
	 */
	function insert_bulk($table, $columns, $values){
		$params = array();
		$cols = array();
		$fs = array();
		if(is_array($columns)){ // if not, foreach fails
			foreach ($columns as $column){
				$cols[] = "`$column`";
				$fs[] = "?";
			}
                } else {
                        throw new DatabaseException("2-nd argument of Database->insert_bulk() must be an array.",$values);
                }
                if(!is_array($fs)) {
                        // followed implode() fails....
                        throw new DatabaseException("2-nd argument of Database->insert_bulk() must be non empty array.",$values);
                }
		$field = "(".implode(", ", $fs).")";
		$fields = array();
		try {
			foreach ($values as $vs){
				foreach ($vs as $v){
					$params[] = $v;
				}	
				$fields[] = $field;
			}
		} catch (Exception $e) {
			throw new DatabaseException('Bad parameter $values - should be non empty array of arrays');
		}
		$sql = "INSERT INTO $table(".implode(",", $cols).") VALUES ".implode(",", $fields);
		$this->execute($sql, $params);
	}	

	/**
	 * Insert or replace row for the keys.
         * @param $table Name of a table
         * @param $values Assoc table with columns and values, i.e. array("column"=>"value")
         *         DatabaseException thrown on error
	 */	
	function replace($table, $values){
		$value = array();
		$params = array();
		try {
			foreach ($values as $k=>$v){
				$value[] = "`$k`=?";
				$params[] = $v;
			}
			$implodedPhrase = implode(", ", $value);
		} catch (Exception $e) {
                        throw new DatabaseException('Bad parameter $values - should be non empty array');
                }
		$sql = "REPLACE `$table` SET ".$implodedPhrase;
		$this->execute($sql, $params);
	}

        /**
         * fetch rows for the keys.
         * @param $table Name of a table
         * @param $values Assoc table with columns and values, i.e. array("column"=>"value")
         * @return {Array} Array of arrays (rows)
         *         or DatabaseException on error
         */
	function select($table, $values){
		$value = array();
		$params = array();
		try {
			foreach ($values as $k=>$v){
				$value[] = "`$k`=?";
				$params[] = $v;
			}
			$implodedPhrase = implode(" AND ", $value);
		} catch (Exception $e) {
                        throw new DatabaseException('Bad parameter $values - should be non empty array');
                }
		$sql = "SELECT * FROM `$table` WHERE ".$implodedPhrase;
		return $this->fetch_rows($sql, $params);
	}

    /**
     * @param $table
     * @param $keyColumn
     * @param $values
	 *
	 * @return one scalar value
     *         DatabaseException on error
     */
	function get_entry_key($table, $keyColumn, $values){
		$sql = "SELECT $keyColumn FROM $table";
		$params = array();
		$wheres = array();
		try {
			foreach ($values as $k=>$v){
				$wheres[] = "`$k`=?";
				$params[] = $v;
			}
		} catch (Exception $e) {
                        throw new DatabaseException('Bad parameter $values - should be non empty array');
		}

		if ( count($wheres)>0 ){
			$sql .= " WHERE " . implode(" AND ", $wheres);
		}
		$keys = $this->fetch_ones($sql, $keyColumn, $params);
		if ( count($keys) == 0 ){
			$this->insert($table, $values);
			return $this->last_id();
		} else {
			return $keys[0];
		}
	}

	/**
	 * Execute query and return result as an assoc array.
	 * @param $class_name {String} Name of the class
	 * @param $sql {String} SQL query.
	 * @param $args {Array} Query arguments.
	 * @return {Array} Array of instance of $class_name with attributtes
	 * 		sets to name and values from selected rows
	 */
	public function fetch_class_rows($class_name, $sql, $args = null){
			if(!class_exists($class_name)) {
				throw new DatabaseException('First argument of fetch_class_rows method from Database class must be a valid class name');
			}
        	$rows = $this->fetch_rows($sql, $args);
        	$objects = array();
        	foreach ($rows as $row){
                	$o = new $class_name();
                	foreach ($row as $k=>$v)
                        	$o->$k = $v;
                	$objects[] = $o;
        	}
        	return $objects;
	} // fetch_class_rows()

    /**
     * Convert a text value into a DBMS specific format that is suitable to
     * compose query statements.
     *
     * @param   string  text string value that is intended to be converted.
     *
     * @return  string  text string that represents the given argument value in
     *       a DBMS specific format.
     */
 
	public function quote($stringValue)
    	{

			$stringValue = $this->escape($stringValue);
			return "'".$stringValue."'";

		}

    /* Umieszcza prefix escapowania ('\') przed znakami tego wymagajacymi
	 * w tekście $text, aby mógł być bezpiecznie użyty w treści zapytań SQL.
	 *  Wymaganie implementacyjne jest takie, że $this->mdb2-getConnection()
	 * musi być działającym połączeniem do bazy, bo inaczej zwróci ''.
	 *  Wynika to z konieczności odczytania charset z bazy i dostosowania
	 * eskejpowanych znaków do aktualnego charsetu. Odbywa się to tutaj
	 * niejawnie ( ugh... :o( ), co może prowadzić do niejasnych zachowań 
     *
     * @param   string  the input string to quote
     *
     * @return  string  quoted string
     *
     * @access  private
	 */
 
    public function escape($text) {

            return $this->mdb2->escape($text);

    } // escape()

	/**
	 * Execute query and return result as one-dimensional array 
	 * of one-dimensional arrays ( lists ) from each row found
	 * @param $sql {String} SQL query.
	 * @return {Array} Array of non-associative arrays from selected rows
     */
	public function fetchOneListForEachRow($sql) {

		$allRowsAsListOfAssoc = $this->fetch_rows($sql);
		$result = array();
		foreach($allRowsAsListOfAssoc  as $rowAsAssoc) {
                        $result[]=array_values($rowAsAssoc);
        }
		return $result;
 
	} // fetchOneListForEachRow

    /**
     * This method is used to collect information about an error
     *
     * @return  array   with MDB2 errorcode, native error code, native message
     *
     */
	// TODO: change to something more universal after checking in tests
	public function errorInfo() {

		return $this->mdb2->errorInfo();

	} // errorInfo()

	 /**
         * Return associative array of values from two selected columns 
	 * for each row returned by the query.
         * @param $sql {String} SQL query.
         * @param $key_column_name {String} Column name for key value
	 * @param $value_column_name {String} Column name for value value
         * @param $args {Array} Query arguments.
         * @return {Array} An associative array of pairs key=>value 
         */
        function fetch_assoc_array($sql, $key_column_name, $value_column_name, $args = null){
                $rows = $this->fetch_rows($sql, $args);
                $result = array();
                foreach ($rows as $row){
			$result[$row[$key_column_name]] = $row[$value_column_name];
                }
                return $result;
        } // fetch_assoc_array()

	public function get_encoding() {

		return $this->_encoding;

	} // get_encoding()

	public function get_collate() {

		return $this->get_encoding().'_general_ci';

	} // get_collate()

} // of Database class		

?>
