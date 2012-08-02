<?php
/*
 * Created on Jul 23, 2012
 */
class Ajax_semquel_run extends CPage {
	var $isSecure = false;
	function execute(){
		global $config;
	
		$question = $_POST['question'];
        
        $cmd = sprintf("echo '%s' | %s", $question, $config->path_semquel);
		
		ob_start();
		$out = shell_exec($cmd);
		ob_get_clean();	
		
		echo json_encode(array("success" => 1, "output" => json_decode($out)));
	}	
}
?>
