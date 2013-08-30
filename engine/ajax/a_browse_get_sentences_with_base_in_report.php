<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_browse_get_sentences_with_base_in_report extends CPage {
	
	function checkPermission(){
            global $user;

            return (boolean) (intval($user['user_id']));
	}
	
	function execute(){
		global $mdb2;
                
		$report_id = (int) $_POST['report_id'];
		$base = $_POST['base'];
                $result = ReportSearcher::get_sentences_with_base_in_report($report_id, $base);
                if (count($result) === 0) {
                    $return = array('error' => 'Base is not found.');
                } else {
                    $return = $result;
                }
		return $result;
	}
	
}
?>
