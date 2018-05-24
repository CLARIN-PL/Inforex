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

}