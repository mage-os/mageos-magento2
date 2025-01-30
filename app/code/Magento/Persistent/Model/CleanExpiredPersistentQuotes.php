<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Persistent\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Quote\Model\ResourceModel\Quote\Collection as QuoteCollection;
use Magento\Persistent\Model\ResourceModel\ExpiredPersistentQuotesCollection;
use Magento\Customer\Model\Logger as CustomerLogger;
use Magento\Quote\Model\QuoteRepository;
use Psr\Log\LoggerInterface;
use Exception;

/**
 * Cleaning expired persistent quotes from the cron
 */
class CleanExpiredPersistentQuotes
{
    /**
     * @param StoreManagerInterface $storeManager
     * @param ExpiredPersistentQuotesCollection $expiredPersistentQuotesCollection
     * @param CustomerLogger $customerLogger
     * @param QuoteRepository $quoteRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private readonly ExpiredPersistentQuotesCollection $expiredPersistentQuotesCollection,
        private readonly CustomerLogger $customerLogger,
        private readonly QuoteRepository $quoteRepository,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * Removes expired persistent quotes for a specific website, identified by its ID
     *
     * @param int $websiteId
     * @return void
     * @throws LocalizedException
     */
    public function execute(int $websiteId): void
    {
        $stores = $this->storeManager->getWebsite($websiteId)->getStores();

        foreach ($stores as $store) {
            /** @var $quoteCollection QuoteCollection */
            $quoteCollection = $this->expiredPersistentQuotesCollection->getExpiredPersistentQuotes($store);
            $quoteCollection->setPageSize(50);

            // Last page returns 1 even when we don't have any results
            $lastPage = $quoteCollection->getSize() ? $quoteCollection->getLastPageNumber() : 0;

            for ($currentPage = $lastPage; $currentPage >= 1; $currentPage--) {
                $quoteCollection->setCurPage($currentPage);

                $this->deletePersistentQuotes($quoteCollection);
            }
        }
    }

    /**
     * Deletes all quotes in the collection.
     *
     * @param QuoteCollection $quoteCollection
     */
    private function deletePersistentQuotes(QuoteCollection $quoteCollection): void
    {
        foreach ($quoteCollection as $quote) {
            try {
                if (!$this->isLoggedInCustomer((int) $quote->getCustomerId())) {
                    $this->quoteRepository->delete($quote);
                }
            } catch (Exception $e) {
                $message = sprintf(
                    'Unable to delete expired quote (ID: %s): %s',
                    $quote->getId(),
                    (string)$e
                );
                $this->logger->error($message);
            }
        }

        $quoteCollection->clear();
    }

    /**
     * Determine if the customer is currently logged in based on their last login and logout timestamps.
     *
     * @param int $customerId
     * @return bool
     */
    private function isLoggedInCustomer(int $customerId): bool
    {
        $isLoggedIn = false;
        $customerLastLoginAt = strtotime($this->customerLogger->get($customerId)->getLastLoginAt());
        $customerLastLogoutAt = strtotime($this->customerLogger->get($customerId)->getLastLogoutAt());
        if ($customerLastLoginAt > $customerLastLogoutAt) {
            $isLoggedIn = true;
        }
        return $isLoggedIn;
    }
}
