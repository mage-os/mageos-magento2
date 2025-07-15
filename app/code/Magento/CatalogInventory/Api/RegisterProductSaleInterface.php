<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Api;

use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * @api
 *
 * @deprecated 100.3.0 Replaced with Multi Source Inventory
 * @link https://developer.adobe.com/commerce/webapi/rest/inventory/index.html
 * @link https://developer.adobe.com/commerce/webapi/rest/inventory/inventory-api-reference.html
 * @since 100.3.0
 */
interface RegisterProductSaleInterface
{
    /**
     * Subtract product qtys from stock
     * Return array of items that require full save
     *
     * Method signature is unchanged for backward compatibility
     *
     * @param float[] $items
     * @param int $websiteId
     * @return StockItemInterface[]
     * @throws LocalizedException
     * @since 100.3.0
     */
    public function registerProductsSale($items, $websiteId = null);
}
