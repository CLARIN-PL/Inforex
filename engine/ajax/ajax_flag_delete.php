<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_flag_delete extends CPageCorpus {
    function execute(){
        global $db;

        $element_id = intval($_POST['element_id']);
        $sql = "DELETE FROM corpora_flags WHERE corpora_flag_id = ?";

        ob_start();
        $db->execute($sql, array($element_id));

        $error_buffer_content = ob_get_contents();
        ob_clean();
        if(strlen($error_buffer_content))
            throw new Exception("Error: ". $error_buffer_content);
        else
            return;
    }
}