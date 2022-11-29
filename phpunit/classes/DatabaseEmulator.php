<?php

class DatabaseEmulator extends Database {

    private $servingRequests = array (
// method => array (query => response)         
    );
    private $defaultResponse = null; // for search not founded in table

    public function setRequest($method,$query,$response) {

        $this->servingRequests[$method][$query] = $response;

    } // setRequest

    private function getRequest($method,$query) {
    
        //var_dump($query);
        if(isset($this->servingRequests[$method][$query])) {
            return $this->servingRequests[$method][$query];
        } else {
            return is_null($this->defaultResponse) ?
                    "you shoud set response to method '$method' in DatabaseEmulator for query '$query'" : $this->$defaultResponse;  
        }

    } // getRequest

    public function setDefaultResponse($defaultResponse) {

        $this->defaultresponse = $defaultResponse;

    } // setDefaultResponse()

    // Database class method emulation
    
        function __construct($dsn=null, $log=false, $log_output="chrome_php", $encoding="utf8mb4"){ }

        function fetch_rows($sql, $args = null){
            return $this->getRequest('fetch_rows',$sql);
        } 
 
} // DatabaseEmulator class

?>
