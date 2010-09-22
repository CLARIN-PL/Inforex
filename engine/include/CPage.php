<?php
class CPage {
	
	var $template;
	var $isSecure = true;
	var $roles = array();
	
	function CPage(){
		$this->template = new Smarty();
		$this->set('RELEASE', RELEASE);		
	}
	
	function checkPermission(){
		return true;
	}
	
	function set($name, $value){
		$this->template->assign($name, $value);
	}
	
	function set_by_ref($name, &$object){
		$this->template->assign_by_ref($name, $object);		
	}

	function get($name){
		if (isset($this->template->_tpl_vars[$name]))
			return $this->template->_tpl_vars[$name];
		else
			return null;
	}
		
	function setVariables($variables){
		foreach ($variables as $k=>$m)
			$this->set($k, $m);
	}

	function setRefs($variables){
		foreach ($variables as $k=>$m)			
			$this->set_by_ref($k, $m);
	}
		
	function execute(){}

	function display($template_name){
		global $config;
		$this->template->display($config->path_engine . "/templates/page_{$template_name}.tpl");
	}
		
	function redirect($url){
		header("Location: $url");
		ob_clean();
	}
}
?>
