<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
  
class GroupedLogger{

    var $warnings = array();

    function warn($type, $details){
        if ( !isset($this->warnings[$type]) ){
            $this->warnings[$type] = array();
        }
        $this->warnings[$type][] = $details;
        echo "WARN: $type: $details\n";
    }

    function printLogs(){
        if ( count($this->warnings) > 0 ) {
            echo "Warnings:\n";
            foreach ($this->warnings as $k => $v) {
                echo sprintf(" %4d x %s\n", count($v), $k);
            }
        }
    }

}