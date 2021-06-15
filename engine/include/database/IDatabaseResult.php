<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

/**
 * Abstract Database Result interface.
 */


interface IDatabaseResult {

    /**
     * Fetch and return all rows from the current row pointer position
     * as array of assoc arrays
     * @return array of assoc arrays on success or Exception on error
     */
    function fetchAll();
  

} // IDatabseResult

?>
