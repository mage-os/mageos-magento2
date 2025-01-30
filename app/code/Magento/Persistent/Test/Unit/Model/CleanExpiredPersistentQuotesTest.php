<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Persistent\Test\Unit\Model;

use Magento\Persistent\Model\CleanExpiredPersistentQuotes;
use Magento\Persistent\Model\ResourceModel\ExpiredPersistentQuotesCollection;
use Magento\Customer\Model\Logger as CustomerLogger;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteRepository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Model\ResourceModel\Quote\Collection;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Website;
use Magento\Customer\Model\Log;

class CleanExpiredPersistentQuotesTest extends TestCase
{
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManagerMock;

    /**
     * @var ExpiredPersistentQuotesCollection
     */
    private ExpiredPersistentQuotesCollection $expiredPersistentQuotesCollectionMock;

    /**
     * @var CustomerLogger
     */
    private CustomerLogger $customerLoggerMock;

    /**
     * @var QuoteRepository
     */
    private QuoteRepository $quoteRepositoryMock;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $loggerMock;

    /**
     * @var CleanExpiredPersistentQuotes
     */
    private CleanExpiredPersistentQuotes $cleanExpiredPersistentQuotes;

    protected function setUp(): void
    {
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->expiredPersistentQuotesCollectionMock = $this->createMock(ExpiredPersistentQuotesCollection::class);
        $this->customerLoggerMock = $this->createMock(CustomerLogger::class);
        $this->quoteRepositoryMock = $this->createMock(QuoteRepository::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->cleanExpiredPersistentQuotes = new CleanExpiredPersistentQuotes(
            $this->storeManagerMock,
            $this->expiredPersistentQuotesCollectionMock,
            $this->customerLoggerMock,
            $this->quoteRepositoryMock,
            $this->loggerMock
        );
    }

    public function testExecuteDeletesExpiredQuotes(): void
    {
        $websiteId = 1;

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->method('getId')->willReturn(1);
        $storeMock->method('getWebsiteId')->willReturn(2);

        $websiteMock = $this->createMock(Website::class);
        $websiteMock->method('getStores')->willReturn([$storeMock]);

        $this->storeManagerMock->method('getWebsite')
            ->with($websiteId)
            ->willReturn($websiteMock);

        $quoteCollectionMock = $this->createMock(Collection::class);
        $quoteCollectionMock->method('getSize')->willReturn(1);  // Simulate that we have expired quotes
        $quoteCollectionMock->method('getLastPageNumber')->willReturn(1);
        $quoteCollectionMock->method('setPageSize')->willReturnSelf();
        $quoteCollectionMock->method('setCurPage')->willReturnSelf();

        $this->expiredPersistentQuotesCollectionMock
            ->method('getExpiredPersistentQuotes')
            ->with($storeMock)
            ->willReturn($quoteCollectionMock);

        $quoteMock = $this->getMockBuilder(Quote::class)
            ->disableOriginalConstructor()
            ->addMethods(['getCustomerId'])
            ->getMock();
        $quoteMock->method('getCustomerId')->willReturn(1);
        $quoteCollectionMock->method('getIterator')->willReturn(new \ArrayIterator([$quoteMock]));

        $logMock = $this->createMock(Log::class);
        $logMock->method('getLastLoginAt')->willReturn('2025-01-01 00:00:00');
        $logMock->method('getLastLogoutAt')->willReturn('2025-01-01 10:05:00');
        $this->customerLoggerMock->method('get')->willReturn($logMock);

        $this->quoteRepositoryMock->expects($this->once())->method('delete');

        $this->cleanExpiredPersistentQuotes->execute($websiteId);
    }
}
