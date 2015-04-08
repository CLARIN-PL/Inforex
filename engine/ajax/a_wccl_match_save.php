<?php
 
class Ajax_wccl_match_save extends CPage {
	
	var $isSecure = true;
	
	function checkPermission(){
		return isCorpusOwner() || hasCorpusRole(CORPUS_ROLE_MANAGER); 
	}
	
	function execute(){
		global $config, $corpus, $user, $db;
		
		$rules = strval($_POST['wccl_rules']);
		$annotations = strval($_POST['annotations']);
		$corpus_id = $corpus['id'];
		$user_id = $user['user_id'];

		$columns = array();
		$columns['user_id'] = $user_id;
		$columns['corpus_id'] = $corpus_id;
		$columns['rules'] = $rules;
		$columns['annotations'] = $annotations;

		$db->replace("wccl_rules", $columns);
									
		return array();
	}
	
}
?>
 