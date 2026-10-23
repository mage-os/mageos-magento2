<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Sales\Test\Unit\Model\Dashboard\Attention;

use Magento\Backend\Model\Dashboard\Attention\CappedCounter;
use Magento\Framework\DB\Select;
use Magento\Sales\Model\Dashboard\Attention\OrdersByState;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OrdersByStateTest extends TestCase
{
    /**
     * @var Collection|MockObject
     */
    private $collection;

    /**
     * @var CollectionFactory|MockObject
     */
    private $factory;

    /**
     * @var Select|MockObject
     */
    private $select;

    protected function setUp(): void
    {
        $this->select = $this->createMock(Select::class);
        $this->collection = $this->createMock(Collection::class);
        $this->collection->method('getSelect')->willReturn($this->select);
        $this->factory = $this->createMock(CollectionFactory::class);
        $this->factory->method('create')->willReturn($this->collection);
    }

    private function createItem(int $count, string $state, bool $hideWhenEmpty = false): OrdersByState
    {
        $counter = $this->createMock(CappedCounter::class);
        $counter->method('count')->with($this->select)->willReturn($count);

        return new OrdersByState($this->factory, $counter, $state, 'code_' . $state, 'Label', 7, $hideWhenEmpty);
    }

    public function testCountsOrdersInStateAndScope(): void
    {
        $filters = [];
        $this->collection->method('addFieldToFilter')->willReturnCallback(
            function ($field, $condition) use (&$filters) {
                $filters[] = [$field, $condition];
                return $this->collection;
            }
        );

        $item = $this->createItem(12, Order::STATE_HOLDED);

        $this->assertSame(12, $item->getCount([1, 2]));
        $this->assertSame([['state', Order::STATE_HOLDED], ['store_id', ['in' => [1, 2]]]], $filters);
        $this->assertSame('code_holded', $item->getCode());
        $this->assertSame('Label', (string)$item->getLabel());
        $this->assertSame(7, $item->getSortOrder());
        $this->assertSame('Magento_Sales::sales_order', $item->getAclResource());
        $this->assertSame('sales/order/index', $item->getUrlPath());
    }

    public function testEmptyScopeMeansAllStores(): void
    {
        $this->collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with('state', Order::STATE_NEW)
            ->willReturnSelf();

        $this->assertSame(0, $this->createItem(0, Order::STATE_NEW)->getCount([]));
    }

    public function testHideWhenEmptyReturnsNullForZero(): void
    {
        $this->collection->method('addFieldToFilter')->willReturnSelf();

        $this->assertNull($this->createItem(0, Order::STATE_PAYMENT_REVIEW, true)->getCount([]));
        $this->assertSame(3, $this->createItem(3, Order::STATE_PAYMENT_REVIEW, true)->getCount([]));
    }
}
