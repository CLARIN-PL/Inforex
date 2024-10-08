<?php

/**
 * Contains auxiliary methods to manipulate on report content.
 *
 * @author Michał Marcińczuk
 */
class ReportContent
{
    /**
     * List of exceptions occured during executing the last operation.
     * @var array
     */
    public static $exceptions = array();

    /**
     * @param $htmlStr
     * @param $tokens
     * @return mixed
     */
    static function insertTokens(HtmlStr2 $htmlStr, $tokens){
        ReportContent::$exceptions = array();
        foreach ($tokens as $token){
            $tag_open = sprintf("<an#%d:%s>", $token['token_id'], "token" . ($token['eos'] ? " eos" : ""));
            try{
                $htmlStr->insertTag((int)$token['from'], $tag_open, $token['to']+1, "</an>", true);
            } catch (Exception $ex) {
                ReportContent::$exceptions[] = sprintf("Token '%s' is crossing an annotation. Verify the annotations.", htmlentities($tag_open));
                for ( $i = $token['from']; $i<=$token['to']; $i++){
                    try{
                        $htmlStr->insertTag($i, "<b class='invalid_border_token' title='{$token['from']}'>", $i+1, "</b>");
                    }catch(Exception $exHtml){
                        ReportContent::$exceptions[] = $exHtml->getMessage();
                    }
                }
            }
        }
        return $htmlStr;
    }

    static function insertTokensWithTag(HtmlStr2 $htmlStr, $tokens){
        ReportContent::$exceptions = array();
        foreach ($tokens as $token){
            $tag_open = sprintf("<tkb id=\"%s\" base=\"%s\" ctag=\"%s\">", $token["token_id"], $token['base'], $token['ctag']);
            try{
                $htmlStr->insertTag((int)$token['from'], $tag_open, $token['to']+1, "<tke id=\"" . $token["token_id"]  . "\" />", true);
            } catch (Exception $ex) {
                ReportContent::$exceptions[] = sprintf("Token '%s' is crossing an annotation. Verify the annotations.", htmlentities($tag_open));
                for ( $i = $token['from']; $i<=$token['to']; $i++){
                    try{
                        $htmlStr->insertTag($i, "<b class='invalid_border_token' title='{$token['from']}'>", $i+1, "</b>");
                    }catch(Exception $exHtml){
                        ReportContent::$exceptions[] = $exHtml->getMessage();
                    }
                }
            }
        }
        return $htmlStr;
    }


    /**
     * @param $htmlStr
     * @param $tokens
     * @return mixed
     */
    static function insertTokensWithIds(HtmlStr2 $htmlStr, $tokens){
        ReportContent::$exceptions = array();
        foreach ($tokens as $token){
            $tag_open = sprintf("<an#%d:%s>", $token['token_id'], "token" . ($token['eos'] ? " eos" : ""));
            try{
                $htmlStr->insertTag((int)$token['from'], $tag_open, $token['to']+1, "</an>", true);
            } catch (Exception $ex) {
                ReportContent::$exceptions[] = sprintf("Token '%s' is crossing an annotation. Verify the annotations.", htmlentities($tag_open));

                for ( $i = $token['from']; $i<=$token['to']; $i++){
                    try{
                        $htmlStr->insertTag($i, "<b class='invalid_border_token' title='{$token['from']}'>", $i+1, "</b>");
                    }catch(Exception $exHtml){
                        ReportContent::$exceptions[] = $exHtml->getMessage();
                    }
                }
            }
        }
        return $htmlStr;
    }

    /**
     * @param HtmlStr2 $htmlStr
     * @param $annotations
     * @return HtmlStr2
     */
    static function insertAnnotations(HtmlStr2 $htmlStr, $annotations){
        ReportContent::$exceptions = array();
        foreach ($annotations as $an) {
            try {
                $htmlStr->insertTag($an['from'],
                    sprintf("<an#%d:annotation %s:%d:%d:'%s'>", $an['id'], $an['type'], $an['group_id'], $an['annotation_subset_id'], $an['lemma']), $an['to'] + 1, "</an>");
            } catch (Exception $ex) {
                try {
                    ReportContent::$exceptions[] = $ex->getMessage();
                    if ($an['from'] == $an['to']) {
                        $htmlStr->insertTag($an['from'], "<b class='invalid_border_one' title='{$an['from']}'>", $an['from'] + 1, "</b>");
                    } else {
                        $htmlStr->insertTag($an['from'], "<b class='invalid_border_start' title='{$an['from']}'>", $an['from'] + 1, "</b>");
                    }
                } catch (Exception $ex2) {
                    ReportContent::$exceptions[] = $ex2->getMessage();
                }
            }
        }
        return $htmlStr;
    }

    /**
     * @param HtmlStr2 $htmlStr
     * @param $relations
     */
    static function insertAnnotationsWithRelations(HtmlStr2 $htmlStr, $annotations, $relations){
        $tags = array();
        $annotationIndex = array();
        $annotationRelations = array();
        foreach ($annotations as $an){
            $annotationIndex[$an['id']] = $an;
        }
        foreach ($relations as $relation){
            if ( isset($annotationIndex[$relation['source_id']]) && isset($annotationIndex[$relation['target_id']]) ) {
                if ( !isset($annotationRelations[$relation['source_id']]) ){
                    $annotationRelations[$relation['source_id']] = array();
                }
                $tag = "<sup class='rel' title='".$relation['name']."' sourcegroupid='".$relation['source_id']."' target='".$relation['target_id']."'/>x</sup>";
                $annotationRelations[$relation['source_id']][] = $tag;
            }
        }
        foreach ($annotations as $an) {
            try {
                $after = "";
                if ( isset($annotationRelations[$an[id]]) ){
                    $after = implode("", $annotationRelations[$an[id]]);
                }
                $htmlStr->insertTag($an['from'],
                    sprintf("<an#%d:annotation %s:%d:%d:'%s'>", $an['id'], $an['type'], $an['group_id'], $an['annotation_subset_id'], $an['lemma']), $an['to'] + 1, "</an>$after");
            } catch (Exception $ex) {
                try {
                    ReportContent::$exceptions[] = $ex->getMessage();
                    if ($an['from'] == $an['to']) {
                        $htmlStr->insertTag($an['from'], "<b class='invalid_border_one' title='{$an['from']}'>", $an['from'] + 1, "</b>");
                    } else {
                        $htmlStr->insertTag($an['from'], "<b class='invalid_border_start' title='{$an['from']}'>", $an['from'] + 1, "</b>");
                    }
                } catch (Exception $ex2) {
                    ReportContent::$exceptions[] = $ex2->getMessage();
                }
            }
        }
        return $htmlStr;
    }

    /**
     * Creates a HtmlStr object for manipulation of the report content based on the report format.
     * @param $report
     * @return HtmlStr2
     */
    static function getHtmlStr($report){
        $content = $report['content'];
        // Escape html special characters for plain format
        if ( $report['format'] == 'plain'){
            $content = htmlspecialchars($content);
        }
        return new HtmlStr2($content, true);
    }

    static function getHtmlStrForReport($report){
        $content = $report->getContent();
        // Escape html special characters for plain format
        if ( $report->getFormatId() == DB_REPORT_FORMATS_PLAIN){
            $content = htmlspecialchars($content);
        }
        return new HtmlStr2($content, true);
    }
}