<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\DB\Test\Unit\Helper;

use Magento\Framework\DB\Select;

class SelectTestHelper extends Select
{
    /**
     * @var mixed
     */
    private $fromValue = null;

    /**
     * @var mixed
     */
    private $joinInnerValue = null;

    /**
     * @var mixed
     */
    private $whereValue = null;

    /**
     * @var mixed
     */
    private $columnsValue = null;

    /**
     * @var mixed
     */
    private $joinLeftValue = null;

    /**
     * @var array
     */
    private $fetchAllResult = [];

    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @param mixed $name
     * @param mixed $cols
     * @param mixed $schema
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function from($name, $cols = '*', $schema = null)
    {
        $this->fromValue = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->fromValue;
    }

    /**
     * @param mixed $name
     * @param mixed $cond
     * @param mixed $cols
     * @param mixed $schema
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function joinInner($name, $cond, $cols = self::SQL_WILDCARD, $schema = null)
    {
        $this->joinInnerValue = [$name, $cond, $cols];
        return $this;
    }

    /**
     * @return mixed
     */
    public function getJoinInner()
    {
        return $this->joinInnerValue;
    }

    /**
     * @param mixed $cond
     * @param mixed $value
     * @param mixed $type
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function where($cond, $value = null, $type = null)
    {
        $this->whereValue = $cond;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWhere()
    {
        return $this->whereValue;
    }

    /**
     * @param mixed $cols
     * @param mixed $correlationName
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function columns($cols = '*', $correlationName = null)
    {
        $this->columnsValue = $cols;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getColumns()
    {
        return $this->columnsValue;
    }

    /**
     * @param mixed $name
     * @param mixed $cond
     * @param mixed $cols
     * @param mixed $schema
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function joinLeft($name, $cond, $cols = self::SQL_WILDCARD, $schema = null)
    {
        $this->joinLeftValue = [$name, $cond, $cols];
        return $this;
    }

    /**
     * @return mixed
     */
    public function getJoinLeft()
    {
        return $this->joinLeftValue;
    }

    /**
     * @param array $result
     * @return $this
     */
    public function setFetchAllResult($result)
    {
        $this->fetchAllResult = $result;
        return $this;
    }

    /**
     * @return array
     */
    public function fetchAll()
    {
        return $this->fetchAllResult;
    }

    /**
     * @return $this
     */
    public function select()
    {
        return $this;
    }
}

