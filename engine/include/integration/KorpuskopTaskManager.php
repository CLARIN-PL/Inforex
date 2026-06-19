<?php

require_once dirname(__FILE__) . '/../database/CDbKorpuskopRun.php';
require_once dirname(__FILE__) . '/KorpuskopRunner.php';

class KorpuskopTaskManager
{
    const TASK_TYPE = 'korpuskop';

    static function createFromExport($corpusId, array $export, $inputKind, $userId = null, array $options = array())
    {
        global $db;

        $corpusId = intval($corpusId);
        if ($corpusId <= 0) {
            throw new RuntimeException('Nieprawidłowy identyfikator korpusu dla zadania Korpuskop.');
        }
        if (!isset($export['export_id'])) {
            throw new RuntimeException('Brak identyfikatora eksportu dla zadania Korpuskop.');
        }
        if (!isset($export['status']) || $export['status'] !== 'done') {
            throw new RuntimeException('Raport Korpuskop można uruchomić dopiero po zakończonym eksporcie.');
        }

        $inputKind = self::normalizeInputKind($inputKind);
        $focusWords = self::normalizeFocusWords(isset($options['focus_words']) ? $options['focus_words'] : array());
        $exportId = intval($export['export_id']);
        $lockName = self::buildLockName($corpusId, $exportId, $inputKind, $focusWords);
        $lockAcquired = false;

        try {
            $lockAcquired = self::acquireLock($lockName);

            $existingTask = self::findTaskByExport($corpusId, $exportId, $inputKind, $focusWords, array('new', 'process', 'done'));
            if ($existingTask && !empty($existingTask['task_id'])) {
                self::ensureRunExists($corpusId, $existingTask, $export, $inputKind, $userId);
                return intval($existingTask['task_id']);
            }

            $inputPath = self::getExportFilePath($export);
            if (!is_file($inputPath)) {
                throw new RuntimeException('Nie znaleziono pliku wejściowego eksportu dla raportu Korpuskop: ' . $inputPath);
            }

            $configPath = self::getConfigPathForKind($inputKind);
            if ($configPath === '' || !is_file($configPath)) {
                throw new RuntimeException('Brak konfiguracji Korpuskop dla wybranego typu korpusu.');
            }

            $outputPath = self::buildOutputPath($corpusId, $exportId, $inputKind);
            self::ensureParentDirectory($outputPath);

            $parameters = array(
                'export_id' => $exportId,
                'input' => $inputPath,
                'output' => $outputPath,
                'config_json' => $configPath,
                'input_kind' => $inputKind,
            );
            if (!empty($focusWords)) {
                $parameters['focus_words'] = $focusWords;
            }

            $payload = array(
                'stage' => 'export_done',
                'export_id' => $exportId,
                'input_kind' => $inputKind,
                'focus_words' => $focusWords,
                'message' => 'Eksport zakończony. Zadanie Korpuskop oczekuje w kolejce.',
            );

            $db->insert('tasks', array(
                'user_id' => $userId ? intval($userId) : null,
                'corpus_id' => $corpusId,
                'type' => self::TASK_TYPE,
                'description' => self::buildTaskDescription($exportId, $inputKind),
                'parameters' => json_encode($parameters),
                'max_steps' => 100,
                'current_step' => 0,
                'status' => 'new',
                'message' => json_encode($payload),
            ));

            $taskId = intval($db->last_id());
            self::ensureRunExists($corpusId, array(
                'task_id' => $taskId,
                'user_id' => $userId ? intval($userId) : null,
                'datetime' => date('Y-m-d H:i:s'),
                'parameters' => json_encode($parameters),
            ), $export, $inputKind, $userId, $outputPath, $configPath);

            return $taskId;
        } finally {
            if ($lockAcquired) {
                self::releaseLock($lockName);
            }
        }
    }

