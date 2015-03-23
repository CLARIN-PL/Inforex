<?
$text = "Ala ma kota w nosie.";
$wsdl = "http://ws.clarin-pl.eu/wsg/tagger/index.php?wsdl";

// Create a stub of the web service 
$client = new SoapClient($wsdl, array('cache_wsdl' => WSDL_CACHE_NONE));

// Send a request 
$request = $client->__soapCall("SendRequest", array(array("content"=>$text)));
$token = $request->token;		
$timeout = 10;
echo "Token:$token\n";

$result = $client->GetResult($token);

do 
{ 
    sleep(1);  
    $result = $client->__soapCall("CheckStatus", array(array("token"=>$token)));
    $status = $result->status;
    echo "$status (timeout=$timeout)\n";
}while ( $status == "QUEUE" || $status == "PROCESSING" && $timeout-- ); 
 
if ( $status == "READY" ){ 
	$result = $client->__soapCall("GetResult", array(array("token"=>$token)));
	$content = $result->xml;
	echo $content;
}
else{
	return false;
} 

?>