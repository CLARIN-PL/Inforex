<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class DbFlag{

    function getAll(){
        global $db;
        $sql = "SELECT flag_id, name FROM flags;";
        return $db->fetch_rows($sql);
    }

}

