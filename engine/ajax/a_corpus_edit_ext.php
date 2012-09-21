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
			echo json_encode(DbCorpus::getCorpusExtColumns($ext));
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
		elseif ($action == 'edit'){
			$sql = "SELECT ext FROM corpora WHERE id=?";
			$ext = $db->fetch_one($sql, array($corpus['id']));
			
			$sql = "ALTER TABLE {$ext} CHANGE {$_POST['old_field']} {$_POST['field']} {$_POST['type']} ". ($_POST['is_null'] == "true" ? "" : " NOT" ) . " NULL";
			ob_start();
			$db->execute($sql);
			ob_clean();
			$error = $db->mdb2->errorInfo();
			if(isset($error[0]))
				echo json_encode(array("error"=> "Error: (". $error[1] . ") -> ".$error[2]));
			else
				echo json_encode(array("success"=>1));
		}
		elseif ($action == 'add_table'){
			$table_name = "reports_ext_".$corpus['id'];
			$sql = "CREATE TABLE {$table_name} (id BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY , {$_POST['field']} {$_POST['type']} ". ($_POST['is_null'] == "true" ? "" : " NOT" ) . " NULL ) ENGINE = InnoDB ";
			ob_start();
			$db->execute($sql);
			ob_clean(); 
			$error = $db->mdb2->errorInfo();
			if(isset($error[0]))
				echo json_encode(array("error"=> "Error: (". $error[1] . ") -> ".$error[2]));
			else{
				$sql = "UPDATE corpora SET ext = '{$table_name}' WHERE id = {$corpus['id']}";
				$db->execute($sql); 
				$error = $db->mdb2->errorInfo();
				if(isset($error[0]))
					echo json_encode(array("error"=> "Error: (". $error[1] . ") -> ".$error[2]));
				else
					echo json_encode(array("success"=>1));
			}			
		}
		else{
			echo json_encode(array("error"=>"Wrong action"));			
		}		
	}	
}
?>
