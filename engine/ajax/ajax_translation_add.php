<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_translation_add extends CPageAdministration {

    function execute(){
        global $db;

        $new_content = $_POST['content'];
        $report_id = $_POST['report_id'];

        $sql = "UPDATE reports SET content = ? WHERE id = ?";
        $db->execute($sql, array($new_content, $report_id));
    }
}
