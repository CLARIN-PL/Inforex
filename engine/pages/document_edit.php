<?php
class Page_document_edit extends CPage{
	
	function execute(){
		if (!$this->get('date'))
			$this->set('date', date("Y-m-d"));
	}
}


?>
