<?php

declare(strict_types=1);

namespace Adexos\JaneSDKBridge\Http\Plugins\Auth;

use Adexos\JaneSDKBridge\Exceptions\InvalidBearerTokenException;
use Http\Client\Common\Plugin;
use Jane\Component\OpenApiRuntime\Client\AuthenticationPlugin;
use Jane\Component\OpenApiRuntime\Client\Plugin\AuthenticationRegistry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class BearerPluginWrapper implements AuthHttpPluginInterface
{
    private string $bearerPluginClass;

    public function __construct(string $bearerPluginClass)
    {
        $this->bearerPluginClass = $bearerPluginClass;
    }

    /**
     * @throws InvalidBearerTokenException
     */
    public function create(array $options = []): AuthenticationPlugin
    {
        if (!isset($options['Bearer'])) {
            throw new InvalidBearerTokenException();
        }

        return new $this->bearerPluginClass($options['Bearer']);
    }
}
