<?php

declare(strict_types=1);

/**
 * Integracja Inforex -> Korpuskop.
 *
 * Klasa uruchamia binarkę Korpuskop jako zewnętrzny proces, przekazuje
 * konfigurację JSON i odbiera postęp w formacie NDJSON ze stderr.
 *
 * Uwaga: warstwa integracyjna Inforex celowo obsługuje wyłącznie eksporty
 * `.parquet.zst` przygotowane przez Inforex, w dwóch wariantach:
 * - document -> clarin-optimized-parquet
 * - dialog   -> dialog-parquet
 */
class KorpuskopRunner
{
    public const INPUT_KIND_AUTO = 'auto';
    public const INPUT_KIND_DOCUMENT = 'document';
    public const INPUT_KIND_DIALOG = 'dialog';

    private string $binaryPath;
    private string $workingDirectory;
    private string $defaultConfigPath;
    private string $progressDir;
    private ?string $workerUrl;

    public function __construct(
        ?string $binaryPath = null,
        ?string $workingDirectory = null,
        ?string $defaultConfigPath = null,
        ?string $progressDir = null
    ) {
        $this->binaryPath = $binaryPath ?: (string) Config::Cfg()->get_korpuskopBinary();
        $this->workingDirectory = $workingDirectory ?: dirname((string) Config::Cfg()->get_path_engine());
        $this->defaultConfigPath = $defaultConfigPath ?: (string) Config::Cfg()->get_korpuskopDefaultConfig();
        $this->progressDir = $progressDir ?: (string) Config::Cfg()->get_korpuskopProgressDir();
        $this->workerUrl = $this->getOptionalWorkerUrl();
    }

    /**
     * @param array<string,string|int|bool|array<mixed>> $overrideArgs
     * @param callable(array<string,mixed>):void|null $onProgress
     * @return array{exit_code:int,stdout:string,stderr_lines:array<int,string>,progress_file:string}
     */
    public function runWithProgress(
        ?string $configPath = null,
        array $overrideArgs = [],
        ?callable $onProgress = null
    ): array {
        if ($this->workerUrl !== null && $this->workerUrl !== '') {
            return $this->runWithProgressRemote($configPath, $overrideArgs, $onProgress);
        }

        return $this->runWithProgressLocal($configPath, $overrideArgs, $onProgress);
    }

    /**
     * @param array<string,string|int|bool|array<mixed>> $overrideArgs
     * @param callable(array<string,mixed>):void|null $onProgress
     * @return array{exit_code:int,stdout:string,stderr_lines:array<int,string>,progress_file:string}
     */
    private function runWithProgressLocal(
        ?string $configPath = null,
        array $overrideArgs = [],
        ?callable $onProgress = null
    ): array {
        $configPath = $configPath ?: $this->defaultConfigPath;
        if (!is_file($this->binaryPath)) {
            throw new RuntimeException("Brak binarki Korpuskop: {$this->binaryPath}");
        }
        if (!is_file($configPath)) {
            throw new RuntimeException("Brak pliku config JSON: {$configPath}");
        }
        if (!is_dir($this->progressDir) && !mkdir($this->progressDir, 0775, true) && !is_dir($this->progressDir)) {
            throw new RuntimeException("Nie można utworzyć katalogu progress: {$this->progressDir}");
        }

        $this->validateOverrideArgs($overrideArgs);
        $progressFile = $this->buildProgressFilePath();
        $command = $this->buildCommand($configPath, $overrideArgs, $progressFile);

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptors, $pipes, $this->workingDirectory, $this->buildRuntimeEnv());
        if (!is_resource($process)) {
            throw new RuntimeException('Nie udało się uruchomić procesu Korpuskop.');
        }

