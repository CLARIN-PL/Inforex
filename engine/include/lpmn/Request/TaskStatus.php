<?php

namespace Inforex\Lpmn\Request;

final class TaskStatus
{
    const DONE = 'DONE';
    const ERROR = 'ERROR';
    const PROCESSING = 'PROCESSING';
    const QUEUE = 'QUEUE';
    const NONEXISTING = 'NONEXISTING';
    const CANCEL = 'CANCEL';
}
