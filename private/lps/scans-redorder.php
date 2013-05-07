<?php

require_once("PEAR.php");
require_once("MDB2.php");
include ("../../engine/config.php");
include ("../../engine/config.local.php");
//$config->dsn = array(
//    			'phptype'  => 'mysql',
//    			'username' => 'gpw',
//    			'password' => 'gpw',
//    			'hostspec' => 'nlp.pwr.wroc.pl:3308',
//    			'database' => 'gpw',
//				);

include ("../../engine/database.php");

function fb($s){ }

$rows = db_fetch_rows("SELECT * FROM reports WHERE corpora = 3")or die(mysql_error());

$num2id = array();
$ids = array();

foreach ($rows as $row){
	if (preg_match("/([A-Z])\.([0-9]+.[0-9]+)/", $row['title'], $m))
	{
		list($a, $b, $c) = explode(".", $row['title']);
		$ids[] = $row['id'];
		
		$imgs = db_fetch_rows("SELECT * FROM reports_and_images WHERE report_id = ?", array($row['id']));
		$images = array();
		foreach ($imgs as $img){
			$images[] = array('image_id'=>$img['image_id'], 'position'=>$img['position']);
		}

		$num2id[$b * 100 + $c] = array("title"=>$m[2], "id"=>$row['id'], "content"=>$row['content'], "images"=>$images);
	}
}

sort($ids);
ksort($num2id);

$idi = 0;
db_execute("DELETE FROM reports_and_images");
foreach ($num2id as $k=>$val){
	$id = $ids[$idi++];
	$title = $val['title'];
	$content = $val['content'];
	
	$sql = "UPDATE reports SET title = ?, content = ? WHERE id = ?";
	db_execute($sql, array($title, $content, $id));
	echo $sql."\n";
	
	foreach ($val['images'] as $p=>$v){
		$image_id = $v['image_id'];
		$position = $v['position'];
		
		$sql = "INSERT INTO reports_and_images VALUES(?, ?, ?)";
		db_execute($sql, array($id, $image_id, $position));
	} 
}

?>
