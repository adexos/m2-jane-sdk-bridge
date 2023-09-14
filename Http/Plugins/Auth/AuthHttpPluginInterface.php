<?php

declare(strict_types=1);

namespace Adexos\JaneSDKBridge\Http\Plugins\Auth;

use Jane\Component\OpenApiRuntime\Client\AuthenticationPlugin;

interface AuthHttpPluginInterface
{
    public function create(array $options = []): AuthenticationPlugin;
}
