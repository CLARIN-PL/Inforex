<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class Phantom {

    public function __call($name, $arguments)
    {
        throw new Exception("Method {$name} is not supported.");
    }

} // Phamtom class

use engine\Config\Singleton as Singleton;

/**
 * Inforex configuration
 */
class Config extends Singleton\Singleton{

    static private $sid = "gpw";
	//var $sid = "gpw";
	static private $session_time = 0;
	static private $offline = false;
    static private $db_charset = "utf8mb4";
	
	/* Inforex basic configuration */
	static private $path_engine       = 'ABSOLUTE_PATH_TO:inforex/engine';
	static private $path_www          = 'ABSOLUTE_PATH_TO:inforex/public_html';	
	static private $path_secured_data = 'ABSOLUTE_PATH_TO:inforex/data';

	/* set $federationLoginUrl to null if regular login is to be used */
    static private $federationLoginUrl = null;
    static private $federationValidateTokenUrl = null;

	static private $url = 'http://localhost/inforex';
	static private $dsn = array(
    		'phptype'  => 'mysql',
    		'username' => 'inforex',
    		'password' => 'password',
    		'hostspec' => 'localhost',
    		'database' => 'inforex',
	);

	static private $wccl_match_enable = false;
	
	static private $wccl_match_tester_corpora = array(
			array("name"=>"KPWr 1.2.2 TimeML train &ndash; all (1&ndash;551)", 
					"path"=>"/nlp/corpora/pwr/kpwr-release/kpwr-1.2.2-time-disamb/index_time_train.txt"),
			array("name"=>"KPWr 1.2.2 TimeML train &ndash; A (1&ndash;100)", "path"=>"/index_time_a.txt"),
			array("name"=>"KPWr 1.2.2 TimeML train &ndash; B (101&ndash;200)", "path"=>"/index_time_b.txt"),
			array("name"=>"KPWr 1.2.2 TimeML train &ndash; C (201&ndash;300)", "path"=>"/index_time_c.txt"),
			array("name"=>"KPWr 1.2.2 TimeML train &ndash; D (301&ndash;551)", "path"=>"/index_time_d.txt"),
			array("name"=>"KPWr 1.2.2 TimeML tune","path"=>"/index_time_tune.txt"),
			array("name"=>"KPWr 1.2.7 TimeML train&ndash; all",	"path"=>"/index_time_train.txt")
		); 	
		
	static private $wccl_match_daemon = null;

	/* Advanced parameters */
	static private $path_python       = 'python';
	static private $path_liner        = null;
	static private $path_liner2       = null;
	static private $path_nerd         = null;
	static private $path_wcrft        = null;
	static private $path_semql        = null;
	static private $file_with_rules   = null; 
	static private $takipi_wsdl       = null;
	static private $liner_wsdl        = null;
	static private $serel_liner_wsdl  = null;
	static private $path_wcrft_model  = "";
	static private $wcrft_config	   = "nkjp_s2.ini";
	static private $wcrft2_config	   = "nkjp_e2";
	static private $log_sql           = false;
	static private $log_output		   = "fb";
	static private $path_grabber	   = null;
	
    // path for local config file - if exists
    static private $localConfigFilename  = "";
		
	// for more friendly call only...
    final public static function Config(){

        return self::getInstance();

    } // Config()
		
	// constructor - default values of some parameters 	
	final protected function __construct(){

        parent::__construct();

		$this->session_time = 60 * 60 * 24 * 356 * 2;

		// Setup default paths
		$path_engine = realpath(__DIR__ . DIRECTORY_SEPARATOR . "..");
		$path_inforex = realpath($path_engine . DIRECTORY_SEPARATOR . '..');

		$this->path_engine       = $path_engine;
		$this->path_www          = $path_inforex . DIRECTORY_SEPARATOR . 'public_html';	
		$this->path_secured_data = $path_inforex . DIRECTORY_SEPARATOR . 'secured_data';
		
		$this->path_exports      = $this->path_secured_data . DIRECTORY_SEPARATOR . 'exports';
		
		$this->wccl_match_tester_script = $path_engine . "/../apps/wccl/wccl-gateway.py";
		$this->wccl_match_script = $path_engine . "/../apps/wccl/wccl-gateway-run.py";		

	}

	function __call($method,$arguments){
        // for crazy or lazy developers may be set_<sth> too
        if ( substr($method, 0, 4) == "set_" ){
            // change set_<sth> to put_<sth>
            $method=preg_replace("/^se/","pu",$method);
        }
		if ( substr($method, 0, 4) == "get_" ){
			$parameter_name = substr($method, 4);
			if (  property_exists($this,$parameter_name) ) {
				if ( isset($this->$parameter_name ) ) {
					return $this->$parameter_name;
				} elseif ( isset(self::${$parameter_name})) { 
					return self::${$parameter_name};
				} else
					// last case - null is not catch by isset()
					return null;
			} else {
				throw new Exception("Parameter '$parameter_name' not defined in the configuration file.");
			}
		} else if ( substr($method, 0, 4) == "put_" ){
            // implementation of put_<sth>($value) method
            $parameter_name = substr($method, 4);
            $value = $arguments[0];
            $this->{$parameter_name}=$value;
            if($parameter_name=="localConfigFilename"){
                $this->loadConfigFromFile($this->localConfigFilename);
            }
        } 
		else
			call_user_func_array(array($this,"_".$method),$arguments);		
	}
	
    private function loadOldLocalConfig($pathname) {

        $config = new Phantom();   
        include($pathname);
        $classVars=get_object_vars($config);
        foreach($classVars as $name => $value) {
                $this->{"put_".$name}($config->{$name});
        }

    } // loadOldLocalConfig()

    public function loadConfigFromFile($pathname) {

        if ( file_exists($pathname) ) {
            try {

                $this->loadOldLocalConfig($pathname);

            } catch(Exception $e) {

                // Ładowanie wg. nowego configa
                $config = $this;
                try {
                    include($pathname);
                } catch( Exception $e ) {
                    throw new Exception("Error loading configuration file '$pathname'.");
                } // try..catch() new syntax
            }  // try...catch() old syntax
        } // if file_exists()

    } // loadConfigFromFile()

	public function dumpConfigSets() {

        return get_class_vars(get_class($this));

	} // dumpConfigSets()


} // Config class

 
?>
