<?php
/*
 * Created on 2009-07-16
 *
 * Skrypt kopiuje treść raportów z bazy dancyh do plików tekstowych.
 * 
 */

chdir("../engine");
include("config.php");
require_once($config->path_engine . '/include.php');

$mdb2 =& MDB2::singleton($config->dsn);
if (PEAR::isError($mdb2)) {
    die($mdb2->getMessage());
}
$mdb2->query("SET CHARACTER SET 'utf8'");

$sql = "SELECT id, date FROM reports WHERE html_downloaded<>'0000-00-00 00:00:00'";
$rows = $mdb2->query($sql)->fetchAll(MDB2_FETCHMODE_ASSOC);
foreach ($rows as $row){
	$year = intval(date("Y", strtotime($row['date'])));
	$month = date("m", strtotime($row['date']));
	if ($year>2000 && $year<2009 && intval($month)>0 && intval($month)<=12) 
		echo "."; 
	else 
		die(sprintf("%d %d %d", $year, $month, $row['id']));
		
	if (false){
		$path = GLOBAL_PATH_REPORTS_HTML;
		if (!is_dir($path . DIR_SEP . $year))
			mkdir($path . DIR_SEP . $year);
		if (!is_dir($path . DIR_SEP . $year . DIR_SEP . $month))
			mkdir($path . DIR_SEP . $year . DIR_SEP . $month);
		$file_path = $path . DIR_SEP . $year . DIR_SEP . $month . DIR_SEP . sprintf("%s_%s_%d.txt", $year, $month, $row['id']);
		
		$id = $row['id'];
		$sql = "SELECT html FROM reports WHERE id=$id";
		$html = $mdb2->query($sql)->fetchOne();
		
		file_put_contents($file_path, $html);
	}
}


echo "Downloaded: ". count($rows) ."\n";

$sql = "SELECT COUNT(*) FROM reports WHERE html_downloaded='0000-00-00 00:00:00'";
echo "Empty: ". $mdb2->query($sql)->fetchOne() ."\n";
 
?>
