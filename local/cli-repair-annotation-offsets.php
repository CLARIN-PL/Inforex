<?php

// Private maintenance tool: dry-run from a snapshot, or apply/rollback a reviewed plan.
// Plans/backups contain corpus text. Keep them outside public_html and out of git.
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/lib/AnnotationOffsetAudit.php';

function offsetReadJson($path) {
    return json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
}

function offsetWritePrivateJson($path, $value) {
    $handle = fopen($path, 'x');
    if (!$handle) throw new RuntimeException('Cannot create a new private output file.');
    chmod($path, 0600);
    $json = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . "\n";
    if (fwrite($handle, $json) !== strlen($json) || !fflush($handle)) {
        fclose($handle);
        throw new RuntimeException('Cannot persist the backup.');
    }
    if (function_exists('fsync')) fsync($handle);
    fclose($handle);
}

function offsetReadReport($connection, $id, $lock = false) {
    $suffix = $lock ? ' FOR UPDATE' : '';
    $report = $connection->query('SELECT r.id, r.corpora, r.content, f.format FROM reports r '
        . 'JOIN reports_formats f ON f.id=r.format_id WHERE r.id=' . (int)$id . $suffix)->fetch_assoc();
    if (!$report) throw new RuntimeException('Report no longer exists.');
    $report['annotations'] = $connection->query('SELECT id, `from`, `to`, text FROM reports_annotations_optimized '
        . 'WHERE report_id=' . (int)$id . ' ORDER BY id' . $suffix)->fetch_all(MYSQLI_ASSOC);
    return $report;
}

$connection = null;
try {
    if (PHP_SAPI !== 'cli') throw new RuntimeException('CLI only.');
    $options = getopt('', array('snapshot:', 'output:', 'apply:', 'rollback:', 'backup:', 'config:'));
    if (isset($options['snapshot'], $options['output']) && !isset($options['apply']) && !isset($options['rollback'])) {
        $snapshot = offsetReadJson($options['snapshot']);
        $documents = array();
        $unresolved = array();
        foreach ($snapshot['reports'] as $report) {
            $plan = AnnotationOffsetAudit::plan($report, $report['annotations']);
            if ($plan['changes']) $documents[] = $plan;
            if ($plan['unresolved']) $unresolved[$report['id']] = $plan['unresolved'];
        }
        $plan = array('version' => 1, 'documents' => $documents, 'unresolved' => $unresolved);
        offsetWritePrivateJson($options['output'], $plan);
        echo json_encode(array('mode' => 'dry-run', 'reports' => count($documents),
            'changes' => array_sum(array_map(function($p) { return count($p['changes']); }, $documents)),
            'unresolved' => array_sum(array_map('count', $unresolved)))), "\n";
        exit(0);
    }
    if (!isset($options['backup']) || (isset($options['apply']) === isset($options['rollback']))) {
        throw new RuntimeException('Use --snapshot=FILE --output=FILE, or --apply=PLAN/--rollback=BACKUP --backup=NEW_FILE [--config=FILE].');
    }
    $rollback = isset($options['rollback']);
    $plan = offsetReadJson($options[$rollback ? 'rollback' : 'apply']);
    if (($plan['version'] ?? null) !== 1 || empty($plan['documents'])) throw new RuntimeException('Empty or unsupported plan.');
    Config::Cfg()->put_localConfigFilename($options['config'] ?? __DIR__ . '/../config/config.local.php');
    Config::Cfg()->put_log_sql(false);
    $dsn = Config::Cfg()->get_dsn();
    $host = explode(':', $dsn['hostspec'], 2);
    $connection = new mysqli($host[0], $dsn['username'], $dsn['password'], $dsn['database'], isset($host[1]) ? (int)$host[1] : 3306);
    $connection->set_charset('utf8mb4');
    $connection->query('SET SESSION innodb_lock_wait_timeout=5');
    $connection->begin_transaction();
    $seen = array();
    // Validate the entire plan under locks before writing any annotation.
    foreach ($plan['documents'] as $document) {
        $id = (int)$document['report_id'];
        if (isset($seen[$id])) throw new RuntimeException('Duplicate report in plan.');
        $seen[$id] = true;
        $report = offsetReadReport($connection, $id, true);
        $expectedHash = $document[$rollback ? 'annotations_after_sha256' : 'annotations_before_sha256'];
        if ((int)$report['corpora'] !== $document['corpus_id']
            || AnnotationOffsetAudit::contentHash($report) !== $document['content_sha256']
            || AnnotationOffsetAudit::annotationHash($report['annotations']) !== $expectedHash) {
            throw new RuntimeException('Stale plan; report content or annotations changed: ' . $id);
        }
        if (!$rollback) {
            $actual = AnnotationOffsetAudit::plan($report, $report['annotations']);
            // Query order is stable, but plans may originate from a differently ordered snapshot.
            $expectedChanges = $document['changes'];
            $actualChanges = $actual['changes'];
            $byId = function($a, $b) { return $a['id'] <=> $b['id']; };
            usort($expectedChanges, $byId);
            usort($actualChanges, $byId);
            if ($actualChanges !== $expectedChanges || $actual['annotations_after_sha256'] !== $document['annotations_after_sha256']) {
                throw new RuntimeException('Plan does not match the verified backslash repair: ' . $id);
            }
        }
    }
    offsetWritePrivateJson($options['backup'], $plan);
    $statement = $connection->prepare('UPDATE reports_annotations_optimized SET `from`=?, `to`=?, text=? '
        . 'WHERE id=? AND report_id=? AND `from`=? AND `to`=? AND BINARY text=BINARY ?');
    $changes = 0;
    foreach ($plan['documents'] as $document) {
        $reportId = $document['report_id'];
        foreach ($document['changes'] as $change) {
            $before = $change[$rollback ? 'after' : 'before'];
            $after = $change[$rollback ? 'before' : 'after'];
            $annotationId = $change['id'];
            $statement->bind_param('iisiiiis', $after['from'], $after['to'], $after['text'], $annotationId,
                $reportId, $before['from'], $before['to'], $before['text']);
            $statement->execute();
            if ($statement->affected_rows !== 1) throw new RuntimeException('Annotation changed during repair.');
            $changes++;
        }
        $report = offsetReadReport($connection, $reportId);
        if (AnnotationOffsetAudit::annotationHash($report['annotations']) !== $document[$rollback ? 'annotations_before_sha256' : 'annotations_after_sha256']) {
            throw new RuntimeException('Post-write verification failed.');
        }
    }
    $connection->commit();
    echo json_encode(array('mode' => $rollback ? 'rollback' : 'apply', 'reports' => count($seen), 'changes' => $changes, 'committed' => true)), "\n";
} catch (Throwable $error) {
    if ($connection) $connection->rollback();
    // Never dump a database exception/DSN or corpus text to terminal/logs.
    $message = $error instanceof RuntimeException ? $error->getMessage() : get_class($error);
    if ($error instanceof mysqli_sql_exception) $message = 'Database operation failed (code ' . $error->getCode() . ').';
    fwrite(STDERR, $message . "\n");
    exit(1);
}
