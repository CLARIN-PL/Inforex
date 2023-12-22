<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class DbReportFlag{
	
	/*
	 * Return list of corpora_flags ids
	 * 
	 * index_flags: array, values: corpora_flags.corpora_flag_id or corpora_flags.short
	 */
	static function getReportFlagData($report_ids, $corpora_flag_ids){
		global $db;
			//$sql .= "LEFT JOIN reports_flags rf ON reports.id=rf.report_id " .
			//		"LEFT JOIN corpora_flags cf ON cf.corpora_flag_id=rf.corpora_flag_id ";
					
		$sql = "SELECT rf.report_id, cf.short " .
				"FROM reports_flags rf " .
				"LEFT JOIN corpora_flags cf " .
				"ON cf.corpora_flag_id=rf.corpora_flag_id " .
				"WHERE rf.report_id " .
				"IN ('" . implode("','",$report_ids) . "') " .
				"AND cf.corpora_flag_id " .
				"IN ('" . implode("','",$corpora_flag_ids) . "') " .
				"AND rf.flag_id " .
				"IN (3,4)";
		return $db->fetch_rows($sql);
	}

	/**
	 * Zwraca wartości flag przypisanych wskazanemu dokumentowi.
	 * @return tablica asocjacyjna, której kluczem jest skrócona nazwa flagi zrzutowana do małych liter,
	 *         a wartością identyfikator flagi (1,..,5)
	 */
	static function getReportFlags($report_id){
		global $db;
		$sql = "SELECT cf.short, rf.flag_id FROM reports_flags rf " .
				" JOIN corpora_flags cf USING (corpora_flag_id)" .
				" WHERE rf.report_id = ?";
		$flags = $db->fetch_rows($sql, array($report_id));
		$docflags = array();
		foreach ($flags as $f){
			$docflags[strtolower($f['short'])] = $f['flag_id'];
		}
		return $docflags;
	}

	/*
	 * Deletes a flag.
	 */
	static function deleteReportFlag($cflag_id, $report_id){
	    global $db;

        $sql = "DELETE FROM reports_flags WHERE corpora_flag_id= ? AND report_id= ?";

        $db->execute($sql, $cflag_id, $report_id);
    }

    static function changeFlagStatus($flag_id, $flag_status, $report_id, $user_id){
	    global $db;

	    $params = array(intval($flag_id), intval($flag_status), intval($report_id), intval($user_id));
	    $sql = "CALL changeFlagStatus(?,?,?,?)";

	    $db->execute($sql, $params);
    }

    static function getReportFlagHistory($report_id, $user, $flag){
        global $db;

        $params = array($report_id);

        if ($user != null) {
            $params[] = $user;
        }

        if ($flag != null) {
            $params[] = $flag;
        }


        $sql = "SELECT cf.name AS 'flag', f1.flag_id AS new_status_id, f1.name AS 'new_status', f2.name AS 'old_status', 
                f2.flag_id AS old_status_id, u.screename, DATE_FORMAT(fsh.date , '%H:%i, %D %M %Y') AS 'date' FROM flag_status_history fsh 
                JOIN corpora_flags cf ON cf.corpora_flag_id = fsh.flag_id
                JOIN flags f1 ON f1.flag_id = fsh.new_status
                LEFT JOIN flags f2 ON f2.flag_id = fsh.old_status
                JOIN users u ON u.user_id = fsh.user_id
                WHERE (fsh.report_id = ? " .
                ($user != null ? " AND u.user_id = ? ": "").
                ($flag != null ? " AND cf.corpora_flag_id = ? ": "").
                ") ORDER BY fsh.date DESC";


        return $db->fetch_rows($sql, $params);
    }

    static function getReportFlagChangeUsers($report_id){
        global $db;

        $sql = "SELECT u.user_id, u.screename FROM flag_status_history fsh 
                JOIN users u ON u.user_id = fsh.user_id
                WHERE fsh.report_id = ?
                GROUP BY u.user_id
                ORDER BY u.screename DESC";
        return $db->fetch_rows($sql, array($report_id));
    }
}

?>
