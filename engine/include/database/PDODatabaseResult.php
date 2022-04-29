<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

/*
 * Implementation of DatabaseResult Interface with PDO library
 */

// for access to MDB2/Datatype.php
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(__DIR__.'/../../external/pear'));
require_once(__DIR__."/../../external/pear/MDB2.php");

class PDODatabaseResult implements IDatabaseResult {

	private $PDOStatement = null;

	public function __construct($PDOStatement) {
		$this->PDOStatement = $PDOStatement;
	} // __construct()

    /**
     * Fetch and return all rows from the current row pointer position
	 * as array of assoc arrays
     *
     * @return  data array on success, exception thrown on error
     */
    function fetchAll(){
	
		$result = $this->PDOStatement->fetchAll(PDO::FETCH_ASSOC);
		return $result; 

	} // fetchAll()


} // of PDODatabaseResult class

?>
