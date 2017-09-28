<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_tokens_tags_final_add extends CPage{

    private $defaultTagsetId = 1;

    function checkPermission(){
        // todo - check if has right to edit morpho
        if (hasRole(USER_ROLE_LOGGEDIN)){
            return true;
        }
        else{
            return "Brak prawa do edycji.";
        }
    }

	public function execute(){
		global $corpus, $user;

		$tags = $_POST['tags'];
        $token_id =  $_POST['token_id'];

		$user_id = $user['user_id'];

        DbTokensTagsOptimized::removeFinalDecisions($token_id);

        if($tags){
            foreach ($tags as $tag){
                $pos = explode(':', $tag['ctag'])[0];
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
