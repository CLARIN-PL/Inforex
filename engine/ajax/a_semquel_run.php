<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_semquel_run extends CPage {
	var $isSecure = false;
	function execute(){
		global $config;
	
		$question = $_POST['question'];

		$wcrft = new Wcrft($config->get_path_wcrft());
		$wcrft->setModel($config->get_path_wcrf_model()); 
		$ccl = $wcrft->tag($question, "text", "ccl");	

		$liner = new WSLiner2($config->get_liner_wsdl());
		$ccl = $liner->chunk($ccl, "CCL", "CCL");
		
		$wccl = new Wccl();
		$ccl = $wccl->run($ccl, $config->get_file_with_rules());

		$semql = new Semql($config->get_path_semql());
		$json = $semql->analyze($ccl);
		
		$json = str_replace('\t\t\t', "", $json);
		
		echo json_encode(array("success" => 1, "output" => json_decode($json)));
	}	
}

?>
