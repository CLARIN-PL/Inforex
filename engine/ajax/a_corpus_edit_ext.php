<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_corpus_edit_ext extends CPage {
	
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || hasCorpusRole(CORPUS_ROLE_MANAGER) || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji danych.";
	}
	
	function execute(){
		global $db, $corpus;

		$action = $_POST['action'];
		
		if ($action == 'get'){
			$sql = "SELECT ext FROM corpora WHERE id=?";
			$ext = $db->fetch_one($sql, array($corpus['id']));
			return DbCorpus::getCorpusExtColumns($ext);
		}			
		elseif ($action == 'add'){
			$sql = "SELECT ext FROM corpora WHERE id=?";
			$ext = $db->fetch_one($sql, array($corpus['id']));
			
			$sql = "ALTER TABLE {$ext} ADD {$_POST['field']} {$_POST['type']} ". ($_POST['is_null'] == "true" ? "" : " NOT" ) . " NULL";
			ob_start();
			$db->execute($sql);
			$error_buffer_content = ob_get_contents();
			ob_clean();
			if(strlen($error_buffer_content))
				throw new Exception("Error: ". $error_buffer_content);
			else
				return;
		}
		elseif ($action == 'edit'){
			$sql = "SELECT ext FROM corpora WHERE id=?";
			$ext = $db->fetch_one($sql, array($corpus['id']));
			
			$sql = "ALTER TABLE {$ext} CHANGE {$_POST['old_field']} {$_POST['field']} {$_POST['type']} ". ($_POST['is_null'] == "true" ? "" : " NOT" ) . " NULL";
			ob_start();
			$db->execute($sql);
			$error_buffer_content = ob_get_contents();
			ob_clean();
			if(strlen($error_buffer_content))
				throw new Exception("Error: ". $error_buffer_content);
			else
				return;
		}
		elseif ($action == 'add_table'){
			$table_name = "reports_ext_".$corpus['id'];
			$sql = "CREATE TABLE {$table_name} (id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY , {$_POST['field']} {$_POST['type']} ". ($_POST['is_null'] == "true" ? "" : " NOT" ) . " NULL ) ENGINE = InnoDB ";
			ob_start();
			$db->execute($sql);
			$error_buffer_content = ob_get_contents();
			ob_clean();
			if(strlen($error_buffer_content))
				throw new Exception("Error: ". $error_buffer_content);
			else{
				$sql = "UPDATE corpora SET ext = '{$table_name}' WHERE id = {$corpus['id']}";
				ob_start();
				$db->execute($sql);
				$error_buffer_content = ob_get_contents();
				ob_clean();
				if(strlen($error_buffer_content))
					throw new Exception("Error: ". $error_buffer_content);
				else
					return;
			}			
		}
		else{
			throw new Exception("Wrong action");			
		}		
	}	
}
?>
