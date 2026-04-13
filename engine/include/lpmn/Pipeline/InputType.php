<?php

namespace Inforex\Lpmn\Pipeline;

use InvalidArgumentException;

final class InputType
{
    const TEXT = 'text';
    const FILE = 'file';
    const CORPORA = 'corpus';

    public static function assertValid($type)
    {
        $allowed = array(self::TEXT, self::FILE, self::CORPORA);
        if (!in_array($type, $allowed, true)) {
            throw new InvalidArgumentException('Unsupported input type: ' . $type);
        }
    }
}
