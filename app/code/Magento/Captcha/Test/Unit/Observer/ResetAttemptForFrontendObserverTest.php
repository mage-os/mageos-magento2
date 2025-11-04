<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Captcha\Test\Unit\Observer;

use Magento\Captcha\Model\ResourceModel\Log;
use Magento\Captcha\Model\ResourceModel\LogFactory;
use Magento\Captcha\Observer\ResetAttemptForFrontendObserver;
use Magento\Customer\Model\Customer;
use Magento\Framework\Event\Observer;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for \Magento\Captcha\Observer\ResetAttemptForFrontendObserver
 */
class ResetAttemptForFrontendObserverTest extends TestCase
{
    /**
     * Test that the method resets attempts for Frontend
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
            ['getModel']
        );
        $eventObserverMock->expects($this->once())
            ->method('getModel')
            ->willReturn($this->createMock(Customer::class));

        $objectManager = $objectManagerHelper;
        /** @var ResetAttemptForFrontendObserver $observer */
        $observer = $objectManager->getObject(
            ResetAttemptForFrontendObserver::class,
            ['resLogFactory' => $resLogFactoryMock]
        );
        $this->assertInstanceOf(Log::class, $observer->execute($eventObserverMock));
    }
}
