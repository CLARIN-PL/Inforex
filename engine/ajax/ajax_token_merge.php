<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_token_merge extends CPageCorpus {

    function __construct($name = null, $description = null){
        parent::__construct($name, $description);

        $this->anyPerspectiveAccess[] = "tokenization";
    }

    function execute(){

        $token1Id = $this->getRequestParameterRequired("token_id");
        $token2Id = $this->getRequestParameterRequired("token_2_id");

        $token1 = DbToken::get($token1Id);
        $token2 = DbToken::get($token2Id);

        $updated = DBToken::updateToken($token1["token_id"], $token1["from"], $token2["to"]);
        DbToken::deleteToken($token2["token_id"]);

        return array("token1" => $updated);
	}
}