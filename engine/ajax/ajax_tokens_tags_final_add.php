<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_tokens_tags_final_add extends CPageAdministration {

    private $defaultTagsetId = 1;

    function __construct(){
        // todo - check if has right to edit morpho
        parent::__construct();
        $this->anySystemRole[] = USER_ROLE_LOGGEDIN;
    }

	public function execute(){
		global $corpus, $user;
        $this->defaultTagsetId = DbTagset::getTagsetId('nkjp');

		$tags = $_POST['tags'];
        $token_id =  $_POST['token_id'];

		$user_id = $user['user_id'];

        DbTokensTagsOptimized::removeFinalDecisions($token_id);

        if($tags){
            foreach ($tags as $tag){
                // php5.3 fix
                $exploded_tags = explode(':', $tag['ctag']);
                $pos = $exploded_tags[0];
                $base_id = self::getBaseId($tag['base_text']);
                $ctag_id = self::getCtagId($tag['ctag']);
                $disamb = (int)$tag['disamb'];

                DbTokensTagsOptimized::addFinalDecision($user_id, $token_id, $base_id, $ctag_id, $pos, $disamb);
            }
        }

        return array('ret'=>$tags, 'user'=>$user);
	}

	private function getBaseId($base_text){
        return DbBase::saveIfNotExists($base_text);
    }

    private function getCtagId($ctag){
        return DbCtag::saveIfNotExists($ctag, $this->defaultTagsetId);
    }
}
