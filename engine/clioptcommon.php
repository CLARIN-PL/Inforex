<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class CliOptCommon {

    static function parseDbParameters($opt, $defaultDsn=array()){
        $dbUser = array_key_exists('username',$defaultDsn) ? $defaultDsn['username'] : "";
        $dbPass = array_key_exists('password',$defaultDsn) ? $defaultDsn['password'] : "";
        $dbName = array_key_exists('database',$defaultDsn) ? $defaultDsn['database'] : "";
	$hostspecArray = array_key_exists('hostspec',$defaultDsn) ? explode(":", $defaultDsn['hostspec']) : array() ;
	$dbHost = isset($hostspecArray[0]) ? $hostspecArray[0] : "";
	$dbPort = isset($hostspecArray[1]) ? $hostspecArray[1] : "";

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
        $dsn['phptype'] = 'mysqli';
        $dsn['username'] = $dbUser;
        $dsn['password'] = $dbPass;
        $dsn['hostspec'] = $dbHost;
	if($dbPort!="") { 
		$dsn['hostspec'] .= ":" . $dbPort;
	}
        $dsn['database'] = $dbName;
        return $dsn;
    }

    static function parseFlag($flag){
        print_r($flag);
        $flags = array();
        foreach($flag as $f){
            if ( preg_match("/(.+)=(.+)/", $f, $n)){
                $flag_name = $n[1];
                if (!array_key_exists($flag_name, $flags)){
                    $flags[$flag_name]=array();
                }
                if ( preg_match_all("/(?P<digit>\d+)/", $n[2], $v)){
                    foreach($v['digit'] as $key => $digit)
                        $flags[$flag_name][]=$digit;
                }
            }else{
                throw new Exception("Flag is incorrect. Given '$flag', but exptected 'name=value'");
            }
        }
        return $flags;
    }

    static function validateFolderExists($folder){
        if ( !file_exists($folder) ){
            throw new Exception("Folder does not exists: $folder");
        }
        return true;
    }

    static function validateCorpusId($corpusId){
        $corpusIdInt = intval($corpusId);
        if ( $corpusIdInt === 0 ){
            throw new Exception("Invalid value of corpus id: $corpusIdInt");
        }
        if ( DbCorpus::getCorpusById($corpusIdInt) === null ){
            throw new Exception("Corpus with id=$corpusIdInt does not exist");
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

    static function validateSubcorpusId($subcorpusId, $nullable=false){
        if ( $nullable && $subcorpusId == null){
            return null;
        }
        if ( is_array($subcorpusId) ){
            foreach ($subcorpusId as $id){
                self::validateSubcorpusId($id, $nullable);
            }
        } else {
            $subcorpusIdInt = intval($subcorpusId);
            if ($subcorpusIdInt === 0) {
                throw new Exception("Invalid value of subcorpus id: $subcorpusId");
            }
            if (DbSuborpus::get($subcorpusId) === null) {
                throw new Exception("Subcorpus with id=$subcorpusIdInt does not exist");
            }
        }
        return true;
    }

    static function validateDocumentId($documentId, $nullable=false){
        if ( $nullable && $documentId == null){
            return null;
        }
        if ( is_array($documentId) ){
            foreach ($documentId as $id){
                self::validateDocumentId($id, $nullable);
            }
        } else {
            $documentIdInt = intval($documentId);
            if ($documentIdInt === 0) {
                throw new Exception("Invalid value of document id: $documentIdInt");
            }
            if (DbReport::get($documentIdInt) === null) {
                throw new Exception("Document with id=$documentIdInt does not exist");
            }
            return true;
        }
    }

    /**
     * Check if valid and unambiguous names of flags were provided for the given corpus.
     * @param $flags
     * @param $corpusId
     */
    static function validateFlags($flags, $corpusId){
        $flagValues = DbFlag::getValuesSet();
        foreach ($flags as $f=>$values){
            foreach ($values as $v){
                if ( !isset($flagValues[$v]) ){
                    throw new Exception("Invalid flag value '$v' for '$f'");
                }
            }
        }
    }

}
