<?php

declare(strict_types=1);

namespace Adexos\JaneSDKBridge\Http\Plugins;

use Http\Client\Common\Plugin;
use Http\Client\Common\Plugin\AddHostPlugin;
use Http\Discovery\Psr17FactoryDiscovery;
use Magento\Framework\App\Config\ScopeConfigInterface;

class HostPlugin implements HttpPluginInterface
{
    private ScopeConfigInterface $scopeConfig;

    private string $configPath;

    public function __construct(ScopeConfigInterface $scopeConfig, string $configPath)
    {
        $this->scopeConfig = $scopeConfig;
        $this->configPath = $configPath;
    }

    public function create(): ?Plugin
    {
        $uri = Psr17FactoryDiscovery::findUriFactory()->createUri(
            $this->scopeConfig->getValue($this->configPath)
        );

        return new AddHostPlugin($uri);
    }
}
