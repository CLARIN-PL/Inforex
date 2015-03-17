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
	 * @param log -- print logs (default: false)
	 * @param log_output -- where to print logs: fb (use fb function), print (use print),
	 */
	function __construct($dsn, $log=false, $log_output="fb"){
		$this->mdb2 =& MDB2::connect($dsn);
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
	
	function log_message($message){
		if ($this->log_output == "print"){
			print $message . "\n";
		}
		elseif ($this->log_output == "fb"){
			fb($message); 		
		}
	}
	
	function execute($sql, $args=array()){
		if ($this->log){
			$this->log_message(__CLASS__.':'.__METHOD__.'() ('.__FILE__.':'.__LINE__.')', "SQL");
                        $time_start = microtime(TRUE);
			$this->log_message(" SQL: " . $sql);
		}
		if ($args == null){
			if (PEAR::isError($r = $this->mdb2->query($sql)))
				throw new Exception($r->getUserInfo());
		}else{
			if (PEAR::isError($sth = $this->mdb2->prepare($sql)))
				throw new Exception($sth->getUserInfo());
			if (PEAR::isError($r = $sth->execute($args))){
				print("<pre>{$r->getUserInfo()}</pre>");
			}
			if ($this->log){				
				$this->log_message(" ARGS: " . var_export($args, true));
			}
		}		
		if ($this->log){
            $this->log_message('  Execute time: '.number_format(microtime(TRUE)-$time_start, 6).' s.', "SQL");
        }
	}
	
	/**
	 * Return an array of rows. Each row is represented as an array.
	 */
	function fetch_rows($sql, $args = null){
		if ($this->log){
			$this->log_message(__CLASS__.':'.__METHOD__.'() ('.__FILE__.':'.__LINE__.')', "SQL");
                        $time_start = microtime(TRUE);
			$this->log_message($sql, "SQL");
		}
		if ($args == null){
			if (PEAR::isError($r = $this->mdb2->query($sql)))
				print("<pre>{$r->getUserInfo()}</pre>");
		}else{
			if (PEAR::isError($sth = $this->mdb2->prepare($sql)))
				print("<pre>{$sth->getUserInfo()}</pre>");
			$r = $sth->execute($args);
			if ($this->log){
				$this->log_message($args, "SQL DATA");
			}		
		}
		if ($this->log)
        	$this->log_message('Execute time: '.number_format(microtime(TRUE)-$time_start, 6).' s.', "SQL");
		if ( method_exists($r, "fetchAll")){
			return $r->fetchAll(MDB2_FETCHMODE_ASSOC);
		}			
		else{
			throw new Exception("Error in SQL query <pre>$sql</pre>" .
					"Arguments:<pre>" . print_r($args, true) . "</pre>" .
					"Info:<pre>" . print_r($r->getUserInfo(), true) . "</pre>");
		}				 
	}

	/**
	 * Return a simple array of values of given column for each row.
	 * @param $sql Pattern of SQL query
	 * @param $column Name of a column from row.
	 * @param $args Array of parameters (optional)
	 * @return simple array of strings, i.e. array("one", "two", "three") 
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
	 * Return a single row.
	 */
	function fetch($sql, $args=null){
		if ($this->log){
			$this->log_message(__CLASS__.':'.__METHOD__.'() ('.__FILE__.':'.__LINE__.')', "SQL");
                        $time_start = microtime(TRUE);
			$this->log_message($sql, "SQL");
		}
		$args = $args == null ? array() : $args;
		
		if (PEAR::isError($sth = $this->mdb2->prepare($sql)))
			print("<pre>{$sth->getUserInfo()}</pre>");
			
		if (PEAR::isError($r = $sth->execute($args)))
			print("<pre>{$r->getUserInfo()}</pre>");	
		if ($this->log)
            $this->log_message('Execute time: '.number_format(microtime(TRUE)-$time_start, 6).' s.', "SQL");
		return $r->fetchRow(MDB2_FETCHMODE_ASSOC);			
	}
	
	/**
	 * Return a single value for the first row.
	 */
	function fetch_one($sql, $args=null){
		if ($this->log){
			$this->log_message(__CLASS__.':'.__METHOD__.'() ('.__FILE__.':'.__LINE__.')', "SQL");
                        $time_start = microtime(TRUE);
			$this->log_message($sql, "SQL");
		}
		if ($args == null){
			if (PEAR::isError($r = $this->mdb2->query($sql)))
				print("<pre>{$r->getUserInfo()}</pre>");		
		}else{
			if (!is_array($args)){
				$args = array($args);
			}
			if (PEAR::isError($sth = $this->mdb2->prepare($sql)))
				print("<pre>{$sth->getUserInfo()}</pre>");
			$r = $sth->execute($args);
			if ($this->log){
				$this->log_message($args, "SQL DATA");
			}		
		}
		if ($this->log)
        	$this->log_message('Execute time: '.number_format(microtime(TRUE)-$time_start, 6).' s.', "SQL");
		return $r->fetchOne();				
	}
	
	function fetch_id($table_name){
		return $this->mdb2->getAfterID(0, $table_name);
	}

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
		$sql = "INSERT INTO $table(".implode(",", $columns).") VALUES ".implode(",", $fields);
		$this->execute($sql, $params);
	}	
}

