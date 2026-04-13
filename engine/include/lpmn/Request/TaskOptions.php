<?php

namespace Inforex\Lpmn\Request;

class TaskOptions
{
    /** @var array<string, mixed> */
    private $options;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(array $options = array())
    {
        $this->options = $options;
    }

    public function withTaskName($taskName)
    {
        $options = $this->options;
        $options['task_name'] = $taskName;

        return new self($options);
    }

    public function withApplication($application)
    {
        $options = $this->options;
        $options['application'] = $application;

        return new self($options);
    }

    public function withTaskMode($taskMode)
    {
        $options = $this->options;
        $options['task_mode'] = $taskMode;

        return new self($options);
    }

    public function withTaskType($taskType)
    {
        $options = $this->options;
        $options['task_type'] = $taskType;

        return new self($options);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray()
    {
        return $this->options;
    }
}
