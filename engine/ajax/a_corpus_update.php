<?php
class Ajax_corpus_update extends CPage {
	
	function checkPermission(){
		if (hasRole(USER_ROLE_ADMIN) || isCorpusOwner() || hasCorpusRole(CORPUS_ROLE_MANAGER))
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
		
		if ($element_type=="subcorpus"){
			$sql = "UPDATE corpus_subcorpora SET name = \"{$name_str}\", description=\"{$desc_str}\" WHERE subcorpus_id = {$element_id}";
		}
		
		if ($element_type=="flag"){
			$short_str = $_POST['sort_str'];
			$sql = "UPDATE corpora_flags SET name = \"{$name_str}\", short = \"{$desc_str}\", sort = \"{$short_str}\" WHERE corpora_flag_id = {$element_id}";
		}
				
		$db->execute($sql);
		echo json_encode(array("success"=>1));
	}	
}
?>