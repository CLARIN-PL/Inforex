<?php
class Ajax_corpus_update extends CPage {
	
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $db, $user, $mdb2;

		$name_str = $_POST['name_str'];
		$desc_str = $_POST['desc_str'];
		
		$element_type = $_POST['element_type'];
		$element_id = $_POST['element_id'];
				
		if ($element_type=="corpus_details"){
			$name_str = ($name_str == 'screename' ? "user_id" : $name_str); 
			$sql = "UPDATE corpora SET $name_str=\"$desc_str\" WHERE id=$element_id";
		}
		
		$db->execute($sql);
		echo json_encode(array("success"=>1));
	}
	
}
?>
