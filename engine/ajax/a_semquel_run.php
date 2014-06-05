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
		$ip = strval($_SERVER['REMOTE_ADDR']);
		
		$db_serel = new Database($config->get_dsn_questions(), true);
		$db_serel->execute("INSERT INTO questions (question, ip)" .
				" VALUES(?, ?)", array($question, $ip));
		
//		$wcrft = new Wcrft($config->get_path_wcrft());
//		$wcrft->setModel($config->get_path_wcrft_model()); 
//		$wcrft->setConfig($config->get_wcrft_config());
//		$ccl = $wcrft->tag($question, "text", "ccl");	

//		$liner = new WSLiner2($config->get_serel_liner_wsdl());
//		$ccl = $liner->chunk($ccl, "CCL", "CCL");

		$liner_cmd = "/nlp/eclipse/workspace_liner2/liner2_dev/liner2.sh";
		$liner_model = "/nlp/eclipse/workspace_liner2/models-workdir/liner2.4/liner2.4-models-fat-pack-SNAPSHOT/config-56nam.ini";

		$liner = new Liner2($liner_cmd, $liner_model);
		$ccl = $liner->chunk($question, "plain:wcrft", "ccl");
		
		$wccl = new Wccl();
		$ccl = $wccl->run($ccl, $config->get_file_with_rules());

		$semql = new Semql($config->get_path_semql());
		$json = $semql->analyze($ccl);
		
		$json = str_replace('\t\t\t', "", $json);
		
		return json_decode($json);
	}	
}

?>
