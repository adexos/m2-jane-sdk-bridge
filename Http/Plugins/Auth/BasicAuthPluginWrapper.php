<?php

declare(strict_types=1);

namespace Adexos\JaneSDKBridge\Http\Plugins\Auth;

use Adexos\JaneSDKBridge\Exceptions\InvalidBasicAuthUsernameException;
use Adexos\JaneSDKBridge\Exceptions\InvalidBasicAuthPasswordException;
use Jane\Component\OpenApiRuntime\Client\AuthenticationPlugin;
use Magento\Framework\App\Config\ScopeConfigInterface;

class BasicAuthPluginWrapper implements AuthHttpPluginInterface
{
    private string $basicAuthPluginClass;

    private ScopeConfigInterface $scopeConfig;

    private string $configPathUsername;

    private string $configPathPassword;

    public function __construct(
        string $basicAuthPluginClass,
        ScopeConfigInterface $scopeConfig,
        string $configPathUsername,
        string $configPathPassword
    ) {
        $this->basicAuthPluginClass = $basicAuthPluginClass;
        $this->scopeConfig = $scopeConfig;
        $this->configPathUsername = $configPathUsername;
        $this->configPathPassword = $configPathPassword;
    }

    /**
     * @param array $options
     *
     * @return AuthenticationPlugin
     * @throws InvalidBasicAuthPasswordException
     * @throws InvalidBasicAuthUsernameException
     */
    public function create(array $options = []): AuthenticationPlugin
    {
        if (!($username = $this->scopeConfig->getValue($this->configPathUsername))) {
            throw new InvalidBasicAuthUsernameException();
        }

        if (!($password = $this->scopeConfig->getValue($this->configPathPassword))) {
            throw new InvalidBasicAuthPasswordException();
        }

        return new $this->basicAuthPluginClass($username, $password);
    }
}
