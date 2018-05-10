<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class PerspectiveUsers extends CCorpusPerspective {
	
	function execute()
	{
		global $db, $corpus;
		$sql = "SELECT u.user_id, u.screename, u.login, u.email, us.role" .
					" FROM users_corpus_roles us " .
					" RIGHT JOIN users u ON (us.user_id=u.user_id AND us.role = '".CORPUS_ROLE_READ."' AND us.corpus_id=?)" .
					" ORDER BY u.screename";

		$users = $db->fetch_rows($sql,array($corpus['id']));

        foreach($users as $key => $user){
            $last_activity_sql = "  SELECT datetime as 'last_activity' FROM `activities`
                                WHERE (user_id = ? AND corpus_id = ?)
                                ORDER BY datetime DESC";
            $last_activity = $db->fetch_one($last_activity_sql, array($user['user_id'], $corpus['id']));
            if($last_activity != null){
                $last_activity_date = new DateTime($last_activity);
                $last_activity = $last_activity_date->format('H:i:s d-m-Y');
            }
            $users[$key]['last_activity'] = $last_activity;
        }
					
		$this->page->set("users_in_corpus", $users);
	}
}
?>
