<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_anonymous_user_activity extends CPageAdministration {

    function execute(){

        $mode = $_POST['mode'];

        switch($mode){
            case("initial_summary"):
                return array(
                    "years" => DbUser::getAnonymousActivitiesByYear(true)
                );

            case("activity_list"):
                $year = $_POST['year'];
                $month = $_POST['month'];
                return $this->getActivitiesForTime($year, $month);

            case("year_summary"):
                $order = isset($_POST['order']) ? $_POST['order'] : "ASC";
                if($order == "DESC"){
                    return DbUser::getAnonymousActivitiesByYear(true);

                } else{
                    return DbUser::getAnonymousActivitiesByYear();
                }

            case("year_month_summary"):
                $year = isset($_POST['year']) ? $_POST['year'] : null;
                return DbUser::getAnonymousActivitiesByYearMonth($year);

            case("year_month_summary_chart"):
                $year = $_POST['year'];
                return DbUser::getAnonymousActivitiesByYearMonthChart($year);
        }
    }

    function getActivitiesForTime($year, $month){
        global $db;

        $startDate = sprintf("%04d-%02d-01 00:00:00", intval($year), intval($month));
        $endDate = date("Y-m-d H:i:s", strtotime($startDate . " +1 month"));

        $sql = " SELECT a.datetime AS date, CONCAT(at.category, '/', at.name) AS name,  i.ip AS ip FROM activities a
                 LEFT JOIN activity_types at ON at.activity_type_id = a.activity_type_id
                 LEFT JOIN ips i ON i.ip_id = a.ip_id
                 WHERE a.user_id IS NULL
                   AND a.datetime >= ?
                   AND a.datetime < ?
                 ORDER BY a.datetime DESC";

        return $db->fetch_rows($sql, array($startDate, $endDate));
    }
}
