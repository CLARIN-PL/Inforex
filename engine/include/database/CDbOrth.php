<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */


class DbOrth{

    static function save($orth){
        global $db;
        $db->execute("INSERT INTO orths (orth) VALUES(?)", array($orth));
        return $db->last_id();
    }

    static function getOrthsMap(){
        global $db;
        $bases = $db->fetch_rows("SELECT * FROM orths");
        return arrayToMap($bases, "orth", "orth_id");
    }
}
