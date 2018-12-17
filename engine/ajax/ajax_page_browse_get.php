<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_page_browse_get extends CPageCorpus {

    function __construct(){
        parent::__construct();
        $this->anyCorpusRole[] = CORPUS_ROLE_READ;
    }

	function execute(){
        list($page, $limitStart, $limitCount) = $this->getPaginationData();

        $reports = new ReportListFilters($this->getDb(), $this->getCorpusId(), $this->getUserId());
        $columns = new ReportListColumns($this->getDb(), $this->getCorpusId());

        $totalCount = $this->getTotalCount($reports->getSql());
        $tableRows = $this->getTableRows($reports->getSql(), $columns, $limitCount, $limitStart);

        $this->postProcessTableRows($tableRows, $columns);
        $this->setTableColumnCheckbox($tableRows);

        // UWAGA: wyjątek - akcja wyjęta spod ujednoliconego wywołania core_ajax
        echo $this->generateResponse($page, $totalCount, $tableRows);
        die();
	}

    /**
     * @param SqlBuilder $sql
     * @return mixed
     */
	function getTotalCount($sql){
        $sql->setSelectColumn(array(new SqlBuilderSelect("COUNT(DISTINCT r.id)", "c")));
        list($sql, $param) = $sql->getSql();
        return $this->getDb()->fetch_one($sql, $param);
    }

    function getTableRows($sql, $columns, $limitCount, $limitStart){
        $sql->setLimitOffset($limitCount, $limitStart);
        foreach ($columns->getColumns() as $c){
            if ( $c->isVisible() || $c->isPinned()){
                $c->applyTo($sql);
            }
        }
        list($sql, $param) = $sql->getSql();
        return $this->getDb()->fetch_rows($sql, $param);
    }

	function postProcessTableRows(&$tableRows, $columns){
        foreach ($tableRows as &$row){
            $columns->postProcessTableRow($row);
        }
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

    function getPaginationData(){
        //$sortName		= $_POST['sortname'];
        //$sortOrder		= $_POST['sortorder'];
        $pageElements	= $_POST['rp'];
        $page			= max(intval($_POST['page']), 1);
        $limitStart = ($page - 1) * $pageElements;
        $limitCount = $pageElements;
        return array($page, $limitStart, $limitCount);
    }

    function getCorpusId(){
        return $_POST['corpus'];
    }

}
