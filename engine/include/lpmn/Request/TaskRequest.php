<?php

namespace Inforex\Lpmn\Request;

use Inforex\Lpmn\Http\HttpClient;
use Inforex\Lpmn\Pipeline\InputType;
use RuntimeException;

class TaskRequest
{
    /** @var HttpClient */
    private $client;

    /** @var Token */
    private $token;

    /** @var string */
    private $baseApiUrl;

    /** @var callable|null */
    private $logger;

    public function __construct(HttpClient $client, Token $token, $baseApiUrl)
    {
        $this->client = $client;
        $this->token = $token;
        $this->baseApiUrl = rtrim($baseApiUrl, '/');
        $this->logger = null;
    }

    public function setLogger(callable $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * @param array<int, mixed>|string $lpmn
     */
    public function run($lpmn, $input, $inputType, TaskOptions $taskOptions = null)
    {
        InputType::assertValid($inputType);
        $payload = array(
            'application' => 'postagger',
            'task' => is_string($lpmn) ? json_decode($lpmn, true) : $lpmn,
            'input' => $input,
            'input_type' => $inputType,
        );

        if ($taskOptions !== null) {
            $payload = array_merge($payload, $taskOptions->toArray());
        }

        if (!isset($payload['task_name']) || $payload['task_name'] === '') {
            $payload['task_name'] = $this->generateTaskName();
        }

        if ($inputType === InputType::CORPORA && !isset($payload['task_mode'])) {
            $payload['task_mode'] = 'corpora';
        }

        $this->log('task', 'Starting task with payload: ' . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $response = $this->client->request(
            'POST',
            $this->baseApiUrl . '/tasks/',
            array(
                'Content-Type' => 'application/json',
                $this->token->head() => $this->token->body(),
            ),
            json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        $this->log('task', sprintf('Task start response HTTP %d: %s', $response->getStatusCode(), $response->getBody()));

        if ($response->getStatusCode() !== 200) {
            throw new RuntimeException('Task start failed: ' . $response->getBody());
        }

        $decoded = json_decode($response->getBody(), true);
        if (!is_array($decoded) || !isset($decoded['task_id'])) {
            throw new RuntimeException('Task response does not contain task_id.');
        }

        return $decoded['task_id'];
    }

    /**
     * @return array{status:string,output:string}
     */
    public function taskInfo($taskId)
    {
        $this->log('task', 'Polling task status for task id: ' . $taskId);
        $response = $this->client->request(
            'GET',
            $this->baseApiUrl . '/tasks/' . rawurlencode($taskId),
            array(
                'Content-Type' => 'application/json',
                $this->token->head() => $this->token->body(),
            )
        );

        $this->log('task', sprintf('Task info response HTTP %d: %s', $response->getStatusCode(), $response->getBody()));

        $decoded = json_decode($response->getBody(), true);
        if (!is_array($decoded)) {
            throw new RuntimeException('Invalid task info response: ' . $response->getBody());
        }

        if ($response->getStatusCode() >= 400 && isset($decoded['detail'])) {
            throw new RuntimeException(sprintf('%s Client Error: %s', $response->getStatusCode(), $decoded['detail']));
        }

        if ($response->getStatusCode() !== 200) {
            return array('status' => TaskStatus::NONEXISTING, 'output' => '');
        }

        return array(
            'status' => isset($decoded['status']) ? $decoded['status'] : TaskStatus::NONEXISTING,
            'output' => isset($decoded['output']) && $decoded['output'] !== null ? $decoded['output'] : '',
        );
    }

    public function waitForResults($taskId, $delayInSeconds = 1)
    {
        do {
            $info = $this->taskInfo($taskId);
            $this->log('task', sprintf('Task %s status: %s', $taskId, $info['status']));
            if ($info['status'] === TaskStatus::DONE) {
                $this->log('task', sprintf('Task %s finished with output: %s', $taskId, $info['output']));
                return $info['output'];
            }

            if ($info['status'] === TaskStatus::ERROR || $info['status'] === TaskStatus::CANCEL) {
                throw new RuntimeException('Task ended with status: ' . $info['status']);
            }

            sleep((int) $delayInSeconds);
        } while (true);
    }

    private function log($channel, $message)
    {
        if ($this->logger !== null) {
            call_user_func($this->logger, $channel, $message);
        }
    }

    private function generateTaskName()
    {
        try {
            return 'lpmn-' . bin2hex(random_bytes(6));
        } catch (\Exception $e) {
            return 'lpmn-' . str_replace('.', '', (string) microtime(true));
        }
    }
}
