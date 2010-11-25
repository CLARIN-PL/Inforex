<?php
/**
 * @package MyCMS
 * @subpackage LCMS
 * @author Michał Marcińczuk <marcinczuk@gmail.com>
 **/
 
 class ATable {
 	
 	var $_meta_table = "";
 	var $_meta_key = "";
 	//var $_meta_stmt = null;
 	
 	function __construct($id){
 		global $mdb2;
 		$sql = "SELECT * FROM {$this->_meta_table}" .
 				" WHERE {$this->_meta_key}=" . $mdb2->quote($id,   'integer');
 		$res = $mdb2->query($sql);
 		if (PEAR::isError($res)) { die($res->getMessage()); }
 		$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
 		$this->assign($row);
 	}
 	
 	function assign($row){
 		foreach ($this as $k=>$v)
 			if (substr($k, 0, 5)!="_meta" && isset($row[$k]))
 				$this->$k = stripslashes($row[$k]); 
 	}
 	
 	function save(){
 		global $mdb2;
 		$key_name = $this->_meta_key;
 		
 		$values = array();
 		
 		if (isset($this->$key_name)){ 			
	 		$sets = array();
	 		foreach (get_object_vars($this) as $k=>$v)
	 			if (substr($k, 0, 5)!="_meta" && $k!=$key_name){
	 				$sets[] = "`$k` = '".mysql_escape_string($v)."'";
	 			}
	 		$values[$key_name] = $this->$key_name;
	 		$sql = "UPDATE {$this->_meta_table} SET ".implode(", ", $sets)." WHERE {$key_name}=".mysql_escape_string($this->$key_name);
			$res = $mdb2->query($sql);
	 		if (PEAR::isError($res)) { die($res->getMessage()); }
 		}else{
	 		$columns = array();
	 		$parameters = array();
	 		
	 		foreach (get_object_vars($this) as $k=>$v)
	 			if (substr($k, 0, 5)!="_meta" && $k!=$key_name){
	 				$columns[] = "`".$k."`";
	 				$parameters[] = "'".mysql_escape_string($v)."'";
	 				$values[$k] = $v; 
	 			}
	 			
	 		$sql = "INSERT INTO {$this->_meta_table}(".implode(", ", $columns).") VALUES(".implode(", ", $parameters).")";
	 		$res = $mdb2->query("INSERT INTO {$this->_meta_table}(".implode(", ", $columns).") VALUES(".implode(", ", $parameters).")");
	 		if (PEAR::isError($res)) { die($res->getMessage()); }
	 			 			
 	 		$this->$key_name = $mdb2->getAfterID(0, $this->_meta_table);
 		}
 	}
 	
 	function delete(){
 		$key_name = $this->_meta_key;
 		db_execute(sprintf("DELETE FROM `%s` WHERE `%s`=?", $this->_meta_table, $key_name), array($this->$key_name));
 	}
 	
// 	function errorInfo(){
// 		return $this->_meta_stmt->errorInfo();
// 	}
 }
 
 ?>
