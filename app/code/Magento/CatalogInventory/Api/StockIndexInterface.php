<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\CatalogInventory\Api;

/**
 * Interface StockIndexInterface
 * @api
 * @since 100.0.2
 *
 * @deprecated 100.3.0 Replaced with Multi Source Inventory
 * @link https://developer.adobe.com/commerce/webapi/rest/inventory/index.html
 * @link https://developer.adobe.com/commerce/webapi/rest/inventory/inventory-api-reference.html
 */
interface StockIndexInterface
{
    /**
     * Rebuild stock index of the given scope
     *
     * @param int $productId
     * @param int $scopeId
     * @return bool
     */
    public function rebuild($productId = null, $scopeId = null);
}
