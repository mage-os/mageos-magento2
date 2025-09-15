<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\NewRelicReporting\Model\NerdGraph;

use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\LaminasClient;
use Magento\Framework\HTTP\LaminasClientFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\NewRelicReporting\Model\Apm\Deployments;
use Magento\NewRelicReporting\Model\Config;
use Magento\NewRelicReporting\Model\NerdGraph\Client;
use Magento\NewRelicReporting\Model\NerdGraph\DeploymentTracker;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Integration test for the complete deployment workflow
 *
 * @magentoAppIsolation enabled
 */
class DeploymentWorkflowTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var MutableScopeConfigInterface
     */
    private $mutableScopeConfig;

    /**
     * @var DeploymentTracker
     */
    private $deploymentTracker;

    /**
     * @var Deployments
     */
    private $deployments;

    /**
     * @var Client
     */
    private $nerdGraphClient;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->config = $this->objectManager->get(Config::class);
        $this->mutableScopeConfig = $this->objectManager->get(MutableScopeConfigInterface::class);
        $this->deploymentTracker = $this->objectManager->get(DeploymentTracker::class);
        $this->deployments = $this->objectManager->get(Deployments::class);
        $this->nerdGraphClient = $this->objectManager->get(Client::class);
    }

    protected function tearDown(): void
    {
        if ($this->mutableScopeConfig) {
            $this->mutableScopeConfig->clean();
        }
        parent::tearDown();
    }

    /**
     * Test complete dependency injection setup
     */
    public function testDependencyInjectionSetup()
    {
        $this->assertInstanceOf(Config::class, $this->config);
        $this->assertInstanceOf(DeploymentTracker::class, $this->deploymentTracker);
        $this->assertInstanceOf(Deployments::class, $this->deployments);
        $this->assertInstanceOf(Client::class, $this->nerdGraphClient);
    }

    /**
     * Test config methods work properly in integration environment
     */
    public function testConfigIntegration()
    {
        // Test default values (from config.xml)
        $this->assertFalse($this->config->isNewRelicEnabled());
        $this->assertEquals('v2_rest', $this->config->getApiMode()); // Default from config.xml
        $this->assertEquals('', $this->config->getEntityGuid()); // No default in config.xml
        $this->assertEquals(0, $this->config->getNewRelicAppId()); // No default in config.xml
        $this->assertEquals('', $this->config->getNewRelicAppName()); // No default in config.xml

        // Test setting values
        $this->mutableScopeConfig->setValue('newrelicreporting/general/enable', '1');
        $this->mutableScopeConfig->setValue('newrelicreporting/general/api_mode', 'nerdgraph');
        $this->mutableScopeConfig->setValue('newrelicreporting/general/entity_guid', 'test-guid-123');

        $this->assertTrue($this->config->isNewRelicEnabled());
        $this->assertEquals('nerdgraph', $this->config->getApiMode());
        $this->assertTrue($this->config->isNerdGraphMode());
        $this->assertEquals('test-guid-123', $this->config->getEntityGuid());
    }

    /**
     * Test deployment workflow when disabled
     */
    public function testDeploymentWorkflowWhenDisabled()
    {
        $this->mutableScopeConfig->setValue('newrelicreporting/general/enable', '0');

        // Should handle gracefully when disabled (specific behavior depends on implementation)
        $this->assertFalse($this->config->isNewRelicEnabled());
    }

    /**
     * Test deployment workflow with missing configuration
     */
    public function testDeploymentWorkflowWithMissingConfiguration()
    {
        $this->mutableScopeConfig->setValue('newrelicreporting/general/enable', '1');
        $this->mutableScopeConfig->setValue('newrelicreporting/general/api_mode', 'nerdgraph');
        // No entity GUID, app ID, or app name configured

        $result = $this->deploymentTracker->createDeployment('Test deployment');

        $this->assertFalse($result);
    }

    /**
     * Test v2 REST API mode selection
     */
    public function testV2RestApiModeSelection()
    {
        $this->mutableScopeConfig->setValue('newrelicreporting/general/enable', '1');
        $this->mutableScopeConfig->setValue('newrelicreporting/general/api_mode', 'v2_rest');
        $this->mutableScopeConfig->setValue('newrelicreporting/general/app_id', '12345');
        $this->mutableScopeConfig->setValue('newrelicreporting/general/api', 'encrypted_api_key');

        $this->assertFalse($this->config->isNerdGraphMode());
        $this->assertEquals('v2_rest', $this->config->getApiMode());

        // Test that deployments service uses correct mode
        $this->assertInstanceOf(Deployments::class, $this->deployments);
    }

    /**
     * Test NerdGraph mode selection
     */
    public function testNerdGraphModeSelection()
    {
        $this->mutableScopeConfig->setValue('newrelicreporting/general/enable', '1');
        $this->mutableScopeConfig->setValue('newrelicreporting/general/api_mode', 'nerdgraph');
        $this->mutableScopeConfig->setValue('newrelicreporting/general/entity_guid', 'test-entity-guid');
        $this->mutableScopeConfig->setValue(
            'newrelicreporting/general/nerd_graph_api_url',
            'https://api.newrelic.com/graphql'
        );

        $this->assertTrue($this->config->isNerdGraphMode());
        $this->assertEquals('nerdgraph', $this->config->getApiMode());
        $this->assertEquals('test-entity-guid', $this->config->getEntityGuid());
        $this->assertEquals('https://api.newrelic.com/graphql', $this->config->getNerdGraphUrl());
    }

    /**
     * Test Deployments service configuration
     * @throws \ReflectionException
     */
    public function testDeploymentsServiceConfiguration()
    {
        // Test that Deployments service is properly configured
        $this->assertInstanceOf(Deployments::class, $this->deployments);

        $reflection = new \ReflectionClass($this->deployments);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $expectedParameters = [
            'config',
            'logger',
            'clientFactory',
            'serializer',
            'deploymentTracker'
        ];

        $this->assertCount(count($expectedParameters), $parameters);

        foreach ($parameters as $index => $parameter) {
            $this->assertEquals($expectedParameters[$index], $parameter->getName());
        }
    }

    /**
     * Test NerdGraph Client configuration
     * @throws \ReflectionException
     */
    public function testNerdGraphClientConfiguration()
    {
        $this->assertInstanceOf(Client::class, $this->nerdGraphClient);

        $reflection = new \ReflectionClass($this->nerdGraphClient);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $expectedParameters = [
            'httpClientFactory',
            'serializer',
            'config',
            'logger'
        ];

        $this->assertCount(count($expectedParameters), $parameters);

        foreach ($parameters as $index => $parameter) {
            $this->assertEquals($expectedParameters[$index], $parameter->getName());
        }
    }

    /**
     * Test DeploymentTracker configuration
     * @throws \ReflectionException
     */
    public function testDeploymentTrackerConfiguration()
    {
        $this->assertInstanceOf(DeploymentTracker::class, $this->deploymentTracker);

        $reflection = new \ReflectionClass($this->deploymentTracker);
        $constructor = $reflection->getConstructor();
        $parameters = $constructor->getParameters();

        $expectedParameters = [
            'nerdGraphClient',
            'config',
            'logger'
        ];

        $this->assertCount(count($expectedParameters), $parameters);

        foreach ($parameters as $index => $parameter) {
            $this->assertEquals($expectedParameters[$index], $parameter->getName());
        }
    }

    /**
     * Test service singleton behavior
     */
    public function testServiceSingleton()
    {
        $config1 = $this->objectManager->get(Config::class);
        $config2 = $this->objectManager->get(Config::class);

        $this->assertSame($config1, $config2);

        $client1 = $this->objectManager->get(Client::class);
        $client2 = $this->objectManager->get(Client::class);

        $this->assertSame($client1, $client2);
    }

    /**
     * Test configuration encryption integration
     */
    public function testConfigurationEncryptionIntegration()
    {
        $testApiKey = 'NRAK-TEST-API-KEY-123';

        // Set encrypted API key
        $this->mutableScopeConfig->setValue('newrelicreporting/general/api', $testApiKey);

        // Config should decrypt it (though in test it might just return as-is)
        $retrievedKey = $this->config->getNewRelicApiKey();
        $this->assertIsString($retrievedKey);
    }

    /**
     * Test HTTP Client Factory integration
     */
    public function testHttpClientFactoryIntegration()
    {
        $httpClientFactory = $this->objectManager->get(LaminasClientFactory::class);
        $this->assertInstanceOf(LaminasClientFactory::class, $httpClientFactory);

        // Should be able to create HTTP client
        $httpClient = $httpClientFactory->create();
        $this->assertInstanceOf(LaminasClient::class, $httpClient);
    }

    /**
     * Test Serializer integration
     */
    public function testSerializerIntegration()
    {
        $serializer = $this->objectManager->get(SerializerInterface::class);
        $this->assertInstanceOf(SerializerInterface::class, $serializer);

        // Test basic serialization
        $testData = ['key' => 'value', 'number' => 123];
        $serialized = $serializer->serialize($testData);
        $unserialized = $serializer->unserialize($serialized);

        $this->assertEquals($testData, $unserialized);
    }

    /**
     * Test Logger integration
     */
    public function testLoggerIntegration()
    {
        $logger = $this->objectManager->get(LoggerInterface::class);
        $this->assertInstanceOf(LoggerInterface::class, $logger);

        // Should be able to log without errors
        $logger->info('Test log message from integration test');
        $this->assertTrue(true);
    }

    /**
     * Test error handling in deployment workflow
     * @throws LocalizedException
     */
    public function testDeploymentWorkflowErrorHandling()
    {
        $this->mutableScopeConfig->setValue('newrelicreporting/general/enable', '1');
        $this->mutableScopeConfig->setValue('newrelicreporting/general/api_mode', 'nerdgraph');
        $this->mutableScopeConfig->setValue('newrelicreporting/general/entity_guid', 'invalid-guid');
        $this->mutableScopeConfig->setValue('newrelicreporting/general/api', 'invalid_api_key');
        $this->mutableScopeConfig->setValue(
            'newrelicreporting/general/nerd_graph_api_url',
            'https://invalid-url.example.com/graphql'
        );

        // This should fail gracefully and return false rather than throwing an exception
        $result = $this->deploymentTracker->createDeployment('Test deployment');
        $this->assertFalse($result);
    }

    /**
     * Test that deployment mode detection works in integration
     */
    public function testDeploymentModeDetectionIntegration()
    {
        // Test v2_rest mode
        $this->mutableScopeConfig->setValue('newrelicreporting/general/api_mode', 'v2_rest');
        $this->assertEquals('v2_rest', $this->config->getApiMode());
        $this->assertFalse($this->config->isNerdGraphMode());

        // Test nerdgraph mode
        $this->mutableScopeConfig->setValue('newrelicreporting/general/api_mode', 'nerdgraph');
        $this->assertEquals('nerdgraph', $this->config->getApiMode());
        $this->assertTrue($this->config->isNerdGraphMode());

        // Test unset/default mode (falls back to default config value)
        $this->mutableScopeConfig->setValue('newrelicreporting/general/api_mode', null);
        $this->assertEquals('v2_rest', $this->config->getApiMode()); // Falls back to default from config.xml
        $this->assertFalse($this->config->isNerdGraphMode());
    }
}
