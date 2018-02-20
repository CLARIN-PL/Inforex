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

	static function getTokensTags($token_ids, $withDisambFalse = true){
        global $db;

        $sql = "SELECT tto.token_tag_id, tto.token_id, tto.disamb, tto.ctag_id, ttc.id as ctag_id, ttc.ctag, b.id as base_id, b.text as base_text "
            ."FROM ". self::$table ." as tto "
            ."JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id "
            ."JOIN bases as b on tto.base_id = b.id "
            ."WHERE tto.user_id IS NULL "
            . ($withDisambFalse ? ' ' : "AND disamb = 1 ")
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

    static function getUserOwnDecisionsByReports($report_ids, $user_id){
        global $db;
        // todo - refractor
        if($user_id == -1){
            $sql = "SELECT tto.token_tag_id, tto.token_id, tto.disamb, ttc.id as ctag_id, ttc.ctag, b.text as base_text, tto.base_id as base_id, tto.user_id, tok.report_id as report_id,  tok.to, tok.from
            FROM tokens_tags_optimized tto
            JOIN tokens tok on tto.token_id = tok.token_id
            JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id
            JOIN bases as b on b.id = tto.base_id
            WHERE (tto.user_id = null) AND (tto.stage = 'tagger') AND tto.disamb = 1
            and tok.report_id in (". self::getStringOrNullTokenIdsList($report_ids). ");";

            return $db->fetch_rows($sql);
        } else if($user_id == 'final'){

            $sql = "SELECT tto.token_tag_id, tto.token_id, tto.disamb, ttc.id as ctag_id, ttc.ctag, b.text as base_text, tto.base_id as base_id, tto.user_id, tok.report_id as report_id,  tok.to, tok.from
            FROM tokens_tags_optimized tto
            JOIN tokens tok on tto.token_id = tok.token_id
            JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id
            JOIN bases as b on b.id = tto.base_id
            WHERE (tto.stage = 'final') AND tto.disamb = 1
            and tok.report_id in (". self::getStringOrNullTokenIdsList($report_ids). ");";

//            var_dump($sql); die();
            return $db->fetch_rows($sql);
        }

        else{
        $sql = "SELECT tto.token_tag_id, tto.token_id, tto.disamb, ttc.id as ctag_id, ttc.ctag, b.text as base_text, tto.base_id as base_id, tto.user_id, tok.report_id as report_id,  tok.to, tok.from
            FROM tokens_tags_optimized tto
            JOIN tokens tok on tto.token_id = tok.token_id
            JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id
            JOIN bases as b on b.id = tto.base_id
            WHERE (tto.user_id = ". $user_id.") AND (tto.stage = 'agreement') -- AND tto.disamb = 1
            and tok.report_id in (". self::getStringOrNullTokenIdsList($report_ids). ");";
        }
        return $db->fetch_rows($sql);
    }

    static function getTaggerDiff($user, $tagger){
        foreach($user as $user_key => $u){
            if($u['disamb'] == '0'){
                foreach($tagger as $tagger_key => $t){

                    if($t['ctag_id'] == $u['ctag_id'] && $t['base_id'] == $u['base_id'] ){
                        unset($tagger[$tagger_key]);
                    }
                }
                array_splice($user, $user_key, 1 );
            }
        }
        return array_merge($user, $tagger);
    }

    static function prepareReportSummary($user_a, $user_b, $report_id){
        // gropping by token
        $all = array();

        $reportContent = DbReport::getReports(null,null,$report_id, null, array("content"));
        $content = $reportContent[0]['content'];
        if ( $content['format'] == 'plain'){
            $content = htmlspecialchars($content);
        }
        $html = new HtmlStr2($content);



        foreach($user_a as $a){
            $all[$a['token_id']] ['a'] [] = $a;
            $all[$a['token_id']]['from']  = $a['from'];
            $all[$a['token_id']]['to']  = $a['to'];
            $all[$a['token_id']]['report_id']  =  $a['report_id'];

            $all[$a['token_id']]['orth']  = $html->getTextAlign($a['from'], $a['to'], false, false);
        }
        foreach($user_b as $b) {
            $all[$b['token_id']] ['b'] [] = $b;
            $all[$b['token_id']]['from'] = $b['from'];
            $all[$b['token_id']]['to'] = $b['to'];
            $all[$b['token_id']]['report_id'] = $b['report_id'];

            $all[$b['token_id']]['orth'] = $html->getTextAlign($b['from'], $b['to'], false, false);
        }
        return $all;
    }

    static private function assignAnnotatorValue(&$decision, $userLetter, $tagger,$is_final=false){

        if(key_exists($userLetter, $decision)){
            if(!$is_final){
                $decision [$userLetter] = self::getTaggerDiff($decision [$userLetter], $tagger);
            }

        }
        else if (!$is_final){
            $decision [$userLetter] = $tagger;
        }
        else{
            $decision [$userLetter] = array();
        }

    }

    static function getDecisionDifferences($all, $tagger, $compare_fcn, $is_a_final = false, $is_b_final = false){
        function array_slice_assoc($array,$keys) {
            return array_intersect_key($array,array_flip($keys));
        }

        foreach($all as $key => $tok){
            $taggerDec = array_filter($tagger,
                function($it) use($key){
                return intval($it['token_id']) ==  $key;
            });

            self::assignAnnotatorValue($all[$key], 'a', $taggerDec, $is_a_final);
            self::assignAnnotatorValue($all[$key], 'b', $taggerDec, $is_b_final);
            $toRemoveFromA = array();
            $toRemoveFromB = array();
            foreach($all[$key] ['a'] as $key_a => $a){
                foreach($all[$key] ['b'] as $key_b => $b){
                    if($compare_fcn($a, $b)){
                        $toRemoveFromA[] = $key_a;
                        $toRemoveFromB[] = $key_b;
                    }
                }
            }

//            foreach ($toRemoveFromA as $idx){
//                unset($all[$key] ['a'][$idx]);
//            }
//            foreach ($toRemoveFromB as $idx){
//                unset($all[$key] ['b'][$idx]);
//            }

            $all[$key] ['a'] = array_values($all[$key] ['a']);
            $all[$key] ['b'] = array_values($all[$key] ['b']);
        }
        return $all;
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
//        var_dump($user_a_id);
        foreach($rows as $r){
            $arr[$r['report_id']] [$r['token_id']] [intval($r['user_id']) == intval($user_a_id) ? 'a' : 'b']  [] = $r;
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

        if (!(array_key_exists('a', $token)))
            $token['a'] = array();

        if (!(array_key_exists('b', $token)))
            $token['b'] = array();

        /*
         * finding matching decisions
         * dropping decisions where both users set disamb to 0
         */
        foreach ($token['a'] as $a_key => $a_decision) {
            foreach ($token['b'] as $b_key => $b_decision) {
                if ($comparisonFcn($a_decision, $b_decision)) {
                    if ($a_decision['disamb'] != 0) {
                        $token['agree'][] = $a_decision;
                    }
//                    unset($token['a'][$a_key]);
                    array_splice($token['a'], $a_key, 1 );
//                    unset($token['b'][$b_key]);
                    array_splice($token['b'], $b_key, 1 );
                }
            }
        }
//        return;
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
                    array_splice($token[$user],$a_key, 1 );
                }
            }
        }
    }

    private static function getUserWhereClause($user_id){
        if($user_id == -1){
            return "(tto.user_id is null and tto.stage = 'tagger')";
        } else if($user_id == 'final'){
            return "(tto.stage = 'final')";
        } else{
            return "(tto.user_id = ". $user_id ." and tto.stage = 'agreement')";
        }
    }

    /**
     * @param int[] $report_ids
     * @param int $user_a
     * @param int $user_b
     *
     * @return object[]|null
     */
    static function getPCSForReportAndUsers($report_ids, $user_a, $user_b, $compare_method){
        global $db;

        $sql = "
        select count(*) as cnt, users, report_id from (
            select tto.token_tag_id, tto.token_id, r.id  as report_id, tto.base_id, tto.ctag_id, tto.disamb,
                GROUP_CONCAT(distinct tto.user_id order by tto.user_id asc) as users,
                count(distinct tto.user_id) as morpho_cnt 
            from `tokens_tags_optimized`tto
            join tokens tok on tok.token_id = tto.token_id
            join reports r on r.id = tok.report_id
            
            where r.id in (". self::getStringOrNullTokenIdsList($report_ids). ")
            -- and tto.user_id is not null
            -- and tto.stage = 'agreement'
            -- and (tto.user_id = ".$user_a." or tto.user_id = ".$user_b.")
            and (". self::getUserWhereClause($user_a) ."
            or ". self::getUserWhereClause($user_b) .")
            
            group by base_id, ". ($compare_method == 'base_ctag' ? 'ctag_id,' : '' ). " disamb, report_id, token_id
         ) derived 
        group by users, report_id
        order by report_id";


        var_dump($sql); die();
        $rows = $db->fetch_rows($sql);

        $reports_data = array();

        // if tagger is selected
        if($user_a == -1 || $user_b == -1){
            $ordinaryUserId = $user_a != -1 ?  $user_a : $user_b;
            $taggerId = -1; // Null in db

            foreach($rows as $row){
                // assign tagger decision as decision of both annotators

                $reports_data[$row['report_id']] ['both'] = 0;
                $reports_data[$row['report_id']] ['only_a'] = 0;
                $reports_data[$row['report_id']] ['only_b'] = 0;

                if(strpos($row['users'], 'NULL') !== false){
                    $reports_data[$row['report_id']] ['both'] = intval($row['cnt']);
                }

                // ordinary user,
                else if($ordinaryUserId == $row['users']){
                    $reports_data[$row['report_id']] ['both'] -= intval($row['cnt']);
                    if($row['users'] == $user_a)
                        $reports_data[$row['report_id']] ['only_a'] = intval($row['cnt']);
                    else
                        $reports_data[$row['report_id']] ['only_b'] = intval($row['cnt']);
                }
            }
        }

//        $reports_data = array();
        else{
            foreach($rows as $row){
                // both users
                if(strpos($row['users'], ',') !== false){
                    $reports_data[$row['report_id']] ['both'] = intval($row['cnt']);
                }

                else if ($user_a == $row['users']){ // type conversion with '=='
                    $reports_data[$row['report_id']] ['only_a'] = intval($row['cnt']);
                }

                else{
                    $reports_data[$row['report_id']] ['only_b'] = intval($row['cnt']);
                }

            }
        }

        return $reports_data;
    }

}
?>
