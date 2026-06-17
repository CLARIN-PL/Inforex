<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

require_once(dirname(__FILE__) . '/../include/database/CDbActivity.php');

class Page_administration_queue_monitor extends CPageAdministration {

    function execute(){
        $queues = DbActivity::getAdminDashboardQueues();
        $selectedQueueId = isset($_GET['queue_id']) ? strval($_GET['queue_id']) : null;
        $selectedQueueStatus = isset($_GET['queue_status']) ? strval($_GET['queue_status']) : null;
        $queueDetail = ($selectedQueueId && $selectedQueueStatus)
            ? DbActivity::getAdminDashboardQueueDetail($selectedQueueId, $selectedQueueStatus, 100)
            : null;

        $this->set('activity_dashboard_queues', $queues);
        $this->set('activity_dashboard_queue_detail', $queueDetail);
    }
}
