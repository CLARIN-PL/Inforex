<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class DbStatus{

	static function getAll(){
		global $db;
		$sql = "SELECT * FROM reports_statuses ORDER BY id ASC";
		return $db->fetch_rows($sql);
	}

    /**
     * Return map of status id => name.
     * @return array
     */
    static function getMap(){
        $items = DbStatus::getAll();
        return arrayToMap($items, "id", "status");
    }

}