<?php
/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
 
class DbTokensTagsOptimized{

    static public $table = '`tokens_tags_optimized`';

    static function getStringOrNullTokenIdsList($token_ids){
        if (count($token_ids) == 0){
            return "(NULL)";
        }
        return implode(",", $token_ids);
    }

    static function getTokenTagsOnlyFinalDecision($token_ids){
        global $db;

        $sql = "SELECT tto.token_tag_id, tto.token_id, tto.disamb, tto.ctag_id, ttc.id as ctag_id, ttc.ctag, b.id as base_id, b.text as base_text, tto.user_id "
            ."FROM ". self::$table ." as tto "
            ."JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id "
            ."JOIN bases as b on tto.base_id = b.id "
            ."WHERE tto.stage = 'final' "
            ."AND token_id IN (". self::getStringOrNullTokenIdsList($token_ids) . ");";

        return $db->fetch_rows($sql);
    }

    static function getTokenTagsFinalDecision($token_ids){
        global $db;

        $sql = "SELECT tto.token_tag_id, tto.token_id, tto.disamb, tto.ctag_id, ttc.id as ctag_id, ttc.ctag, b.id as base_id, b.text as base_text, tto.user_id "
            ."FROM ". self::$table ." as tto "
            ."JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id "
            ."JOIN bases as b on tto.base_id = b.id "
            ."WHERE (tto.user_id IS NULL OR tto.stage = 'final') "
            ."AND token_id IN (". self::getStringOrNullTokenIdsList($token_ids) . ");";

        return $db->fetch_rows($sql);
    }

	static function getTokensTags($token_ids){
        global $db;

        $sql = "SELECT tto.token_tag_id, tto.token_id, tto.disamb, tto.ctag_id, ttc.id as ctag_id, ttc.ctag, b.id as base_id, b.text as base_text "
            ."FROM ". self::$table ." as tto "
            ."JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id "
            ."JOIN bases as b on tto.base_id = b.id "
            ."WHERE tto.user_id IS NULL "
            ."AND token_id IN (". self::getStringOrNullTokenIdsList($token_ids) . ");";

        return $db->fetch_rows($sql);
    }

    static function getTokensTagsUserDecision($token_ids, $user_id){
        global $db;

        $sql = "SELECT tto.token_tag_id, tto.token_id, tto.disamb, tto.ctag_id, ttc.id as ctag_id, ttc.ctag, b.id as base_id, b.text as base_text, tto.user_id "
            ."FROM ". self::$table ." as tto "
            ."JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id "
            ."JOIN bases as b on tto.base_id = b.id "
            ."WHERE (tto.user_id IS NULL OR (tto.user_id = ". $user_id." AND tto.stage = 'agreement')) "
            ."AND token_id IN (". self::getStringOrNullTokenIdsList($token_ids) . ");";

        return $db->fetch_rows($sql);
    }

    static function getTokensTagsOnlyUserDecison($token_ids, $user_id){
        global $db;

        $sql = "SELECT tto.token_tag_id, tto.token_id, tto.disamb, tto.ctag_id, ttc.id as ctag_id, ttc.ctag, b.id as base_id, b.text as base_text, tto.user_id "
            ."FROM ". self::$table ." as tto "
            ."JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id "
            ."JOIN bases as b on tto.base_id = b.id "
            ."WHERE (tto.user_id = ". $user_id.") "
            ."AND (tto.stage = 'agreement') "
            ."AND token_id IN (". self::getStringOrNullTokenIdsList($token_ids) . ");";

        return $db->fetch_rows($sql);
    }

    static function removeUserDecisions($user_id, $token_id){
        global $db;

        $sql = "DELETE FROM ". self::$table
            ." WHERE `token_id` = " . $token_id
            ." AND `user_id` = " . $user_id
            ." AND `stage` = 'agreement';";

        $db->execute($sql);
    }

    static function addUserDecision($user_id, $token_id, $base_id, $ctag_id, $pos, $disamb){
        global $db;

        $sql = 'INSERT INTO '.self::$table.' (`token_id`, `base_id`, `disamb`, `ctag_id`, `pos`, `user_id`, `stage`) '
          .'VALUES (' . $token_id .', '. $base_id .', '. $disamb .', '. $ctag_id.', "'.$pos.'", '.$user_id.', "agreement");';

        $db->execute($sql);
    }

    static function removeFinalDecisions($token_id){
        global $db;

        $sql = "DELETE FROM ". self::$table
            ." WHERE `token_id` = " . $token_id
            ." AND `stage` = 'final'";

        $db->execute($sql);
    }

    static function addFinalDecision($user_id, $token_id, $base_id, $ctag_id, $pos, $disamb){
        global $db;

        $sql = 'INSERT INTO '.self::$table.' (`token_id`, `base_id`, `disamb`, `ctag_id`, `pos`, `user_id`, `stage`) '
            .'VALUES (' . $token_id .', '. $base_id .', '. $disamb .', '. $ctag_id.', "'.$pos.'", '.$user_id.', "final");';

        $db->execute($sql);
    }

