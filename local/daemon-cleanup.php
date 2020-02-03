<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

$enginePath = realpath(dirname(__FILE__) . "/../engine/");
$configPath = realpath(dirname(__FILE__) . "/../config/");
include($enginePath . "/config.php");
include($configPath . "/config.local.php");
include($enginePath . "/include.php");
include($enginePath . "/cliopt.php");
include($enginePath . "/clioptcommon.php");

mb_internal_encoding("utf-8");
ob_end_clean();
 
/******************** set configuration   *********************************************/

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));

/******************** parse cli *********************************************/

try{
	$opt->parseCli($argv);
	$config->dsn = CliOptCommon::parseDbParameters($opt, $config->dsn);
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	die("\n");
}

try{
	$daemon = new TaskDaemon($config->dsn);
	$daemon->tick();
	sleep(2);
}
catch(Exception $ex){
	print "Error: " . $ex->getMessage() . "\n";
	print_r($ex);
}


/**
 * Handle single request from tasks_documents.
 */
class TaskDaemon{
    var $db = null;

    function __construct($dsn){
        $this->db = new Database($dsn, false);
        $GLOBALS['db'] = $this->db;
    }

    /**
     * Check the queue for new request.
     */
    function tick(){
        $sql = "SELECT id FROM reports WHERE deleted = 1";
        foreach ($this->db->fetch_ones($sql, 'id') as $id){
            DbReport::deleteReport($id);
            echo sprintf("[%s] INFO: Document with id %d has been deleted\n", date("Y-m-d H:i:s"), $id);
        }
    }

}
