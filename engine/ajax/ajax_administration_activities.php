<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_administration_activities extends CPageAdministration {

    function execute(){
        global $db;

        $sql = "SELECT
                    u.user_id,
                    u.login,
                    u.screename,
                    activity_summary.last_activity,
                    activity_summary.num_of_activities,
                    COALESCE(recent_activity_summary.num_of_activities_30, 0) AS num_of_activities_30
                FROM (
                    SELECT
                        a.user_id,
                        MAX(a.datetime) AS last_activity,
                        COUNT(*) AS num_of_activities
                    FROM activities a FORCE INDEX (activities_user_datetime_idx)
                    WHERE a.user_id IS NOT NULL
                    GROUP BY a.user_id
                ) activity_summary
                JOIN users u ON u.user_id = activity_summary.user_id
                LEFT JOIN (
                    SELECT
                        a.user_id,
                        COUNT(*) AS num_of_activities_30
                    FROM activities a FORCE INDEX (activities_datetime_user_idx)
                    WHERE a.user_id IS NOT NULL
                      AND a.datetime >= NOW() - INTERVAL 30 DAY
                    GROUP BY a.user_id
                ) recent_activity_summary ON recent_activity_summary.user_id = activity_summary.user_id
                ORDER BY activity_summary.last_activity DESC";

        return $db->fetch_rows($sql);
    }
}
