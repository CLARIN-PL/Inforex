<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

class FileWriter {
	
    public function writeTextToFile($fileName,$text) {

        $handle = fopen($fileName, "w");
        fwrite($handle, $text);
        fclose($handle);

    } // writeTextToFile()

    public function writeJSONToFile($fileName,array $jsonArray) {

        $textContent = json_encode($jsonArray,
                                    JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE);
        $this->writeTextToFile($fileName,$textContent);

    } // writeJSONToFile()

}  // FileWriter class
