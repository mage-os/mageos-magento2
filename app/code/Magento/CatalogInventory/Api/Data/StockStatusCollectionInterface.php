<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
namespace Magento\CatalogInventory\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Stock Status collection interface
 * @api
 * @since 100.0.2
 *
 * @deprecated 100.3.0 Replaced with Multi Source Inventory
 * @link https://developer.adobe.com/commerce/webapi/rest/inventory/index.html
 * @link https://developer.adobe.com/commerce/webapi/rest/inventory/inventory-api-reference.html
 */
interface StockStatusCollectionInterface extends SearchResultsInterface
{
    /**
     * Get items
     *
     * @return \Magento\CatalogInventory\Api\Data\StockStatusInterface[]
     */
    public function getItems();

    /**
     * Sets items
     *
     * @param \Magento\CatalogInventory\Api\Data\StockStatusInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

    /**
     * Get search criteria.
     *
     * @return \Magento\CatalogInventory\Api\StockStatusCriteriaInterface
     */
    public function getSearchCriteria();
}
