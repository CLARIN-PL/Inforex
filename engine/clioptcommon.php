<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class CliOptCommon {

    static function parseDbParameters($opt, $dbHost, $dbUser, $dbPass, $dbName, $dbPort){
        if ( $opt->exists("db-uri")){
            $uri = $opt->getRequired("db-uri");
            if ( preg_match("/(.+):(.+)@(.*):(.*)\/(.*)/", $uri, $m)){
                $dbUser = $m[1];
                $dbPass = $m[2];
                $dbHost = $m[3];
                $dbPort = $m[4];
                $dbName = $m[5];
            }else{
                throw new Exception("DB URI is incorrect. Given '$uri', but exptected 'user:pass@host:port/name'");
            }
        }
        $dsn = array();
        $dsn['phptype'] = 'mysql';
        $dsn['username'] = $dbUser;
        $dsn['password'] = $dbPass;
        $dsn['hostspec'] = $dbHost . ":" . $dbPort;
        $dsn['database'] = $dbName;
        return $dsn;
    }

    static function validateFolderExists($folder){
        if ( !file_exists($folder) ){
            throw new Exception("Folder does not exists: $folder");
        }
        return true;
    }

    static function validateUserId($userId){
        $userIdInt = intval($userId);
        if ( $userIdInt === 0 ){
            throw new Exception("Invalid value of user id: $userId");
        }
        if ( DbUser::get($userIdInt) === null ){
            throw new Exception("User with id=$userIdInt does not exist");
        }
        return true;
    }

    static function validateSubcorpusId($subcorpusId){
        $subcorpusIdInt = intval($subcorpusId);
        if ( $subcorpusIdInt === 0 ){
            throw new Exception("Invalid value of subcorpus id: $subcorpusId");
        }
        if ( DbSuborpus::get($subcorpusId) === null ){
            throw new Exception("Subcorpus with id=$subcorpusIdInt does not exist");
        }
        return true;
    }


}