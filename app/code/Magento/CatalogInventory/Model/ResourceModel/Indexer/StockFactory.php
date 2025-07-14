<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */

/**
 * CatalogInventory Stock Indexers Factory
 */
namespace Magento\CatalogInventory\Model\ResourceModel\Indexer;

/**
 * @api
 * @since 100.0.2
 *
 * @deprecated 100.3.0 Replaced with Multi Source Inventory
 * @link https://developer.adobe.com/commerce/webapi/rest/inventory/index.html
 * @link https://developer.adobe.com/commerce/webapi/rest/inventory/inventory-api-reference.html
 */
class StockFactory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Default Stock Indexer resource model name
     *
     * @var string
     */
    protected $_defaultIndexer = \Magento\CatalogInventory\Model\ResourceModel\Indexer\Stock\DefaultStock::class;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new indexer object
     *
     * @param string $indexerClassName
     * @param array $data
     * @return \Magento\CatalogInventory\Model\ResourceModel\Indexer\Stock\StockInterface
     * @throws \InvalidArgumentException
     */
    public function create($indexerClassName = '', array $data = [])
    {
        if (empty($indexerClassName)) {
            $indexerClassName = $this->_defaultIndexer;
        }
        $indexer = $this->_objectManager->create($indexerClassName, $data);
        if (false == $indexer instanceof \Magento\CatalogInventory\Model\ResourceModel\Indexer\Stock\StockInterface) {
            throw new \InvalidArgumentException(
                $indexerClassName .
                ' doesn\'t implement \Magento\CatalogInventory\Model\ResourceModel\Indexer\Stock\StockInterface'
            );
        }
        return $indexer;
    }
}
