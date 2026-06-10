<?php
/**
 * Validates CLARIN JSON exports.
 *
 * Usage:
 *   php local/validate-clarin-json.php [path]
 *
 * Path may point to a single JSON file or a directory.
 */

function split_utf8_chars($text) {
    return preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
}

function slice_utf8_text(array $chars, $start, $stop) {
    if ($stop < $start) {
        return '';
    }
    return implode('', array_slice($chars, $start, $stop - $start));
}

function collect_json_files($path) {
    if (is_file($path)) {
        return substr($path, -5) === '.json' ? array($path) : array();
    }

    $files = array();
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        if (substr($file->getFilename(), -5) === '.json') {
            $files[] = $file->getPathname();
        }
    }

    sort($files);
    return $files;
}

function flatten_span_layers(array $spans) {
    $annotations = array();
    $sentences = array();

    foreach ($spans as $layerName => $layerDefinition) {
        if (isset($layerDefinition['items']) && is_array($layerDefinition['items'])) {
            $items = $layerDefinition['items'];
            $kind = isset($layerDefinition['meta']['kind']) ? $layerDefinition['meta']['kind'] : null;
        } else {
            $items = is_array($layerDefinition) ? $layerDefinition : array();
            $kind = ($layerName === 'sentence') ? 'sentence' : 'annotation';
        }

        if ($kind === 'sentence' || $layerName === 'sentence') {
            $sentences = array_merge($sentences, $items);
        } else {
            $annotations = array_merge($annotations, $items);
        }
    }

    return array($annotations, $sentences);
}

function flatten_relation_layers(array $relations) {
    $flat = array();

    foreach ($relations as $layerDefinition) {
        if (isset($layerDefinition['items']) && is_array($layerDefinition['items'])) {
            $flat = array_merge($flat, $layerDefinition['items']);
            continue;
        }
        if (is_array($layerDefinition) && isset($layerDefinition[0])) {
            $flat = array_merge($flat, $layerDefinition);
        }
    }

    return $flat;
}

function validate_clarin_json_file($file) {
    $raw = file_get_contents($file);
    $data = json_decode($raw, true);

    if (!is_array($data)) {
        return array('Invalid JSON');
    }

    $requiredTopLevel = array('text', 'tokens', 'spans');
    foreach ($requiredTopLevel as $key) {
        if (!array_key_exists($key, $data)) {
            return array("Missing top-level field: $key");
        }
    }

    if (!isset($data['tokens']['default']) || !is_array($data['tokens']['default'])) {
        return array('Missing tokens.default array');
    }

    $text = (string)$data['text'];
    $chars = split_utf8_chars($text);
    $textLength = count($chars);
    $tokens = $data['tokens']['default'];
    list($annotations, $sentences) = flatten_span_layers($data['spans']);
    $relations = isset($data['relations']) && is_array($data['relations'])
        ? flatten_relation_layers($data['relations'])
        : array();

    $errors = array();
    $annotationIds = array();

    foreach ($tokens as $index => $token) {
        $start = isset($token['start']) ? intval($token['start']) : null;
        $stop = isset($token['stop']) ? intval($token['stop']) : null;

        if ($start === null || $stop === null || $start < 0 || $stop < $start || $stop > $textLength) {
            $errors[] = "token#$index invalid range [$start,$stop)";
            continue;
        }

        $fragment = slice_utf8_text($chars, $start, $stop);
        if ($fragment === '') {
            $errors[] = "token#$index empty fragment";
            continue;
        }
        if (preg_match('/^\s/u', $fragment) || preg_match('/\s$/u', $fragment)) {
            $errors[] = "token#$index has edge whitespace";
        }
    }

    foreach ($annotations as $index => $annotation) {
        $start = isset($annotation['start']) ? intval($annotation['start']) : null;
        $stop = isset($annotation['stop']) ? intval($annotation['stop']) : null;

        if ($start === null || $stop === null || $start < 0 || $stop < $start || $stop > $textLength) {
            $errors[] = "annotation#$index invalid range [$start,$stop)";
            continue;
        }

        $fragment = slice_utf8_text($chars, $start, $stop);
        if (array_key_exists('text', $annotation) && (string)$annotation['text'] !== $fragment) {
            $errors[] = "annotation#$index text mismatch";
        }
        if (isset($annotation['id'])) {
            $annotationIds[(string)$annotation['id']] = true;
        }
    }

    foreach ($sentences as $index => $sentence) {
        $start = isset($sentence['start']) ? intval($sentence['start']) : null;
        $stop = isset($sentence['stop']) ? intval($sentence['stop']) : null;

        if ($start === null || $stop === null || $start < 0 || $stop < $start || $stop > $textLength) {
            $errors[] = "sentence#$index invalid range [$start,$stop)";
            continue;
        }

        $fragment = slice_utf8_text($chars, $start, $stop);
        if (trim($fragment) === '') {
            $errors[] = "sentence#$index blank fragment";
        }
    }

    if (!empty($sentences)) {
        foreach ($tokens as $index => $token) {
            $contained = false;
            foreach ($sentences as $sentence) {
                if (intval($token['start']) >= intval($sentence['start']) && intval($token['stop']) <= intval($sentence['stop'])) {
                    $contained = true;
                    break;
                }
            }
            if (!$contained) {
                $errors[] = "token#$index not contained in any sentence";
            }
        }

        foreach ($annotations as $index => $annotation) {
            $contained = false;
            foreach ($sentences as $sentence) {
                if (intval($annotation['start']) >= intval($sentence['start']) && intval($annotation['stop']) <= intval($sentence['stop'])) {
                    $contained = true;
                    break;
                }
            }
            if (!$contained) {
                $errors[] = "annotation#$index not contained in any sentence";
            }
        }
    }

    foreach ($relations as $index => $relation) {
        $source = isset($relation['source']) ? (string)$relation['source'] : null;
        $target = isset($relation['target']) ? (string)$relation['target'] : null;

        if ($source === null || !isset($annotationIds[$source])) {
            $errors[] = "relation#$index invalid source";
        }
        if ($target === null || !isset($annotationIds[$target])) {
            $errors[] = "relation#$index invalid target";
        }
    }

    return $errors;
}

$path = isset($argv[1]) ? $argv[1] : 'test-files';

if (!file_exists($path)) {
    fwrite(STDERR, "Path does not exist: $path\n");
    exit(2);
}

$files = collect_json_files($path);
if (empty($files)) {
    fwrite(STDERR, "No JSON files found in: $path\n");
    exit(2);
}

$summary = array(
    'files' => 0,
    'errors' => 0
);

foreach ($files as $file) {
    $summary['files']++;
    $errors = validate_clarin_json_file($file);
    if (empty($errors)) {
        echo "OK $file\n";
        continue;
    }

    $summary['errors'] += count($errors);
    echo "FAIL $file\n";
    foreach (array_slice($errors, 0, 10) as $error) {
        echo "- $error\n";
    }
    if (count($errors) > 10) {
        echo "- ... " . (count($errors) - 10) . " more\n";
    }
}

echo 'SUMMARY ' . json_encode($summary, JSON_UNESCAPED_UNICODE) . "\n";
exit($summary['errors'] > 0 ? 1 : 0);
