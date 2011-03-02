<?php
/**
 */
class Ajax_lps_validate_xml extends CPage {
	
	function checkPermission(){
		if (hasRole('admin') || hasCorpusRole('editor') || isCorpusOwner())
			return true;
		else
			return "Brak prawa do edycji treści.";
	}
		
	/**
	 * Generate AJAX output.
	 */
	function execute(){
		global $mdb2, $user, $corpus, $config;
	
		$report_id = intval($_POST['report_id']);
		$content = stripslashes(strval($_POST['content']));

		$c = new MyDOMDocument();
		$c->loadXML($content);
		$c->schemaValidate("{$config->path_engine}/resources/lps/lps.xsd");

		ob_start();
		print_r($c->errors);
		$html = ob_get_clean();
		
		
		
		$json = array( "success"=>1 );		
		echo json_encode($json);
	}
	
}
?>
