<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Captcha\Test\Unit\Observer;

use Magento\Captcha\Model\ResourceModel\Log;
use Magento\Captcha\Model\ResourceModel\LogFactory;
use Magento\Captcha\Observer\ResetAttemptForBackendObserver;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for \Magento\Captcha\Observer\ResetAttemptForBackendObserver
 */
class ResetAttemptForBackendObserverTest extends TestCase
{
    /**
     * Test that the method resets attempts for Backend
     */
    public function testExecuteExpectsDeleteUserAttemptsCalled()
    {
        $objectManagerHelper = new ObjectManagerHelper($this);

        $logMock = $this->createMock(Log::class);
        $logMock->expects($this->once())->method('deleteUserAttempts')->willReturnSelf();

        $resLogFactoryMock = $this->createMock(LogFactory::class);
        $resLogFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($logMock);

        /** @var MockObject|Observer $eventObserverMock */
        $eventObserverMock = $objectManagerHelper->createPartialMockWithReflection(
            Observer::class,
            ['getUser']
        );
        $eventMock = $this->createMock(Event::class);
        $eventObserverMock->expects($this->once())
            ->method('getUser')
            ->willReturn($eventMock);

        $objectManager = $objectManagerHelper;
        /** @var ResetAttemptForBackendObserver $observer */
        $observer = $objectManager->getObject(
            ResetAttemptForBackendObserver::class,
            ['resLogFactory' => $resLogFactoryMock]
        );
        $observer->execute($eventObserverMock);
    }
}
