<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogRule\Test\Unit\Observer;

use Magento\CatalogRule\Model\Flag;
use Magento\CatalogRule\Observer\AddDirtyRulesNotice;
use Magento\Framework\Event\Observer;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AddDirtyRulesNoticeTest extends TestCase
{
    /**
     * @var AddDirtyRulesNotice
     */
    private $observer;

    /**
     * @var ManagerInterface|MockObject
     */
    private $messageManagerMock;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->messageManagerMock = $this->createMock(ManagerInterface::class);
        $objectManagerHelper = new ObjectManager($this);
        $this->observer = $objectManagerHelper->getObject(
            AddDirtyRulesNotice::class,
            [
                'messageManager' => $this->messageManagerMock
            ]
        );
    }

    /**
     * @return void
     */
    public function testExecute(): void
    {
        $message = "test";
        // Create anonymous class extending Flag with dynamic methods
        $flagMock = new class extends Flag {
            /** @var mixed */
            private $state = null;

            public function __construct()
            {
                // Skip parent constructor to avoid complex dependencies
            }

            public function getState()
            {
                return $this->state;
            }

            public function setState($value)
            {
                $this->state = $value;
                return $this;
            }
        };
        $eventObserverMock = $this->getMockBuilder(Observer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $flagMock->setState(1);
        $eventObserverMock
            ->method('getData')
            ->willReturnCallback(fn($param) => match ([$param]) {
                ['dirty_rules'] => $flagMock,
                ['message'] => $message
            });
        $this->messageManagerMock->expects($this->once())->method('addNoticeMessage')->with($message);
        $this->observer->execute($eventObserverMock);
    }
}
