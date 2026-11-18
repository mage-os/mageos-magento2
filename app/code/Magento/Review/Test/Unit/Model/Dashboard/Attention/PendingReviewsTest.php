<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Review\Test\Unit\Model\Dashboard\Attention;

use Magento\Backend\Model\Dashboard\Attention\CappedCounter;
use Magento\Framework\DB\Select;
use Magento\Review\Model\Dashboard\Attention\PendingReviews;
use Magento\Review\Model\ResourceModel\Review\Collection;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory;
use Magento\Review\Model\Review;
use PHPUnit\Framework\TestCase;

class PendingReviewsTest extends TestCase
{
    public function testCountsDistinctPendingReviewsInScope(): void
    {
        $select = $this->createMock(Select::class);
        $collection = $this->createMock(Collection::class);
        $collection->expects($this->once())->method('addStatusFilter')->with(Review::STATUS_PENDING)->willReturnSelf();
        $collection->expects($this->once())->method('addStoreFilter')->with([1])->willReturnSelf();
        $collection->method('getSelect')->willReturn($select);
        $factory = $this->createMock(CollectionFactory::class);
        $factory->method('create')->willReturn($collection);

        $counter = $this->createMock(CappedCounter::class);
        $counter->expects($this->once())
            ->method('count')
            ->with($select, CappedCounter::CAP, 'main_table.review_id')
            ->willReturn(4);

        $item = new PendingReviews($factory, $counter);

        $this->assertSame(4, $item->getCount([1]));
        $this->assertSame('Magento_Review::pending', $item->getAclResource());
        $this->assertSame('review/product/pending', $item->getUrlPath());
    }

    public function testEmptyScopeSkipsStoreJoin(): void
    {
        $collection = $this->createMock(Collection::class);
        $collection->method('addStatusFilter')->willReturnSelf();
        $collection->expects($this->never())->method('addStoreFilter');
        $collection->method('getSelect')->willReturn($this->createMock(Select::class));
        $factory = $this->createMock(CollectionFactory::class);
        $factory->method('create')->willReturn($collection);
        $counter = $this->createMock(CappedCounter::class);
        $counter->method('count')->willReturn(0);

        $this->assertSame(0, (new PendingReviews($factory, $counter))->getCount([]));
    }
}
