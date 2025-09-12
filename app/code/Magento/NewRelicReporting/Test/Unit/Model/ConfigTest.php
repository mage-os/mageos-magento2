<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\NewRelicReporting\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\NewRelicReporting\Model\Config;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for Config model
 */
class ConfigTest extends TestCase
{
    /**
     * @var ScopeConfigInterface|MockObject
     */
    private $scopeConfigMock;

    /**
     * @var EncryptorInterface|MockObject
     */
    private $encryptorMock;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config|MockObject
     */
    private $resourceConfigMock;

    /**
     * @var Config
     */
    private $config;

    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->encryptorMock = $this->createMock(EncryptorInterface::class);
        $this->resourceConfigMock = $this->createMock(\Magento\Config\Model\ResourceModel\Config::class);

        $this->config = new Config($this->scopeConfigMock, $this->encryptorMock, $this->resourceConfigMock);
    }

    /**
     * Test isNewRelicEnabled when enabled
     */
    public function testIsNewRelicEnabledTrue()
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with('newrelicreporting/general/enable')
            ->willReturn(true);

        $this->assertTrue($this->config->isNewRelicEnabled());
    }

    /**
     * Test isNewRelicEnabled when disabled
     */
    public function testIsNewRelicEnabledFalse()
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with('newrelicreporting/general/enable')
            ->willReturn(false);

        $this->assertFalse($this->config->isNewRelicEnabled());
    }

    /**
     * Test getNewRelicApiUrl
     */
    public function testGetNewRelicApiUrl()
    {
        $expectedUrl = 'https://api.newrelic.com/v2/applications/%s/deployments.json';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('newrelicreporting/general/api_url')
            ->willReturn($expectedUrl);

        $this->assertEquals($expectedUrl, $this->config->getNewRelicApiUrl());
    }

    /**
     * Test getNewRelicApiKey with encrypted value
     */
    public function testGetNewRelicApiKey()
    {
        $encryptedKey = 'encrypted_api_key_value';
        $decryptedKey = 'NRAK-ABCD1234567890';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('newrelicreporting/general/api')
            ->willReturn($encryptedKey);

        $this->encryptorMock->expects($this->once())
            ->method('decrypt')
            ->with($encryptedKey)
            ->willReturn($decryptedKey);

        $this->assertEquals($decryptedKey, $this->config->getNewRelicApiKey());
    }

    /**
     * Test getNewRelicAppId
     */
    public function testGetNewRelicAppId()
    {
        $expectedAppId = '123456789';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('newrelicreporting/general/app_id')
            ->willReturn($expectedAppId);

        $this->assertEquals($expectedAppId, $this->config->getNewRelicAppId());
    }

    /**
     * Test getNewRelicAppName
     */
    public function testGetNewRelicAppName()
    {
        $expectedAppName = 'My Application';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('newrelicreporting/general/app_name')
            ->willReturn($expectedAppName);

        $this->assertEquals($expectedAppName, $this->config->getNewRelicAppName());
    }

    /**
     * Test getNerdGraphUrl
     */
    public function testGetNerdGraphUrl()
    {
        $expectedUrl = 'https://api.newrelic.com/graphql';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('newrelicreporting/general/nerd_graph_api_url')
            ->willReturn($expectedUrl);

        $this->assertEquals($expectedUrl, $this->config->getNerdGraphUrl());
    }

    /**
     * Test getEntityGuid
     */
    public function testGetEntityGuid()
    {
        $expectedGuid = 'ENTITY123456789';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('newrelicreporting/general/entity_guid')
            ->willReturn($expectedGuid);

        $this->assertEquals($expectedGuid, $this->config->getEntityGuid());
    }

    /**
     * Test getApiMode
     */
    public function testGetApiMode()
    {
        $expectedMode = 'nerdgraph';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('newrelicreporting/general/api_mode')
            ->willReturn($expectedMode);

        $this->assertEquals($expectedMode, $this->config->getApiMode());
    }

    /**
     * Test isNerdGraphMode when mode is nerdgraph
     */
    public function testIsNerdGraphModeTrue()
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('newrelicreporting/general/api_mode')
            ->willReturn('nerdgraph');

        $this->assertTrue($this->config->isNerdGraphMode());
    }

    /**
     * Test isNerdGraphMode when mode is v2_rest
     */
    public function testIsNerdGraphModeFalse()
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('newrelicreporting/general/api_mode')
            ->willReturn('v2_rest');

        $this->assertFalse($this->config->isNerdGraphMode());
    }

    /**
     * Test isNerdGraphMode when mode is empty
     */
    public function testIsNerdGraphModeEmptyMode()
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('newrelicreporting/general/api_mode')
            ->willReturn('');

        $this->assertFalse($this->config->isNerdGraphMode());
    }

    /**
     * Test getNerdGraphUrl with custom value
     */
    public function testGetNerdGraphUrlCustom()
    {
        $customUrl = 'https://api.eu.newrelic.com/graphql';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('newrelicreporting/general/nerd_graph_api_url')
            ->willReturn($customUrl);

        $this->assertEquals($customUrl, $this->config->getNerdGraphUrl());
    }

    /**
     * Test getEntityGuid with empty value
     */
    public function testGetEntityGuidEmpty()
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('newrelicreporting/general/entity_guid')
            ->willReturn('');

        $this->assertEquals('', $this->config->getEntityGuid());
    }

    /**
     * Test getNewRelicApiKey with empty encrypted value
     */
    public function testGetNewRelicApiKeyEmpty()
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('newrelicreporting/general/api')
            ->willReturn('');

        $this->encryptorMock->expects($this->once())
            ->method('decrypt')
            ->with('')
            ->willReturn('');

        $this->assertEquals('', $this->config->getNewRelicApiKey());
    }

    /**
     * Test getNewRelicAppId with integer return
     */
    public function testGetNewRelicAppIdInteger()
    {
        $appId = 123456789;

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('newrelicreporting/general/app_id')
            ->willReturn($appId);

        $this->assertEquals($appId, $this->config->getNewRelicAppId());
    }

    /**
     * Test type casting for getNewRelicAppId
     */
    public function testGetNewRelicAppIdTypeCasting()
    {
        $stringAppId = '987654321';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('newrelicreporting/general/app_id')
            ->willReturn($stringAppId);

        $this->assertEquals($stringAppId, $this->config->getNewRelicAppId());
    }

    /**
     * Test getApiMode with null value
     */
    public function testGetApiModeNull()
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('newrelicreporting/general/api_mode')
            ->willReturn(null);

        $this->assertEquals('', $this->config->getApiMode());
    }

    /**
     * Test configuration path constants/values
     */
    public function testConfigurationPaths()
    {
        // Test multiple configuration calls to ensure consistent paths
        $this->scopeConfigMock->expects($this->exactly(8))
            ->method('getValue')
            ->willReturnCallback(function ($path) {
                $values = [
                    'newrelicreporting/general/api_url' => 'api_url_value',
                    'newrelicreporting/general/api' => 'encrypted_api_key',
                    'newrelicreporting/general/app_id' => '123456789',
                    'newrelicreporting/general/app_name' => 'app_name_value',
                    'newrelicreporting/general/nerd_graph_api_url' => 'nerdgraph_url_value',
                    'newrelicreporting/general/entity_guid' => 'entity_guid_value',
                    'newrelicreporting/general/api_mode' => 'nerdgraph'
                ];
                return $values[$path] ?? null;
            });

        $this->encryptorMock->expects($this->once())
            ->method('decrypt')
            ->with('encrypted_api_key')
            ->willReturn('decrypted_api_key');

        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with('newrelicreporting/general/enable')
            ->willReturn(true);

        // Test all methods
        $this->assertEquals('api_url_value', $this->config->getNewRelicApiUrl());
        $this->assertEquals('decrypted_api_key', $this->config->getNewRelicApiKey());
        $this->assertEquals(123456789, $this->config->getNewRelicAppId());
        $this->assertEquals('app_name_value', $this->config->getNewRelicAppName());
        $this->assertEquals('nerdgraph_url_value', $this->config->getNerdGraphUrl());
        $this->assertEquals('entity_guid_value', $this->config->getEntityGuid());
        $this->assertEquals('nerdgraph', $this->config->getApiMode());
        $this->assertTrue($this->config->isNerdGraphMode());
        $this->assertTrue($this->config->isNewRelicEnabled());
    }

    /**
     * Test encryption/decryption integration
     */
    public function testEncryptionDecryptionIntegration()
    {
        $plainTextKey = 'NRAK-PLAIN-TEXT-API-KEY';
        $encryptedKey = 'encrypted_string_abc123';

        // Test encryption flow (normally done by admin save)
        $this->encryptorMock->expects($this->once())
            ->method('encrypt')
            ->with($plainTextKey)
            ->willReturn($encryptedKey);

        $encryptedResult = $this->encryptorMock->encrypt($plainTextKey);
        $this->assertEquals($encryptedKey, $encryptedResult);

        // Test decryption flow (what Config does)
        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('newrelicreporting/general/api')
            ->willReturn($encryptedKey);

        $this->encryptorMock->expects($this->once())
            ->method('decrypt')
            ->with($encryptedKey)
            ->willReturn($plainTextKey);

        $decryptedResult = $this->config->getNewRelicApiKey();
        $this->assertEquals($plainTextKey, $decryptedResult);
    }

    /**
     * Test EU endpoint URL configuration
     */
    public function testGetNerdGraphUrlWithEuEndpoint(): void
    {
        $euNerdGraphUrl = 'https://api.eu.newrelic.com/graphql';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('newrelicreporting/general/nerd_graph_api_url')
            ->willReturn($euNerdGraphUrl);

        $result = $this->config->getNerdGraphUrl();
        $this->assertEquals($euNerdGraphUrl, $result);
    }

    /**
     * Test EU v2 REST API URL configuration
     */
    public function testGetNewRelicApiUrlWithEuEndpoint(): void
    {
        $euApiUrl = 'https://api.eu.newrelic.com/v2/applications/%s/deployments.json';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('newrelicreporting/general/api_url')
            ->willReturn($euApiUrl);

        $result = $this->config->getNewRelicApiUrl();
        $this->assertEquals($euApiUrl, $result);
    }

    /**
     * Test EU Insights API URL configuration
     */
    public function testGetInsightsApiUrlWithEuEndpoint(): void
    {
        $euInsightsUrl = 'https://insights-collector.eu01.nr-data.net/v1/accounts/%s/events';

        $this->scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with('newrelicreporting/general/insights_api_url')
            ->willReturn($euInsightsUrl);

        $result = $this->config->getInsightsApiUrl();
        $this->assertEquals($euInsightsUrl, $result);
    }

    /**
     * Test combined EU configuration setup
     */
    public function testCompleteEuEndpointConfiguration(): void
    {
        $euEndpoints = [
            'newrelicreporting/general/nerd_graph_api_url' => 'https://api.eu.newrelic.com/graphql',
            'newrelicreporting/general/api_url' => 'https://api.eu.newrelic.com/v2/applications/%s/deployments.json',
            'newrelicreporting/general/insights_api_url' =>
                'https://insights-collector.eu01.nr-data.net/v1/accounts/%s/events',
            'newrelicreporting/general/api_mode' => 'nerdgraph',
            'newrelicreporting/general/enable' => '1'
        ];

        $this->scopeConfigMock->expects($this->exactly(5))
            ->method('getValue')
            ->willReturnCallback(function ($path) use ($euEndpoints) {
                return $euEndpoints[$path] ?? null;
            });

        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with('newrelicreporting/general/enable')
            ->willReturn(true);

        // Test all EU endpoints are configured correctly
        $this->assertEquals('https://api.eu.newrelic.com/graphql', $this->config->getNerdGraphUrl());
        $this->assertEquals(
            'https://api.eu.newrelic.com/v2/applications/%s/deployments.json',
            $this->config->getNewRelicApiUrl()
        );
        $this->assertEquals(
            'https://insights-collector.eu01.nr-data.net/v1/accounts/%s/events',
            $this->config->getInsightsApiUrl()
        );
        $this->assertEquals('nerdgraph', $this->config->getApiMode());
        $this->assertTrue($this->config->isNewRelicEnabled());
        $this->assertTrue($this->config->isNerdGraphMode());
    }
}
