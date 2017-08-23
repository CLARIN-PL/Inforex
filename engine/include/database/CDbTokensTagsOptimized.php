<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class DbTokensTagsOptimized{

	static function getTokensTags($token_ids){
        global $db;
        $sql = "SELECT * FROM tokens_tags_optimized " .
            "WHERE token_id IN('" . implode("','",$token_ids) . "')";

        $sql = "SELECT tto.token_tag_id, tto.token_id, tto.disamb, tto.ctag_id, ttc.id as ctag_id, ttc.ctag, b.id as base_id, b.text as base_text "
            ."FROM `tokens_tags_optimized` as tto "
            ."JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id "
            ."JOIN bases as b on tto.base_id = b.id "
            ."WHERE tto.user_id IS NULL "
            ."AND token_id IN (". implode(",", $token_ids) . ")";

        return $db->fetch_rows($sql);
    }
}

?>