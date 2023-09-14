<?php

declare(strict_types=1);

namespace Adexos\JaneSDKBridge\Http\Plugins;

use Http\Client\Common\Plugin;

interface HttpPluginInterface
{
    public function create(): ?Plugin;
}
