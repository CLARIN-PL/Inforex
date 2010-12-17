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
class GPWdb{
	
	var $db = null;
	
	function __construct(){
		global $config;
		// gets an existing instance with the same DSN
		// otherwise create a new instance using MDB2::factory()
		$this->mdb2 =& MDB2::singleton($config->dsn);
		if (PEAR::isError($this->mdb2)) {
		    throw new Exception($mdb2->getMessage());
		}
		$this->mdb2->loadModule('Extended');
		
		$this->mdb2->query("SET CHARACTER SET 'utf8'");
	}
}

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
function db_replace($table, $values){
	$value = "";
	foreach ($values as $k=>$v)
		$value[] = "$k='$v'";
	$key = "";
	$sql = "REPLACE $table SET ".implode(", ", $value);
	db_execute($sql);
}

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
?>