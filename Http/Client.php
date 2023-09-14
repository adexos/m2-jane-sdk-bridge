<?php

declare(strict_types=1);

namespace Adexos\JaneSDKBridge\Http;

use Adexos\JaneSDKBridge\Http\Plugins\Auth\ApiKeyPluginWrapper;
use Adexos\JaneSDKBridge\Http\Plugins\Auth\BearerPluginWrapper;
use Adexos\JaneSDKBridge\Http\Plugins\Auth\BasicAuthPluginWrapper;
use Adexos\JaneSDKBridge\Http\Plugins\HttpPluginInterface;
use Http\Client\Common\Plugin;
use Http\Client\Common\PluginClient;
use Http\Discovery\HttpClientDiscovery;
use Jane\Component\OpenApiRuntime\Client\Plugin\AuthenticationRegistry;

class Client
{
    /**
     * @var Plugin[]
     */
    private array $plugins;

    private string $clientName;

    /**
     * @var Plugin[]
     */
    private array $additionalPlugins;

    private ?ApiKeyPluginWrapper $apiKeyPlugin;

    private ?BearerPluginWrapper $bearerPlugin;

    private ?BasicAuthPluginWrapper $basicAuthPlugin;

    private array $clientRegistry = [];

    public function __construct(
        array $plugins,
        string $clientName,
        array $additionalPlugins = [],
        ?ApiKeyPluginWrapper $apiKeyPlugin = null,
        ?BearerPluginWrapper $bearerPlugin = null,
        ?BasicAuthPluginWrapper $basicAuthPlugin = null
    ) {
        $this->plugins = $plugins;
        $this->clientName = $clientName;
        $this->additionalPlugins = $additionalPlugins;
        $this->apiKeyPlugin = $apiKeyPlugin;
        $this->bearerPlugin = $bearerPlugin;
        $this->basicAuthPlugin = $basicAuthPlugin;
    }

    public function getClient(array $authenticationOptions = [])
    {
        $cacheKey = sha1(json_encode($authenticationOptions));
        if (array_key_exists($cacheKey, $this->clientRegistry)) {
            return $this->clientRegistry[$cacheKey];
        }

        $plugins = $this->getPlugins();

        if ($authenticationRegistry = $this->getAuthenticationRegistry($authenticationOptions)) {
            $plugins[] = $authenticationRegistry;
        }

        $httpClient = new PluginClient(HttpClientDiscovery::find(), $plugins);

        $this->clientRegistry[$cacheKey] = $this->clientName::create($httpClient);

        return $this->clientRegistry[$cacheKey];
    }

    /**
     * @return Plugin[]
     */
    private function getPlugins(): array
    {
        $result = array_map(static function(HttpPluginInterface $plugin): ?Plugin {
            return $plugin->create();
        }, array_merge($this->plugins, $this->additionalPlugins));

        // remove null plugins that are not enabled yet
        return array_filter($result);
    }

    private function getAuthenticationRegistry(array $options = []): ?AuthenticationRegistry
    {
        $authenticationPlugins = [];

        if ($this->apiKeyPlugin !== null) {
            $authenticationPlugins[] = $this->apiKeyPlugin->create();
        }

        if ($this->bearerPlugin !== null) {
            $authenticationPlugins[] = $this->bearerPlugin->create($options);
        }

        if ($this->basicAuthPlugin !== null) {
            $authenticationPlugins[] = $this->basicAuthPlugin->create($options);
        }

        if (empty($authenticationPlugins)) {
            return null;
        }

        return new AuthenticationRegistry($authenticationPlugins);
    }
}
