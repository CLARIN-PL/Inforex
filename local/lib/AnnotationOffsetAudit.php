<?php

/** Plans conservative repairs of offsets created by the old stripslashes renderer. */
class AnnotationOffsetAudit {
    public static function contentHash($report) {
        return hash('sha256', $report['format'] . "\0" . $report['content']);
    }

    public static function annotationHash($annotations) {
        $rows = array();
        foreach ($annotations as $a) {
            $rows[(int)$a['id']] = array((int)$a['from'], (int)$a['to'], $a['text']);
        }
        ksort($rows, SORT_NUMERIC);
        return hash('sha256', json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
    }

    private static function comparable($text) {
        return preg_replace('/\s/u', '', $text);
    }

    private static function matches($html, $a) {
        return $a['from'] >= 0 && $a['to'] >= $a['from'] && $a['to'] < count($html->chars)
            && self::comparable(custom_html_entity_decode($html->getText($a['from'], $a['to'])))
                === self::comparable($a['text']);
    }

    /** Map only the deletion of literal backslashes, never search for a similar phrase. */
    private static function legacyToRaw($raw, $legacy) {
        $map = array();
        $chars = array();
        for ($i = 0; $i < count($raw->chars); $i++) {
            $char = $raw->chars[$i]->toString();
            if ($char === '\\') {
                // stripslashes removes the first slash of a pair and retains the second.
                if (isset($raw->chars[$i + 1]) && $raw->chars[$i + 1]->toString() === '\\') {
                    $i++;
                } else {
                    continue;
                }
            }
            $map[] = $i;
            $chars[] = $raw->chars[$i]->toString();
        }
        $legacyChars = array_map(function($char) { return $char->toString(); }, $legacy->chars);
        // Escapes such as \0 and transformations of markup are intentionally excluded.
        return $chars === $legacyChars ? $map : null;
    }

    public static function plan($report, $annotations) {
        $result = array(
            'report_id' => (int)$report['id'],
            'corpus_id' => (int)$report['corpora'],
            'content_sha256' => self::contentHash($report),
            'annotations_before_sha256' => self::annotationHash($annotations),
            'changes' => array(), 'unresolved' => array(), 'unchanged' => 0
        );
        $raw = ReportContent::getHtmlStr($report);
        $legacyReport = $report;
        $legacyReport['content'] = stripslashes($report['content']);
        $legacy = ReportContent::getHtmlStr($legacyReport);
        $map = self::legacyToRaw($raw, $legacy);
        foreach ($annotations as &$a) {
            if (self::matches($raw, $a)) {
                $result['unchanged']++;
                continue;
            }
            if ($map === null || !self::matches($legacy, $a)
                || !isset($map[$a['from']], $map[$a['to']])) {
                $result['unresolved'][] = (int)$a['id'];
                continue;
            }
            $from = $map[$a['from']];
            $to = $map[$a['to']];
            $text = custom_html_entity_decode($raw->getText($from, $to));
            if (self::comparable(stripslashes($text)) !== self::comparable($a['text'])) {
                $result['unresolved'][] = (int)$a['id'];
                continue;
            }
            // Keep existing whitespace when only coordinates need to change.
            if (self::comparable($text) === self::comparable($a['text'])) {
                $text = $a['text'];
            }
            $before = array('from' => (int)$a['from'], 'to' => (int)$a['to'], 'text' => $a['text']);
            $after = array('from' => $from, 'to' => $to, 'text' => $text);
            $result['changes'][] = array('id' => (int)$a['id'], 'before' => $before, 'after' => $after);
            $a = array_merge($a, $after);
        }
        unset($a);
        $result['annotations_after_sha256'] = self::annotationHash($annotations);
        return $result;
    }
}
