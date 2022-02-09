<?php

$enginePath = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), "..", "engine")));
require_once($enginePath. DIRECTORY_SEPARATOR . "settings.php");
require_once($enginePath. DIRECTORY_SEPARATOR . 'include.php');

/* for pHP7
$enginePath = realpath(implode(DIRECTORY_SEPARATOR, array(dirname(__FILE__), "..", "engine")));
require_once($enginePath."/config/Singleton.php");
require_once($enginePath."/config/Config.php");
require_once($enginePath."/include/database/Database.php");
*/

/*** wrap */
Config::Config()->put_localConfigFilename(realpath($enginePath . "/../config/").DIRECTORY_SEPARATOR."config.local.php");
$db = new Database(Config::Config()->get_dsn());
$sql = "REPLACE INTO `annotation_sets` (`annotation_set_id`,`name`,`description`) VALUES (1,'default name','default desc'), (2,'default name','default desc'), (3,'default name','default desc');";
$db->execute($sql);
$db->disconnect(); unset($db);
//ini_set("log_errors", 1);
//ini_set("error_log","memorylog.txt");

require_once("import-corpus-cli.php"); 

?>
