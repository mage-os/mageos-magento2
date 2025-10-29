<?php
/**
 * Copyright 2019 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Observer;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Observer\UpgradeQuoteCustomerEmailObserver;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\Test\Unit\Helper\EventTestHelper;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Test\Unit\Helper\QuoteTestHelper;
use PHPUnit\Framework\TestCase;

/** for testing upgrade quote customer email
 */
class UpgradeQuoteCustomerEmailObserverTest extends TestCase
{
    /**
     * @var UpgradeQuoteCustomerEmailObserver
     */
    protected $model;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepositoryMock;

    /**
     * @var Observer
     */
    protected $observerMock;

    /**
     * @var Event
     */
    protected $eventMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->observerMock = $this->createMock(Observer::class);

        $this->eventMock = $this->createPartialMock(
            EventTestHelper::class,
            ['getCustomerDataObject', 'getOrigCustomerDataObject']
        );

        $this->observerMock->expects($this->any())->method('getEvent')->willReturn($this->eventMock);

        $this->quoteRepositoryMock = $this->createMock(CartRepositoryInterface::class);
        $this->model = new UpgradeQuoteCustomerEmailObserver($this->quoteRepositoryMock);
    }

    /**
     * Unit test for verifying quote customers email upgrade observer
     */
    public function testUpgradeQuoteCustomerEmail()
    {
        $email = "test@test.com";
        $origEmail = "origtest@test.com";

        $customer = $this->createMock(CustomerInterface::class);
        $customerOrig = $this->createMock(CustomerInterface::class);

        $quoteMock = $this->createPartialMock(
            QuoteTestHelper::class,
            ['setCustomerEmail']
        );

        $this->eventMock->expects($this->any())
            ->method('getCustomerDataObject')
            ->willReturn($customer);
        $this->eventMock->expects($this->any())
            ->method('getOrigCustomerDataObject')
            ->willReturn($customerOrig);

        $customerOrig->expects($this->any())
            ->method('getEmail')
            ->willReturn($origEmail);

        $customer->expects($this->any())
            ->method('getEmail')
            ->willReturn($email);

        $this->quoteRepositoryMock->expects($this->once())
            ->method('getForCustomer')
            ->willReturn($quoteMock);

        $quoteMock->expects($this->once())
            ->method('setCustomerEmail');

        $this->quoteRepositoryMock->expects($this->once())
            ->method('save')
            ->with($quoteMock);

        $this->model->execute($this->observerMock);
    }
}
