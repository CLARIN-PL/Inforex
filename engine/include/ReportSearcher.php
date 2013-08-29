<?php

/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 */
class ReportSearcher {

    /**
     * 
     * @param string $content Report content
     * @param string $base_tokens_pos Ex. "10-12,34-39,45-46". From db: GROUP_CONCAT(CONCAT(tokens.from,'-',tokens.to) separator ',') AS base_tokens_pos
     * @return array
     */
    public static function get_sentences_with_base_in_content_by_positions($content, $base_tokens_pos) {
        $reportHtml = new HtmlStr2($content);
        $words_positions = explode(',', $base_tokens_pos);
        $base_sentences = array();
        foreach ($words_positions AS $word_position) {
            list($from, $to) = explode('-', $word_position);
            list($sentence_begin, $sentence_end) = $reportHtml->getSentencePos($from);

            $word = $reportHtml->getText($from, $to);
            $sentence_text = html_entity_decode($reportHtml->getText($sentence_begin, $sentence_end));
            
            $word = html_entity_decode($word, ENT_XML1 | ENT_QUOTES);
            //$sentence_text = html_entity_decode($sentence_text, ENT_XML1 | ENT_QUOTES);

            $word_in_sentence_begin = $reportHtml->getCharNumberBetweenPositions($sentence_begin, $from) - 1;
            $word_in_sentence_end = $word_in_sentence_begin + mb_strlen($word);

            $sentence_text_highlighted = mb_substr($sentence_text, 0, $word_in_sentence_begin)
                    . '<span class="highlighted">' . mb_substr($sentence_text, $word_in_sentence_begin, $word_in_sentence_end - $word_in_sentence_begin)
                    . '</span>' . mb_substr($sentence_text, $word_in_sentence_end);

            $base_sentences[] = array(
                'word' => $word,
                'word_from' => $from,
                'word_to' => $to,
                'sentence' => $sentence_text,
                'sentence_with_highlighted' => $sentence_text_highlighted,
                'sentence_begin' => $sentence_begin,
                'sentence_end' => $sentence_end,
                'word_in_sentence_begin' => $word_in_sentence_begin,
                'word_in_sentence_end' => $word_in_sentence_end,
            );
        }

        return $base_sentences;
    }

    public static function get_sentences_with_base_in_report($report_id, $base) {
        global $mdb2;
        
        $report_id = (int) $report_id;
        $base_escaped = $mdb2->quote($base);
        $sql = 'SELECT 
            GROUP_CONCAT(CONCAT(tokens.from,"-",tokens.to) separator ",") AS base_tokens_pos,
            r.content
          FROM reports r
          LEFT JOIN reports_types rt ON (r.type = rt.id)
          JOIN tokens AS tokens ON (r.id=tokens.report_id)
          JOIN tokens_tags AS tt USING(token_id)
          LEFT JOIN bases AS b ON b.id=tt.base_id
          WHERE r.id= '.$report_id.' AND (b.text = '.$base_escaped.' COLLATE utf8_bin AND tt.disamb = 1)
          GROUP BY r.id';
        $result = $mdb2->queryRow($sql, null, MDB2_FETCHMODE_ASSOC);
        if (isset($result['base_tokens_pos'])) {
            $return = self::get_sentences_with_base_in_content_by_positions($result['content'], $result['base_tokens_pos']);
        } else {
            $return = array();
        }
        return $return;
    }
}
?>

