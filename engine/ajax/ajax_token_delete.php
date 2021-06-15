<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class Ajax_token_delete extends CPageCorpus {

    function __construct($name = null, $description = null){
        parent::__construct($name, $description);

        $this->anyPerspectiveAccess[] = "tokenization";
    }

    function execute(){
        $tokenId = $this->getRequestParameterRequired("token_id");

        $token = DbToken::get($tokenId);
        $reportId = $token["report_id"];

        DbToken::deleteTokenWithIndexUpdate($tokenId);

        return array("report_id" => $reportId);
	}
}