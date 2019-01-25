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

    static function getTokenTagsOnlyFinalDecision($token_ids, $report_ids=null){
        global $db;

        $where_clause = " ";
        if($token_ids != null){
            $where_clause .= " AND token_id IN (". self::getStringOrNullTokenIdsList($token_ids) . ") ";
        }

        if($report_ids != null){
            $where_clause .= " AND report_id in (". self::getStringOrNullTokenIdsList($report_ids) . ") ";
        }

        $sql = "SELECT tto.token_tag_id, tto.token_id, tto.disamb, tto.ctag_id, ttc.id as ctag_id, ttc.ctag, b.id as base_id, b.text as base_text, tto.user_id 
            FROM ". self::$table ." as tto 
            JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id
            JOIN bases as b on tto.base_id = b.id ".
            ($report_ids == null ? " " : " JOIN tokens tok on tok.token_id = tto.token_id ")
            ." WHERE tto.stage = 'final' "
            .$where_clause . ';';

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

        if($user_id == -1 || $user_id == 'final'){
            $select_fields = " tto.token_tag_id, tto.token_id, tto.disamb, ttc.id as ctag_id, ttc.ctag, b.text as base_text, tto.base_id as base_id, tto.user_id, tok.report_id as report_id,  tok.to, tok.from ";
        }
        else{
            $select_fields = " tto.token_tag_id, tto.token_id, tto.disamb, ttc.id as ctag_id, ttc.ctag, b.text as base_text, tto.base_id as base_id, tto.user_id, tok.report_id as report_id,  tok.to, tok.from ";
        }

        if($user_id == -1){
            $where_field = " (tto.user_id is null) AND (tto.stage = 'tagger') AND tto.disamb = 1 ";
        } else if($user_id  == 'final'){
            $where_field = " (tto.stage = 'final') AND tto.disamb = 1 ";
        }else{
            $where_field = " (tto.user_id = ". $user_id.") AND (tto.stage = 'agreement') ";
        }

        $sql = "SELECT ". $select_fields . " 
            FROM tokens_tags_optimized tto
            JOIN tokens tok on tto.token_id = tok.token_id
            JOIN tokens_tags_ctags as ttc ON tto.ctag_id = ttc.id
            JOIN bases as b on b.id = tto.base_id
            WHERE ". $where_field ."
            and tok.report_id in (". self::getStringOrNullTokenIdsList($report_ids). ");";

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

            foreach ($toRemoveFromA as $idx){
                unset($all[$key] ['a'][$idx]);
            }
            foreach ($toRemoveFromB as $idx){
                unset($all[$key] ['b'][$idx]);
            }

            $all[$key] ['a'] = array_values($all[$key] ['a']);
            $all[$key] ['b'] = array_values($all[$key] ['b']);
        }
        return $all;
    }

    private static function getUserWhereClause($user_id){
        if($user_id == -1){
            return "(tto.user_id is null and tto.stage = 'tagger' and tto.disamb = 1)";
        } else if($user_id == 'final'){
            return "(tto.stage = 'final')";
        } else if ($user_id == ''){
            return '(null)';
        } else if ($user_id == 'user_with_tagger') {
            return "(tto.user_id is null and tto.stage = 'tagger' and tto.disamb = 1)";
        }
        else{
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
    static function getPSAForReportAndUser($report_ids, $user_a, $user_b, $cmp_method) {
        global $db;

        $reports_data = array();

        foreach($report_ids as $row) {
            $reports_data[$row] = array();
            $reports_data[$row] ['both'] = 0;
            $reports_data[$row] ['only_a'] = 0;
            $reports_data[$row] ['only_b'] = 0;
        }

        $sql = "
        select tto.token_tag_id, tto.token_id, r.id  as report_id, tto.base_id, tto.ctag_id, tto.disamb,
            GROUP_CONCAT(distinct IF(tto.stage = 'agreement', tto.user_id, IF(tto.user_id is null, 'tagger', 'final'))
            order by tto.user_id asc) as users,
            count(distinct tto.user_id) as user_cnt 
        from `tokens_tags_optimized`tto
        join tokens tok on tok.token_id = tto.token_id
        join reports r on r.id = tok.report_id
      
        where r.id in (". self::getStringOrNullTokenIdsList($report_ids). ")

        and (". self::getUserWhereClause($user_a) ."
        or ". self::getUserWhereClause($user_b) ."
        or ". self::getUserWhereClause('user_with_tagger') . /*add tagger by default*/ ")
        group by base_id, ". ($cmp_method == 'base_ctag' ? 'ctag_id,' : '' ). " disamb, report_id, token_id";

        $rows = $db->fetch_rows($sql);

        foreach ($rows as $row) {
            if ($row['users'] === 'tagger') {
                if($row['disamb'] ==1 ) // when comparing to tagger, only count true disambs
                    $reports_data[$row['report_id']] ['both'] += 1;
            }
            // conditional statements below never fire for tagger

            else if (strpos($row['users'], ',') !== false) { // both users
                if ($row['disamb'] == 0)
                    $reports_data[$row['report_id']] ['both'] -= 1; // subtracting one from tagger decision
                else
                    $reports_data[$row['report_id']] ['both'] += 1;
            }
            else if($row['users'] == $user_a) {
                if ($row['disamb'] == 0)                            // user disagreed with tagger decision (but only one user did)
                    $reports_data[$row['report_id']] ['both'] -= 1; // subtract one from tagger data
                else                                                // user assigned his own interpretation
                    $reports_data[$row['report_id']] ['only_a'] += 1; // add one to only user
            }
            else if ($row['users'] == $user_b) {
                if ($row['disamb'] == 0)
                    $reports_data[$row['report_id']] ['both'] -= 1;
                else
                    $reports_data[$row['report_id']] ['only_b'] += 1;
            }
        }
        return $reports_data;
    }

    static function getPSAForReportAndUserWithFinal($report_ids, $user_a, $user_b, $cmp_method) {
        global $db;

        $reports_data = array();

        foreach($report_ids as $row) {
            $reports_data[$row] = array();
            $reports_data[$row] ['both'] = 0;
            $reports_data[$row] ['only_a'] = 0;
            $reports_data[$row] ['only_b'] = 0;
        }


        $sql = "
        select tto.token_tag_id, tto.token_id, r.id  as report_id, tto.base_id, tto.ctag_id, tto.disamb,
            GROUP_CONCAT(distinct IF(tto.stage = 'agreement', tto.user_id, IF(tto.user_id is null, 'tagger', 'final'))
            order by tto.user_id asc) as users,
            count(distinct tto.user_id) as user_cnt 
        from `tokens_tags_optimized`tto
        join tokens tok on tok.token_id = tto.token_id
        join reports r on r.id = tok.report_id
      
        where r.id in (". self::getStringOrNullTokenIdsList($report_ids). ")

        and (". self::getUserWhereClause($user_a) ."
        or ". self::getUserWhereClause($user_b) ."
        or ". self::getUserWhereClause('user_with_tagger') . /*add tagger by default*/ ")
        group by base_id, ". ($cmp_method == 'base_ctag' ? 'ctag_id,' : '' ). " disamb, report_id, token_id";

        $rows = $db->fetch_rows($sql);


        if ($user_a === 'final'){
            $userField = 'only_b';
            $finalField = 'only_a';
        } else {
            $userField = 'only_a';
            $finalField = 'only_b';
        }

        foreach ($rows as $row) {
            if ($row['users'] === 'tagger,final') {
                if($row['disamb'] ==1 ) // when comparing to tagger, only count true disambs
                    $reports_data[$row['report_id']] ['both'] += 1;
            }
            else if ($row['users'] === 'tagger') { // user agreed with tagger
                $reports_data[$row['report_id']] [$userField] += 1;
            }

            else if (strpos($row['users'], ',') !== false) { // both user and final
                $reports_data[$row['report_id']] ['both'] += 1;
            }
            else if($row['users'] == 'final') {
                $reports_data[$row['report_id']] [$finalField] += 1;
            }
            else {  // only user decision
                if ($row['disamb'] == 1)
                    $reports_data[$row['report_id']] [$userField] += 1;
            }
        }
        return $reports_data;
    }
}
?>
