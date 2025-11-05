<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Eav\Test\Unit\Helper;

use Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection;

class CollectionTestHelper extends Collection
{
    /**
     * @var mixed
     */
    private $joinLeftResult = null;

    /**
     * @var mixed
     */
    private $orderResult = null;

    /**
     * @var mixed
     */
    private $checkSql = null;

    /**
     * @var mixed
     */
    private $select = null;

    /**
     * @var mixed
     */
    private $storeId = null;

    /**
     * @var mixed
     */
    private $connection = null;

    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @param string $table
     * @param string $condition
     * @param string $columns
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function joinLeft($table, $condition, $columns = '*')
    {
        return $this->joinLeftResult ?: $this;
    }

    /**
     * @param mixed $result
     * @return $this
     */
    public function setJoinLeftResult($result)
    {
        $this->joinLeftResult = $result;
        return $this;
    }

    /**
     * @param string $field
     * @param string $direction
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function order($field, $direction = 'ASC')
    {
        return $this->orderResult ?: $this;
    }

    /**
     * @param mixed $result
     * @return $this
     */
    public function setOrderResult($result)
    {
        $this->orderResult = $result;
        return $this;
    }

    /**
     * @param string $condition
     * @param string $true
     * @param string $false
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getCheckSql($condition, $true, $false)
    {
        return $this->checkSql;
    }

    /**
     * @param mixed $checkSql
     * @return $this
     */
    public function setCheckSql($checkSql)
    {
        $this->checkSql = $checkSql;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSelect()
    {
        return $this->select ?: $this;
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
     * @return mixed
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * @param mixed $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConnection()
    {
        return $this->connection ?: $this;
    }

    /**
     * @param mixed $connection
     * @return $this
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
        return $this;
    }
}

