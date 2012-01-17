<?php
class Ajax_sens_edit_get_words extends CPage {
	function execute(){
		$sens = DbSens::getSensList();
		foreach($sens as $key => $value){
			$sens[$key]['annotation_type'] = substr($sens[$key]['annotation_type'], 4); // obcinanie "wsd_" 
		}
		echo json_encode($sens);
	}	
}
?>