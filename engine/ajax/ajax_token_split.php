<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_token_split extends CPageCorpus {

    function __construct($name = null, $description = null){
        parent::__construct($name, $description);

        $this->anyPerspectiveAccess[] = "tokenization";
    }

    function execute(){
        $tokenId = $this->getRequestParameterRequired("token_id");
        $token_len = $this->getRequestParameterRequired("token_length");

        $token = DbToken::get($tokenId);

        DBToken::updateToken($token["token_id"], $token["from"], $token["from"] + $token_len - 1);
        DBToken::saveToken($token["report_id"], $token["from"] + $token_len, $token["to"], $token["eos"]);

        return array("tokens" => []);
	}
}