        fclose($pipes[0]);
        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);

        $stdoutBuffer = '';
        $stderrBuffer = '';
        $stderrLines = [];

        while (true) {
            $status = proc_get_status($process);

            $stdoutChunk = stream_get_contents($pipes[1]);
            if ($stdoutChunk !== false && $stdoutChunk !== '') {
                $stdoutBuffer .= $stdoutChunk;
            }

            $stderrChunk = stream_get_contents($pipes[2]);
            if ($stderrChunk !== false && $stderrChunk !== '') {
                $stderrBuffer .= $stderrChunk;
                while (($newlinePos = strpos($stderrBuffer, "\n")) !== false) {
                    $line = trim(substr($stderrBuffer, 0, $newlinePos));
                    $stderrBuffer = (string) substr($stderrBuffer, $newlinePos + 1);
                    if ($line === '') {
                        continue;
                    }
                    $decoded = json_decode($line, true);
                    if (is_array($decoded)) {
                        if ($onProgress !== null) {
                            $onProgress($decoded);
                        }
                    } else {
                        $stderrLines[] = $line;
                    }
                }
            }

            if (!$status['running']) {
                break;
            }
            usleep(200000);
        }

        $stdoutTail = stream_get_contents($pipes[1]);
        if ($stdoutTail !== false && $stdoutTail !== '') {
            $stdoutBuffer .= $stdoutTail;
        }
        $stderrTail = stream_get_contents($pipes[2]);
        if ($stderrTail !== false && $stderrTail !== '') {
            foreach (preg_split("/\r?\n/", trim($stderrTail)) as $line) {
                if ($line === '') {
                    continue;
                }
                $decoded = json_decode($line, true);
                if (is_array($decoded)) {
                    if ($onProgress !== null) {
                        $onProgress($decoded);
                    }
                } else {
                    $stderrLines[] = $line;
                }
            }
        }

        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        return [
            'exit_code' => $exitCode,
            'stdout' => $stdoutBuffer,
            'stderr_lines' => $stderrLines,
            'progress_file' => $progressFile,
        ];
    }

    /**
     * @param array<string,string|int|bool|array<mixed>> $overrideArgs
     * @param callable(array<string,mixed>):void|null $onProgress
     * @return array{exit_code:int,stdout:string,stderr_lines:array<int,string>,progress_file:string}
     */
    private function runWithProgressRemote(
        ?string $configPath = null,
        array $overrideArgs = [],
        ?callable $onProgress = null
    ): array {
        $configPath = $configPath ?: $this->defaultConfigPath;
        if (!is_file($configPath)) {
            throw new RuntimeException("Brak pliku config JSON: {$configPath}");
        }
        if (!is_dir($this->progressDir) && !mkdir($this->progressDir, 0775, true) && !is_dir($this->progressDir)) {
            throw new RuntimeException("Nie można utworzyć katalogu progress: {$this->progressDir}");
        }

        $this->validateOverrideArgs($overrideArgs);
        $progressFile = $this->buildProgressFilePath();
        $command = $this->buildCommand($configPath, $overrideArgs, $progressFile);

        $start = $this->httpJsonRequest(
            'POST',
            rtrim((string) $this->workerUrl, '/') . '/run',
            array(
                'command' => $command,
                'cwd' => '/opt/korpuskop',
                'progress_file' => $progressFile,
                'env' => $this->buildRuntimeEnv(),
            )
        );
        if (!isset($start['job_id'])) {
            throw new RuntimeException('Worker Korpuskop nie zwrócił identyfikatora zadania.');
        }

        $jobId = (string) $start['job_id'];
        $lastSeq = 0;
        while (true) {
            usleep(250000);
            $status = $this->httpJsonRequest(
                'GET',
                rtrim((string) $this->workerUrl, '/') . '/status/' . rawurlencode($jobId) . '?after_seq=' . intval($lastSeq)
            );

            if (isset($status['events']) && is_array($status['events'])) {
                foreach ($status['events'] as $event) {
                    if (isset($event['seq'])) {
                        $lastSeq = max($lastSeq, intval($event['seq']));
                    }
                    if ($onProgress !== null && isset($event['payload']) && is_array($event['payload'])) {
                        $onProgress($event['payload']);
                    }
                }
            }

            if (empty($status['running'])) {
                return array(
                    'exit_code' => isset($status['exit_code']) ? intval($status['exit_code']) : 1,
                    'stdout' => isset($status['stdout']) ? (string) $status['stdout'] : '',
                    'stderr_lines' => isset($status['stderr_lines']) && is_array($status['stderr_lines']) ? $status['stderr_lines'] : array(),
                    'progress_file' => isset($status['progress_file']) ? (string) $status['progress_file'] : $progressFile,
                );
            }
        }
    }

    public function detectInputKind(string $inputPath): string
    {
        $this->assertSupportedInforexExportFile($inputPath, self::INPUT_KIND_AUTO);

        $scriptPath = $this->getDetectorScriptPath();
        if (!is_file($scriptPath)) {
            throw new RuntimeException("Brak skryptu detekcji formatu: {$scriptPath}");
        }

        $command = ['python3', $scriptPath, $inputPath];
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptors, $pipes, $this->workingDirectory);
        if (!is_resource($process)) {
            throw new RuntimeException('Nie udało się uruchomić detekcji formatu eksportu Inforex.');
        }

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            $details = trim((string) $stderr);
            throw new RuntimeException(
                'Nie udało się automatycznie rozpoznać wariantu eksportu Inforex.'
                . ($details !== '' ? ' ' . $details : '')
            );
        }

        $detected = trim((string) $stdout);
        if (!in_array($detected, [self::INPUT_KIND_DOCUMENT, self::INPUT_KIND_DIALOG], true)) {
            throw new RuntimeException('Detektor eksportu Inforex zwrócił nieobsługiwany wynik.');
        }

        return $detected;
    }

    /**
     * Buduje nadpisania CLI dla jedynego wspieranego wejścia z Inforex.
     *
     * @param array<string,string|int|bool|array<mixed>> $extraArgs
     * @return array<string,string|int|bool|array<mixed>>
     */
    public function buildInforexExportArgs(
        string $inputPath,
        string $outputPath,
        string $inputKind = self::INPUT_KIND_AUTO,
        array $extraArgs = []
    ): array {
        $normalizedInputKind = $inputKind === self::INPUT_KIND_AUTO ? $this->detectInputKind($inputPath) : $inputKind;
        $inputFormat = $this->normalizeInputKindToFormat($normalizedInputKind);
        $this->assertSupportedInforexExportFile($inputPath, $normalizedInputKind);

        return array_merge([
            'input' => [$inputPath],
            'output' => $outputPath,
            'input-format' => $inputFormat,
        ], $extraArgs);
    }

    /**
     * @param array<string,string|int|bool|array<mixed>> $overrideArgs
     */
    private function validateOverrideArgs(array $overrideArgs): void
    {
        if (!array_key_exists('input-format', $overrideArgs) && !array_key_exists('input_format', $overrideArgs)) {
            return;
        }

        $format = (string) ($overrideArgs['input-format'] ?? $overrideArgs['input_format']);
        if (!in_array($format, ['clarin-optimized-parquet', 'dialog-parquet'], true)) {
            throw new RuntimeException(
                'Integracja Inforex -> Korpuskop obsługuje wyłącznie exporty Inforex w formatach: clarin-optimized-parquet albo dialog-parquet.'
            );
        }

        $inputs = $overrideArgs['input'] ?? $overrideArgs['inputs'] ?? null;
        if ($inputs === null) {
            return;
        }

        $items = is_array($inputs) ? $inputs : [$inputs];
        foreach ($items as $inputPath) {
            $this->assertSupportedInforexExportFile(
                (string) $inputPath,
                $format === 'dialog-parquet' ? self::INPUT_KIND_DIALOG : self::INPUT_KIND_DOCUMENT
            );
        }
    }

    private function normalizeInputKindToFormat(string $inputKind): string
    {
        if ($inputKind === self::INPUT_KIND_DOCUMENT) {
            return 'clarin-optimized-parquet';
        }
        if ($inputKind === self::INPUT_KIND_DIALOG) {
            return 'dialog-parquet';
        }

        throw new RuntimeException(
            sprintf(
                'Nieobsługiwany input-kind `%s`. Dozwolone: `%s`, `%s`, `%s`.',
                $inputKind,
                self::INPUT_KIND_AUTO,
                self::INPUT_KIND_DOCUMENT,
                self::INPUT_KIND_DIALOG
            )
        );
    }

    private function assertSupportedInforexExportFile(string $inputPath, string $inputKind): void
    {
        if (!is_file($inputPath)) {
            throw new RuntimeException("Brak pliku wejściowego: {$inputPath}");
        }
        if (!preg_match('/\.parquet\.zst$/', $inputPath)) {
            throw new RuntimeException(
                'Integracja Inforex -> Korpuskop przyjmuje wyłącznie pliki wyeksportowane z Inforex w formacie .parquet.zst.'
            );
        }
        if (!in_array($inputKind, [self::INPUT_KIND_AUTO, self::INPUT_KIND_DOCUMENT, self::INPUT_KIND_DIALOG], true)) {
            throw new RuntimeException('Nieobsługiwany typ wejścia Inforex.');
        }
    }

    private function getDetectorScriptPath(): string
    {
        return dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'local' . DIRECTORY_SEPARATOR . 'detect-korpuskop-parquet-kind.py';
    }

    private function getOptionalWorkerUrl(): ?string
    {
        try {
            $value = (string) Config::Cfg()->get_korpuskopWorkerUrl();
            return $value !== '' ? $value : null;
        } catch (ConfigException $ex) {
            return null;
        }
    }

    private function buildProgressFilePath(): string
    {
        return $this->progressDir . DIRECTORY_SEPARATOR . 'korpuskop_' . date('Ymd_His') . '_' . uniqid('', true) . '.json';
    }

    /**
     * @return array<string,string>
     */
    private function buildRuntimeEnv(): array
    {
        $env = array();
        try {
            $apiKey = trim((string) Config::Cfg()->get_lpmn_api_key());
            if ($apiKey !== '') {
                $env['CLARIN_OAPI_KEY'] = $apiKey;
            }
        } catch (ConfigException $ex) {
        }
        try {
            $apiUrl = trim((string) Config::Cfg()->get_lpmn_api_url());
            if ($apiUrl !== '') {
                $env['CLARIN_OAPI_URL'] = $apiUrl;
            }
        } catch (ConfigException $ex) {
        }

        return $env;
    }

    /**
     * @param array<string,string|int|bool|array<mixed>> $overrideArgs
     * @return array<int,string>
     */
    private function buildCommand(string $configPath, array $overrideArgs, string $progressFile): array
    {
        $command = array(
            $this->binaryPath,
            '--config-json',
            $configPath,
            '--progress-json',
            '--progress-file',
            $progressFile,
        );

        foreach ($this->normalizeOverrideArgs($overrideArgs) as $item) {
            $command[] = $item;
        }

        return $command;
    }

    /**
     * @param array<string,mixed>|null $payload
     * @return array<string,mixed>
     */
    private function httpJsonRequest(string $method, string $url, ?array $payload = null): array
    {
        $headers = array("Content-Type: application/json");
        $options = array(
            'http' => array(
                'method' => $method,
                'header' => implode("\r\n", $headers),
                'ignore_errors' => true,
                'timeout' => 300,
            ),
        );
        if ($payload !== null) {
            $options['http']['content'] = json_encode($payload);
        }

        $context = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            throw new RuntimeException('Nie udało się połączyć z workerem Korpuskop: ' . $url);
        }

        $statusCode = 0;
        if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $matches)) {
            $statusCode = intval($matches[1]);
        }

        $decoded = json_decode($response, true);
        if (!is_array($decoded)) {
            throw new RuntimeException('Worker Korpuskop zwrócił niepoprawny JSON.');
        }
        if ($statusCode >= 400) {
            $message = isset($decoded['message']) ? $decoded['message'] : 'Błąd workera Korpuskop.';
            throw new RuntimeException((string) $message);
        }

        return $decoded;
    }

    /**
     * @param array<string,string|int|bool|array<mixed>> $overrideArgs
     * @return array<int,string>
     */
    private function normalizeOverrideArgs(array $overrideArgs): array
    {
        $result = [];
        foreach ($overrideArgs as $key => $value) {
            $option = '--' . str_replace('_', '-', $key);
            if (is_bool($value)) {
                if ($value) {
                    $result[] = $option;
                }
                continue;
            }
            if (is_array($value)) {
                foreach ($value as $item) {
                    $result[] = $option;
                    $result[] = (string) $item;
                }
                continue;
            }
            $result[] = $option;
            $result[] = (string) $value;
        }
        return $result;
    }
}
