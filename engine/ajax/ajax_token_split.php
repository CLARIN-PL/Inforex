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
        $tokenText = $this->getRequestParameterRequired("token_text");
        $tokenOrth = $this->getRequestParameterRequired("token_orth");
        $newTokenText = $this->getRequestParameterRequired("new_token_text");
        $newTokenOrth = $this->getRequestParameterRequired("new_token_orth");

        $token = DbToken::get($tokenId);


        $htmlStr = ReportContent::getHtmlStr($row);

        var_dump($token);
/*
        var_dump($tokenText);
        var_dump($tokenOrth);
        var_dump($newTokenText);
        var_dump($newTokenOrth);*/

        return array("tokens" => []);
	}
}