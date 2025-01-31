<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Persistent\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Persistent\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\ResourceModel\Quote\Collection;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Handles the collection of expired persistent quotes.
 */
class ExpiredPersistentQuotesCollection
{
    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param CollectionFactory $quoteCollectionFactory
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly CollectionFactory $quoteCollectionFactory
    ) {
    }

    /**
     * Retrieves the collection of expired persistent quotes.
     *
     * Filters and returns all quotes that have expired based on the persistent lifetime threshold.
     *
     * @param StoreInterface $store
     * @return AbstractCollection
     */
    public function getExpiredPersistentQuotes(StoreInterface $store): AbstractCollection
    {
        $lifetime = $this->scopeConfig->getValue(
            Data::XML_PATH_LIFE_TIME,
            ScopeInterface::SCOPE_WEBSITE,
            $store->getWebsiteId()
        );

        /** @var $quotes Collection */
        $quotes = $this->quoteCollectionFactory->create();
        $quotes->getSelect()->join(
            ['cl' => $quotes->getTable('customer_log')],
            'cl.customer_id = main_table.customer_id',
            []
        )->where('cl.last_logout_at > cl.last_login_at');
        $quotes->addFieldToFilter('main_table.store_id', $store->getId());
        $quotes->addFieldToFilter('main_table.updated_at', ['lt' => gmdate("Y-m-d H:i:s", time() - $lifetime)]);
        $quotes->addFieldToFilter('main_table.is_persistent', 1);

        return $quotes;
    }
}
