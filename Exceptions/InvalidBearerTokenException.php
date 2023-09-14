<?php

declare(strict_types=1);

namespace Adexos\JaneSDKBridge\Exceptions;

class InvalidBearerTokenException extends \Exception
{
    public function __construct()
    {
        parent::__construct(
            'Empty bearer token on client instantiation',
            400
        );
    }
}