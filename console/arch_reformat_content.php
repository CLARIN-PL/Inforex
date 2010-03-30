<?php
/*
 * Created on 2009-08-26
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

// Konfiguracja 
chdir("../engine");
include("config.php");
require_once($conf_global_path . '/include.php');

$mdb2 =& MDB2::singleton($dsn);
if (PEAR::isError($mdb2)) {
    die($mdb2->getMessage());
}
$mdb2->query("SET CHARACTER SET 'utf8'");
// ----------------------------------------------------

$sql = "SELECT id, content FROM reports";
$result = mysql_query($sql);

$n = 0;
while($row = mysql_fetch_array($result)){

	$content = trim($row['content']);
	if ($content){	
		$content = reformat_content($content);
		echo strlen($content).",";
	}
}

?>
