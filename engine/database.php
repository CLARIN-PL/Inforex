<?php
/* 
 * ---
 * 
 * ---
 * Created on 2010-01-17
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */
 

/********************************************************************8
 * Połączenie z bazą danych
 */

ob_start();
$options = array(
    'debug' => 2,
    'result_buffering' => false,
);

$mdb2 =& MDB2::singleton($config->dsn, $options);

if (PEAR::isError($mdb2)) {
    die($mdb2->getMessage());
}
$mdb2->loadModule('Extended');
$mdb2->loadModule('TableBrowser');
db_execute("SET CHARACTER SET 'utf8'");
db_execute("SET NAMES 'utf8'");
ob_clean();

/**
 * Warstwa komunikacyjna z bazą danych. 
 */
class Database{
	
	var $mdb2 = null;
	var $log = false;
	
	function __construct($dsn, $log=false){
		// gets an existing instance with the same DSN
		// otherwise create a new instance using MDB2::factory()
		$this->mdb2 =& MDB2::factory($dsn);
		if (PEAR::isError($this->mdb2)) {
		    throw new Exception($mdb2->getMessage());
		}
		$this->mdb2->loadModule('Extended');
		
		$this->mdb2->query("SET CHARACTER SET 'utf8'");
		
		$this->log = $log;
	}
	
	function execute($sql, $args=array()){
		if ($this->log){
			fb($sql, "SQL");
		}
		if ($args == null){
			if (PEAR::isError($r = $this->mdb2->query($sql)))
				die("<pre>{$r->getUserInfo()}</pre>");
		}else{
			if (PEAR::isError($sth = $this->mdb2->prepare($sql)))
				die("<pre>{$sth->getUserInfo()}</pre>");
			$sth->execute($args);
			if ($this->log){
				fb($args, "SQL DATA");
			}
		}		
	}
	
	function fetch_rows($sql, $args = null){
		if ($this->log){
			fb($sql, "SQL");
		}
		if ($args == null){
			if (PEAR::isError($r = $this->mdb2->query($sql)))
				die("<pre>{$r->getUserInfo()}</pre>");
		}else{
			if (PEAR::isError($sth = $this->mdb2->prepare($sql)))
				die("<pre>{$sth->getUserInfo()}</pre>");
			$r = $sth->execute($args);
			if ($this->log){
				fb($args, "SQL DATA");
			}		
		}
		return $r->fetchAll(MDB2_FETCHMODE_ASSOC);
	}
	
	function fetch($sql, $args=null){
		if ($this->log){
			fb($sql, "SQL");
		}
		$args = $args == null ? array() : $args;
		
		if (PEAR::isError($sth = $this->mdb2->prepare($sql)))
			die("<pre>{$sth->getUserInfo()}</pre>");
			
		if (PEAR::isError($r = $sth->execute($args)))
			die("<pre>{$r->getUserInfo()}</pre>");	
		return $r->fetchRow(MDB2_FETCHMODE_ASSOC);			
	}
	
	function fetch_one($sql, $args=null){
		if ($this->log){
			fb($sql, "SQL");
		}
		if ($args == null){
			if (PEAR::isError($r = $this->mdb2->query($sql)))
				die("<pre>{$r->getUserInfo()}</pre>");		
		}else{
			if (!is_array($args)){
				$args = array($args);
			}
			if (PEAR::isError($sth = $this->mdb2->prepare($sql)))
				die("<pre>{$sth->getUserInfo()}</pre>");
			$r = $sth->execute($args);
			if ($this->log){
				fb($args, "SQL DATA");
			}		
		}
		return $r->fetchOne();				
	}
}

//######################### deprecated functions ##########################
//######################### deprecated functions ##########################
function db_fetch_rows($sql, $args = null){
	global $mdb2, $sql_log;
	if ($sql_log){
		fb($sql, "SQL");
	}
	if ($args == null){
		if (PEAR::isError($r = $mdb2->query($sql)))
			die("<pre>{$r->getUserInfo()}</pre>");
	}else{
		if (PEAR::isError($sth = $mdb2->prepare($sql)))
			die("<pre>{$sth->getUserInfo()}</pre>");
		$r = $sth->execute($args);
		if ($sql_log){
			fb($args, "SQL DATA");
		}		
	}
	return $r->fetchAll(MDB2_FETCHMODE_ASSOC);
}

//######################### deprecated functions ##########################
function db_execute($sql, $args=null){
	global $mdb2, $sql_log;
	if ($sql_log){
		fb($sql, "SQL");
	}
	if ($args == null){
		if (PEAR::isError($r = $mdb2->query($sql)))
			die("<pre>{$r->getUserInfo()}</pre>");
	}else{
		if (PEAR::isError($sth = $mdb2->prepare($sql)))
			die("<pre>{$sth->getUserInfo()}</pre>");
		$sth->execute($args);
		if ($sql_log){
			fb($args, "SQL DATA");
		}
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
		fb($sql, "SQL");
	}
	$args = $args == null ? array() : $args;
	
	if (PEAR::isError($sth = $mdb2->prepare($sql)))
		die("<pre>{$sth->getUserInfo()}</pre>");
		
	if (PEAR::isError($r = $sth->execute($args)))
		die("<pre>{$r->getUserInfo()}</pre>");	
	return $r->fetchRow(MDB2_FETCHMODE_ASSOC);			
}

//######################### deprecated functions ##########################
function db_fetch_one($sql, $args=null){
	global $mdb2, $sql_log;
	if ($sql_log){
		fb($sql, "SQL");
	}
	if ($args == null){
		if (PEAR::isError($r = $mdb2->query($sql)))
			die("<pre>{$r->getUserInfo()}</pre>");		
	}else{
		if (!is_array($args)){
			$args = array($args);
		}
		if (PEAR::isError($sth = $mdb2->prepare($sql)))
			die("<pre>{$sth->getUserInfo()}</pre>");
		$r = $sth->execute($args);
		if ($sql_log){
			fb($args, "SQL DATA");
		}		
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