<?php
 
class Ajax_wccl_match_save extends CPage {
	
	var $isSecure = false;
	
	function checkPermission(){
		return true;
	}
	
	function execute(){
		global $config, $corpus, $user, $db;
		
		$rules = strval($_POST['wccl_rules']);
		$corpus_id = $corpus['id'];
		$user_id = $user['user_id'];

		$columns = array();
		$columns['user_id'] = $user_id;
		$columns['corpus_id'] = $corpus_id;
		$columns['rules'] = $rules;

		$db->replace("wccl_rules", $columns);
									
		return array();
	}
	
}
?>
 