    static function findTaskByExport($corpusId, $exportId, $inputKind = null, $focusWords = array(), $statuses = null)
    {
        global $db;

        $corpusId = intval($corpusId);
        $exportId = intval($exportId);
        $statuses = is_array($statuses) ? array_values($statuses) : array('new', 'process', 'done');

        $sql = "SELECT task_id, user_id, corpus_id, datetime, status, parameters
                FROM tasks
                WHERE corpus_id = ? AND type = ?";
        $params = array($corpusId, self::TASK_TYPE);
        if (!empty($statuses)) {
            $placeholders = implode(',', array_fill(0, count($statuses), '?'));
            $sql .= " AND status IN (" . $placeholders . ")";
            foreach ($statuses as $status) {
                $params[] = strval($status);
            }
        }
        $sql .= " ORDER BY task_id DESC LIMIT 200";

        $normalizedFocusWords = self::normalizeFocusWords($focusWords);
        $rows = $db->fetch_rows($sql, $params);
        foreach ($rows as $row) {
            $taskParams = json_decode($row['parameters'], true);
            if (!is_array($taskParams)) {
                continue;
            }
            if (intval(isset($taskParams['export_id']) ? $taskParams['export_id'] : 0) !== $exportId) {
                continue;
            }
            if ($inputKind !== null && $inputKind !== '' && (string) (isset($taskParams['input_kind']) ? $taskParams['input_kind'] : '') !== (string) $inputKind) {
                continue;
            }
            if (self::normalizeFocusWords(isset($taskParams['focus_words']) ? $taskParams['focus_words'] : array()) !== $normalizedFocusWords) {
                continue;
            }
            return $row;
        }

        return null;
    }

    static function getLinkedTaskIdForExport($corpusId, $exportId, $inputKind = null, $focusWords = array())
    {
        $task = self::findTaskByExport($corpusId, $exportId, $inputKind, $focusWords, array('new', 'process', 'done'));
        return $task && !empty($task['task_id']) ? intval($task['task_id']) : null;
    }

    private static function normalizeInputKind($inputKind)
    {
        $inputKind = trim((string) $inputKind);
        if (!in_array($inputKind, array(KorpuskopRunner::INPUT_KIND_DOCUMENT, KorpuskopRunner::INPUT_KIND_DIALOG), true)) {
            throw new RuntimeException('Nieobsługiwany typ korpusu dla Korpuskop.');
        }
        return $inputKind;
    }

    private static function buildTaskDescription($exportId, $inputKind)
    {
        return sprintf('Korpuskop report (%s) for export #%d', $inputKind === KorpuskopRunner::INPUT_KIND_DIALOG ? 'dialogi' : 'dokumenty', intval($exportId));
    }

    private static function getExportFilePath(array $export)
    {
        $format = isset($export['export_format']) ? (string) $export['export_format'] : 'legacy';
        $extension = in_array($format, array('clarin_parquet_zst', 'clarin_jsonl_zst', 'dialog_parquet_zst'), true) ? 'parquet.zst' : 'zip';
        return Config::Cfg()->get_path_exports() . DIRECTORY_SEPARATOR . sprintf('inforex_export_%d.%s', intval($export['export_id']), $extension);
    }

    private static function getConfigPathForKind($inputKind)
    {
        $candidates = array();
        if ($inputKind === KorpuskopRunner::INPUT_KIND_DIALOG) {
            $candidates[] = self::getOptionalConfigValue('korpuskopDialogConfig', 'korpuskopDefaultConfig');
            $candidates[] = '/opt/korpuskop/config/dialog.report.json';
            $candidates[] = '/opt/korpuskop/config/dramaty.report.json';
        } else {
            $candidates[] = self::getOptionalConfigValue('korpuskopDocumentConfig', 'korpuskopDefaultConfig');
            $candidates[] = '/opt/korpuskop/config/document.report.json';
            $candidates[] = '/opt/korpuskop/config/dialog.report.json';
            $candidates[] = '/opt/korpuskop/config/dramaty.report.json';
        }
        foreach ($candidates as $candidate) {
            $candidate = trim((string) $candidate);
            if ($candidate !== '' && is_file($candidate)) {
                return $candidate;
            }
        }
        return '';
    }

    private static function getOptionalConfigValue($primaryName, $fallbackName)
    {
        $cfg = Config::Cfg();
        $primaryGetter = 'get_' . $primaryName;
        $fallbackGetter = 'get_' . $fallbackName;
        try {
            $value = (string) $cfg->$primaryGetter();
            if ($value !== '') {
                return $value;
            }
        } catch (Exception $ex) {
        }
        try {
            return (string) $cfg->$fallbackGetter();
        } catch (Exception $ex) {
            return '';
        }
    }

