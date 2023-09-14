# SDK Bridge from JanePHP to Magento 2

# How to install

You can install it by typing : `composer require adexos/m2-jane-sdk-bridge:^1.0`

# How to use

This SDK Bridge is a tool to easier Jane SDK generated client your implementation through your Magento 2 application.

In your `di.xml`, you have to declare : 

```xml
<!-- Start HTTP Config -->
    <type name="Vendor\MySDKBridge\Http\MyClient">
        <arguments>
            <argument name="client" xsi:type="object">MyHttpClient</argument>
        </arguments>
    </type>

    <virtualType name="MyHttpClient" type="Adexos\JaneSDKBridge\Http\Client">
        <arguments>
            <argument name="plugins" xsi:type="array">
                <item name="hostPlugin" xsi:type="object">MyClientHostPlugin</item>
                <item name="pathPlugin" xsi:type="object">MyClientPathPlugin</item>
            </argument>
            <!-- You can change it if you are willing to override the generated client or extends it -->
            <argument name="clientName" xsi:type="string">PathTo\Generated\Jane\Client</argument>
        </arguments>
    </virtualType>

    <virtualType name="MyClientHostPlugin" type="Adexos\JaneSDKBridge\Http\Plugins\HostPlugin">
        <arguments>
            <argument name="configPath" xsi:type="const">
                Vendor\MySDKBridge\Model\Config::PATH_TO_ENDPOINT_URL_YOU_MUST_DEFINE
            </argument>
        </arguments>
    </virtualType>

    <virtualType name="MyClientPathPlugin" type="Adexos\JaneSDKBridge\Http\Plugins\PathPlugin">
        <arguments>
            <argument name="configPath" xsi:type="const">
                Vendor\MySDKBridge\Model\Config::XPATH_TO_ENDPOINT_URL_YOU_MUST_DEFINE
            </argument>
        </arguments>
    </virtualType>
<!-- End HTTP Config -->
```

Where your `Vendor\MySDKBridge\Http\MyClient` is

```php
<?php

declare(strict_types=1);

namespace Vendor\MySDKBridge\Http;

use Adexos\JaneSDKBridge\Http\Bridge\ClientExtender;

class MyClient
{
    private \Adexos\JaneSDKBridge\Http\Client $client;

    public function __construct(\Adexos\JaneSDKBridge\Http\Client $client)
    {
        $this->client = $client;
    }
    
    public function getClient(): \PathTo\Generated\JaneClient
    {
        //This class allows you to have an attachment point to the HTTP client 
        //and to have hint types because of the generic SDKs 
        //you can do whatever you want here such as doe somes checks or passing Bearer token :
        // $this->tokenStrategy->getToken();
        // return $this->client->getClient(['Bearer' => $token]);
        
        return $this->client->getClient();
    }
}
```

You must extends this specific class to have autocompletion for your injected client.

Then, in a class you can inject :

```php
<?php

declare(strict_types=1);

namespace Vendor\Magento\Helper;

use Magento\Customer\Api\Data\CustomerInterface;
use Vendor\MySDKBridge\Http\MyClient;

class UpdateCustomers
{
    private MyClient $client;

    public function __construct(MyClient $client)
    
    public function updateCustomer(CustomerInterface $customer): void
    {
        //or whatever endpoint available on your JanePHP SDK
        $this->client->getClient()->updateCustomer(['firstname' => $customer->getFirstname()]); 
    }
}
```

## Authentication

You may want to use authentication provided by JanePHP in a generic way (API key or Bearer for example).

JanePHP generates (if needed) an authentication class in the `Authentication` folder of your SDK :

`PathTo\Generated\Authentication\ApiKeyAuthentication`

To do so, use the `ApiKeyPluginWrapper` provided in this module and add it to your plugins :

You can use following auth methods :
- Bearer authorization
- Basic auth authorization

```xml
    <type name="Vendor\MySDKBridge\Http\MyClient">
        <arguments>
            <argument name="client" xsi:type="object">MyHttpClient</argument>
        </arguments>
    </type>

    <virtualType name="MyHttpClient" type="Adexos\JaneSDKBridge\Http\Client">
        <arguments>
            <argument name="plugins" xsi:type="array">
                <!-- Your plugins... -->
            </argument>
            <argument name="apiKeyPlugin" xsi:type="object">MyApiKeyPlugin</argument>
            <argument name="bearerPlugin" xsi:type="object">MyBearerPlugin</argument>
            <argument name="basicAuthPlugin" xsi:type="object">MyBasicAuthPlugin</argument>
        </arguments>
    </type>

    <virtualType name="MyApiKeyPlugin" type="Adexos\JaneSDKBridge\Http\Plugins\Auth\ApiKeyPluginWrapper">
        <arguments>
            <argument name="authenticationPluginClass" xsi:type="string">
                PathTo\Generated\Authentication\ApiKeyAuthentication
            </argument>
            <!-- XML path to your ENCRYPTED API Key -->
            <argument name="configPath" xsi:type="const">
                Vendor\MySDKBridge\Model\Config::XML_PATH_ZDFR_CUSTOMER_SDK_API_KEY
            </argument>
        </arguments>
    </virtualType>
    
    <!-- Not needed if no Bearer are required -->
    <virtualType name="MyBearerPlugin" type="Adexos\JaneSDKBridge\Http\Plugins\Auth\BearerPluginWrapper">
        <arguments>
            <argument name="bearerPluginClass" xsi:type="string">
                PathTo\Generated\Authentication\Bearer
            </argument>
        </arguments>
    </virtualType>
    
    <!-- Not needed if no Basic auth are required -->
    <virtualType name="MyBasicAuthPlugin" type="Adexos\JaneSDKBridge\Http\Plugins\Auth\BasicAuthPluginWrapper">
        <arguments>
            <argument name="basicAuthPluginClass" xsi:type="string">
                PathTo\Generated\Authentication\BasicAuth
            </argument>
            <argument name="configPathUsername" xsi:type="const">
                Vendor\MySDKBridge\Model\Config::XML_PATH_API_BASIC_AUTH_USERNAME
            </argument>
            <argument name="configPathPassword" xsi:type="const">
                Vendor\MySDKBridge\Model\Config::XML_PATH_API_BASIC_AUTH_PASSWORD
            </argument>
        </arguments>
    </virtualType>
```

> /!\ Be aware that your API key **MUST** be [encrypted](https://www.magevision.com/blog/post/decrypt-an-encrypted-config-value-magento-2).
> This behavior won't be modified for security reasons.

## Disclaimer

Your PHP application must implement its own `Client` or have some packages installed to handle :

- `Psr\Http\Client\ClientInterface` PSR-18
- `Psr\Http\Message\RequestFactoryInterface` PSR-17
- `Psr\Http\Message\StreamFactoryInterface` PSR-17

In this package : 
- PSR-18 is handled by `symfony/http-client >= 4.4`
- PSR-17 is handled by `nyholm/psr7 ^1.4`

You can use the package you want as long as it respects the PSR standards.

## Logger

- The bridge implement the http logger plugin and if you active this feature you can log all request and response to a loggerInterface. You can admin this in Configuration section of your magento. 