<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Test\Unit\Observer;

use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Observer\CustomerLogin as Observer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CustomerLoginTest extends TestCase
{
    /**
     * @var Observer
     */
    protected $observer;

    /**
     * @var Data|MockObject
     */
    protected $helper;

    protected function setUp(): void
    {
        $this->helper = $this->createMock(Data::class);

        $this->observer = new Observer($this->helper);
    }

    public function testExecute()
    {
        $event = $this->createMock(\Magento\Framework\Event\Observer::class);
        /** @var \Magento\Framework\Event\Observer $event */

        $this->helper->expects($this->once())
            ->method('calculate');

        $this->observer->execute($event);
    }
}
