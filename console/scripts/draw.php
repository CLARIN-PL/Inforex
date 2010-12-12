<?php

//mysql_connect("localhost", "root", "krasnal");
mysql_connect("nlp.pwr.wroc.pl:3308", "gpw", "gpw");
mysql_select_db("gpw");
mysql_query("SET CHARACTER SET utf8");

mb_internal_encoding("utf8");

function fb(){}

require_once("PEAR.php");
require_once("MDB2.php");
include ("../../engine/config.php");
include ("../../engine/config.local.php");
//include ("../../engine/database.php");

$sql = "select r.* from reports r where r.corpora = 7";
$rows = mysql_query($sql);

while ($r = mysql_fetch_array($rows)){
	file_put_contents("draw/".$r['link'], $r['content']);
}

?>