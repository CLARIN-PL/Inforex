<?php
/*include("../include/anntakipi/ixtTakipiAligner.php"); 
include("../include/anntakipi/ixtTakipiStruct.php"); 
include("../include/anntakipi/ixtTakipiDocument.php"); 
include("../include/anntakipi/ixtTakipiHelper.php"); */


class Action_report_set_tokens extends CAction{
		
	var $isSecure = false;
	
	function checkPermission(){
		return true;
	}
	
	function execute(){
		global $corpus, $user, $mdb2;
		if ($_FILES["file"]["error"] > 0){
			$this->set("error","file upload error");
			return null;
			//echo "Error: " . $_FILES["xcesFile"]["error"] . "<br />";
		}
		//echo "Upload: " . $_FILES["xcesFile"]["name"] . "<br />";
	  	//echo "Type: " . $_FILES["xcesFile"]["type"] . "<br />";
	  	//echo "Size: " . ($_FILES["xcesFile"]["size"]) . " bytes<br />";
	  	//echo "Stored in: " . $_FILES["xcesFile"]["tmp_name"];
	  	$report_id = $_GET['id']; 
	  	$xcesFileName = $_FILES["xcesFile"]["tmp_name"];
	  	try {
	  		$takipiDoc = TakipiReader::createDocument($xcesFileName);
	  	}
	  	catch (Exception $e){
			$this->set("error","file upload error");
	  		return null;
	  	}
	  	$takipiText = "";
	  	//$tokensArray = array();
	  	$tokensValues = "";
	  	var_dump($takipiDoc->getTokens());
	  	foreach ($takipiDoc->getTokens() as $token){
	  		$tokensValues = $tokensValues ."($report_id,".mb_strlen($takipiText);
	  		$takipiText = $takipiText . $token->orth;
	  		//echo "|".$token->orth . "| " . mb_strlen(html_entity_decode($token->orth, ENT_COMPAT, "UTF-8")) . "<br/>";
	  		$tokensValues = $tokensValues ."," . (mb_strlen($takipiText)-1) . ")," ;
	  		//array_push($tokensArray, array($startToken, $endToken));
	  	}
		$dbHtml = new HtmlStr(
					html_entity_decode(
						normalize_content(
							$mdb2->queryOne("SELECT content " .
											"FROM reports " .
											"WHERE id=$report_id")), 
						ENT_COMPAT, 
						"UTF-8"), 
					true);
		$takipiText = html_entity_decode($takipiText, ENT_COMPAT, "UTF-8");
		$dbText = preg_replace("/\n+|\r+|\s+/","",$dbHtml->getText(0, false));
		/*echo strlen($takipiText) . "<br/>";
		echo strlen($dbText) . "<br/>";
		echo ($takipiText==$dbText) . "<br/>";
	  	echo $takipiText . "<br/>";
	  	echo $dbText;*/
	  	//echo "<pre>" . $takipiText . "</pre>";
	  	//echo json_encode($tokensArray);
	  	if ($takipiText==$dbText){
	  		$tokensValues = mb_substr($tokensValues,0,-1);
	  		//echo $tokensValues;
	  		db_execute("DELETE FROM tokens WHERE report_id=$report_id");
			db_execute("INSERT INTO `tokens` (`report_id`, `from`, `to`) VALUES $tokensValues");  		
			$this->set("message","Tokens successfully set");
	  	}
	  	else {
	  		$this->set("error","Wrong file format");		  		
	  	}
		  	
		  	
		/*$users_roles = $_POST['role'];
		db_execute("DELETE FROM users_corpus_roles WHERE corpus_id = {$corpus['id']}");
		foreach ($users_roles as $user_id=>$roles){
			foreach ($roles as $role=>$desc)
				db_execute("INSERT INTO users_corpus_roles VALUES(?, ?, ?)", array($user_id, $corpus['id'], $role));
		}
		
		$this->set("action_performed", "Zmiany ustawień zostały zapisane");*/
		return null;
	}
	
} 

?>
