<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

require_once(dirname(__FILE__) . '/../include/diagnostics/CAdminErrorLogs.php');

class Page_administration_error_logs extends CPageAdministration
{
    function execute()
    {
        $overview = CAdminErrorLogs::getLogOverview();
        $sources = CAdminErrorLogs::getConfiguredSources();

        $selectedSource = isset($_GET['source']) ? strval($_GET['source']) : '';
        if ($selectedSource === '' && !empty($overview)) {
            $selectedSource = $overview[0]['key'];
        }

        $lines = isset($_GET['lines']) ? intval($_GET['lines']) : 200;
        $lines = max(20, min(1000, $lines));
        $query = isset($_GET['q']) ? trim(strval($_GET['q'])) : '';

        $logData = $selectedSource !== '' ? CAdminErrorLogs::readLog($selectedSource, $lines, $query) : array(
            'source' => null,
            'lines' => array(),
            'error' => 'No log source is configured.',
        );

        $this->set('admin_error_logs_overview', $overview);
        $this->set('admin_error_logs_sources', $sources);
        $this->set('admin_error_logs_selected_source', $selectedSource);
        $this->set('admin_error_logs_selected_lines', $lines);
        $this->set('admin_error_logs_query', $query);
        $this->set('admin_error_logs_data', $logData);
    }
}