    static function getUsersDecisionCount($token_ids){
        global $db;

        $sql = "SELECT tto.user_id, count(distinct tto.token_id) as 'annotation_count', screename
                FROM `tokens_tags_optimized` as tto
                JOIN `users` as u ON  tto.user_id = u.user_id
                where stage = 'agreement'
                and token_id IN (". self::getStringOrNullTokenIdsList($token_ids) . ")"
                ."group by user_id;";

        return $db->fetch_rows($sql);
    }

    static function getUsersOwnDecisions($token_ids, $user_a_id, $user_b_id){
        global $db;

        $sql = "SELECT tto.token_tag_id, tto.token_id, tto.disamb, tto.ctag_id, ttc.id as ctag_id, ttc.ctag, b.id as base_id, b.text as base_text, tto.user_id 
            FROM ". self::$table ." as tto 
            JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id 
            JOIN bases as b on tto.base_id = b.id 
            WHERE (tto.user_id = ". $user_a_id." OR tto.user_id = ".$user_b_id.") ".
            "AND (tto.stage = 'agreement')
            AND token_id IN (". self::getStringOrNullTokenIdsList($token_ids). ");";


        return $db->fetch_rows($sql);
    }

    static function getUsersOwnDecisionsByReports($report_ids, $user_a_id, $user_b_id, $comparisonMode = 'base_ctag'){
        global $db;

        $sql = "SELECT tto.token_tag_id, tto.token_id, tto.disamb, ttc.id as ctag_id, ttc.ctag, b.text, tto.base_id as base_id, tto.user_id, tok.report_id as report_id,  tok.to, tok.from
            FROM tokens_tags_optimized tto
            JOIN tokens tok on tto.token_id = tok.token_id
            JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id
            JOIN bases as b on b.id = tto.base_id
            WHERE (tto.user_id = ". $user_a_id." OR tto.user_id = ".$user_b_id.")  AND (tto.stage = 'agreement') -- AND tto.disamb = 1
            and tok.report_id in (". self::getStringOrNullTokenIdsList($report_ids). ");";

        $rows = $db->fetch_rows($sql);

        $reportContent = DbReport::getReports(null,null,$report_ids[0], null, array("content"));

        $content = $reportContent[0]['content'];
        if ( $content['format'] == 'plain'){
            $content = htmlspecialchars($content);
        }
        $html = new HtmlStr2($content);

        /*
         * grouping results by report and user
         */
        $arr = array();
        foreach($rows as $r){
            $arr[$r['report_id']] [$r['token_id']] [$r['user_id'] == $user_a_id ? 'a' : 'b']  [] = $r;
            if(!key_exists('orth', $arr[$r['report_id']] [$r['token_id']])){
                $arr[$r['report_id']] [$r['token_id']]['orth'] = $html->getTextAlign($r['from'], $r['to'], false, false);
                $arr[$r['report_id']] [$r['token_id']]['tok_range'] = $r['from'].'-'.$r['to'];
            }

        }

        if($comparisonMode == 'base_ctag'){
            $comparisonFcn = function($a,$b){
                return $a['ctag'] == $b['ctag'] && $a['base_text'] == $b['base_text'];
            };
        } else{
            $comparisonFcn = function($a,$b){
                return $a['base_text'] == $b['base_text'];
            };
        }

        foreach($arr as $report_id => $report){
            foreach($report as $token_id => $token){
                self::groupMathingDecisions($arr[$report_id][$token_id], $comparisonFcn);
            }
        }

        return $arr;
    }

    /**
     * grouping users decision into matching ones,
     * leaving only unique values for each use
     *
     *         | user_a | user_b
     * --------------------------
     *  disamb |    1   |   1      => removing from 'a' and 'b', adding to 'agree'
     *         |    0   |   0      => removing completely
     *         |    0   |   1      => nothing (vice versa other way)
     *         |    0   |   null   => transfer from 'a' to 'b'
     *
     * @param $token
     * @param $comparisonFcn
     */
    private static function groupMathingDecisions(&$token, $comparisonFcn){
        if (!(array_key_exists('a', $token) && array_key_exists('b', $token)))
            return;

        /*
         * finding matching decisions
         * dropping decisions where both users set disamb to 0
         */
        foreach($token['a'] as $a_key => $a_decision){
            foreach($token['b'] as $b_key => $b_decision){
                if($comparisonFcn($a_decision, $b_decision)){
                    if ($a_decision['disamb'] != 0){
                        $token['agree'][] = $a_decision;
                    }
                    unset($token['a'][$a_key]);
                    unset($token['b'][$b_key]);
                }
            }
        }

        /*
         * transferring decisions from one user to another
         * in the case when one user has decision disamb set to 0
         * (he doesn't agree with tagger decision),
         * other will have this decision transferred to him with disamb 1
         */
        foreach(array('a' => 'b', 'b' => 'a') as $user => $complementaryUser){
            foreach($token[$user] as $a_key => $a_decision) {
                if ($a_decision['disamb'] == 0) {
                    $a_decision['disamb'] = '1';

                    $token[$complementaryUser][] = $a_decision;
                    unset($token[$user][$a_key]);
                }
            }
        }
    }
}
?>
