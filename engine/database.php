<?php
/* 
 * ---
 * 
 * ---
 * Created on 2010-01-17
 * Michał Marcińczuk <marcinczuk@gmail.com> [czuk.eu]
 */
 
/**
 * Warstwa komunikacyjna z bazą danych. 
 */
class GPWdb{
	
	var $db = null;
	
	function __construct(){
		global $dsn;
		// gets an existing instance with the same DSN
		// otherwise create a new instance using MDB2::factory()
		$this->mdb2 =& MDB2::singleton($dsn);
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

function db_execute($sql, $args = null){
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
function db_fetch($sql){
	global $mdb2, $sql_log;
	if ($sql_log){
		fb($sql, "SQL");
	}
	if (PEAR::isError($r = $mdb2->query($sql)))
		die("<pre>{$r->getUserInfo()}</pre>");	
	return $r->fetchRow(MDB2_FETCHMODE_ASSOC);			
}

function db_fetch_one($sql, $args){
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
?>
