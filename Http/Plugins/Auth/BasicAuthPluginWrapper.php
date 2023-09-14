<?php

declare(strict_types=1);

namespace Adexos\JaneSDKBridge\Http\Plugins\Auth;

use Adexos\JaneSDKBridge\Exceptions\InvalidBasicAuthUsernameException;
use Adexos\JaneSDKBridge\Exceptions\InvalidBasicAuthPasswordException;
use Http\Client\Common\Plugin;
use Jane\Component\OpenApiRuntime\Client\AuthenticationPlugin;
use Jane\Component\OpenApiRuntime\Client\Plugin\AuthenticationRegistry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class BasicAuthPluginWrapper implements AuthHttpPluginInterface
{
    private string $basicAuthPluginClass;

    private ScopeConfigInterface $scopeConfig;

    private EncryptorInterface $encryptor;

    private string $configPathUsername;

    private string $configPathPassword;

    public function __construct(
        string $basicAuthPluginClass,
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        string $configPathUsername,
        string $configPathPassword
    ) {
        $this->basicAuthPluginClass = $basicAuthPluginClass;
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
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
        if (!($passwordEncrypt = $this->scopeConfig->getValue($this->configPathPassword))) {
            throw new InvalidBasicAuthPasswordException();
        }

        $password = $this->encryptor->decrypt(
            $passwordEncrypt
        );

        return new $this->basicAuthPluginClass($username, $password);
    }
}
