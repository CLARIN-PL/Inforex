<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class DbTask{

    static function getCorpusIdForTaskId($taskId){
        global $db;
        return $db->fetch_one("SELECT corpus_id FROM tasks WHERE task_id = ?", $taskId);
    }

}