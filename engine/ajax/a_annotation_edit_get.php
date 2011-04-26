<?php
class Ajax_annotation_edit_get extends CPage {
	
	function checkPermission(){
		if (hasRole('admin'))
			return true;
		else
			return "Brak prawa do edycji.";
	}
	
	function execute(){
		global $mdb2, $user;

		if (!intval($user['user_id'])){
			echo json_encode(array("error"=>"Brak identyfikatora użytkownika"));
			return;
		}
		$parent_id = intval($_POST['parent_id']);
		$parent_type = $_POST['parent_type'];
		
		if ($parent_type=="annotation_set"){
			$sql = "SELECT annotation_subset_id AS id, description FROM annotation_subsets WHERE annotation_set_id={$parent_id}";
		} 
		else if ($parent_type=="annotation_subset"){
			$sql = "SELECT name, short_description AS short, description, css FROM annotation_types WHERE annotation_subset_id={$parent_id}";
		}
				
		$result = db_fetch_rows($sql);
		echo json_encode($result);
	}
	
}
?>
