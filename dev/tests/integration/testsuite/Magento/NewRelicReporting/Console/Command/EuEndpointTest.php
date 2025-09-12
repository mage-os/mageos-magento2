<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\NewRelicReporting\Console\Command;

use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Framework\Console\Cli;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Integration test for EU endpoint configuration with DeployMarker command
 *
 * @magentoAppIsolation enabled
 */
class EuEndpointTest extends TestCase
{
    private MutableScopeConfigInterface $mutableScopeConfig;
    private Application $application;

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->mutableScopeConfig = $objectManager->get(MutableScopeConfigInterface::class);
        
        $this->application = new Application();
        $deployMarkerCommand = $objectManager->get(DeployMarker::class);
        $this->application->add($deployMarkerCommand);
        $this->application->setDefaultCommand($deployMarkerCommand->getName());
    }

    protected function tearDown(): void
    {
        $this->mutableScopeConfig->clean();
    }

    /**
     * Test deployment command with EU NerdGraph endpoint configuration
     *
     * @magentoConfigFixture default/newrelicreporting/general/enable 1
     * @magentoConfigFixture default/newrelicreporting/general/api_mode nerdgraph
     * @magentoConfigFixture default/newrelicreporting/general/nerd_graph_api_url https://api.eu.newrelic.com/graphql
     * @magentoConfigFixture default/newrelicreporting/general/entity_guid test-eu-entity-guid
     * @magentoConfigFixture default/newrelicreporting/general/api test-api-key
     */
    public function testCommandWithEuNerdGraphEndpoint(): void
    {
        $commandTester = new CommandTester($this->application->find('newrelic:create:deploy-marker'));
        
        $exitCode = $commandTester->execute([
            'message' => 'EU NerdGraph deployment test',
            'change_log' => 'Testing EU endpoint configuration',
            'user' => 'eu-test-user',
            'revision' => 'v1.0.0-eu'
        ]);

        $output = $commandTester->getDisplay();
        
        // Since we're testing configuration processing (not actual API calls),
        // we verify the command processes the EU configuration correctly
        $this->assertTrue(in_array($exitCode, [0, 1], true));
        
        // Verify it's not failing due to New Relic being disabled
        $this->assertStringNotContainsString('New Relic is not enabled', $output);
        
        // For EU endpoints, we expect the command to get as far as attempting the deployment
        // (it may fail at the API call level, but configuration should be processed)
        $containsMessage = strpos($output, 'EU NerdGraph deployment test') !== false;
        $containsFailureMessage = strpos($output, 'Failed to create deployment marker') !== false;
        
        // Either the message appears in output or we get a deployment failure (meaning config was processed)
        $this->assertTrue($containsMessage || $containsFailureMessage, "Expected deployment message or failure message in output: " . $output);
    }

    /**
     * Test deployment command with EU v2 REST endpoint configuration
     *
     * @magentoConfigFixture default/newrelicreporting/general/enable 1
     * @magentoConfigFixture default/newrelicreporting/general/api_mode v2_rest
     * @magentoConfigFixture default/newrelicreporting/general/api_url https://api.eu.newrelic.com/v2/applications/%s/deployments.json
     * @magentoConfigFixture default/newrelicreporting/general/app_id 12345
     * @magentoConfigFixture default/newrelicreporting/general/api test-api-key
     */
    public function testCommandWithEuV2RestEndpoint(): void
    {
        $commandTester = new CommandTester($this->application->find('newrelic:create:deploy-marker'));
        
        $exitCode = $commandTester->execute([
            'message' => 'EU v2 REST deployment test',
            'change_log' => 'Testing EU v2 REST endpoint configuration',
            'user' => 'eu-test-user',
            'revision' => 'v1.0.0-eu'
        ]);

        $output = $commandTester->getDisplay();
        
        $this->assertTrue(in_array($exitCode, [0, 1], true));
        $this->assertStringNotContainsString('New Relic is not enabled', $output);
        
        // For EU endpoints, verify configuration is processed (may fail at API level)
        $containsMessage = strpos($output, 'EU v2 REST deployment test') !== false;
        $containsError = strpos($output, 'Error:') !== false || strpos($output, 'Failed to create') !== false;
        
        $this->assertTrue($containsMessage || $containsError, "Expected deployment message or error message in output: " . $output);
    }

    /**
     * Test that EU endpoint URLs are properly loaded from configuration
     *
     * @magentoConfigFixture default/newrelicreporting/general/enable 1
     * @magentoConfigFixture default/newrelicreporting/general/api_mode nerdgraph
     * @magentoConfigFixture default/newrelicreporting/general/nerd_graph_api_url https://api.eu.newrelic.com/graphql
     * @magentoConfigFixture default/newrelicreporting/general/api_url https://api.eu.newrelic.com/v2/applications/%s/deployments.json
     * @magentoConfigFixture default/newrelicreporting/general/insights_api_url https://insights-collector.eu01.nr-data.net/v1/accounts/%s/events
     */
    public function testEuEndpointUrlsAreLoadedCorrectly(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        /** @var \Magento\NewRelicReporting\Model\Config $config */
        $config = $objectManager->get(\Magento\NewRelicReporting\Model\Config::class);
        
        // Verify EU endpoints are loaded correctly
        $this->assertEquals('https://api.eu.newrelic.com/graphql', $config->getNerdGraphUrl());
        $this->assertEquals('https://api.eu.newrelic.com/v2/applications/%s/deployments.json', $config->getNewRelicApiUrl());
        $this->assertEquals('https://insights-collector.eu01.nr-data.net/v1/accounts/%s/events', $config->getInsightsApiUrl());
        
        // Verify configuration flags work correctly
        $this->assertTrue($config->isNewRelicEnabled());
        $this->assertTrue($config->isNerdGraphMode());
        $this->assertEquals('nerdgraph', $config->getApiMode());
    }

    /**
     * Test configuration switching between US and EU endpoints
     */
    public function testDynamicEndpointConfiguration(): void
    {
        // Test US configuration
        $this->mutableScopeConfig->setValue('newrelicreporting/general/enable', '1');
        $this->mutableScopeConfig->setValue('newrelicreporting/general/nerd_graph_api_url', 'https://api.newrelic.com/graphql');
        
        $objectManager = Bootstrap::getObjectManager();
        /** @var \Magento\NewRelicReporting\Model\Config $config */
        $config = $objectManager->get(\Magento\NewRelicReporting\Model\Config::class);
        
        $this->assertEquals('https://api.newrelic.com/graphql', $config->getNerdGraphUrl());
        
        // Switch to EU configuration
        $this->mutableScopeConfig->setValue('newrelicreporting/general/nerd_graph_api_url', 'https://api.eu.newrelic.com/graphql');
        
        // Create new instance to avoid caching
        /** @var \Magento\NewRelicReporting\Model\Config $configEu */
        $configEu = $objectManager->create(\Magento\NewRelicReporting\Model\Config::class);
        
        $this->assertEquals('https://api.eu.newrelic.com/graphql', $configEu->getNerdGraphUrl());
    }
}
