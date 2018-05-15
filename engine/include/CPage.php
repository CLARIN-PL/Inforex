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
 *
 * ToDo: The description below will change
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

    var $name="";
    var $description="";
	var $template = null;
    var $warnings = array();

    /* ToDo: to remove */
    var $isSecure = true;
    var $roles = array();

    /** @var array By default any page requires 'admin' role */
    var $anySystemRole = array(ROLE_SYSTEM_USER_ADMIN);

    /** @var array If set then the access is possible only in the corpus context. */
    var $anyCorpusRole = array();

    /** @var array If set then the access is possible only in the perspective context. */
    var $anyPerspectiveAccess = array();


    /**
	 * List of media fiels (js, css, etc.) to include in the header section of html.
	 * array("type" => "js|css", "file => "path_to_a_file")
	 * @var array
	 */
	var $include_files = array();
	
	function CPage($name=null,$description=null){
		global $config;
		$this->name = $name;
		$this->description = $description;
		$this->template = new Smarty();
		$this->template->compile_dir = $config->path_engine . "/templates_c";
		$this->set('RELEASE', RELEASE);

		/**
		 * Include default JS and CSS files for the page
		 * js/page_{$page}.js — JS script for the $page,
		 * js/page_{$page}_resize.js — JS script to resize page content to window size,
		 * css/page_{$page}.css — CSS styles used on the $page.
		 * 
		 * The page name is taken from the class name, i.e. Page_{$page}.
		 */
		$className = get_class($this);
		if ( substr($className, 0, 5) == "Page_"){
			$this->includeJs("js/page.js");				
			$page = str_replace("Page_", "", $className);
			if (file_exists($config->path_www . "/js/page_{$page}.js")){
				$this->includeJs("js/page_{$page}.js");
			}
			if (file_exists($config->path_www . "/js/page_{$page}_resize.js")){
				$this->includeJs("js/page_{$page}_resize.js");
			}
			if (file_exists($config->path_www . "/css/page_{$page}.css")){
				$this->includeCss("css/page_{$page}.css");
			}
		}
	}

	function getName(){
		return $this->name;
	}

	function getDescription(){
		return $this->description;
	}

    /**
	 * Check if given user has privileges to see access the page.
     * @param $user User for which the authentication is done. Null represent not logged user.
     * @param $corpus Corpus context in which the authentication is done.
	 * @return true|AccessError true if user has privileges to access the page or AccessError with error description in other case.
     */
	function hasAccess($user=null, $corpus=null){
		$customAccess = $this->customPermissionRule($user, $corpus);
		if ( $customAccess !== null ){
			if ( $customAccess ){
				return true;
			} else {
				return "Does not have permission to access the page";
			}
		} else {
		    $rolesRequired = array();
		    $rolesUser = array();
            if ( count($this->anyPerspectiveAccess) > 0 ) {
                if (hasUserPerspectiveAccess($user['user_id'], $corpus['id'], $this->anyPerspectiveAccess)) {
                    return true;
                } else {
                    $userPerspectiveAccess = DBReportPerspective::getUserPerspectiveAccess($user['user_id'], $corpus['id']);

                    $userPerspectiveRoles = is_array($userPerspectiveAccess) ? $userPerspectiveAccess : array(ROLE_SYSTEM_USER_PUBLIC);
                    $rolesRequired = array_merge($rolesRequired, $this->anyPerspectiveAccess);
                    $rolesUser = array_merge($rolesUser, $userPerspectiveRoles);
                }
            }
			if ( count($this->anyCorpusRole) > 0 ) {
                if (hasUserCorpusRole($user, $corpus, $this->anyCorpusRole)) {
                    return true;
                } else {
                    $userCorpusRoles = is_array($corpus['role'][$user['user_id']]) ? array_keys($corpus['role'][$user['user_id']]) : array(ROLE_SYSTEM_USER_PUBLIC);
                    $rolesRequired = array_merge($rolesRequired, $this->anyCorpusRole);
                    $rolesUser = array_merge($rolesUser, $userCorpusRoles);
                }
            }
			if ( count($this->anySystemRole) > 0) {
                if ( hasUserSystemRole($user, $this->anySystemRole) ){
                    return true;
                } else {
                    $userSystemRoles = is_array($user['role']) ? array_keys($user['role']) : array(ROLE_SYSTEM_USER_PUBLIC=>"");
                    $rolesRequired = array_merge($rolesRequired, $this->anySystemRole);
                    $rolesUser = array_merge($rolesUser, $userSystemRoles);
				}
			}

			//array_values to prevent $rolesUser from becoming an object.
			$rolesUser = array_values(array_unique($rolesUser));
            $msg = "You do not have permission to access this page.";
			return new AccessError($msg, $rolesRequired, $rolesUser);
		}
	}

    /**
     * @param null $user
     * @param null $corpus
     * @return null If null then there is no custom permission rules and the default role-based mechanism is used.
     */
	function customPermissionRule($user=null, $corpus=null){
		return null;
	}

	/**
	 * Check any custom permission to the page.
     * ToDo: to remove
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
     * @return variable value or null if the variable is undefined
	 */
	function get($name){
		if (isset($this->template->_tpl_vars[$name])) {
            return $this->template->_tpl_vars[$name];
        } else {
            return null;
        }
	}
		
	/**
	 * Assign to the page a table of variables.
	 * @param $variables -- a table of variables
	 */
	function setVariables($variables){
		foreach ($variables as $k=>$m) {
            $this->set($k, $m);
        }
	}

	/**
	 * Assign to the page a table of variable references. 
	 * @param $variables -- a table of variable references
	 */
	function setRefs($variables){
		foreach ($variables as $k=>$m) {
            $this->set_by_ref($k, $m);
        }
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
		$this->set("include_files", $this->include_files);
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
	
	/**
	 * Include a JS file in the header section of the page.
	 * @param unknown $path
	 */
	function includeJs($path){
		$this->include_files[] = array("type"=>"js", "file"=>$path);	
	}
	
	/**
	 * Include a CSS file in the header section of the page.
	 * @param unknown $path
	 */
	function includeCss($path){
		$this->include_files[] = array("type"=>"css", "file"=>$path);	
	}

    /**
     * Add an warning occured during execution of the page.
     * @param $warning
     */
    function addWarning($warning){
        $this->warnings[] = $warning;
    }

    function addWarnings($warnings){
        $this->warnings = array_merge($this->warnings, $warnings);
    }

    /**
	 * Returns a list of warnings occured during execution of the page.
     * @return array
     */
    function getWarnings(){
        return $this->warnings;
    }

}
