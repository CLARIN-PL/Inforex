<?php

/**
 * Prepare a transactional SQL import of the Wanda plays as dialogue XML.
 *
 * Usage:
 *   php local/import-wanda-dialogues.php --source=/path/to/dane
 *   php local/import-wanda-dialogues.php --source=/path/to/dane --sql=/tmp/wanda.sql
 *
 * The script never modifies source files or connects to the database. Review the
 * summary before executing the generated SQL against the local Inforex database.
 */

$options = getopt('', array('source:', 'sql::', 'audit'));
if (empty($options['source'])) {
    fwrite(STDERR, "Missing --source=/path/to/dane\n");
    exit(1);
}

$root = realpath($options['source']);
if ($root === false || !is_dir($root)) {
    fwrite(STDERR, "Source directory does not exist\n");
    exit(1);
}

$works = array(
    array('path' => 'wanda_dembowski', 'name' => 'Wanda - Dembowski', 'author' => 'Dembowski'),
    array('path' => 'wanda_fredro', 'name' => 'Wanda - Fredro (wersja 1)', 'author' => 'Fredro',
        'aliases' => array('ARCYKAPLAN' => 'ARCYKAPŁAN', 'WŁADYBOJ' => 'WŁADYBÓJ')),
    array('path' => 'wanda_fredro2/wanda_fredro', 'name' => 'Wanda - Fredro (wersja 2)', 'author' => 'Fredro',
        'aliases' => array('WKADYBÓJ' => 'WŁADYBÓJ', 'WLADYBÓJ' => 'WŁADYBÓJ',
            'WRADYBÓJ' => 'WŁADYBÓJ')),
    array('path' => 'wanda_slowacki/wanda_slowacki', 'name' => 'Wanda - Słowacki', 'author' => 'Słowacki',
        'aliases' => array('GRAMOH' => 'GRAMOR', 'ZEISŁAWA' => 'ZBISŁAWA')),
    array('path' => 'wanda_wezyk', 'name' => 'Wanda - Wężyk', 'author' => 'Wężyk',
        'aliases' => array('SKARBIMR' => 'SKARBIMIR', 'WLADYBOY' => 'WŁADYBOY',
            'CHOR LUDU' => 'CHÓR LUDU')),
);

function sqlText($value)
{
    return 'CONVERT(0x' . bin2hex($value) . ' USING utf8mb4)';
}

