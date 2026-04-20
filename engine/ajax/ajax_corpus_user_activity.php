<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_corpus_user_activity extends CPageCorpus {

    function execute(){
        global $db;

        $corpus_id = $_POST['corpus_id'];

        $sql = "SELECT
                    u.user_id,
                    u.login,
                    u.screename,
                    activity_summary.last_activity,
                    activity_summary.num_of_activities,
                    activity_summary.num_of_activities_30
                FROM (
                    SELECT
                        a.user_id,
                        MAX(a.datetime) AS last_activity,
                        COUNT(*) AS num_of_activities,
                        SUM(CASE WHEN a.datetime >= NOW() - INTERVAL 30 DAY THEN 1 ELSE 0 END) AS num_of_activities_30
                    FROM activities a FORCE INDEX (activities_corpus_user_datetime_idx)
                    WHERE a.corpus_id = ?
                      AND a.user_id IS NOT NULL
                    GROUP BY a.user_id
                ) activity_summary
                JOIN users u ON u.user_id = activity_summary.user_id
                ORDER BY activity_summary.last_activity DESC";

        return $db->fetch_rows($sql, array($corpus_id));
    }
}
?>
