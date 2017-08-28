<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 */

class Ajax_tokens_tags_add extends CPage{

    function checkPermission(){
        if (hasRole(USER_ROLE_LOGGEDIN)){
            return true;
        }
        else{
            return "Brak prawa do edycji.";
        }
    }

	public function execute(){
		global $corpus, $user;

		$tags = $_POST['tag'];

		$user_id = $user['user_id'];
        $token_id = $tags[0]['token_id'];


        DbTokensTagsOptimized::removeUserDecisions($user_id, $token_id);
//        return array('ret'=>$tags, 'user'=>$user);
        foreach ($tags as $tag){
            $pos = explode(':', $tag['ctag'])[0];
            if($tag['custom']){
                $base_id = self::getBaseId($tag['base_text']);
                $ctag_id = self::getCtagId($tag['ctag']);
            }
            else {
                $base_id = $tag['base_id'];
                $ctag_id = $tag['ctag_id'];
            }
            DbTokensTagsOptimized::addUserDecision($user_id, $token_id, $base_id, $ctag_id, $pos);
        }
//        sleep(1);
//        return array('ret'=>$tags, 'user'=>$user);
	}

	private function getBaseId($base_text){
        return DbBase::saveIfNotExists($base_text);
    }

    private function getCtagId($ctag){
        return DbCtag::saveIfNotExists($ctag);
    }
}
