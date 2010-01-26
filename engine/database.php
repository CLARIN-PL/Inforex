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
?>
