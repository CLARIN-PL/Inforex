<?php
/*
 * Created on Jul 23, 2012
 */
class Ajax_semquel_run extends CPage {
	var $isSecure = false;
	function execute(){
		global $config;
	
		$question = $_POST['question'];
		$ip = strval($_SERVER['REMOTE_ADDR']);
		
		$db_serel = new Database($config->get_dsn_questions(), true);
		$db_serel->execute("INSERT INTO questions (question, ip)" .
				" VALUES(?, ?)", array($question, $ip));
		
		$wcrft = new Wcrft($config->get_path_wcrft());
		$wcrft->setModel($config->get_path_wcrft_model()); 
		$wcrft->setConfig($config->get_wcrft_config());
		$ccl = $wcrft->tag($question, "text", "ccl");	

		$liner = new WSLiner2($config->get_serel_liner_wsdl());
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
