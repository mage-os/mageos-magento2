<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Persistent\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Persistent\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;

/**
 * Delete expired quote model
 */
class DeleteExpiredQuote
{
    /**
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly ResourceConnection $resourceConnection
    ) {
    }

    /**
     * Delete expired persistent quote for the website
     *
     * @param int|null $websiteId
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function deleteExpiredQuote(?int $websiteId): void
    {
        if ($websiteId === null) {
            $websiteId = $this->storeManager->getStore()->getWebsiteId();
        }

        $storeIds = $this->storeManager->getWebsite($websiteId)->getStoreIds();

        $lifetime = $this->scopeConfig->getValue(
            Data::XML_PATH_LIFE_TIME,
            'website',
            (int)$websiteId
        );

        if ($lifetime) {
            $expiredBefore = gmdate('Y-m-d H:i:s', time() - $lifetime);
            $this->resourceConnection->getConnection()->delete(
                $this->resourceConnection->getTableName('quote'),
                ['store_id in (?)' => $storeIds, 'updated_at < ?' => $expiredBefore, 'is_persistent' => 1]
            );
        }
    }
}
