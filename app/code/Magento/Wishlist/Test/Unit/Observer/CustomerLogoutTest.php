<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Test\Unit\Observer;

use Magento\Customer\Model\Session;
use Magento\Wishlist\Observer\CustomerLogout as Observer;
use Magento\Customer\Test\Unit\Helper\SessionTestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CustomerLogoutTest extends TestCase
{
    /**
     * @var Observer
     */
    protected $observer;

    /**
     * @var Session|MockObject
     */
    protected $customerSession;

    protected function setUp(): void
    {
        $this->customerSession = new SessionTestHelper();
        $this->customerSession->setIsLoggedIn(true);
        $this->customerSession->setCustomerId(1);

        $this->observer = new Observer(
            $this->customerSession
        );
    }

    public function testExecute()
    {
        $event = $this->getMockBuilder(\Magento\Framework\Event\Observer::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var \Magento\Framework\Event\Observer $event */

        $this->observer->execute($event);
        
        $this->assertEquals(0, $this->customerSession->wishlistItemCount);
    }
}