function xmlText($value)
{
    return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

function parseSpeaker($line)
{
    if (preg_match('/^\[\((.+)\)\]$/u', $line, $wrapped)) {
        $line = $wrapped[1];
    }
    if (preg_match('/^(KONIEC|KOŃIEC|SCENA|PRÓCZ)\b/u', $line)) {
        return null;
    }
    if (preg_match('/^([\p{Lu}][\p{Lu}\p{M}-]*(?:\s+[\p{Lu}\p{M}-]+){0,4})(?:\s+(sam|sama|sami|same))?(?:\s+(\([^)]*\)))?[.:]?$/u', $line, $matches)) {
        return array($matches[1], trim((isset($matches[2]) ? $matches[2] : '') . ' ' . (isset($matches[3]) ? $matches[3] : '')));
    }
    if (preg_match('/^(Wanda|Ullo)(?:\s+(\([^)]*\)))?[.:]?$/u', $line, $matches)) {
        return array(mb_strtoupper($matches[1], 'UTF-8'), isset($matches[2]) ? $matches[2] : '');
    }
    return null;
}

function parseScene($path, $aliases = array())
{
    $raw = file_get_contents($path);
    if ($raw === false || !mb_check_encoding($raw, 'UTF-8')) {
        throw new RuntimeException("Cannot read UTF-8 scene: $path");
    }
    $raw = preg_replace('/^\xEF\xBB\xBF/', '', $raw);
    $lines = preg_split('/\r\n|\r|\n/', $raw);
    $header = null;
    $speaker = '';
    $speech = array();
    $messages = array();
    $cast = array();
    $speakerCount = 0;
    $flush = function () use (&$speech, &$messages, &$speaker) {
        $content = trim(implode("\n", $speech));
        if ($content !== '') {
            $messages[] = array($speaker, $content);
        }
        $speech = array();
    };

    foreach ($lines as $line) {
        $line = trim($line);
        if ($header === null) {
            if ($line === '') {
                continue;
            }
            if (preg_match('/^SCENA\s+.+$/ui', $line)) {
                if (!preg_match('/^Akt\.([IVX]+)\.Scena\./i', basename($path), $filenameParts)) {
                    throw new RuntimeException("Cannot infer act from filename: $path");
                }
                $line = 'AKT ' . $filenameParts[1] . ' | ' . $line;
            }
            if (!preg_match('/^AKT\s+.+\|\s*SCENA\s+.+$/ui', $line)) {
                throw new RuntimeException("Invalid scene heading in $path: $line");
            }
            $header = $line;
            continue;
        }
        $speakerInfo = parseSpeaker($line);
        if ($speakerInfo !== null) {
            $flush();
            $speaker = $speakerInfo[0];
            $speaker = isset($aliases[$speaker]) ? $aliases[$speaker] : $speaker;
            $cast[$speaker] = true;
            $speakerCount++;
            if ($speakerInfo[1] !== '') {
                $speech[] = $speakerInfo[1];
            }
            continue;
        }
        if ($line !== '' || count($speech) > 0) {
            $speech[] = $line;
        }
    }
    $flush();

    if ($speakerCount === 0 || count($messages) === 0) {
        throw new RuntimeException("No dialogue found in $path");
    }

    $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<body>\n";
    $xml .= '<subtitle>' . xmlText($header) . "</subtitle>\n";
    foreach (array_keys($cast) as $name) {
        $xml .= '<out>' . xmlText($name . '.') . "</out>\n";
    }
    foreach ($messages as $message) {
        $content = preg_replace('/\s+/u', ' ', $message[1]);
        $xml .= '<message><author>' . xmlText($message[0] === '' ? '' : $message[0] . '.') . '</author>';
        $xml .= '<content>' . xmlText($content) . "</content></message>\n";
    }
    $xml .= "</body>\n";

    $document = new DOMDocument();
    if (!@$document->loadXML($xml, LIBXML_NONET)) {
        throw new RuntimeException("Generated XML is invalid: $path");
    }

    return array('header' => $header, 'xml' => $xml, 'messages' => count($messages), 'speakers' => array_keys($cast));
}

function romanValue($roman)
{
    $values = array('I' => 1, 'V' => 5, 'X' => 10);
    $total = 0;
    $previous = 0;
    for ($index = strlen($roman) - 1; $index >= 0; $index--) {
        $value = $values[$roman[$index]];
        $total += $value < $previous ? -$value : $value;
        $previous = $value;
    }
    return $total;
}

function sceneOrder($path)
{
    if (!preg_match('/^Akt\.([IVX]+)\.Scena\.([IVX]+)\.txt$/i', basename($path), $parts)) {
        throw new RuntimeException("Unexpected scene filename: $path");
    }
    return romanValue(strtoupper($parts[1])) * 100 + romanValue(strtoupper($parts[2]));
}

try {
    $rows = array();
    foreach ($works as $work) {
        $directory = $root . '/' . $work['path'];
        $files = glob($directory . '/Akt.*.txt');
        if (!$files) {
            throw new RuntimeException("No scene files found in $directory");
        }
        usort($files, function ($left, $right) {
            return sceneOrder($left) <=> sceneOrder($right);
        });
        $messageCount = 0;
        $workSpeakers = array();
        foreach ($files as $file) {
            $scene = parseScene($file, isset($work['aliases']) ? $work['aliases'] : array());
            $rows[] = array('work' => $work, 'file' => $file, 'scene' => $scene);
            $messageCount += $scene['messages'];
            foreach ($scene['speakers'] as $name) {
                $workSpeakers[$name] = true;
            }
        }
        printf("%s: %d scen, %d wypowiedzi\n", $work['name'], count($files), $messageCount);
        if (isset($options['audit'])) {
            echo '  Postaci: ' . implode(', ', array_keys($workSpeakers)) . "\n";
        }
    }
    printf("Razem: %d dokumentów\n", count($rows));

    if (isset($options['sql'])) {
        if ($options['sql'] === false || $options['sql'] === '') {
            throw new RuntimeException('The --sql option requires a file path');
        }
        $sql = "SET NAMES utf8mb4;\nSTART TRANSACTION;\n";
        $sql .= "INSERT INTO corpora (name, description, public, user_id, date_created)\n";
        $sql .= "SELECT 'dramaty-o-wandzie', 'Sceny dialogowe pięciu wersji dramatu Wanda.', 0, user_id, NOW() FROM corpora WHERE id = 158\n";
        $sql .= "AND NOT EXISTS (SELECT 1 FROM corpora WHERE name = 'dramaty-o-wandzie');\n";
        $sql .= "SET @wanda_created := ROW_COUNT();\nSET @wanda_corpus_id := LAST_INSERT_ID();\n";
        $sql .= "INSERT INTO annotation_sets_corpora (annotation_set_id, corpus_id)\n";
        $sql .= "SELECT annotation_set_id, @wanda_corpus_id FROM annotation_sets_corpora WHERE corpus_id = 158 AND @wanda_created = 1;\n";
        $sql .= "INSERT INTO corpus_and_report_perspectives (corpus_id, perspective_id, access)\n";
        $sql .= "SELECT @wanda_corpus_id, perspective_id, access FROM corpus_and_report_perspectives WHERE corpus_id = 158 AND @wanda_created = 1;\n";
        foreach ($works as $work) {
            $sql .= "INSERT INTO corpus_subcorpora (corpus_id, name, description)\n";
            $sql .= 'SELECT @wanda_corpus_id, ' . sqlText($work['name']) . ', ' . sqlText($work['path']) . " WHERE @wanda_created = 1;\n";
            $sql .= 'SET @wanda_subcorpus_id := LAST_INSERT_ID();' . "\n";
            foreach ($rows as $row) {
                if ($row['work']['path'] !== $work['path']) {
                    continue;
                }
                $relative = $work['path'] . '/' . basename($row['file']);
                $title = $work['name'] . ' - ' . $row['scene']['header'];
                $sql .= "INSERT INTO reports (corpora, subcorpus_id, date, title, source, author, content, type, status, user_id, format_id, lang, filename)\n";
                $sql .= 'SELECT @wanda_corpus_id, @wanda_subcorpus_id, CURDATE(), '
                    . sqlText($title) . ', ' . sqlText($relative) . ', ' . sqlText($work['author']) . ', '
                    . sqlText($row['scene']['xml']) . ", 1, 2, user_id, 1, 'pol', " . sqlText($relative)
                    . " FROM corpora WHERE id = 158 AND @wanda_created = 1;\n";
            }
        }
        $sql .= "COMMIT;\n";
        if (file_put_contents($options['sql'], $sql) === false) {
            throw new RuntimeException('Could not write SQL file');
        }
        printf("SQL zapisany: %s (%d bajtów)\n", $options['sql'], strlen($sql));
    }
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->getMessage() . "\n");
    exit(1);
}
