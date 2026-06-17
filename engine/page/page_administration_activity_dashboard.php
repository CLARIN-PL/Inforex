<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

require_once(dirname(__FILE__) . '/../include/database/CDbActivity.php');

class Page_administration_activity_dashboard extends CPageAdministration {

    function execute(){
        $summary = DbActivity::getAdminDashboardSummary();
        $timeline = DbActivity::getAdminDashboardTimeline(24);
        $users = DbActivity::getAdminDashboardActiveUsers(25);

        $this->set('activity_dashboard_summary', $summary);
        $this->set('activity_dashboard_timeline', $timeline);
        $this->set('activity_dashboard_users', $users);
    }
}
