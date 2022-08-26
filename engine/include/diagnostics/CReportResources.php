<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class ReportResources {

    /* class for developers only to diagnostic memory usage and time
       consuming needed for some code execution 
        Usage:
            $r = new ReportResources()->sendReportResourceToLog('on start');
            <tested code>
            $r->sendReportResourceDiffToLog('on end');                          
    */

    private $id = "";
    private $time = null;
    private $memory = null;
    private $memory_max = null;

    public function __construct($uniqueID = null) {

        if($uniqueID === null) {
            // generate unique ID for identify process in concurent
            // logging environment
            $uniqueID = "";
            for($i=0;$i<4;$i++) {
                $uniqueID .= strval($this->randomDigit());
            }
        } 
        $this->id = $uniqueID;

        $this->startReportResourcesCounting();

    } // __constructor()

    public function startReportResourcesCounting() {

        // initial values. From this values diff will be counted
        // for first step. 
        $this->time = $this->readTime();
        $this->memory = $this->readMemory();
        $this->memory_max = $this->readMemoryMax();

    } // startReportResourceCounting

    public function sendReportResourceDiffToLog($msg, $timeConsumingInSeconds=null, $memoryUsageInBytes=null, $memoryMaxUsageInBytes=null) {

        // send to system log line with $msg string and actual
        // measurement diff from last send... function call. If extended 
        // parameter exists, they overwrite selfmeasuring 

        if($memoryUsageInBytes==null) {
            $memoryUsageInBytes = $this->readMemory();
        }
        $memoryDiff = $memoryUsageInBytes - $this->memory;
        $this->memory = $memoryUsageInBytes;
 
        if($memoryMaxUsageInBytes==null) {
            $memoryMaxUsageInBytes = $this->readMemoryMax();
        }
        $memoryMaxDiff = $memoryMaxUsageInBytes - $this->memory_max;
        $this->memory_max = $memoryMaxUsageInBytes;

        if($timeConsumingInSeconds==null) {
            $timeConsumingInSeconds = $this->readTime();
        }
        $timeDiff = $timeConsumingInSeconds - $this->time;
        $this->time = $timeConsumingInSeconds;

        $line = $this->id.": "
                .strval($memoryDiff)
                ."[".strval($memoryMaxDiff)."]"
                ." bytes "
                .strval($timeDiff)." secs "
                ."for ".$msg;
        error_log($line); 
        
    } // sendReportResourceDiffToLog()

    public function sendReportResourceToLog($msg, $timeFromMidnightInSeconds=null, $memoryUsageInBytes=null, $memoryMaxUsageInBytes=null) {

        // send to system log line with $msg string and actual
        // measurement absolute values. If extended parameter
        // exists, they overwrite selfmeasuring

        if($memoryUsageInBytes==null) {
            $memoryUsageInBytes = $this->readMemory();
        }
        $this->memory = $memoryUsageInBytes;

        if($memoryMaxUsageInBytes==null) {
            $memoryMaxUsageInBytes = $this->readMemoryMax();
        }
        $this->memory_max = $memoryMaxUsageInBytes;

        if($timeFromMidnightInSeconds==null) {
            $timeFromMidnightInSeconds = $this->readTime();
        }
        $this->time = $timeFromMidnightInSeconds;
 
        $line = $this->id.": "
                .strval($memoryUsageInBytes)
                ."[".strval($memoryMaxUsageInBytes)."]"
                ." bytes "
                .strval($timeFromMidnightInSeconds)." secs "
                ."at ".$msg;
        error_log($line);

    } // sendReportResourceToLog()

    private function randomDigit() {

        return rand(0,9);    

    } // randomDigit()

    private function readMemory() {

        // Returns the memory amount in bytes. 
        return memory_get_usage();

    } // readMemory()

    private function readMemoryMax() {

        // Returns the maximal memory usage in bytes.
        return memory_get_peak_usage();

    } // readMemoryMax()

    private function readTime() {

        // with get_as_float = true returns in seconds
        return microtime(True);

    } // readTime()

} // ReportResources class

?>
