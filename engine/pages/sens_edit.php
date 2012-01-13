<?php
class Page_sens_edit extends CPage{

	function execute(){		
		$sens = DbSens::getSensList();
		foreach($sens as $key => $value){
			$sens[$key]['annotation_type'] = substr($sens[$key]['annotation_type'], 4); // obcinanie "wsd_" 
		}
		$this->set("sensList", $sens);
	}
}

?>