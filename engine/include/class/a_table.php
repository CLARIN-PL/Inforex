<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
 class ATable {
 	
 	var $_meta_table = "";
 	var $_meta_key = "";
 	
 	function __construct($id=null){
 		global $db;
 		if ($id){
	 		$sql = "SELECT * FROM {$this->_meta_table}" .
	 				" WHERE {$this->_meta_key}=" . mysql_real_escape_string($id);
	 		$row = $db->fetch($sql);
	 		$this->assign($row);
 		}
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
	 		$cols = array();
	 		foreach (get_object_vars($this) as $k=>$v)
	 			if (substr($k, 0, 5)!="_meta" && $k!=$key_name){
	 				$cols[$k] = $v;
	 			}
	 		$keys = array($key_name => $this->$key_name);
	 		$db->update($this->_meta_table, $cols, $keys);
 		}else{
	 		$values = array();
	 		
	 		foreach (get_object_vars($this) as $k=>$v)
	 			if (substr($k, 0, 5)!="_meta" && $k!=$key_name){
	 				$values[$k] = $v; 
	 			}
	 		
	 		$db->insert($this->_meta_table, $values);
	 			
 	 		$this->$key_name = $db->fetch_id($this->_meta_table);
 		}
 	}
 	
 	function delete(){
 		global $db;
 		$key_name = $this->_meta_key;
 		$db->execute(sprintf("DELETE FROM `%s` WHERE `%s`=?", $this->_meta_table, $key_name), array($this->$key_name));
 	}
 }
 
?>
