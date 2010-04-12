<?php
class CPage {
	
	var $template;
	var $isSecure = true;
	
	function CPage(){
		$this->template = new Smarty();
		$this->set('RELEASE', RELEASE);		
	}
	
	function set($name, $value){
		$this->template->assign($name, $value);
	}
	
	function display($template_name){
		global $conf_global_path;
		$this->template->display("$conf_global_path/templates/page_{$template_name}.tpl");
	}
	
	function execute(){}
	
	function redirect($url){
		header("Location: $url");
		ob_clean();
	}
}
?>
