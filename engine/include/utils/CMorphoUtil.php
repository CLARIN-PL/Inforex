<?php

if (! function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( !array_key_exists($columnKey, $value)) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( !array_key_exists($indexKey, $value)) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }
}

class MorphoUtil
{
    static public function getPossibleAnnotators($tokenIds, $reportIds=null){
        if($tokenIds == null){
            $tokenIds = DbToken::getTokensByReportIds($reportIds, 'token_id');
            $tokenIds = array_map(function($it){return intval($it['token_id']);}, $tokenIds);
        }
//        ChromePhp::log($tokenIds);
        $users = DBTokensTagsOptimized::getUsersDecisionCount($tokenIds);
        $tokensLen = count($tokenIds);

        foreach($users as $key => $user)
            $users[$key]['annotation_count'] = number_format(($tokensLen - $user['annotation_count']) / $tokensLen * 100, 0).'%';

        // passing '-1' as user_id, will return only tagger tags
        array_unshift($users, array('user_id' => -1, 'screename' => 'Tagger', 'annotation_count' => '100%'));
        return $users;
    }

    public static function getPossibleAnnotatorsQuick($reportIds){
        global $db;

        $reportIdsString = count($reportIds) > 0 ? implode(',', $reportIds) : 'null';

        $sql = "select cnt.annotation_count, users.user_id, users.screename, users.email, users.clarin_login from users join(
            select user_id, count(*) annotation_count from (
            SELECT COUNT(*), user_id, t.token_id FROM `tokens_tags_optimized` as tto
            join `tokens` t on tto.token_id = t.token_id 
            where t.report_id in ("
            . $reportIdsString .
            ") and user_id is not null
             and stage = 'agreement'
             group by user_id, t.token_id
             order by token_id
             
             ) a group by user_id) cnt
            on users.user_id = cnt.user_id";

        $users =  $db->fetch_rows($sql);
        array_unshift($users, array('user_id' => -1, 'screename' => 'Tagger', 'annotation_count' => '100%'));
        return $users;
    }

    public static function getUsersDifferingDecisionsCnt($token_ids, $userA, $userB){
        if($userA == $userB)
            return 0; // the same user is compared

        $tags = DBTokensTagsOptimized::getUsersOwnDecisions($token_ids, $userA, $userB);
        $grouped = self::groupArr($tags, 'token_id');
        foreach($grouped as $key => $tags)
            $grouped[$key] = self::groupArr($tags, 'user_id');

        $differ = 0;

        foreach($grouped as $tags){
            if((count($tags)) < 2){
                $differ++; // only one user made decision
                continue;
            }
            $firstUserDecisions = array_pop($tags);
            $secondUserDecisions = array_pop($tags);

            foreach($firstUserDecisions as $firstUserTag){
                $foundTag = array_filter($secondUserDecisions, function($item) use ($firstUserTag){
                    if ($item['ctag'] !== $firstUserTag['ctag']
                        || $item['base_id'] !== $firstUserTag['base_id'])
                        return false;
                    return true;
                });
                if(count($foundTag) === 0){
                    $differ++;
                    break;
                }
            }
        }
        return $differ;
    }
    private static function groupArr($arr, $groupingOn){
        $result = array();
        foreach ($arr as $data) {
            $id = $data[$groupingOn];
            if (isset($result[$id])) {
                $result[$id][] = $data;
            } else {
                $result[$id] = array($data);
            }
        }
        return $result;
    }


}