    private static function buildOutputPath($corpusId, $exportId, $inputKind)
    {
        $baseDir = rtrim((string) Config::Cfg()->get_korpuskopOutputDir(), DIRECTORY_SEPARATOR);
        if ($baseDir === '') {
            throw new RuntimeException('Brak skonfigurowanego katalogu wyjściowego Korpuskop.');
        }
        $suffix = $inputKind === KorpuskopRunner::INPUT_KIND_DIALOG ? 'dialog' : 'document';
        return $baseDir . DIRECTORY_SEPARATOR . sprintf('korpuskop_report_corpus_%d_export_%d_%s.zip', intval($corpusId), intval($exportId), $suffix);
    }

    private static function ensureParentDirectory($path)
    {
        $directory = dirname($path);
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException('Nie można utworzyć katalogu wynikowego Korpuskop: ' . $directory);
        }
    }

    private static function ensureRunExists($corpusId, array $task, array $export, $inputKind, $userId = null, $outputPath = null, $configPath = null)
    {
        $taskId = intval($task['task_id']);
        if ($taskId <= 0) {
            return;
        }
        $existingRun = DbKorpuskopRun::getRunByTask($taskId, $corpusId);
        if ($existingRun && isset($existingRun['run_id'])) {
            return;
        }

        $taskParams = isset($task['parameters']) ? json_decode($task['parameters'], true) : array();
        if (!is_array($taskParams)) {
            $taskParams = array();
        }
        $resolvedOutputPath = $outputPath ?: (isset($taskParams['output']) ? $taskParams['output'] : self::buildOutputPath($corpusId, $export['export_id'], $inputKind));
        $resolvedConfigPath = $configPath ?: (isset($taskParams['config_json']) ? $taskParams['config_json'] : self::getConfigPathForKind($inputKind));

        DbKorpuskopRun::insertRun(array(
            'task_id' => $taskId,
            'corpus_id' => intval($corpusId),
            'user_id' => $userId ? intval($userId) : (isset($task['user_id']) ? intval($task['user_id']) : null),
            'input_path' => self::getExportFilePath($export),
            'input_kind' => $inputKind,
            'output_path' => $resolvedOutputPath,
            'config_json_path' => $resolvedConfigPath !== '' ? $resolvedConfigPath : null,
            'progress_file' => null,
            'status' => isset($task['status']) ? $task['status'] : 'new',
            'exit_code' => null,
            'message' => 'Zadanie Korpuskop oczekuje w kolejce.',
            'file_size' => is_file($resolvedOutputPath) ? filesize($resolvedOutputPath) : null,
            'created_at' => isset($task['datetime']) ? $task['datetime'] : date('Y-m-d H:i:s'),
            'finished_at' => null,
        ));
    }

    private static function buildLockName($corpusId, $exportId, $inputKind, array $focusWords = array())
    {
        $fingerprint = md5(json_encode(array(
            intval($corpusId),
            intval($exportId),
            strval($inputKind),
            self::normalizeFocusWords($focusWords),
        )));
        return 'kkexp_' . $fingerprint;
    }

    private static function normalizeFocusWords($focusWords)
    {
        if (!is_array($focusWords)) {
            $focusWords = preg_split('/[\r\n,;]+/', (string) $focusWords);
        }

        $normalized = array();
        foreach ($focusWords as $word) {
            $word = trim((string) $word);
            if ($word === '') {
                continue;
            }
            $normalized[] = $word;
        }

        $normalized = array_values(array_unique($normalized));
        sort($normalized, SORT_NATURAL | SORT_FLAG_CASE);
        return $normalized;
    }

    private static function acquireLock($lockName)
    {
        global $db;

        return intval($db->fetch_one("SELECT GET_LOCK(?, 5)", array($lockName))) === 1;
    }

    private static function releaseLock($lockName)
    {
        global $db;

        $db->fetch_one("SELECT RELEASE_LOCK(?)", array($lockName));
    }
}
