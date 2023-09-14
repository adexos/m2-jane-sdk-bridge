<?php

declare(strict_types=1);

namespace Adexos\JaneSDKBridge\Http\Plugins\Auth;

use Http\Client\Common\Plugin;
use Jane\Component\OpenApiRuntime\Client\AuthenticationPlugin;
use Jane\Component\OpenApiRuntime\Client\Plugin\AuthenticationRegistry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class ApiKeyPluginWrapper implements AuthHttpPluginInterface
{
    private ScopeConfigInterface $scopeConfig;

    private EncryptorInterface $encryptor;

    private string $authenticationPluginClass;

    private string $configPath;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        string $authenticationPluginClass,
        string $configPath
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
        $this->authenticationPluginClass = $authenticationPluginClass;
        $this->configPath = $configPath;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function create(array $options = []): AuthenticationPlugin
    {
        $apiKey = $this->encryptor->decrypt(
            $this->scopeConfig->getValue($this->configPath)
        );

        return new $this->authenticationPluginClass($apiKey);
    }
}
