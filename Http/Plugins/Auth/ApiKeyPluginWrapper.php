<?php

declare(strict_types=1);

namespace Adexos\JaneSDKBridge\Http\Plugins\Auth;

use Jane\Component\OpenApiRuntime\Client\AuthenticationPlugin;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ApiKeyPluginWrapper implements AuthHttpPluginInterface
{
    private ScopeConfigInterface $scopeConfig;

    private string $authenticationPluginClass;

    private string $configPath;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        string $authenticationPluginClass,
        string $configPath
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->authenticationPluginClass = $authenticationPluginClass;
        $this->configPath = $configPath;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function create(array $options = []): AuthenticationPlugin
    {
        $apiKey = $this->scopeConfig->getValue($this->configPath);

        return new $this->authenticationPluginClass($apiKey);
    }
}
