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
     * @param int $lastId
     * @param int $batchSize
     * @return AbstractCollection
     */
    public function getExpiredPersistentQuotes(StoreInterface $store, int $lastId, int $batchSize): AbstractCollection
    {
        $lifetime = $this->scopeConfig->getValue(
            Data::XML_PATH_LIFE_TIME,
            ScopeInterface::SCOPE_WEBSITE,
            $store->getWebsiteId()
        );

        $lastLoginCondition = gmdate("Y-m-d H:i:s", time() - $lifetime);

        /** @var $quotes Collection */
        $quotes = $this->quoteCollectionFactory->create();

        $select = $quotes->getSelect();
        $select->joinLeft(
            ['cl1' => $quotes->getTable('customer_log')],
            'cl1.customer_id = main_table.customer_id
            AND cl1.last_login_at < cl1.last_logout_at
            AND cl1.last_logout_at IS NOT NULL',
            []
        )->joinLeft(
            ['cl2' => $quotes->getTable('customer_log')],
            'cl2.customer_id = main_table.customer_id
            AND cl2.last_login_at < "' . $lastLoginCondition . '"
            AND (cl2.last_logout_at IS NULL OR cl2.last_login_at > cl2.last_logout_at)',
            []
        );

        $quotes->addFieldToFilter('main_table.store_id', $store->getId());
        $quotes->addFieldToFilter('main_table.updated_at', ['lt' => $lastLoginCondition]);
        $quotes->addFieldToFilter('main_table.is_persistent', 1);
        $quotes->addFieldToFilter('main_table.entity_id', ['gt' => $lastId]);
        $quotes->setOrder('entity_id', Collection::SORT_ORDER_ASC);
        $quotes->setPageSize($batchSize);

        return $quotes;
    }
}
