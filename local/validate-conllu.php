<?php

if ($argc < 2) {
    fwrite(STDERR, "Usage: php local/validate-conllu.php <file-or-directory>\n");
    exit(1);
}

function collect_conllu_files($path) {
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

function validate_sentence(array $lines, $file, $sentenceIndex) {
    $errors = array();
    $tokenIds = array();
    $rootCount = 0;

    foreach ($lines as $lineNo => $line) {
        if ($line === '') {
            continue;
        }
        if ($line[0] === '#') {
            continue;
        }

        $cols = explode("\t", $line);
        if (count($cols) !== 10) {
            $errors[] = "$file:$lineNo invalid column count: " . count($cols);
            continue;
        }

        list($id, $form, $lemma, $upos, $xpos, $feats, $head, $deprel, $deps, $misc) = $cols;

        if (!preg_match('/^[1-9][0-9]*$/', $id)) {
            $errors[] = "$file:$lineNo invalid ID: $id";
            continue;
        }

        $idInt = intval($id);
        $tokenIds[$idInt] = true;

        if ($form === '') {
            $errors[] = "$file:$lineNo empty FORM";
        }
        if ($lemma === '') {
            $errors[] = "$file:$lineNo empty LEMMA";
        }
        if ($upos === '' || $upos === '_') {
            $errors[] = "$file:$lineNo missing UPOS";
        }
        if ($xpos === '') {
            $errors[] = "$file:$lineNo empty XPOS";
        }
        if (!preg_match('/^(0|[1-9][0-9]*)$/', $head)) {
            $errors[] = "$file:$lineNo invalid HEAD: $head";
        }
        if ($deprel === '' || $deprel === '_') {
            $errors[] = "$file:$lineNo missing DEPREL";
        }

        if ($head === '0') {
            $rootCount++;
            if ($deprel !== 'root') {
                $errors[] = "$file:$lineNo HEAD=0 must use DEPREL=root";
            }
        } elseif ($deprel === 'root') {
            $errors[] = "$file:$lineNo DEPREL=root requires HEAD=0";
        }

        if ($deps !== '_' && !preg_match('/^[0-9:|a-zA-Z_.-]+$/', $deps)) {
            $errors[] = "$file:$lineNo invalid DEPS: $deps";
        }
    }

    if ($rootCount !== 1) {
        $errors[] = "$file sentence $sentenceIndex must have exactly one root, got $rootCount";
    }

    foreach ($lines as $lineNo => $line) {
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        $cols = explode("\t", $line);
        if (count($cols) !== 10) {
            continue;
        }
        $idInt = intval($cols[0]);
        $headInt = intval($cols[6]);
        if ($headInt !== 0 && !isset($tokenIds[$headInt])) {
            $errors[] = "$file:$lineNo HEAD points outside sentence: $headInt";
        }
        if ($headInt === $idInt) {
            $errors[] = "$file:$lineNo HEAD cannot point to self";
        }
    }

    return $errors;
}

function validate_conllu_file($file) {
    $rawLines = file($file, FILE_IGNORE_NEW_LINES);
    if ($rawLines === false) {
        return array("Cannot read file: $file");
    }

    $errors = array();
    $sentenceLines = array();
    $sentenceIndex = 0;
    $hasTokenLine = false;

    foreach ($rawLines as $index => $line) {
        $lineNo = $index + 1;
        if ($line !== '' && strpos($line, "\r") !== false) {
            $errors[] = "$file:$lineNo contains carriage return";
        }

        if ($line === '') {
            if (!empty($sentenceLines)) {
                $sentenceIndex++;
                $errors = array_merge($errors, validate_sentence($sentenceLines, $file, $sentenceIndex));
                $sentenceLines = array();
            }
            continue;
        }

        if ($line[0] !== '#') {
            $hasTokenLine = true;
        }
        $sentenceLines[$lineNo] = $line;
    }

    if (!empty($sentenceLines)) {
        $sentenceIndex++;
        $errors = array_merge($errors, validate_sentence($sentenceLines, $file, $sentenceIndex));
    }

    if (!$hasTokenLine) {
        $errors[] = "$file contains no token lines";
    }

    return $errors;
}

$target = $argv[1];
$files = collect_conllu_files($target);

if (empty($files)) {
    fwrite(STDERR, "No .conllu files found in: $target\n");
    exit(1);
}

$allErrors = array();
foreach ($files as $file) {
    $allErrors = array_merge($allErrors, validate_conllu_file($file));
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
