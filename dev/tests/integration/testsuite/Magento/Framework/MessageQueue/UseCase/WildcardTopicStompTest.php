<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
namespace Magento\Framework\MessageQueue\UseCase;

use Magento\Framework\MessageQueue\DefaultValueProvider;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestModuleAsyncStomp\Model\AsyncTestData;

class WildcardTopicStompTest extends QueueTestCaseAbstract
{
    /**
     * @var string[]
     */
    protected $consumers = [
        'stomp.wildcard.queue.one.consumer',
        'stomp.wildcard.queue.two.consumer'
    ];

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
        }
    }

    /**
     * @param string $topic
     * @param string[] $matchingQueues
     * @param string[] $nonMatchingQueues
     *
     * @dataProvider wildCardTopicsDataProvider
     */
    public function testWildCardMatchingTopic($topic, $matchingQueues, $nonMatchingQueues)
    {
        if ($this->connectionType === 'amqp') {
            $this->markTestSkipped('STOMP test skipped because AMQP connection is available.
            This test is STOMP-specific.');
        }

        $testObject = $this->generateTestObject();
        $this->publisher->publish($topic, $testObject);

        $this->waitForAsynchronousResult(count($matchingQueues), $this->logFilePath);

        $this->assertFileExists($this->logFilePath, "No handlers invoked (log file was not created).");
        foreach ($nonMatchingQueues as $queueName) {
            $this->assertStringNotContainsString($queueName, file_get_contents($this->logFilePath));
        }
        foreach ($matchingQueues as $queueName) {
            $this->assertStringContainsString($queueName, file_get_contents($this->logFilePath));
        }
    }

    public static function wildCardTopicsDataProvider()
    {
        return [
            'stomp.segment1.segment2.segment3.wildcard' => [
                'stomp.segment1.segment2.segment3.wildcard',
                ['stomp.wildcard.queue.one'],
                ['stomp.wildcard.queue.three']
            ],
            'stomp.segment2.segment3.wildcard' => [
                'stomp.segment2.segment3.wildcard',
                ['stomp.wildcard.queue.one'],
                ['stomp.wildcard.queue.two']
            ]
        ];
    }

    public function testWildCardNonMatchingTopic()
    {
        if ($this->connectionType === 'amqp') {
            $this->markTestSkipped('STOMP test skipped because AMQP connection is available.
            This test is STOMP-specific.');
        }

        $testObject = $this->generateTestObject();
        $this->publisher->publish('no.match.at.all', $testObject);
        sleep(2);
        $this->assertFileDoesNotExist($this->logFilePath, "No log file must be created for non-matching topic.");
    }

    /**
     * @return AsyncTestData
     */
    private function generateTestObject()
    {
        $testObject = $this->objectManager->create(AsyncTestData::class); // @phpstan-ignore-line
        $testObject->setValue('||Message Contents||');
        $testObject->setTextFilePath($this->logFilePath);
        return $testObject;
    }
}
