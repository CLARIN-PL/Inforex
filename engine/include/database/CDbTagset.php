<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class DbTagset{

    static public $table = '`tagsets`';

	static function getTagsetId($tagset_name){
        global $db;

        $sql = "SELECT tagset_id "
            ."FROM ". self::$table." "
            ."WHERE name LIKE '".$tagset_name ."';";

        return $db->fetch_one($sql);
    }

    static function getTokensTagsUserDecision($token_ids, $user_id){
        global $db;

        $sql = "SELECT tto.token_tag_id, tto.token_id, tto.disamb, tto.ctag_id, ttc.id as ctag_id, ttc.ctag, b.id as base_id, b.text as base_text, tto.user_id "
            ."FROM ". self::$table ." as tto "
            ."JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id "
            ."JOIN bases as b on tto.base_id = b.id "
            ."WHERE (tto.user_id IS NULL OR tto.user_id = ". $user_id.") "
            ."AND token_id IN (". DbTokensTagsOptimized::getStringOrNullTokenIdsList($token_ids) . ");";

//        return $sql;

        return $db->fetch_rows($sql);
    }

    static function removeUserDecisions($user_id, $token_id){
        global $db;

        $sql = "DELETE FROM ". self::$table ." "
            ."WHERE `token_id` = " . $token_id. " "
            ." AND `user_id` = " . $user_id. ";";

        $db->execute($sql);
    }

    static function addUserDecision($user_id, $token_id, $base_id, $ctag_id, $pos, $disamb){
        global $db;

        $sql = 'INSERT INTO '.self::$table.' (`token_id`, `base_id`, `disamb`, `ctag_id`, `pos`, `user_id`) '
          .'VALUES (' . $token_id .', '. $base_id .', '. $disamb .', '. $ctag_id.', "'.$pos.'", '.$user_id.');';

        $db->execute($sql);
    }

    static function test(){
	    return "test";
    }
}

?>