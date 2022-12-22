<?php

class DatabaseEmulator extends Database {

    // table with registered response indexed by method and query
    private $servingResponses = array (
        // method => array (query => response) 
        // ex:
        // 'fetch_rows' => ( 'SELECT id FROM table' => array('id'=>1))        
    );
    private $defaultResponse = null; // for search not founded in table
    private $logFile = null; // null redirects log to stderr, otherwise to file 

    private function normalizeKey($key) {

        // normalize key used for indexing, for clarity
        // change \t chars to spaces for clarity
        $key = str_replace("\t", " ", $key);
        // remove all \n and \r
        $key = str_replace("\n", " ", $key);
        $key = str_replace("\r", " ", $key);
        return $key;
    }

    public function setResponse($method,$query,$response) {

        $this->servingResponses[$method][$this->normalizeKey($query)] 
            = $response;

    } // setResponse

    private function getResponse($method,$query) {
    
        $query = $this->normalizeKey($query);
        if(isset($this->servingResponses[$method][$query])) {
            return $this->servingResponses[$method][$query];
        } else {
            $stdMissMsg = "you shoud set response to method '$method' in DatabaseEmulator for query '$query'";
            $this->writeLogMsg($stdMissMsg."\n"); // log missed
            return is_null($this->defaultResponse) ? $stdMissMsg : $this->$defaultResponse;  
        }

    } // getResponse

    public function setDefaultResponse($defaultResponse) {

        $this->defaultresponse = $defaultResponse;

    } // setDefaultResponse()

    public function clearAllResponses() {

        $this->servingRequests = array();

    } // clearAllResponses()

    public function setLog($filename = null) {

        // null means std error_log
        $this->logFile = ($filename) ? $filename : null;

    } // setLog()

    private function writeLogMsg($msg) {

        if($this->logFile) {
            file_put_contents($this->logFile,$msg,FILE_APPEND);
        } else { 
            fwrite(STDERR,$msg);
        }

    } // writeLogMsg()

    // Database class method emulation
    
        function __construct($dsn=null, $log=false, $log_output="chrome_php", $encoding="utf8mb4"){ }

        function disconnect(){ }    /// return nothing

        public function set_encoding($encoding) { }  // return nothing

        function execute($sql, $args=null){
            return $this->getResponse('execute',$sql);
        }

        function fetch_rows($sql, $args = null){
            return $this->getResponse('fetch_rows',$sql);
        } 

        public function quote($value, $type = null, $quote = true, $escape_wildcards = false) {
            return $this->getResponse('quote',$value);
        }

        public function fetchAll($sql) {
            return $this->getResponse(__FUNCTION__,$sql);
        }

        public function errorInfo() {
            return "No errorInfo for emulation...";
        }

        public function escape($text) {
            return $this->getResponse(__FUNCTION__,$text);
        }

        function queryOne($query) {
            return $this->getResponse(__FUNCTION__,$query);
        }

        function fetch_one($sql, $args=null){
            return $this->getResponse(__FUNCTION__,$sql);
        }

        function fetch_id($table_name){
            return $this->getResponse(__FUNCTION__,$table_name);
        }

} // DatabaseEmulator class

?>
