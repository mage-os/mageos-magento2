<?php
/**
 * Copyright 2019 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Sales\Cron;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Quote\Model\QuoteRepository;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory as QuoteCollectionFactory;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test for Magento\Sales\Cron\CleanExpiredQuotes class.
 *
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 */
class CleanExpiredQuotesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CleanExpiredQuotes
     */
    private $cleanExpiredQuotes;

    /**
     * @var QuoteCollectionFactory
     */
    private $quoteCollectionFactory;

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->cleanExpiredQuotes = $objectManager->get(CleanExpiredQuotes::class);
        $this->quoteCollectionFactory = $objectManager->get(QuoteCollectionFactory::class);
        $this->storeRepository = $objectManager->get(StoreRepositoryInterface::class);
    }

    /**
     * Check if outdated quotes are deleted.
     *
     * @magentoConfigFixture default_store checkout/cart/delete_quote_after -365
     * @magentoDataFixture Magento/Sales/_files/quotes.php
     */
    public function testExecute()
    {
        // The fixture adds one quote to each store. Earlier tests can leave quotes in the default store.
        $this->assertGreaterThanOrEqual(1, $this->getQuotesCount('default'));
        $this->assertEquals(1, $this->getQuotesCount('fixture_second_store'));

        //Deleting expired quotes
        $this->cleanExpiredQuotes->execute();

        //Only the quotes of the store that has all of them expired by config (default_store) are deleted
        $this->assertEquals(0, $this->getQuotesCount('default'));
        $this->assertEquals(1, $this->getQuotesCount('fixture_second_store'));
    }

    /**
     * Check if outdated quotes are deleted.
     *
     * @magentoConfigFixture default_store checkout/cart/delete_quote_after -365
     * @magentoDataFixture Magento/Sales/_files/quotes_big_amount.php
     */
    public function testExecuteWithBigAmountOfQuotes()
    {
        // The fixture adds 6000 quotes to the default store. Earlier tests can leave more there.
        $this->assertGreaterThanOrEqual(6000, $this->getQuotesCount('default'));

        //Deleting expired quotes
        $this->cleanExpiredQuotes->execute();

        //There should be no quotes in the default store anymore
        $this->assertEquals(0, $this->getQuotesCount('default'));
    }

    /**
     * Count the quotes of one store, not of the whole database, which earlier tests can leave quotes in.
     * Uses the collection size in order to get a quick result.
     *
     * @param string $storeCode
     * @return int
     */
    private function getQuotesCount(string $storeCode): int
    {
        return $this->quoteCollectionFactory->create()
            ->addFieldToFilter('store_id', (int)$this->storeRepository->get($storeCode)->getId())
            ->getSize();
    }
}
