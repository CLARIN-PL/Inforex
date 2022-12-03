<?php

class DatabaseEmulator extends Database {

    // table with registered response indexed by method and query
    private $servingResponses = array (
        // method => array (query => response) 
        // ex:
        // 'fetch_rows' => ( 'SELECT id FROM table' => array('id'=>1))        
    );
    private $defaultResponse = null; // for search not founded in table

    public function setResponse($method,$query,$response) {

        $this->servingResponses[$method][$query] = $response;

    } // setResponse

    private function getResponse($method,$query) {
    
        //var_dump($query);
        if(isset($this->servingResponses[$method][$query])) {
            return $this->servingResponses[$method][$query];
        } else {
            return is_null($this->defaultResponse) ?
                    "you shoud set response to method '$method' in DatabaseEmulator for query '$query'" : $this->$defaultResponse;  
        }

    } // getResponse

    public function setDefaultResponse($defaultResponse) {

        $this->defaultresponse = $defaultResponse;

    } // setDefaultResponse()

    public function clearAllResponses() {

        $this->servingRequests = array();

    } // clearAllResponses()

    // Database class method emulation
    
        function __construct($dsn=null, $log=false, $log_output="chrome_php", $encoding="utf8mb4"){ }

        function execute($sql, $args=null){
            return $this->getResponse('execute',$sql);
        }

        function fetch_rows($sql, $args = null){
            return $this->getResponse('fetch_rows',$sql);
        } 
 
} // DatabaseEmulator class

?>
