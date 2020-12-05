<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

/*
 * Implementation of DatabaseResult Interface with MDB2 library
 */

// for access to MDB2/Datatype.php
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(__DIR__.'/../../external/pear'));
require_once(__DIR__."/../../external/pear/MDB2.php");

class MDB2DatabaseResult implements IDatabaseResult {

	private $MDB2_Result_Object	= null;

	public function __construct($MDB2_Result) {
		$this->MDB2_Result_object = $MDB2_Result;
	} // __construct()

    /**
     * Fetch and return all rows from the current row pointer position
	 * as array of assoc arrays
     *
     * @return  data array on success, exception thrown on error
     */
    function fetchAll(){
	
     /* @param   int     $fetchmode  the fetch mode to use:
     *                            + MDB2_FETCHMODE_ORDERED
     *                            + MDB2_FETCHMODE_ASSOC
     *                            + MDB2_FETCHMODE_ORDERED | MDB2_FETCHMODE_FLIPPED
     *                            + MDB2_FETCHMODE_ASSOC | MDB2_FETCHMODE_FLIPPED
 	 */
		$result = $this->MDB2_Result_object->fetchAll(MDB2_FETCHMODE_ASSOC);
        // $result is array() or MDB2_Error object
        // all MDB2::Error errors converts to exception
        if (MDB2::isError($result)){
            throw new DatabaseException($result->getMessage() . "\n" . $result->getUserInfo(), $result);
        }
		return $result; 

	} // fetchAll()


} // of MDB2DatabaseResult class

?>
