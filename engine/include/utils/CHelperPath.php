<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */

mb_internal_encoding("utf-8");

class HelperPath{

	static function loadFilePathsWithExtensionFromFolder($rootPath, $extension){
		$paths = array();
		if ($handle = opendir($rootPath)){
			while ( false !== ($file = readdir($handle))){
				if ( self::getPathExtension($file) === $extension ){
					$paths[] = $rootPath . DIRECTORY_SEPARATOR . $file;
				}
			}
		}
		return $paths;
	}

	static function getPathExtension($path){
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    static function getPathBasename($path){
        return pathinfo($path, PATHINFO_BASENAME);
    }

    static function getPathFilename($path){
        return pathinfo($path, PATHINFO_FILENAME);
    }
}
