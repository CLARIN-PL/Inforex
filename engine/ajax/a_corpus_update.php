<?php
/*
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

		$name_str = $_POST['name_str'];
		$desc_str = $_POST['desc_str'];		
		$element_type = $_POST['element_type'];
		$element_id = $_POST['element_id'];
		$sql = "";
				
		if ($element_type=="corpus_details")
			$sql = "UPDATE corpora SET $name_str=\"$desc_str\" WHERE id = {$corpus['id']}";
		
		if ($element_type=="subcorpus")
			$sql = "UPDATE corpus_subcorpora SET name = \"{$name_str}\", description=\"{$desc_str}\" WHERE subcorpus_id = {$element_id}";
		
		if ($element_type=="flag")
			$sql = "UPDATE corpora_flags SET name = \"{$name_str}\", short = \"{$desc_str}\", sort = \"{$_POST['sort_str']}\" WHERE corpora_flag_id = {$element_id}";
		
		if ($sql != ""){
			$db->execute($sql);
			$error = $db->mdb2->errorInfo();
			if(isset($error[0]))
				echo json_encode(array("error"=> "Error: (". $error[1] . ") -> ".$error[2]));
			else
				echo json_encode(array("success"=>1));
		}		
		
		if ($element_type == "users"){
			if ($_POST['operation_type'] == "add"){
				$db->execute("INSERT INTO users_corpus_roles VALUES(?, ?, ?)", array($_POST['value'], $corpus['id'], 'read'));
				$error = $db->mdb2->errorInfo();
				if(isset($error[0]))
					echo json_encode(array("error"=> "Error: (". $error[1] . ") -> ".$error[2]));
				else
					echo json_encode(array("success"=>1));
			} elseif ($_POST['operation_type'] == "remove"){				
				$db->execute("DELETE FROM users_corpus_roles WHERE user_id = ? AND corpus_id = ? ", array($_POST['value'], $corpus['id']));
				$error1 = $db->mdb2->errorInfo();
				$db->execute("DELETE FROM corpus_perspective_roles WHERE user_id = ? AND corpus_id = ? ", array($_POST['value'], $corpus['id']));
				$error2 = $db->mdb2->errorInfo();
				if (isset($error1[0]))
					echo json_encode(array("error"=> "Error: (". $error1[1] . ") -> ".$error1[2]));
				elseif (isset($error2[0]))
					echo json_encode(array("error"=> "Error: (". $error2[1] . ") -> ".$error2[2]));
				else
					echo json_encode(array("success"=>1));
			} else {
				echo json_encode(array("error"=> "Error: wrong \"operation_type\" parametr"));
			}					
		}
	}	
}
?>