<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class BacktraceService {

    static public function getBacktrace($removeLevels=1) {
        // return backtrace as array of calls
        // remove $removeLevels calls from top of stack

        $trace = array_reverse(debug_backtrace());
        while($removeLevels && count($trace)>0) {
            array_pop($trace);
            $removeLevels--;
        }
        return $trace;        

    } // getBacktrace

    static private function formatCLITraceStr($traceArray) {

        $result = "";
        $index = count($traceArray)-1;
        while($index>=0) {
        //foreach($traceArray as $item) {
            $item = $traceArray[$index--];
            $result .= '  ' 
                       .(isset($item['file']) ? $item['file'] : '<unknown file>')
                       .' ' 
                       .(isset($item['line']) ? $item['line'] : '<unknown line>')
                       .' calling ' . $item['function'] . '()' 
                       . "\n";
        }
        return $result; 

    } // formatCLITraceStr

    static private function formatHTMLTraceStr($traceArray) {

        $result =    '  <ol>' . "\n";
        $index = count($traceArray)-1;
        while($index>=0) {
        //foreach($traceArray as $item) {
            $item = $traceArray[$index--];
            $result .= '    <li>' 
                        . (isset($item['file']) ? $item['file'] : '<unknown file>') 
                        . ' ' 
                        . (isset($item['line']) ? $item['line'] : '<unknown line>') 
                        . ' calling ' . $item['function'] . '()' 
                        . "</li>\n";
        }
        $result .=   '  </ol>' . "\n";
 
        return $result;

    } // formatHTMLTraceStr

    static public function formatTraceStr($traceArray) {

        if(php_sapi_name() == 'cli') {
            return self::formatCLITraceStr($traceArray);
        } else {
            return self::formatHTMLTraceStr($traceArray);
        }

    } // formatTraceStr()

    static public function formatTraceLogStr($traceArray) {

        $items = array();
        $index = count($traceArray)-1;
        while($index>=0) {
        //foreach($traceArray as $item) {
            $item = $traceArray[$index--];
            $items[] = (isset($item['file']) ? $item['file'] : '<unknown file>')                       . ' ' 
                       . (isset($item['line']) ? $item['line'] : '<unknown line>') 
                       . ' calling ' . $item['function'] . '()';
        }
        return join(' | ', $items);

    } // formatTraceLogStr()

} // BacktraceService class

?>

