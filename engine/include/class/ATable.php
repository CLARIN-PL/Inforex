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
 	var $_meta_key_auto_increment = true;
 	
 	function __construct($id=null){
 		global $db;
 		if ($id){
	 		$sql = "SELECT * FROM {$this->_meta_table}" .
	 				" WHERE {$this->_meta_key}= ? ";
	 		$row = $db->fetch($sql, array($id));
	 		$this->assign($row);
 		}
 	}

 	function getId(){
 	    $key = $this->_meta_key;
 	    return $this->$key;
    }

 	function assign($row){
 		foreach ($this as $k=>$v) {
            if (substr($k, 0, 5) != "_meta" && isset($row[$k])) {
                $this->$k = stripslashes($row[$k]);
            }
        }
 	}
 	
 	function save(){
 		$key_name = $this->_meta_key;
 		if ( !$key_name || isset($this->$key_name) ){
 		    $this->update();
 		}else{
 		    $this->insert();
 		}
 	}

 	function replace(){
 	    global $db;
        $values = array();
        foreach (get_object_vars($this) as $k=>$v) {
            if (substr($k, 0, 5) != "_meta" ) {
                $values[$k] = $v;
            }
        }
        $db->replace($this->_meta_table, $values);
    }

    function update(){
        global $db;
        $key_name = $this->_meta_key;
        $cols = array();
        foreach (get_object_vars($this) as $k=>$v)
            if (substr($k, 0, 5)!="_meta" && $k!=$key_name){
                $cols[$k] = $v;
            }
        $keys = array($key_name => $this->$key_name);
        $db->update($this->_meta_table, $cols, $keys);
    }

    function insert(){
        global $db;
        $key_name = $this->_meta_key;
        $values = array();
        foreach (get_object_vars($this) as $k=>$v) {
            if (substr($k, 0, 5) != "_meta" ) {
                $values[$k] = $v;
            }
        }
        $db->insert($this->_meta_table, $values);
        $this->$key_name = intval($db->last_id());
    }
 	
 	function delete(){
 		global $db;
 		$key_name = $this->_meta_key;
 		$db->execute(sprintf("DELETE FROM `%s` WHERE `%s`=?", $this->_meta_table, $key_name), array($this->$key_name));
 	}

 	function getFields(){
        $key_name = $this->_meta_key;
        $cols = array();
        foreach (get_object_vars($this) as $k=>$v) {
            if (substr($k, 0, 5) != "_meta" && $k != $key_name) {
                $cols[] = $k;
            }
        }
        return $cols;
	}

	function exists(){
        global $db;
        $values = array();
        foreach (get_object_vars($this) as $k=>$v) {
            if (substr($k, 0, 5) != "_meta" && $v !== null ) {
                $values[$k] = $v;
            }
        }
        return count($db->select($this->_meta_table, $values))>0;
    }
 }
