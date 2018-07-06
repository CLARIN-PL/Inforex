<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

/**
 * Class DocumentImporterTxt
 * Load documents from txt and ini files located in given folder.
 */
class DocumentReaderTxt{

    /**
     * @param $files
     * @return array return a list of assoc arrays with values array("txt"=>path, "ini"=>path"). Ini path may be null.
     */
    static function pairTxtAndIniFiles($files){
        $fileIndex = array();
        foreach ($files as $file){
            $fileIndex[$file] = 1;
        }
        $pairs = array();
        foreach ($files as $file) {
            if (strtolower(substr($file, strlen($file) - 4, 4)) == ".txt") {
                $iniFile = substr($file, 0, strlen($file) - 4) . ".ini";
                $pairs[] = array("txt"=>$file, "ini"=>isset($fileIndex[$iniFile])?$iniFile:null);
            }
        }
        return $pairs;
    }

    static function getFolderFiles($dir, &$results = array()){
        $files = scandir($dir);
        foreach($files as $key => $value){
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
            if(!is_dir($path)) {
                $results[] = $path;
            } else if($value != "." && $value != "..") {
                DocumentReaderTxt::getFolderFiles($path, $results);
                $results[] = $path;
            }
        }
        return $results;
    }

    static function loadMetadataFromIniFile($pathIni){
        $metadata = array();
        $metadata["date"] = null;
        $metadata["title"] = null;
        $metadata["source"] = null;
        $metadata["author"] = null;
        $metadata["format_id"] = null;
        $metadata["lang"] = null;
        $metadata["filename"] = DocumentReaderTxt::getBasenameWithoutExtension($pathIni);
        $custom = array();
        if ( $pathIni != null ) {
            $ini = parse_ini_file($pathIni, true, INI_SCANNER_RAW);
            if (isset($ini["metadata"])) {
                foreach ($metadata as $key => $val) {
                    if ($key == "date" && strtotime($ini["metadata"][$key])) {
                        $metadata["date"] = date("Y-m-d", strtotime($ini["metadata"][$key]));
                    }
                    if (isset($ini["metadata"][$key])) {
                        $metadata[$key] = isset($ini["metadata"][$key]) ? $ini["metadata"][$key] : null;
                    }
                }
            }
            if (isset($ini["custom"])) {
                foreach ($ini["custom"] as $key => $val) {
                    $custom[$key] = $val;
                }
            }
        }
        return array("metadata"=>$metadata, "custom"=>$custom);
    }

    static function getBasenameWithoutExtension($path){
        $file_extension = pathinfo($path, PATHINFO_EXTENSION);
        return basename($path, ".".$file_extension);
    }

}