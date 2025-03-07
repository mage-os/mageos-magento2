<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Model\Product;

use Magento\Framework\Exception\LocalizedException;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\Store\Model\StoreManagerInterface;

class QuantityValidator
{
    /**
     * @param GetStockItemConfigurationInterface $getStockItemConfiguration
     * @param StoreManagerInterface $storeManager
     * @param StockResolverInterface $stockResolver
     */
    public function __construct(
        private readonly GetStockItemConfigurationInterface $getStockItemConfiguration,
        private readonly StoreManagerInterface $storeManager,
        private readonly StockResolverInterface $stockResolver
    ) {
    }

    /**
     * To get quantity validators
     *
     * @param string $sku
     * @param int|null $websiteId
     *
     * @return array
     */
    public function getData(string $sku, int|null $websiteId): array
    {
        try {
            $stockItemConfig = $this->getStockItemConfiguration->execute(
                $sku,
                $this->getStockId($websiteId)
            );

            $params = [];
            $validators = [];
            $params['minAllowed'] = $stockItemConfig->getMinSaleQty();

            if ($stockItemConfig->getMaxSaleQty()) {
                $params['maxAllowed'] = $stockItemConfig->getMaxSaleQty();
            }
            if ($stockItemConfig->getQtyIncrements() > 0) {
                $params['qtyIncrements'] = $stockItemConfig->getQtyIncrements();
            }
            $validators['validate-item-quantity'] = $params;

            return $validators;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get Stock ID by Website ID
     *
     * @param int|null $websiteId
     * @return int
     * @throws LocalizedException
     */
    private function getStockId(?int $websiteId): int
    {
        if ($websiteId === null) {
            $websiteId = $this->storeManager->getWebsite()->getId();
        }

        $websiteCode = $this->storeManager->getWebsite($websiteId)->getCode();
        $stock = $this->stockResolver->execute(
            SalesChannelInterface::TYPE_WEBSITE,
            $websiteCode
        );

        return (int) $stock->getStockId();
    }
}
