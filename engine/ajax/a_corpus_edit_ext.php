<?php
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
			if(substr($ext, 0, 12) == "reports_ext_"){
				$sql = "SHOW COLUMNS FROM ". $ext;
				echo json_encode($db->fetch_rows($sql));
			}
			else
				echo json_encode(array("empty"=>1));
		}			
		elseif ($action == 'add'){			
			$sql = "SELECT ext FROM corpora WHERE id=?";
			$ext = $db->fetch_one($sql, array($corpus['id']));
			
			$sql = "ALTER TABLE {$ext} ADD {$_POST['field']} {$_POST['type']} ". ($_POST['is_null'] == "true" ? "" : " NOT" ) . " NULL";
			$db->execute($sql); 
			$error = $db->mdb2->errorInfo();
			if(isset($error[0]))
				echo json_encode(array("error"=> "Error: (". $error[1] . ") -> ".$error[2]));
			else
				echo json_encode(array("success"=>1));
		}			
		else{
			echo json_encode(array("error"=>"Wrong action"));			
		}		
	}	
}
?>
