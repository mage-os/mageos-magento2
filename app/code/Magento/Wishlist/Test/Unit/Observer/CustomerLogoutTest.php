<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Test\Unit\Observer;

use Magento\Customer\Model\Session;
use Magento\Wishlist\Observer\CustomerLogout as Observer;
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
        $this->customerSession = $this->createPartialMock(Session::class, []);
        
        // Initialize storage for magic __call methods
        $reflection = new \ReflectionClass($this->customerSession);
        $property = $reflection->getProperty('storage');
        $property->setValue($this->customerSession, new \Magento\Framework\Session\Storage());
        
        // Set customer ID (makes isLoggedIn() return true)
        $this->customerSession->setCustomerId(1);
        
        // Set wishlist item count in storage
        $this->customerSession->setData('wishlist_item_count', 0);

        $this->observer = new Observer(
            $this->customerSession
        );
    }

    public function testExecute()
    {
        $event = $this->createMock(\Magento\Framework\Event\Observer::class);
        /** @var \Magento\Framework\Event\Observer $event */

        $this->observer->execute($event);
        
        $this->assertEquals(0, $this->customerSession->getData('wishlist_item_count'));
    }
}
