<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_page_browse_get2 extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_READ;
    }

	function execute(){
        $cid = $this->getCorpusId();

        list($page, $limitStart, $limitCount) = $this->getPaginationData();

        $reports = new FilteredReportList($this->getDb(), $cid);
        $reportsId = $reports->getIds();
        $ids = array_slice($reportsId, $limitStart, $limitCount);
        if (count($ids) > 0 ) {
            $tableRows = DbReport::getReports(null, null, $ids);
        } else {
            $tableRows = array();
        }

        $this->setTableColumnCheckbox($tableRows);
        $this->setTableColumnLink($tableRows);
        $this->setTableColumnSubcorpus($tableRows);
        $this->setTableColumnStatus($tableRows);

        // UWAGA: wyjątek - akcja wyjęta spod ujednoliconego wywołania core_ajax
        echo $this->generateResponse($page, count($reportsId), $tableRows);
        die();
	}

	function generateResponse($page, $total, $tableRows){
        $response = array();
        $response['page'] = $page;
        $response['total'] = $total;
        $response['rows'] = $tableRows;
        $response['post'] = $_POST;
        return json_encode($response);
    }

	function setTableColumnCheckbox(&$tableRows){
        $checked =  $this->getDb()->fetch_ones("SELECT * FROM reports_users_selection WHERE user_id = ?",
            DB_COLUMN_REPORTS_USERS_SELECTION__REPORT_ID, array($this->getUserId()));
        $checkedSet = arrayToAssoc($checked);
        foreach ($tableRows as &$row){
            $id = $row['id'];
            $checked = isset($checkedSet[$id]) ? 'checked' : '';
            $row['checkbox_action'] = "<input class='checkbox_action' id='checkbox{$id}' type='checkbox' $checked name='checkbox{$id}' value='$id'>";
        }
    }

    function setTableColumnLink(&$tableRows){
        $link = '<a href="index.php?page=report&corpus=%d&id=%d">%s</a>';
        foreach ($tableRows as &$row){
            $row['title'] = sprintf($link, $this->getCorpusId(), $row['id'], $row['title']);
        }
    }

    function setTableColumnSubcorpus(&$tableRows){
        $subcorpora = DbCorpus::getCorpusSubcorpora($this->getCorpusId());
        $subcorporaMap = arrayToMap($subcorpora, "subcorpus_id", "name");
        foreach ($tableRows as &$row){
            $row['subcorpus_id'] = array_get_str($subcorporaMap, $row['subcorpus_id'], null);
        }
    }

    function setTableColumnStatus(&$tableRows){
        $statusMap = DbStatus::getMap();
        foreach ($tableRows as &$row){
            $row['status_name'] = $statusMap[$row[status]];
        }
    }

    function getPaginationData(){
        //$sortName		= $_POST['sortname'];
        //$sortOrder		= $_POST['sortorder'];
        $pageElements	= $_POST['rp'];
        $page			= $_POST['page'];
        $limitStart = ($page - 1) * $pageElements;
        $limitCount = $pageElements;
        return array($page, $limitStart, $limitCount);
    }

    function getCorpusId(){
        return $_POST['corpus'];
    }

}
