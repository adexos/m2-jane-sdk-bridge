<?php

declare(strict_types=1);

namespace Adexos\JaneSDKBridge\Exceptions;

use Exception;

class InvalidBasicAuthPasswordException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            'Empty basic auth password on client instantiation',
            400
        );
    }
}