<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SalesSequence\Test\Unit\Observer;

use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\SalesSequence\Model\Sequence\DeleteByStore;
use Magento\SalesSequence\Observer\ScopeSequenceRemovalObserver;
use Magento\Store\Model\ResourceModel\Store\Collection as StoreCollection;
use Magento\Store\Model\ResourceModel\Website as WebsiteResource;
use Magento\Store\Model\Website;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ScopeSequenceRemovalObserverTest extends TestCase
{
    /**
     * @var DeleteByStore|MockObject
     */
    private $deleteByStore;

    /**
     * @var WebsiteResource|MockObject
     */
    private $resource;

    /**
     * @var ScopeSequenceRemovalObserver
     */
    private $observer;

    protected function setUp(): void
    {
        $this->deleteByStore = $this->createMock(DeleteByStore::class);
        $this->resource = $this->createMock(WebsiteResource::class);
        $this->observer = new ScopeSequenceRemovalObserver($this->deleteByStore);
    }

    public function testSequencesAreDeletedAfterCommit(): void
    {
        $callback = null;
        $this->resource->expects($this->once())
            ->method('addCommitCallback')
            ->willReturnCallback(
                function (callable $commitCallback) use (&$callback) {
                    $callback = $commitCallback;
                    return $this->resource;
                }
            );
        $deletedStoreIds = [];
        $this->deleteByStore->expects($this->exactly(2))->method('execute')->willReturnCallback(
            function ($storeId) use (&$deletedStoreIds) {
                $deletedStoreIds[] = $storeId;
            }
        );

        $this->observer->execute($this->createObserver(['2', '3']));
        $this->assertSame([], $deletedStoreIds, 'Sequences must not be removed before commit.');

        $callback();
        $this->assertSame([2, 3], $deletedStoreIds);
    }

    public function testNothingIsScheduledWithoutStores(): void
    {
        $this->resource->expects($this->never())->method('addCommitCallback');
        $this->deleteByStore->expects($this->never())->method('execute');

        $this->observer->execute($this->createObserver([]));
    }

    /**
     * @param array $storeIds
     * @return Observer
     */
    private function createObserver(array $storeIds): Observer
    {
        $storeCollection = $this->createStub(StoreCollection::class);
        $storeCollection->method('getAllIds')->willReturn($storeIds);
        $website = $this->createStub(Website::class);
        $website->method('getStoreCollection')->willReturn($storeCollection);
        $website->method('getResource')->willReturn($this->resource);

        return new Observer(['event' => new Event(['data_object' => $website])]);
    }
}
