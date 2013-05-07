<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
/**
 * Class representsa a page displaying a content to a user.
 *
 * @tutorial
 * == How to set access to a page ==
 * 1. The access parameters must be set in the derived class.
 * 2. Level of access:
 *    a) public access -- any user can see the content,
 *       > $isSecure = false;
 *       
 *    b) access only for the administrators
 *    	 > $isSecure = true;
 *       > $roles = array(); // no role defined
 *       
 *    c) any logged in user and administrators
 *       > $isSecure = true;
 *       > $roles = array('loggedin');
 *       
 *    d) users with a custom role and administrators
 *       > $isSecure = true;
 *       > $roles = array('page_unique_role', 'any_other_role');
 *       User must have at least one of the defined rules to see the content.
 *
 * 3. Defining user roles
 *    The role should be registered in the database in the tabel 'roles'. The roles are granted to a user by inserting an
 *    entry into the table 'user_roles'. An entry (1, 'admin') means, that a user with id=1 has role 'admin'.
 * 4. Dynamic access
 * 	  Any other dynamic access rules can be defined in the function 'checkPermission()'. The additional rules does not aplly
 *    the the administrators but only to users with 'loggedin' or any other defined custom role.
 * 5. Corpus subpage
 *    Any page that is a corpus subpage can have a predefined role 'corpus_owner'. The role allows the corpus owner to see the 
 *    page content with no respect to other access requirements (roles and checkPermission function).
 *       
 * @author Michał Marcińczuk
 */
class CPage {
	
	var $template;
	var $isSecure = true;
	var $roles = array();
	
	function CPage(){	
		global $config;	
		$this->template = new Smarty();
		$this->template->compile_dir = $config->path_engine . "/templates_c";
		$this->set('RELEASE', RELEASE);		
	}
	
	/**
	 * Check any custom permission to the page.
	 * @return true if user can access the page
	 */
	function checkPermission(){
		return true;
	}
	
	/**
	 * Assign a variable. The variable can be accessed from the smarty template.
	 * @param $name -- variable name
	 * @param $value -- variable value
	 */
	function set($name, $value){
		$this->template->assign($name, $value);
	}
	
	/**
	 * Assign a variable by a reference. The variable can be accessed from the smarty template.
	 * @param $name -- variable name
	 * @param $object -- variable reference to an object
	 */
	function set_by_ref($name, &$object){
		$this->template->assign_by_ref($name, $object);		
	}

	/**
	 * Get an variable value assign to the page.
	 * @param $name -- a variable name
	 */
	function get($name){
		if (isset($this->template->_tpl_vars[$name]))
			return $this->template->_tpl_vars[$name];
		else
			return null;
	}
		
	/**
	 * Assign to the page a table of variables.
	 * @param $variables -- a table of variables
	 */
	function setVariables($variables){
		foreach ($variables as $k=>$m)
			$this->set($k, $m);
	}

	/**
	 * Assign to the page a table of variable references. 
	 * @param $variables -- a table of variable references
	 */
	function setRefs($variables){
		foreach ($variables as $k=>$m)			
			$this->set_by_ref($k, $m);
	}
		
	/**
	 * Generate page content. This function must be overloaded in the derived function.
	 */
	function execute(){}

	/**
	 * Display page content using given template.
	 * @param $template_name -- name of a template. The template should save as a file /template/page_template_name.tpl 
	 */
	function display($template_name){
		global $config;
		$this->template->display($config->path_engine . "/templates/page_{$template_name}.tpl");
	}
		
	/**
	 * Make the browser to redirect the user to another page.
	 * @param $url -- location where the user should be redirected.
	 */
	function redirect($url){
		header("Location: $url");
		ob_clean();
	}
	
	function loadAnnotations(){
		$sql = "SELECT name, css FROM annotation_types WHERE css IS NOT NULL";
		$annotation_types = db_fetch_rows($sql);
		$annotationCss = "";
		foreach ($annotation_types as $an){
			if ($an['css']!=null && $an['css']!="") 
				$annotationCss = $annotationCss . "span." . $an['name'] . " {" . $an['css'] . "} \n"; 
		}		
		$this->set('new_style',$annotationCss);		
	}
}
?>
