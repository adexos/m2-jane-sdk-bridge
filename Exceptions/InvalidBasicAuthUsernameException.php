<?php

declare(strict_types=1);

namespace Adexos\JaneSDKBridge\Exceptions;

use Exception;

class InvalidBasicAuthUsernameException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            'Empty basic auth username on client instantiation',
            400
        );
    }
}