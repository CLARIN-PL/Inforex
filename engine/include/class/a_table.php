<?php
/**
 * @package MyCMS
 * @subpackage LCMS
 * @author Michał Marcińczuk <marcinczuk@gmail.com>
 **/
 
 class ATable {
 	
 	var $_meta_table = "";
 	var $_meta_key = "";
 	
 	function __construct($id){
 		global $db;
 		$sql = "SELECT * FROM {$this->_meta_table}" .
 				" WHERE {$this->_meta_key}=" . mysql_real_escape_string($id);
 		$row = $db->fetch($sql);
 		$this->assign($row);
 	}
 	
 	function assign($row){
 		foreach ($this as $k=>$v)
 			if (substr($k, 0, 5)!="_meta" && isset($row[$k]))
 				$this->$k = stripslashes($row[$k]); 
 	}
 	
 	function save(){
 		global $db;
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
			$res = $db->execute($sql);
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
	 		$res = $db->execute("INSERT INTO {$this->_meta_table}(".implode(", ", $columns).") VALUES(".implode(", ", $parameters).")");
	 			 			
 	 		$this->$key_name = $mdb2->getAfterID(0, $this->_meta_table);
 		}
 	}
 	
 	function delete(){
 		global $db;
 		$key_name = $this->_meta_key;
 		$db->execute(sprintf("DELETE FROM `%s` WHERE `%s`=?", $this->_meta_table, $key_name), array($this->$key_name));
 	}
 }
 
?>
