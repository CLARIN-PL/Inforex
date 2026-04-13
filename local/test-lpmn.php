<?php

$enginePath = realpath(implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'engine')));
require_once($enginePath . DIRECTORY_SEPARATOR . 'settings.php');

Config::Cfg()->put_path_engine($enginePath);
$localConfigFilename = realpath($enginePath . '/../config/') . DIRECTORY_SEPARATOR . 'config.local.php';
$dockerLocalConfigFilename = realpath($enginePath . '/../docker/www/config/') . DIRECTORY_SEPARATOR . 'config.local.php';
if (!file_exists($localConfigFilename) && file_exists($dockerLocalConfigFilename)) {
    $localConfigFilename = $dockerLocalConfigFilename;
}
Config::Cfg()->put_localConfigFilename($localConfigFilename);

use Inforex\Lpmn\LpmnClientBuilder;
use Inforex\Lpmn\Pipeline\InputType;
use Inforex\Lpmn\Pipeline\Language;
use Inforex\Lpmn\Pipeline\Pipeline;
use Inforex\Lpmn\Pipeline\PipelineBuilder;
use Inforex\Lpmn\Pipeline\PosTaggerPropertiesBuilder;
use Inforex\Lpmn\Request\TaskOptions;

function printUsage()
{
    $script = basename(__FILE__);
    echo "Usage:\n";
    echo "  php local/{$script} [--apikey=KEY] [--api-url=URL] [--text='Ala ma kota.']\n";
    echo "  php local/{$script} [--apikey=KEY] [--api-url=URL] --file=/path/to/file.txt\n";
    echo "\n";
    echo "Options:\n";
    echo "  --apikey   CLARIN API key; when omitted, uses Config::Cfg()->get_lpmn_api_key()\n";
    echo "  --api-url  API base URL; when omitted, uses Config::Cfg()->get_lpmn_api_url()\n";
    echo "  --text     Text input for InputType::TEXT\n";
    echo "  --file     File path for InputType::FILE\n";
    echo "  --task-name Name sent as task_name; when omitted a random one is generated\n";
    echo "  --application Application sent in payload, default: postagger\n";
    echo "  --task-mode Task mode, eg. corpora\n";
    echo "  --task-type Task type, eg. cb\n";
    echo "  --pipeline-json Raw pipeline JSON, eg. '[\"any2txt\", {\"morphodita\": {}}, {\"liner2\": {\"model\": \"n82\"}}]'\n";
    echo "  --quiet    Disable progress logs\n";
    echo "\n";
}

$options = getopt('', array('apikey:', 'api-url::', 'text::', 'file::', 'task-name::', 'application::', 'task-mode::', 'task-type::', 'pipeline-json::', 'quiet'));

$hasText = isset($options['text']) && $options['text'] !== '';
$hasFile = isset($options['file']) && $options['file'] !== '';

if (($hasText && $hasFile) || (!$hasText && !$hasFile)) {
    printUsage();
    fwrite(STDERR, "Provide exactly one of: --text or --file\n");
    exit(1);
}

$inputType = $hasFile ? InputType::FILE : InputType::TEXT;
$input = $hasFile ? $options['file'] : $options['text'];
$quiet = isset($options['quiet']);

if ($hasFile && !is_file($input)) {
    fwrite(STDERR, "File does not exist: {$input}\n");
    exit(1);
}

$logger = $quiet ? null : function ($channel, $message) {
    fwrite(STDERR, '[' . date('Y-m-d H:i:s') . '] [' . strtoupper($channel) . '] ' . $message . "\n");
};

$clientBuilder = new LpmnClientBuilder();

if (isset($options['api-url']) && $options['api-url'] !== '') {
    $clientBuilder->apiUrl($options['api-url']);
}

if (isset($options['apikey']) && $options['apikey'] !== '') {
    $clientBuilder->apikey($options['apikey']);
}

$pipeline = isset($options['pipeline-json']) && $options['pipeline-json'] !== ''
    ? Pipeline::fromJson($options['pipeline-json'])
    : (new PipelineBuilder())
        ->any2Txt()
        ->postagger(
            (new PosTaggerPropertiesBuilder())
                ->methodTagger()
                ->language(Language::POLISH)
                ->taggerType('morphodita')
                ->outputFormat('json')
                ->build()
        )
        ->build();

$taskOptions = new TaskOptions();

if (isset($options['task-name']) && $options['task-name'] !== '') {
    $taskOptions = $taskOptions->withTaskName($options['task-name']);
}

$taskOptions = $taskOptions->withApplication(
    isset($options['application']) && $options['application'] !== '' ? $options['application'] : 'postagger'
);

if (isset($options['task-mode']) && $options['task-mode'] !== '') {
    $taskOptions = $taskOptions->withTaskMode($options['task-mode']);
}

if (isset($options['task-type']) && $options['task-type'] !== '') {
    $taskOptions = $taskOptions->withTaskType($options['task-type']);
}

try {
    if (!$quiet) {
        fwrite(STDERR, '[' . date('Y-m-d H:i:s') . "] [CLI] Starting LPMN test script\n");
    }
    $client = $clientBuilder
        ->logger($logger)
        ->build();
    $resultId = $client->runTask($inputType, $input, $pipeline, $taskOptions);
    $result = $client->downloadResults();

    echo "Result file id: {$resultId}\n";
    echo $result . "\n";
} catch (Throwable $e) {
    fwrite(STDERR, 'LPMN error: ' . $e->getMessage() . "\n");
    exit(1);
}
