<?php

declare(strict_types=1);

namespace Adexos\JaneSDKBridge\Http\Plugins;

use Http\Client\Common\Plugin;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;


class LoggerPlugin implements HttpPluginInterface
{
    private const JANE_LOGGER_ACTIVE_PATH = 'janeSdk/configuration/logger_active';

    private ScopeConfigInterface $scopeConfig;

    private LoggerInterface $logger;

    public function __construct(ScopeConfigInterface $scopeConfig, LoggerInterface $logger)
    {
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }

    public function create(): ?Plugin
    {
        if (!$this->scopeConfig->isSetFlag(self::JANE_LOGGER_ACTIVE_PATH)) {
            return null;
        }

        return new Plugin\LoggerPlugin($this->logger);
    }
}

