<?php

declare(strict_types=1);

namespace Adexos\JaneSDKBridge\Http\Plugins\Auth;

use Jane\Component\OpenApiRuntime\Client\AuthenticationPlugin;
use Magento\Framework\ObjectManagerInterface;
use RuntimeException;

use function get_class;

class BearerPluginWrapper implements AuthHttpPluginInterface
{
    private ObjectManagerInterface $objectManager;
    private string $bearerPluginClass;

    public function __construct(ObjectManagerInterface $objectManager, string $bearerPluginClass)
    {
        $this->objectManager = $objectManager;
        $this->bearerPluginClass = $bearerPluginClass;
    }

    public function create(array $options = []): AuthenticationPlugin
    {
        $object = $this->objectManager->create($this->bearerPluginClass, $options);

        if (!$object instanceof AuthenticationPlugin) {
            throw new RuntimeException(
                sprintf(
                    '%s must implements %s',
                    get_class($object),
                    AuthenticationPlugin::class
                )
            );
        }

        return $object;
    }
}