//######################### deprecated functions ##########################
//######################### deprecated functions ##########################
function db_fetch_rows($sql, $args = null){
	global $mdb2, $sql_log;
	if ($sql_log){
                fb(__CLASS__.':'.__METHOD__.'() ('.__FILE__.':'.__LINE__.')', "SQL");
                $time_start = microtime(TRUE);
		fb($sql, "SQL");
	}
	//var_dump($mdb2);
	if ($args == null){
		if (PEAR::isError($r = $mdb2->query($sql)))
			throw new Exception("<pre>{$r->getUserInfo()}</pre>");
		
	}else{
		if (PEAR::isError($sth = $mdb2->prepare($sql)))
			throw new Exception("<pre>{$sth->getUserInfo()}</pre>");
		$r = $sth->execute($args);
		if ($sql_log){
			fb($args, "SQL DATA");
		}		
	}
	
        if ($sql_log){
            fb('Execute time: '.number_format(microtime(TRUE)-$time_start, 6).' s.', "SQL");
        }
	return $r->fetchAll(MDB2_FETCHMODE_ASSOC);
}

//######################### deprecated functions ##########################
function db_execute($sql, $args=null){
	global $mdb2, $sql_log;
	if ($sql_log){
                fb(__CLASS__.':'.__METHOD__.'() ('.__FILE__.':'.__LINE__.')', "SQL");
                $time_start = microtime(TRUE);
		fb($sql, "SQL");
	}
	if ($args == null){
		if (PEAR::isError($r = $mdb2->query($sql)))
			throw new Exception("<pre>{$r->getUserInfo()}</pre>");
	}else{
		if (PEAR::isError($sth = $mdb2->prepare($sql)))
			throw new Exception("<pre>{$sth->getUserInfo()}</pre>");
		$sth->execute($args);
		if ($sql_log){
			fb($args, "SQL DATA");
		}
	}
        if ($sql_log){
            fb('Execute time: '.number_format(microtime(TRUE)-$time_start, 6).' s.', "SQL");
        }
    }

/**
 * Fetch single row as assoc array.
 * @param $sql SELECT query statement
 * @return array with the query result
 */
//######################### deprecated functions ##########################
function db_fetch($sql, $args=null){
	global $mdb2, $sql_log;
	if ($sql_log){
                fb(__CLASS__.':'.__METHOD__.'() ('.__FILE__.':'.__LINE__.')', "SQL");
                $time_start = microtime(TRUE);
		fb($sql, "SQL");
	}
	$args = $args == null ? array() : $args;
	
	if (PEAR::isError($sth = $mdb2->prepare($sql)))
		throw new Exception("<pre>{$sth->getUserInfo()}</pre>");
		
	if (PEAR::isError($r = $sth->execute($args)))
		throw new Exception("<pre>{$r->getUserInfo()}</pre>");	
        if ($sql_log){
            fb('Execute time: '.number_format(microtime(TRUE)-$time_start, 6).' s.', "SQL");
        }
	return $r->fetchRow(MDB2_FETCHMODE_ASSOC);			
}

//######################### deprecated functions ##########################
function db_fetch_one($sql, $args=null){
	global $mdb2, $sql_log;
	if ($sql_log){
                fb(__CLASS__.':'.__METHOD__.'() ('.__FILE__.':'.__LINE__.')', "SQL");
                $time_start = microtime(TRUE);
		fb($sql, "SQL");
	}
	if ($args == null){
		if (PEAR::isError($r = $mdb2->query($sql)))
			throw new Exception("<pre>{$r->getUserInfo()}</pre>");		
	}else{
		if (!is_array($args)){
			$args = array($args);
		}
		if (PEAR::isError($sth = $mdb2->prepare($sql)))
			throw new Exception("<pre>{$sth->getUserInfo()}</pre>");
		$r = $sth->execute($args);
		if ($sql_log){
			fb($args, "SQL DATA");
		}		
	}
        if ($sql_log){
            fb('Execute time: '.number_format(microtime(TRUE)-$time_start, 6).' s.', "SQL");
        }
	return $r->fetchOne();				
}

//######################### deprecated functions ##########################
function db_fetch_class_rows($class_name, $sql, $args = null){
	$rows = db_fetch_rows($sql, $args);
	$objects = array();
	foreach ($rows as $row){
		$o = new $class_name();
		foreach ($row as $k=>$v)
			$o->$k = $v;
		$objects[] = $o;			
	}
	return $objects;
}

/**
 * Replace a row in a given table.
 * @param $table -- table name
 * @param $values -- assoc table with values column=>value
 */
//######################### deprecated functions ##########################
function db_replace($table, $values){
	$value = "";
	foreach ($values as $k=>$v)
		$value[] = "$k='$v'";
	$key = "";
	$sql = "REPLACE $table SET ".implode(", ", $value);
	db_execute($sql);
}

//######################### deprecated functions ##########################
function db_update($table, $values, $keys){
	$value = "";
	foreach ($values as $k=>$v)
		$value[] = "$k='$v'";
	$key = "";
	foreach ($keys as $k=>$v)
		$key[] = "$k='$v'";
	$sql = "UPDATE $table SET ".implode(", ", $value)." WHERE ".implode(" AND ", $key);
	db_execute($sql);
}

/**
 * Generuje i wykonuje kwerendę INSERT.
 * @param $table -- nazwa tabeli, do której mają być wstawione dane
 * @param $attributes -- tablica asocjacyjna atrybytów (nazwa_kolumny=>wartość)
 */
//######################### deprecated functions ##########################
function db_insert($table, $attributes){
	$cols = array();
	$vals = array();
	foreach ($attributes as $k=>$v){
		$cols[] = "`$k`";
		$vals[] = "?"; 
	}
	$sql = "INSERT INTO $table(".implode(",", $cols).") VALUES(".implode(",", $vals).")";
	db_execute($sql, array_values($attributes));
}

?>
