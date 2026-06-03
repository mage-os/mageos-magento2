<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Model\ConfigOptionsList;

use Magento\Framework\App\Backpressure\ContextInterface;
use Magento\Framework\App\Backpressure\SlidingWindow\LimitConfig;
use Magento\Framework\App\Backpressure\SlidingWindow\LimitConfigManagerInterface;
use Magento\Framework\App\Backpressure\SlidingWindow\RequestLoggerFactoryInterface;
use Magento\Framework\App\Backpressure\SlidingWindow\RequestLoggerInterface;
use Magento\Framework\App\Backpressure\SlidingWindow\SlidingWindowEnforcer;
use Magento\Framework\App\Backpressure\SlidingWindow\ValkeyRequestLogger;
use Magento\Framework\App\DeploymentConfig;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Integration tests for backpressure logger setup options (ACP2E-4899 / ACQE-9901).
 *
 * Verifies Valkey is registered via DI as a valid backpressure logger for Order Rate Limit
 * and that the object manager can instantiate the Valkey request logger implementation.
 */
class BackpressureLoggerTest extends TestCase
{
    /**
     * @var BackpressureLogger
     */
    private BackpressureLogger $backpressureLogger;

    /**
     * @var DeploymentConfig
     */
    private DeploymentConfig $deploymentConfig;

    /**
     * @var RequestLoggerFactoryInterface
     */
    private RequestLoggerFactoryInterface $requestLoggerFactory;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->backpressureLogger = $objectManager->get(BackpressureLogger::class);
        $this->deploymentConfig = $objectManager->get(DeploymentConfig::class);
        $this->requestLoggerFactory = $objectManager->get(RequestLoggerFactoryInterface::class);
    }

    /**
     * Valkey must be accepted when generating env.php config during setup (not skipped as unknown type).
     *
     * @return void
     */
    public function testCreateConfigPersistsValkeyBackpressureLoggerType(): void
    {
        $configData = $this->backpressureLogger->createConfig(
            ['backpressure-logger' => 'valkey'],
            $this->deploymentConfig
        );

        $data = $configData->getData();
        $this->assertSame('valkey', $data['backpressure']['logger']['type']);
        $this->assertSame('127.0.0.1', $data['backpressure']['logger']['options']['server']);
        $this->assertSame(6379, $data['backpressure']['logger']['options']['port']);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{3}_$/', $data['backpressure']['logger']['id-prefix']);
    }

    /**
     * Setup validation must not reject valkey as an invalid logger type (regression for ACP2E-4899).
     *
     * @return void
     */
    public function testValidateDoesNotRejectValkeyLoggerType(): void
    {
        $errors = $this->backpressureLogger->validate(
            ['backpressure-logger' => 'valkey'],
            $this->deploymentConfig
        );

        $this->assertNotContains(
            "Invalid backpressure request logger type: 'valkey'",
            $errors
        );
    }

    /**
     * Request logger factory must resolve valkey to ValkeyRequestLogger for runtime order rate limiting.
     *
     * @return void
     */
    public function testRequestLoggerFactoryCreatesValkeyRequestLogger(): void
    {
        $logger = $this->requestLoggerFactory->create('valkey');

        $this->assertInstanceOf(ValkeyRequestLogger::class, $logger);
        $this->assertInstanceOf(RequestLoggerInterface::class, $logger);
    }

    /**
     * Unknown logger types must still be rejected during setup validation.
     *
     * @return void
     */
    public function testValidateRejectsUnknownBackpressureLoggerType(): void
    {
        $errors = $this->backpressureLogger->validate(
            ['backpressure-logger' => 'unknown-logger'],
            $this->deploymentConfig
        );

        $this->assertSame(["Invalid backpressure request logger type: 'unknown-logger'"], $errors);
    }

    /**
     * After enforced requests with valkey logger configured, system.log must not contain
     * "Invalid request logger type: valkey" — regression test for ACP2E-4899.
     *
     * @return void
     */
    public function testEnforceDoesNotLogInvalidRequestLoggerTypeWhenValkeyConfigured(): void
    {
        $loggedErrors = [];
        $psrLogger = $this->createStub(LoggerInterface::class);
        $psrLogger->method('error')->willReturnCallback(
            function (string $message) use (&$loggedErrors): void {
                $loggedErrors[] = $message;
            }
        );

        // Confirm the real DI-configured factory resolves 'valkey' to ValkeyRequestLogger.
        // This is the integration check: if the factory threw RuntimeException here, it would
        // also trigger "Backpressure sliding window not applied. Invalid request logger type: valkey"
        // inside SlidingWindowEnforcer::enforce().
        $realFactory = Bootstrap::getObjectManager()->get(RequestLoggerFactoryInterface::class);
        $this->assertInstanceOf(ValkeyRequestLogger::class, $realFactory->create('valkey'));

        // Use a stub request logger to avoid requiring a live Redis/Valkey connection.
        $requestLogger = $this->createStub(RequestLoggerInterface::class);
        $requestLogger->method('incrAndGetFor')->willReturn(1);
        $requestLogger->method('getFor')->willReturn(null);

        $factory = $this->createMock(RequestLoggerFactoryInterface::class);
        $factory->method('create')->with('valkey')->willReturn($requestLogger);

        $deploymentConfig = $this->createMock(DeploymentConfig::class);
        $deploymentConfig->method('get')
            ->with(RequestLoggerInterface::CONFIG_PATH_BACKPRESSURE_LOGGER)
            ->willReturn('valkey');

        $limitConfigManager = $this->createStub(LimitConfigManagerInterface::class);
        $limitConfigManager->method('readLimit')->willReturn(new LimitConfig(3, 60));

        $enforcer = Bootstrap::getObjectManager()->create(
            SlidingWindowEnforcer::class,
            [
                'requestLoggerFactory' => $factory,
                'configManager' => $limitConfigManager,
                'deploymentConfig' => $deploymentConfig,
                'logger' => $psrLogger,
            ]
        );

        $context = $this->createStub(ContextInterface::class);
        $context->method('getTypeId')->willReturn('test_type');
        $context->method('getIdentityType')->willReturn(ContextInterface::IDENTITY_TYPE_IP);
        $context->method('getIdentity')->willReturn('127.0.0.1');

        $enforcer->enforce($context);

        $this->assertEmpty(
            array_filter($loggedErrors, fn(string $msg) => str_contains($msg, 'Invalid request logger type')),
            'No "Invalid request logger type" error must appear in system.log when valkey is the backpressure logger.'
        );
    }
}
