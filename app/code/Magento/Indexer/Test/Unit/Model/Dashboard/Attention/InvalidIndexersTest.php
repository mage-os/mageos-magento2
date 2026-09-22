<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Indexer\Test\Unit\Model\Dashboard\Attention;

use Magento\Backend\Model\Dashboard\Attention\CappedCounter;
use Magento\Framework\DB\Select;
use Magento\Framework\Indexer\StateInterface;
use Magento\Indexer\Model\Dashboard\Attention\InvalidIndexers;
use Magento\Indexer\Model\ResourceModel\Indexer\State\Collection;
use Magento\Indexer\Model\ResourceModel\Indexer\State\CollectionFactory;
use PHPUnit\Framework\TestCase;

class InvalidIndexersTest extends TestCase
{
    public function testCountsInvalidStatesIgnoringScope(): void
    {
        $select = $this->createMock(Select::class);
        $collection = $this->createMock(Collection::class);
        $collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with('status', StateInterface::STATUS_INVALID)
            ->willReturnSelf();
        $collection->method('getSelect')->willReturn($select);
        $factory = $this->createMock(CollectionFactory::class);
        $factory->method('create')->willReturn($collection);

        $counter = $this->createMock(CappedCounter::class);
        $counter->expects($this->once())->method('count')->with($select)->willReturn(3);

        $item = new InvalidIndexers($factory, $counter);

        $this->assertSame(3, $item->getCount([7]));
        $this->assertSame('Magento_Indexer::index', $item->getAclResource());
        $this->assertSame('indexer/indexer/list', $item->getUrlPath());
    }
}
