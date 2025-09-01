<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Stomp\UseCase;

use Magento\Framework\App\DeploymentConfig\FileReader;
use Magento\Framework\App\DeploymentConfig\Writer;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\Filesystem;
use Magento\Framework\MessageQueue\DefaultValueProvider;
use Magento\Framework\MessageQueue\UseCase\QueueTestCaseAbstract;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestModuleAsyncStomp\Model\AsyncTestData;

class WaitAndNotWaitMessagesTest extends QueueTestCaseAbstract
{
    /**
     * @var FileReader
     */
    private $reader;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var array
     */
    private $config;

    /**
     * @var AsyncTestData
     */
    protected $msgObject;

    /**
     * @var array
     */
    protected $consumers = ['stomp.mixed.sync.and.async.queue.consumer'];

    /**
     * @var string[]
     */
    protected $messages = ['message1', 'message2', 'message3'];

    /**
     * @var int|null
     */
    protected $maxMessages = 4;

    /**
     * @var string
     */
    private $connectionType;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        /** @var DefaultValueProvider $defaultValueProvider */
        $defaultValueProvider = $this->objectManager->get(DefaultValueProvider::class);
        $this->connectionType = $defaultValueProvider->getConnection();

        if ($this->connectionType === 'stomp') {
            parent::setUp();
            $this->reader = $this->objectManager->get(FileReader::class);
            $this->filesystem = $this->objectManager->get(Filesystem::class);
            $this->config = $this->loadConfig();
        }
    }

    /**
     * Get message object, creating it lazily
     *
     * @return AsyncTestData
     */
    private function getMsgObject(): AsyncTestData
    {
        if (!$this->msgObject) {
            // phpstan:ignore "Class Magento\TestModuleAsyncStomp\Model\AsyncTestData not found."
            $this->msgObject = $this->objectManager->create(AsyncTestData::class);
        }
        return $this->msgObject;
    }

    /**
     * Check if consumers wait for messages from the queue
     */
    public function testWaitForMessages(): void
    {
        if ($this->connectionType === 'amqp') {
            $this->markTestSkipped('STOMP test skipped because AMQP connection is available.
            This test is STOMP-specific.');
        }

        $this->publisherConsumerController->stopConsumers();

        $config = $this->config;
        $config['queue']['consumers_wait_for_messages'] = 1;
        $this->writeConfig($config);

        $loadedConfig = $this->loadConfig();
        $this->assertArrayHasKey('queue', $loadedConfig);
        $this->assertArrayHasKey('consumers_wait_for_messages', $loadedConfig['queue']);
        $this->assertEquals(1, $loadedConfig['queue']['consumers_wait_for_messages']);

        foreach ($this->messages as $message) {
            $this->publishMessage($message);
        }
        $this->publisherConsumerController->startConsumers();
        $this->waitForAsynchronousResult(count($this->messages), $this->logFilePath);

        foreach ($this->messages as $item) {
            $this->assertStringContainsString($item, file_get_contents($this->logFilePath));
        }

        $this->publishMessage('message4');
        $this->waitForAsynchronousResult(count($this->messages) + 1, $this->logFilePath);
        $this->assertStringContainsString('message4', file_get_contents($this->logFilePath));
    }

    /**
     * Check if consumers do not wait for messages from the queue and die
     */
    public function testNotWaitForMessages(): void
    {
        if ($this->connectionType !== 'stomp') {
            $this->markTestSkipped('STOMP test skipped because AMQP connection is available.
            This test is STOMP-specific.');
        }

        $this->publisherConsumerController->stopConsumers();

        $config = $this->config;
        $config['queue']['consumers_wait_for_messages'] = 0;
        $this->writeConfig($config);

        $loadedConfig = $this->loadConfig();
        $this->assertArrayHasKey('queue', $loadedConfig);
        $this->assertArrayHasKey('consumers_wait_for_messages', $loadedConfig['queue']);
        $this->assertEquals(0, $loadedConfig['queue']['consumers_wait_for_messages']);
        foreach ($this->messages as $message) {
            $this->publishMessage($message);
        }

        $this->publisherConsumerController->startConsumers();
        sleep(5);
        $this->waitForAsynchronousResult(count($this->messages), $this->logFilePath);

        foreach ($this->messages as $item) {
            $this->assertStringContainsString($item, file_get_contents($this->logFilePath));
        }

        // Checks that consumers do not wait 4th message and die
        $consumersProcessIds = $this->publisherConsumerController->getConsumersProcessIds();
        $this->assertArrayHasKey('stomp.mixed.sync.and.async.queue.consumer', $consumersProcessIds);
        $this->assertEquals([], $consumersProcessIds['stomp.mixed.sync.and.async.queue.consumer']);
    }

    /**
     * @param string $message
     */
    private function publishMessage(string $message): void
    {
        $this->getMsgObject()->setValue($message);
        $this->getMsgObject()->setTextFilePath($this->logFilePath);
        $this->publisher->publish('stomp.multi.topic.queue.topic.c', $this->getMsgObject());
    }

    /**
     * @return array
     */
    private function loadConfig(): array
    {
        return $this->reader->load(ConfigFilePool::APP_ENV);
    }

    /**
     * @param array $config
     */
    private function writeConfig(array $config): void
    {
        $writer = $this->objectManager->get(Writer::class);
        $writer->saveConfig([ConfigFilePool::APP_ENV => $config], true);
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->writeConfig($this->config);
    }
}
