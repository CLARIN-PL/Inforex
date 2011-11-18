<?php
class Page_questions extends CPage{

	var $isSecure = false;
	
	function execute(){		
		global $mdb2, $config;
				
		$this->set('questions', $this->getSampleQuestions());
	}
	
	function getSampleQuestions(){
		$questions = array();
		
		$questions[] = "Gdzie znajduje się Wrocław?";
		$questions[] = "Kto pracuje w IBM?";
		
		return $questions;
	}
	
}


?>
