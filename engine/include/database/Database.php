<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

/**
 * Database gateway. 
 */
class Database{
	
	var $mdb2 = null;
	var $log = false;
	
	/**
	 * @param dsn {array}
	 * @param log {boolean} -- print logs (default: false)
	 * @param log_output {String} -- where to print logs: fb (use fb function), print (use print),
	 */
	function __construct($dsn, $log=false, $log_output="fb"){
		$options = array('portability' => MDB2_PORTABILITY_NONE);
		$this->mdb2 =& MDB2::connect($dsn, $options);
		if (PEAR::isError($this->mdb2)) {
		    throw new Exception($this->mdb2->getMessage());
		}
		$this->mdb2->loadModule('Extended');		
		$this->mdb2->query("SET CHARACTER SET 'utf8'");
		$this->mdb2->query("SET NAMES 'utf8'");
		$this->mdb2->query("SET SESSION query_cache_type = ON");		
		$this->log = $log;
		$this->log_output = $log_output;
	}
	
	/**
	 * Log out and disconnect from the database.
	 */
	function disconnect(){
		$this->mdb2->disconnect();
	}
	
	/**
	 * Log message using Database internal logger.
	 */
	function log_message($message){
		if ( $this->log ){
			if ($this->log_output == "print"){
				print '<pre>\n'.$message.'</pre>\n';
			}
			elseif ($this->log_output == "fb"){
				fb($message); 		
			}
		}
	}
	
	/**
	 * Log SQL statement with backtrace of its execution.
	 * @param $sql {String} SQL query.
	 * @param $args {Array} Query arguments
	 */
	function log_sql($sql, $args){
		if ( $this->log ){
			$backtrace = array();
			foreach (debug_backtrace() as $d){
				$backtrace[] = sprintf("File %s, line %d, %s%s%s(...)", 
					$d['file'], $d['line'], $d['class'], $d['type'], 
					$d['function']);
			}

			if ($this->log_output == "print"){
				$msg = "SQL LOG\n";
				$msg .= $sql . "\n";
				$sqm .= implode("\n", $backtrace);
				$msg .= print_r($args, true);
				print '<pre>\n'.$msg.'</pre>\n';
			}
			elseif ($this->log_output == "fb"){
				FB::info($sql, "SQL LOG");
				fb($args, "Args");
				fb($backtrace, "Backtrace");
				//fb(debug_backtrace()); 		
			}			
		}
	}

	/**
	 * Execute query with optional argument and return result of the execution.
	 * @param $sql {String} SQL query.
	 * @param $args {Array} Query argumnets.
	 */
	function execute($sql, $args=null){
		$time_start = microtime(TRUE);
		$sth = null;
		$result = null;
		try{
			$this->log_sql($sql, $args);
			if ($args == null){
				if (PEAR::isError($result = $this->mdb2->query($sql))){
					var_dump($result);
					print("<pre>{$rresult->getUserInfo()}</pre>");		
				}
			}else{
				if (PEAR::isError($sth = $this->mdb2->prepare($sql))){
					print("<pre>{$sth->getUserInfo()}</pre>");
				}
				$result = $sth->execute($args);
				if ($this->log){
					$this->log_message($args, "SQL DATA");
				}		
			}
			if ($this->log)
	        	$this->log_message('Execute time: '.number_format(microtime(TRUE)-$time_start, 6).' s.', "SQL");
		}		
		catch(Exception $ex){
			if ( $sth !== null && !PEAR::isError($sth) ){
				$sth->free();
			}	
			throw $ex;
		}	
		if ( $sth !== null && !PEAR::isError($sth) ){
			$sth->free();
		}	
		return $result;
	}
	
	/**
	 * Execute query and return result as an assoc array.
	 * @param $sql {String} SQL query.
	 * @param $args {Array} Query arguments.
	 * @return {Array} Array of arrays (rows)
	 */
	function fetch_rows($sql, $args = null){
		return $this->execute($sql, $args)->fetchAll(MDB2_FETCHMODE_ASSOC);
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
			$vals[] = $row[$column];
		}
		return $vals;
	}
	
	/**
	 * Return a one-dimensional array of values representing a single row
	 * returned by the query.
	 * @param $sql {String} SQL query.
	 * @param $args {Array} Query arguments.
	 * @return {Array} An assoc array of strings.
	 */
	function fetch($sql, $args=null){
		$r = $this->execute($sql, $args);
		return $r->fetchRow(MDB2_FETCHMODE_ASSOC);			
	}
	
	/**
	 * Return a single value for the first row.
	 */
	function fetch_one($sql, $args=null){
		$r = $this->execute($sql, $args);
		return $r->fetchOne();	
	}

	/**
	 * 
	 */
	function fetch_id($table_name){
		return $this->mdb2->getAfterID(0, $table_name);
	}

	/**
	 * 
	 */
	function last_id(){
		return $this->mdb2->lastInsertID();
	}
	
	/**
	 * Update row values from a table for given key.
	 * @param table Name of a table.
	 * @param values Assoc array with values to update, i.e. array("column"=>"value")
	 * @param keys Assoc array with keys, i.e. array("key"=>"value")
	 */
	function update($table, $values, $keys){
		$value = "";
		foreach ($values as $k=>$v)
			$value[] = "`$k`=?";
		$key = "";
		foreach ($keys as $k=>$v)
			$key[] = "`$k`=?";
		$sql = "UPDATE $table SET ".implode(", ", $value)." WHERE ".implode(" AND ", $key);
		$args = array_merge(array_values($values), array_values($keys));
		$this->execute($sql, $args);
	}
	
	/**
	 * Inserts a row with values to given table.
	 * @param $table Name of a table
	 * @param $attributes Assoc table with colument and values, i.e. array("column"=>"value")
	 */
	function insert($table, $values){
		$cols = array();
		$vals = array();
		foreach ($values as $k=>$v){
			$cols[] = "`$k`";
			$vals[] = "?"; 
		}
		$sql = "INSERT INTO $table(".implode(",", $cols).") VALUES(".implode(",", $vals).")";
		$this->execute($sql, array_values($values));
	}	
	
	/**
	 * Inserts multiple rows to a single table.
	 * @param $table Name of a table
	 * @param $columns Array with column names.
	 * @param $values Array of array of column values.
	 */
	function insert_bulk($table, $columns, $values){
		$params = array();
		$cols = array();
		$fs = array();
		foreach ($columns as $column){
			$cols[] = "`$column`";
			$fs[] = "?";
		}
		$field = "(".implode(", ", $fs).")";
		$fields = array();
		foreach ($values as $vs){
			foreach ($vs as $v){
				$params[] = $v;
			}
			$fields[] = $field;
		}
		$sql = "INSERT INTO $table(".implode(",", $cols).") VALUES ".implode(",", $fields);
		$this->execute($sql, $params);
	}	

	/**
	 * Insert or replace row for the keys.
	 */	
	function replace($table, $values){
		$value = array();
		$params = array();
		foreach ($values as $k=>$v){
			$value[] = "`$k`=?";
			$params[] = $v;
		}
		$sql = "REPLACE `$table` SET ".implode(", ", $value);
		$this->execute($sql, $params);
	}
}

?>
