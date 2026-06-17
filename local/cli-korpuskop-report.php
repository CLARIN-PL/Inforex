<?php
/**
 * Uruchomienie Korpuskop z poziomu Inforex.
 *
 * Przykład:
 * php local/cli-korpuskop-report.php \
 *   --config-json /opt/korpuskop/config/document.report.json \
 *   --input /data/dramaty.parquet.zst \
 *   --output dramaty
 */

$enginePath = realpath(implode(DIRECTORY_SEPARATOR, [dirname(__FILE__), '..', 'engine']));
require_once($enginePath . DIRECTORY_SEPARATOR . 'settings.php');
Config::Cfg()->put_path_engine($enginePath);
Config::Cfg()->put_localConfigFilename(realpath($enginePath . '/../config/') . DIRECTORY_SEPARATOR . 'config.local.php');
require_once($enginePath . '/include/integration/KorpuskopRunner.php');
require_once($enginePath . '/cliopt.php');

$opt = new Cliopt();
$opt->addParameter(new ClioptParameter('config-json', 'c', 'PATH', 'Ścieżka do config JSON Korpuskop'));
$opt->addParameter(new ClioptParameter('input-kind', 'k', 'KIND', 'Typ wejścia z Inforex: auto, document albo dialog'));
$opt->addParameter(new ClioptParameter('input', 'i', 'PATH', 'Plik wejściowy .parquet.zst wyeksportowany z Inforex'));
$opt->addParameter(new ClioptParameter('output', 'o', 'PATH', 'Ścieżka wyjściowa raportu / ZIP'));
$opt->addParameter(new ClioptParameter('limit-corpus-size', 'l', 'N', 'Opcjonalny limit dokumentów'));
$opt->addParameter(new ClioptParameter('threads', 't', 'N', 'Liczba wątków'));

try {
    $opt->parseCli(isset($argv) ? $argv : null);

    if (!$opt->exists('input')) {
        throw new RuntimeException('Parametr --input jest wymagany i musi wskazywać plik .parquet.zst wyeksportowany z Inforex.');
    }
    if (!$opt->exists('output')) {
        throw new RuntimeException('Parametr --output jest wymagany.');
    }

    $inputKind = $opt->exists('input-kind') ? $opt->getRequired('input-kind') : KorpuskopRunner::INPUT_KIND_AUTO;
    $runner = new KorpuskopRunner();

    $extraArgs = [];
    if ($opt->exists('limit-corpus-size')) {
        $extraArgs['limit-corpus-size'] = (int) $opt->getRequired('limit-corpus-size');
    }
    if ($opt->exists('threads')) {
        $extraArgs['threads'] = (int) $opt->getRequired('threads');
    }

    $resolvedInputKind = $inputKind === KorpuskopRunner::INPUT_KIND_AUTO
        ? $runner->detectInputKind($opt->getRequired('input'))
        : $inputKind;

    $overrideArgs = $runner->buildInforexExportArgs(
        $opt->getRequired('input'),
        $opt->getRequired('output'),
        $resolvedInputKind,
        $extraArgs
    );

    echo json_encode([
        'stage' => 'inforex_input_detection',
        'input' => $opt->getRequired('input'),
        'input_kind' => $resolvedInputKind,
        'message' => 'Rozpoznano wariant eksportu Inforex.',
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;

    $result = $runner->runWithProgress(
        $opt->exists('config-json') ? $opt->getRequired('config-json') : null,
        $overrideArgs,
        static function (array $event): void {
            echo json_encode($event, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
        }
    );

    if (!empty($result['stderr_lines'])) {
        fwrite(STDERR, implode(PHP_EOL, $result['stderr_lines']) . PHP_EOL);
    }

    echo 'PROGRESS_FILE=' . $result['progress_file'] . PHP_EOL;
    echo 'EXIT_CODE=' . $result['exit_code'] . PHP_EOL;

    exit((int) $result['exit_code']);
} catch (Exception $ex) {
    fwrite(STDERR, 'Error: ' . $ex->getMessage() . PHP_EOL);
    exit(1);
}
