<?php
/*
 * Skrypt do wygenerowania korpusu na potrzeby eksperymenty twyd09
 */

/* Konfiguracja */ 

$db_host = "localhost";
$db_user = "root";
$db_pass = "krasnal";
$db_name = "gpw";

$corpus_path = "/home/czuk/nlp/corpora/gpw2004/";

/* Konfiguracja - koniec */

if (!file_exists($corpus_path))
	mkdir($corpus_path, true);
	
mysql_connect($db_host, $db_user, $db_pass);
mysql_select_db($db_name);
mysql_query("SET CHARACTER SET utf8;");

$sql = "SELECT * FROM reports WHERE YEAR(date)=2004 AND status=2";
$result = mysql_query($sql);

while ($row = mysql_fetch_array($result)){
	$content = $row['content'];
	$content = html_entity_decode($content, ENT_COMPAT, "utf-8");

	// Location of the WSDL file 
	$url = "http://plwordnet.pwr.wroc.pl/clarin/ws/takipi/takipi.wsdl"; 
	 
	// Create a stub of the web service 
	$client = new SoapClient($url); 
	 
	// Send a request 
	$request = $client->Tag($content, "XML", true);
	
	echo $request->status . ": ". $request->msg ."\n"; 
}

?>
