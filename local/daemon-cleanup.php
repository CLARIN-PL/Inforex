<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

$enginePath = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), "..", "engine")));
require_once($enginePath. DIRECTORY_SEPARATOR . "settings.php");
Config::Config()->put_path_engine($enginePath);
Config::Config()->put_localConfigFilename(realpath($enginePath . "/../config/").DIRECTORY_SEPARATOR."config.local.php");

require_once($enginePath . "/cliopt.php");
require_once($enginePath . "/clioptcommon.php");

mb_internal_encoding("utf-8");
ob_end_clean();

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter("db-uri", "U", "URI", "connection URI: user:pass@host:ip/name"));

try{
	$opt->parseCli(isset($argv) ? $argv : null);
	Config::Config()->put_dsn(CliOptCommon::parseDbParameters($opt, Config::Config()->get_dsn()));
}catch(Exception $ex){
	print "!! ". $ex->getMessage() . " !!\n\n";
	$opt->printHelp();
	print("\n");
	return;
}

try{
	$daemon = new TaskDaemon(Config::Config()->get_dsn());
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
