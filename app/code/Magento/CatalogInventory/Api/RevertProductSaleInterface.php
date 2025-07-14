<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Api;

/**
 * @api
 *
 * @deprecated 100.3.0 Replaced with Multi Source Inventory
 * @link https://developer.adobe.com/commerce/webapi/rest/inventory/index.html
 * @link https://developer.adobe.com/commerce/webapi/rest/inventory/inventory-api-reference.html
 * @since 100.3.0
 */
interface RevertProductSaleInterface
{
    /**
     * Revert register product sale
     *
     * Method signature is unchanged for backward compatibility
     *
     * @param string[] $items
     * @param int $websiteId
     * @return bool
     * @since 100.3.0
     */
    public function revertProductsSale($items, $websiteId = null);
}
