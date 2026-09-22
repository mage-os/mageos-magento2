<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Sales\Test\Unit\Model\Dashboard\Attention;

use Magento\Backend\Model\Dashboard\Attention\CappedCounter;
use Magento\Framework\DB\Select;
use Magento\Sales\Model\Dashboard\Attention\PendingOrders;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use PHPUnit\Framework\TestCase;

class PendingOrdersTest extends TestCase
{
    public function testCountsNewStateOrdersInScope(): void
    {
        $select = $this->createMock(Select::class);
        $collection = $this->createMock(Collection::class);
        $collection->expects($this->exactly(2))
            ->method('addFieldToFilter')
            ->willReturnCallback(function ($field, $condition) use ($collection) {
                $this->assertContains(
                    [$field, $condition],
                    [['state', Order::STATE_NEW], ['store_id', ['in' => [1, 2]]]]
                );
                return $collection;
            });
        $collection->method('getSelect')->willReturn($select);
        $factory = $this->createMock(CollectionFactory::class);
        $factory->method('create')->willReturn($collection);

        $counter = $this->createMock(CappedCounter::class);
        $counter->expects($this->once())->method('count')->with($select)->willReturn(12);

        $item = new PendingOrders($factory, $counter);

        $this->assertSame(12, $item->getCount([1, 2]));
        $this->assertSame('pending_orders', $item->getCode());
        $this->assertSame('Magento_Sales::sales_order', $item->getAclResource());
        $this->assertSame('sales/order/index', $item->getUrlPath());
    }

    public function testEmptyScopeMeansAllStores(): void
    {
        $collection = $this->createMock(Collection::class);
        $collection->expects($this->once())->method('addFieldToFilter')->with('state', Order::STATE_NEW)->willReturnSelf();
        $collection->method('getSelect')->willReturn($this->createMock(Select::class));
        $factory = $this->createMock(CollectionFactory::class);
        $factory->method('create')->willReturn($collection);
        $counter = $this->createMock(CappedCounter::class);
        $counter->method('count')->willReturn(0);

        $this->assertSame(0, (new PendingOrders($factory, $counter))->getCount([]));
    }
}
