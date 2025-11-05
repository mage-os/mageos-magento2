<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\ResourceModel\Product\Link;

/**
 * Test helper for Product Link Resource Model
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ProductLinkResourceTestHelper extends Link
{
    /**
     * @var mixed
     */
    private $saveProductLinksResult = null;

    /**
     * @var mixed
     */
    private $attributeTypeTable = null;

    /**
     * @var mixed
     */
    private $attributesByType = null;

    /**
     * @var mixed
     */
    private $table = null;

    /**
     * @var mixed
     */
    private $idFieldName = null;

    /**
     * @var mixed
     */
    private $connection = null;

    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    protected function _construct()
    {
        // Implement abstract method - skip initialization
    }

    public function saveProductLinks($productId, $linkData, $typeId)
    {
        return $this->saveProductLinksResult;
    }

    public function setSaveProductLinksResult($result)
    {
        $this->saveProductLinksResult = $result;
        return $this;
    }

    public function getAttributeTypeTable($attributeType)
    {
        return $this->attributeTypeTable;
    }

    public function setAttributeTypeTable($attributeTypeTable)
    {
        $this->attributeTypeTable = $attributeTypeTable;
        return $this;
    }

    public function getAttributesByType($typeId)
    {
        return $this->attributesByType;
    }

    public function setAttributesByType($attributesByType)
    {
        $this->attributesByType = $attributesByType;
        return $this;
    }

    public function getTable($tableName)
    {
        return $this->table;
    }

    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    public function getIdFieldName()
    {
        return $this->idFieldName;
    }

    public function setIdFieldName($idFieldName)
    {
        $this->idFieldName = $idFieldName;
        return $this;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function setConnection($connection)
    {
        $this->connection = $connection;
        return $this;
    }
}

