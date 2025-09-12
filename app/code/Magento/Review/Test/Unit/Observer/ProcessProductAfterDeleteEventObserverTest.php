<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Review\Test\Unit\Observer;

use Magento\Catalog\Model\Product;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Review\Model\ResourceModel\Rating;
use Magento\Review\Model\ResourceModel\Review;
use Magento\Review\Observer\ProcessProductAfterDeleteEventObserver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProcessProductAfterDeleteEventObserverTest extends TestCase
{
    /**
     * Testable Object
     *
     * @var ProcessProductAfterDeleteEventObserver
     */
    private $observer;

    /**
     * @var Review|MockObject
     */
    private $resourceReviewMock;

    /**
     * @var Rating|MockObject
     */
    private $resourceRatingMock;

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->resourceReviewMock = $this->createMock(Review::class);
        $this->resourceRatingMock = $this->createMock(Rating::class);

        $this->observer = new ProcessProductAfterDeleteEventObserver(
            $this->resourceReviewMock,
            $this->resourceRatingMock
        );
    }

    /**
     * Test cleanup product reviews after product delete
     *
     * @return void
     */
    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testCleanupProductReviewsWithProduct()
    {
        $productId = 1;
        $observerMock = $this->createMock(Observer::class);
        $eventMock = new class extends Event {
            /**
             * @var mixed
             */
            private $product;
            public function __construct()
            {
            }
            public function getProduct()
            {
                return $this->product;
            }
            public function setProduct($product)
            {
                $this->product = $product;
            }
        };

        $productMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId'])
            ->getMock();

        $productMock->expects(self::exactly(3))
            ->method('getId')
            ->willReturn($productId);
        $eventMock->setProduct($productMock);
        $observerMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);
        $this->resourceReviewMock->expects($this->once())
            ->method('deleteReviewsByProductId')
            ->willReturnSelf();
        $this->resourceRatingMock->expects($this->once())
            ->method('deleteAggregatedRatingsByProductId')
            ->willReturnSelf();

        $this->observer->execute($observerMock);
    }

    /**
     * Test with no event product
     *
     * @return void
     */
    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testCleanupProductReviewsWithoutProduct()
    {
        $observerMock = $this->createMock(Observer::class);
        $eventMock = new class extends Event {
            /**
             * @var mixed
             */
            private $product;
            public function __construct()
            {
            }
            public function getProduct()
            {
                return $this->product;
            }
            public function setProduct($product)
            {
                $this->product = $product;
            }
        };

        // Event mock methods provided by anonymous class (returns null by default)
        $observerMock->expects($this->once())
            ->method('getEvent')
            ->willReturn($eventMock);
        $this->resourceReviewMock->expects($this->never())
            ->method('deleteReviewsByProductId')
            ->willReturnSelf();
        $this->resourceRatingMock->expects($this->never())
            ->method('deleteAggregatedRatingsByProductId')
            ->willReturnSelf();

        $this->observer->execute($observerMock);
    }
}
