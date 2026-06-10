<?php

if ($argc < 2) {
    fwrite(STDERR, "Usage: php local/validate-conllu-clarin.php <file-or-directory>\n");
    exit(1);
}

function collect_clarin_conllu_files($path) {
    if (is_file($path)) {
        return array($path);
    }

    $files = array();
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $fileInfo) {
        if (!$fileInfo->isFile()) {
            continue;
        }
        if (preg_match('/\.conllu$/i', $fileInfo->getFilename())) {
            $files[] = $fileInfo->getPathname();
        }
    }

    sort($files);
    return $files;
}

function validate_clarin_sentence(array $lines, $file, $sentenceIndex) {
    $errors = array();
    $expectedTokenId = 0;

    foreach ($lines as $lineNo => $line) {
        if ($line === '') {
            continue;
        }

        $cols = explode("\t", $line);
        if (count($cols) !== 10) {
            $errors[] = "$file:$lineNo invalid column count: " . count($cols);
            continue;
        }

        list($orderId, $tokenId, $orth, $ctag, $from, $to, $annTags, $annIds, $relIds, $relTargets) = $cols;

        if (!preg_match('/^[0-9]+$/', $orderId)) {
            $errors[] = "$file:$lineNo invalid ORDER_ID: $orderId";
        }
        if (!preg_match('/^[0-9]+$/', $tokenId)) {
            $errors[] = "$file:$lineNo invalid TOKEN_ID: $tokenId";
        } elseif (intval($tokenId) !== $expectedTokenId) {
            $errors[] = "$file:$lineNo unexpected TOKEN_ID sequence: $tokenId, expected $expectedTokenId";
        }
        $expectedTokenId++;

        if ($orth === '') {
            $errors[] = "$file:$lineNo empty ORTH";
        }
        if (!preg_match('/^[0-9]+$/', $from)) {
            $errors[] = "$file:$lineNo invalid FROM: $from";
        }
        if (!preg_match('/^[0-9]+$/', $to)) {
            $errors[] = "$file:$lineNo invalid TO: $to";
        }
        if (preg_match('/^[0-9]+$/', $from) && preg_match('/^[0-9]+$/', $to) && intval($from) > intval($to)) {
            $errors[] = "$file:$lineNo FROM greater than TO";
        }

        if ($annTags === '') {
            $errors[] = "$file:$lineNo empty ANN_TAGS";
        }
        if ($annIds === '') {
            $errors[] = "$file:$lineNo empty ANN_IDS";
        }
        if ($relIds === '') {
            $errors[] = "$file:$lineNo empty REL_IDS";
        }
        if ($relTargets === '') {
            $errors[] = "$file:$lineNo empty REL_TARGET_ANN_IDS";
        }

        if ($annIds === '_' && $annTags !== 'O') {
            $errors[] = "$file:$lineNo ANN_IDS is '_' but ANN_TAGS is not 'O'";
        }
        if ($annTags === 'O' && $annIds !== '_') {
            $errors[] = "$file:$lineNo ANN_TAGS is 'O' but ANN_IDS is not '_'";
        }

        if ($relIds !== '_') {
            foreach (explode(':', $relIds) as $relId) {
                if (!preg_match('/^[0-9]+$/', $relId)) {
                    $errors[] = "$file:$lineNo invalid REL_ID: $relId";
                }
            }
        }

        if ($relTargets !== '_') {
            foreach (explode(':', $relTargets) as $targetId) {
                if (!preg_match('/^[0-9]+$/', $targetId)) {
                    $errors[] = "$file:$lineNo invalid REL_TARGET_ANN_ID: $targetId";
                }
            }
        }
    }

    return $errors;
}

function validate_clarin_conllu_file($file) {
    $rawLines = file($file, FILE_IGNORE_NEW_LINES);
    if ($rawLines === false) {
        return array("Cannot read file: $file");
    }

    $errors = array();
    if (empty($rawLines)) {
        return array("$file is empty");
    }

    $expectedHeader = "ORDER_ID\tTOKEN_ID\tORTH\tCTAG\tFROM\tTO\tANN_TAGS\tANN_IDS\tREL_IDS\tREL_TARGET_ANN_IDS";
    if ($rawLines[0] !== $expectedHeader) {
        $errors[] = "$file invalid header";
    }

    $sentenceLines = array();
    $sentenceIndex = 0;
    $hasTokenLine = false;

    for ($i = 1; $i < count($rawLines); $i++) {
        $line = $rawLines[$i];
        $lineNo = $i + 1;

        if ($line === '') {
            if (!empty($sentenceLines)) {
                $sentenceIndex++;
                $errors = array_merge($errors, validate_clarin_sentence($sentenceLines, $file, $sentenceIndex));
                $sentenceLines = array();
            }
            continue;
        }

        $hasTokenLine = true;
        $sentenceLines[$lineNo] = $line;
    }

    if (!empty($sentenceLines)) {
        $sentenceIndex++;
        $errors = array_merge($errors, validate_clarin_sentence($sentenceLines, $file, $sentenceIndex));
    }

    if (!$hasTokenLine) {
        $errors[] = "$file contains no token lines";
    }

    return $errors;
}

$target = $argv[1];
$files = collect_clarin_conllu_files($target);

if (empty($files)) {
    fwrite(STDERR, "No .conllu files found in: $target\n");
    exit(1);
}

$allErrors = array();
foreach ($files as $file) {
    $allErrors = array_merge($allErrors, validate_clarin_conllu_file($file));
}

if (!empty($allErrors)) {
    foreach ($allErrors as $error) {
        echo $error . "\n";
    }
}

echo 'SUMMARY ' . json_encode(array(
    'files' => count($files),
    'errors' => count($allErrors)
)) . "\n";

exit(empty($allErrors) ? 0 : 2);
