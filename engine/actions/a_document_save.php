<?php

class Action_document_save extends CAction{
	
	function execute(){
		global $user, $mdb2, $corpus;
		
//		if ($_POST['formatowanie']){
//			// Uaktualnij formatowanie raportu
//			$content = $_POST['content'];			
//			$content = stripslashes($content); 
//			$content = mysql_escape_string($content);
//			$sql = "UPDATE reports SET content = '{$content}', formated=1 WHERE id = {$id}";
//			$mdb2->query($sql);
//			
//			// Uaktualnij status i typ raportu
//			$status = intval($_POST['status']);
//			$type = intval($_POST['type']);			
//			$sql = "UPDATE reports SET type = {$type}, status = {$status} WHERE id = {$id}";
//			$mdb2->query($sql);						
//		}
				
		return "document_edit";
	}
	
} 

?>
