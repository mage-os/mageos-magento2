<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Test\Unit\Helper;

use Magento\Framework\Data\Collection\AbstractDb;

class OptionCollectionTestHelper extends AbstractDb
{
    /**
     * @var mixed
     */
    private $select = null;

    /**
     * @var mixed
     */
    private $newEmptyItem = null;

    /**
     * @var mixed
     */
    private $resource = null;

    /**
     * @param mixed $entityFactory
     * @param mixed $logger
     * @param mixed $fetchStrategy
     */
    public function __construct($entityFactory, $logger, $fetchStrategy)
    {
        parent::__construct($entityFactory, $logger, $fetchStrategy);
    }
    
    /**
     * Load data
     *
     * @return $this
     */
    protected function _loadEntities($printQuery = false, $logQuery = false)
    {
        if (!$this->_isCollectionLoaded) {
            $data = $this->getData();
            $this->_items = [];
            if (is_array($data) && !empty($data)) {
                foreach ($data as $row) {
                    $item = clone $this->getNewEmptyItem();
                    // Set all data - the Option mock uses magic methods
                    $item->setData($row);
                    $this->_items[] = $item;
                }
            }
            $this->_isCollectionLoaded = true;
        }
        return $this;
    }
    
    /**
     * Init collection state
     *
     * @return $this
     */
    protected function _initSelect()
    {
        return $this;
    }
    
    /**
     * Load collection
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            $this->_loadEntities($printQuery, $logQuery);
        }
        return $this;
    }
    
    /**
     * Render sql select orders
     *
     * @return $this
     */
    protected function _renderOrders()
    {
        return $this;
    }
    
    /**
     * Render sql select limit
     *
     * @return $this
     */
    protected function _renderLimit()
    {
        return $this;
    }

    /**
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return mixed
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * @return mixed
     */
    public function getNewEmptyItem()
    {
        return $this->newEmptyItem;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function addProductToFilter()
    {
        return $this;
    }

    /**
     * @param mixed $select
     * @return $this
     */
    public function setSelect($select)
    {
        $this->select = $select;
        return $this;
    }

    /**
     * @param mixed $item
     * @return $this
     */
    public function setNewEmptyItem($item)
    {
        $this->newEmptyItem = $item;
        return $this;
    }

    /**
     * @param mixed $resource
     * @return $this
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }
}

