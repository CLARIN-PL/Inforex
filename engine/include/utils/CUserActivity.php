<?php
/*
 * Created on 2010-09-21
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class UserActivity{

	static function login($user_id){
		$now = date("Y-m-d H:i:s");
		db_execute("INSERT INTO `user_activities`(`user_id`, `started`, `ended`, `counter`, `login`)" .
				" VALUES (?, ?, ?, 0, 1)",
				array( $user_id, $now, $now));
	}
	
	static function log($user_id){
		
		$time = date("Y-m-d H:i:s", strtotime("-15 minutes"));
		$now = date("Y-m-d H:i:s");
		
		$activity = db_fetch("" .
				"SELECT *" .
				" FROM `user_activities`" .
				" WHERE `user_id` = ?" .
				"   AND ? <= `ended`" .
				" ORDER BY `started` DESC" .
				" LIMIT 1",
				array( $user_id, $time ));
				
		fb($activity);
				
		if ($activity){
			db_execute("UPDATE `user_activities`" .
					" SET `ended` = ?" .
					"   , `counter` = `counter` + 1 " .
					" WHERE `id` = ?",
					array( $now, $activity['id'] ) );
		}else{
		db_execute("INSERT INTO `user_activities` (`user_id`, `started`, `ended`, `counter`, `login`)" .
				" VALUES (?, ?, ?, 0, 0)",
				array( $user_id, $now, $now));
		}
		
	}
	
}
?>
