<?php

class CAdminErrorLogs
{
    static function getConfiguredSources()
    {
        $sources = Config::Cfg()->get_adminErrorLogFiles();
        if (!is_array($sources)) {
            return array();
        }

        $normalized = array();
        foreach ($sources as $source) {
            if (!is_array($source)) {
                continue;
            }
            $key = isset($source['key']) ? trim($source['key']) : '';
            $title = isset($source['title']) ? trim($source['title']) : '';
            $path = isset($source['path']) ? trim($source['path']) : '';
            if ($key === '' || $title === '' || $path === '') {
                continue;
            }
            $normalized[$key] = array(
                'key' => $key,
                'title' => $title,
                'path' => $path,
            );
        }

        return $normalized;
    }

    static function getLogOverview()
    {
        $sources = self::getConfiguredSources();
        $overview = array();
        foreach ($sources as $source) {
            $path = $source['path'];
            $exists = file_exists($path);
            $readable = $exists && is_readable($path);
            $overview[] = array(
                'key' => $source['key'],
                'title' => $source['title'],
                'path' => $path,
                'exists' => $exists,
                'readable' => $readable,
                'size_bytes' => ($readable ? @filesize($path) : null),
                'modified_at' => ($readable ? @date('Y-m-d H:i:s', @filemtime($path)) : null),
            );
        }

        return $overview;
    }

    static function readLog($sourceKey, $lineLimit = 200, $query = '')
    {
        $sources = self::getConfiguredSources();
        if (!isset($sources[$sourceKey])) {
            return array(
                'source' => null,
                'lines' => array(),
                'error' => 'The selected log source is not available.',
            );
        }

        $source = $sources[$sourceKey];
        $path = $source['path'];
        if (!file_exists($path)) {
            return array(
                'source' => $source,
                'lines' => array(),
                'error' => 'The log file does not exist on this server.',
            );
        }

        if (!is_readable($path)) {
            return array(
                'source' => $source,
                'lines' => array(),
                'error' => 'The log file exists, but it is not readable for the application process.',
            );
        }

        $lineLimit = max(20, min(1000, intval($lineLimit)));
        $lines = self::tailLines($path, $lineLimit * 5);

        $query = trim((string) $query);
        if ($query !== '') {
            $filtered = array();
            foreach ($lines as $line) {
                if (stripos($line, $query) !== false) {
                    $filtered[] = $line;
                }
            }
            $lines = $filtered;
        }

        if (count($lines) > $lineLimit) {
            $lines = array_slice($lines, -$lineLimit);
        }

        $parsedLines = array();
        foreach ($lines as $line) {
            $parsedLines[] = array(
                'raw' => $line,
                'level' => self::detectLevel($line),
            );
        }

        return array(
            'source' => $source,
            'lines' => $parsedLines,
            'error' => null,
        );
    }

    static private function detectLevel($line)
    {
        $lineUpper = strtoupper($line);
        if (strpos($lineUpper, 'FATAL') !== false || strpos($lineUpper, 'UNCaught EXCEPTION') !== false || strpos($lineUpper, 'UNCAUGHT EXCEPTION') !== false) {
            return 'fatal';
        }
        if (strpos($lineUpper, 'ERROR') !== false) {
            return 'error';
        }
        if (strpos($lineUpper, 'WARNING') !== false) {
            return 'warning';
        }
        if (strpos($lineUpper, 'NOTICE') !== false) {
            return 'notice';
        }
        return 'info';
    }

    static private function tailLines($path, $maxLines)
    {
        $handle = @fopen($path, 'rb');
        if (!$handle) {
            return array();
        }

        $buffer = '';
        $chunkSize = 4096;
        $lineCount = 0;
        fseek($handle, 0, SEEK_END);
        $position = ftell($handle);

        while ($position > 0 && $lineCount <= $maxLines) {
            $readSize = min($chunkSize, $position);
            $position -= $readSize;
            fseek($handle, $position);
            $chunk = fread($handle, $readSize);
            $buffer = $chunk . $buffer;
            $lineCount = substr_count($buffer, "\n");
        }

        fclose($handle);

        $lines = preg_split("/\\r\\n|\\n|\\r/", $buffer);
        $lines = array_values(array_filter($lines, function ($line) {
            return trim($line) !== '';
        }));

        if (count($lines) > $maxLines) {
            $lines = array_slice($lines, -$maxLines);
        }

        return $lines;
    }
}
