<?php
class CPage {
	
	var $template;
	
	function CPage(){
		$this->template = new Smarty();
		$this->set('IS_RELEASE', IS_RELEASE);		
	}
	
	function set($name, $value){
		$this->template->assign($name, $value);
	}
	
	function display($template_name){
		global $conf_global_path;
		$this->template->display("$conf_global_path/templates/page_{$template_name}.tpl");
	}
	
	function execute(){}
}
?>
