<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/**
 * Update corpus elements:
 * - element_type=corpus_details -> update corpora table
 * - element_type=subcorpus -> update corpus_subcorpora table
 * - element_type=flag -> update corpora_flags table
 * - element_type=users -> update users_corpus_roles table where 
 * 		operation_type=add -> add user role 'read' in corpus 
 * 		operation_type=remove -> delete user from corpus
 */
class Ajax_corpus_update extends CPage {
	
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || isCorpusOwner() || hasCorpusRole(CORPUS_ROLE_MANAGER))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $db, $user, $mdb2, $corpus;

		$desc_str = strval($_POST['desc_str']);		
		$element_type = strval($_POST['element_type']);
		$element_id = strval($_POST['element_id']);
		$name_str = strval($_POST['name_str']);
		
		$sql = "";
		$params = array();
				
		if ($element_type=="corpus_details"){
			$cols = array($element_id => $desc_str);
			$db->update("corpora", $cols, array('id'=>$corpus['id']));
		}
		
		if ($element_type=="subcorpus")
			$sql = "UPDATE corpus_subcorpora SET name = \"{$name_str}\", description=\"{$desc_str}\" WHERE subcorpus_id = {$element_id}";
		
		if ($element_type=="flag"){
			$params[] = $name_str;
			$params[] = strval($_POST['short_str']);
			$params[] = intval($_POST['sort_str']);
			$params[] = $desc_str;
			$params[] = $element_id;
			$sql = "UPDATE corpora_flags SET name = ?, short = ?, sort = ?, description = ? WHERE corpora_flag_id = ?";
		}
		
		if ($sql != ""){
			ob_start();
			$db->execute($sql, $params);
			$error_buffer_content = ob_get_contents();
			ob_clean();
			if(strlen($error_buffer_content))
				throw new Exception("Error: ". $error_buffer_content);
		}		
		
		if ($element_type == "users"){
		    $corpus_id = $_POST['corpus_id'];

			if ($_POST['operation_type'] == "add"){
				ob_start();
				$db->execute("INSERT INTO users_corpus_roles VALUES(?, ?, ?)", array($_POST['value'], $corpus_id, 'read'));
				$error_buffer_content = ob_get_contents();
				ob_clean();
				if(strlen($error_buffer_content))
					throw new Exception("Error: ". $error_buffer_content);
			} elseif ($_POST['operation_type'] == "remove"){
				ob_start();				
				$db->execute("DELETE FROM users_corpus_roles WHERE user_id = ? AND corpus_id = ? ", array($_POST['value'], $corpus_id));
				$db->execute("DELETE FROM corpus_perspective_roles WHERE user_id = ? AND corpus_id = ? ", array($_POST['value'], $corpus_id));
				$error_buffer_content = ob_get_contents();
				ob_clean();
				if(strlen($error_buffer_content))
					throw new Exception("Error: ". $error_buffer_content);
					
			} else {
				throw new Exception("Error: wrong \"operation_type\" parametr");
			}					
		}
	}	
}