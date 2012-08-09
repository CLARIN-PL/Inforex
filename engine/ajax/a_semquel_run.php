<?php
/*
 * Created on Jul 23, 2012
 */
class Ajax_semquel_run extends CPage {
	var $isSecure = false;
	function execute(){
		global $config;
	
		$question = $_POST['question'];

		$wcrft = new Wcrft($config->get_path_wcrft());
		$wcrft->setModel($config->get_path_wcrf_model()); 
		$ccl = $wcrft->tag($question, "text", "ccl");	

		$liner = new WSLiner2($config->get_liner_wsdl);
		$ccl = $liner->chunk($ccl, "CCL", "CCL");
		
		$file_with_rules = "/nlp/eclipse/workspace_inforex/semquel/transformations-common.ccl";
		$wccl = new Wccl();
		$ccl = $wccl->run($ccl, $file_with_rules);

		$semql = new Semql($config->get_path_semql());
		$json = $semql->analyze($ccl);
		
		$json = str_replace('\t\t\t', "", $json);
		
		echo json_encode(array("success" => 1, "output" => json_decode($json)));
	}	
}

?>
