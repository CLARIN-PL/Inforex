<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class PerspectiveUser_activity extends CCorpusPerspective {

    function execute()
    {
        global $db, $corpus;

        $this->page->includeJs('js/corpus_user_activity.js');

        $sql = "SELECT u.*, max(a.datetime) as last_activity, COUNT(a.activity_page_id) as num_of_activities, COUNT(CASE WHEN (a.datetime BETWEEN NOW() - INTERVAL 30 DAY AND NOW() = TRUE) THEN 1 END) as 'num_of_activities_30' FROM activities a
                JOIN users u ON u.user_id = a.user_id
                WHERE a.corpus_id = ?
                GROUP BY u.user_id";

        $activities = $db->fetch_rows($sql, $corpus['id']);

        $this->page->set("activities", $activities);
    }

}
?>
