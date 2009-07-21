<?php
mysql_connect("localhost", "root", "krasnal");
mysql_select_db("gpw");

$id = intval($_GET['id']);

if ($_POST['skip']){
	$sql = "UPDATE reports SET skip=1 WHERE id={$id}";
	$result = mysql_query($sql);	
}

$sql = "SELECT id FROM reports WHERE id<{$id} AND content='' AND html_downloaded!='0000-00-00 00:00:00' AND skip=0 ORDER BY id DESC LIMIT 1";
$result = mysql_query($sql);
$row_prev = mysql_fetch_array($result);

$sql = "SELECT id FROM reports WHERE id>{$id} AND content='' AND html_downloaded!='0000-00-00 00:00:00' AND skip=0 ORDER BY id ASC LIMIT 1";
$result = mysql_query($sql);
$row_next = mysql_fetch_array($result);

$sql = "SELECT * FROM reports WHERE id={$id}";
$result = mysql_query($sql);
$row = mysql_fetch_array($result);

$links = ($row_prev?'<a href="index.php?id='.$row_prev['id'].'">poprzedni</a>':'poprzedni');
$links .= " | ";
$links .= ($row_next?'<a href="index.php?id='.$row_next['id'].'">następny</a>':'następny');
$links = "<div>{$links}</div>";

$skip = '<form method="post"><input type="submit" value="skip" name="skip"/></form>';

$html = $row['html'];
if ($row['skip']==0)
	$html = str_replace("</HEAD>", "</HEAD>".$links.$skip, $html);
else
	$html = str_replace("</HEAD>", "</HEAD>{$links}<h1 style='color: red'>Ten raport jest oznaczony jako SKIP</h1>", $html);
	 
echo $html;

?>
