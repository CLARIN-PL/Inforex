<?php

namespace Inforex\Lpmn;

use Inforex\Lpmn\Pipeline\InputType;
use Inforex\Lpmn\Pipeline\Pipeline;
use Inforex\Lpmn\Request\FileRequest;
use Inforex\Lpmn\Request\TaskOptions;
use Inforex\Lpmn\Request\TaskRequest;
use RuntimeException;

class LpmnClient
{
    const DEFAULT_API_URL = 'https://services.clarin-pl.eu/api/v1';

    /** @var FileRequest */
    private $fileRequest;

    /** @var TaskRequest */
    private $taskRequest;

    /** @var string */
    private $fileId = '';

    /** @var string */
    private $resultId = '';

    /** @var callable|null */
    private $logger;

    public function __construct(FileRequest $fileRequest, TaskRequest $taskRequest)
    {
        $this->fileRequest = $fileRequest;
        $this->taskRequest = $taskRequest;
        $this->logger = null;
    }

    public function setLogger(callable $logger = null)
    {
        $this->logger = $logger;
        $this->fileRequest->setLogger($logger);
        $this->taskRequest->setLogger($logger);
    }

    public function uploadFile($filePath)
    {
        $this->log('client', 'Preparing file upload.');
        $this->fileId = $this->fileRequest->uploadFile($filePath);
        $this->log('client', 'Uploaded file id: ' . $this->fileId);
        return $this->fileId;
    }

    public function runTask($inputType, $input, Pipeline $pipeline, TaskOptions $taskOptions = null)
    {
        InputType::assertValid($inputType);
        $this->log('client', 'Running task for input type: ' . $inputType);
        $this->log('client', 'Pipeline: ' . $pipeline->toJson());
        $normalizedInput = $this->normalizeInput($inputType, $input);
        $taskId = $this->taskRequest->run($pipeline->getLpmn(), $normalizedInput, $inputType, $taskOptions);
        $this->log('client', 'Started task id: ' . $taskId);
        $this->resultId = $this->taskRequest->waitForResults($taskId);
        $this->log('client', 'Received result file id: ' . $this->resultId);

        return $this->resultId;
    }

    public function checkTaskById($taskId, $delayInSeconds = 1)
    {
        return $this->taskRequest->waitForResults($taskId, $delayInSeconds);
    }

    public function downloadResults($outputPath = null)
    {
        if ($this->resultId === '') {
            throw new RuntimeException('No result available. Run a task first.');
        }

        $this->log('client', 'Downloading result for file id: ' . $this->resultId);
        if ($outputPath === null) {
            return $this->fileRequest->downloadFileContent($this->resultId);
        }

        $this->fileRequest->downloadFile($this->resultId, $outputPath);
        return $outputPath;
    }

    public function downloadFileById($fileId, $outputPath = null)
    {
        if ($outputPath === null) {
            return $this->fileRequest->downloadFileContent($fileId);
        }

        $this->fileRequest->downloadFile($fileId, $outputPath);
        return $outputPath;
    }

    private function normalizeInput($inputType, $input)
    {
        if ($inputType === InputType::FILE && is_string($input) && is_file($input)) {
            return $this->uploadFile($input);
        }

        return $input;
    }

    private function log($channel, $message)
    {
        if ($this->logger !== null) {
            call_user_func($this->logger, $channel, $message);
        }
    }
}
