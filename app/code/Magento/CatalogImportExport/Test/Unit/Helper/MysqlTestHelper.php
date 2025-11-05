<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Test\Unit\Helper;

use Magento\Framework\DB\Adapter\Pdo\Mysql;

class MysqlTestHelper extends Mysql
{
    /**
     * @var mixed
     */
    private $selectResult = null;

    /**
     * @var mixed
     */
    private $fetchAllResult = null;

    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    public function select()
    {
        return $this->selectResult;
    }

    /**
     * @param mixed $sql
     * @param array $bind
     * @param mixed $fetchMode
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetchAll($sql = '', $bind = [], $fetchMode = null)
    {
        return $this->fetchAllResult;
    }

    /**
     * @param mixed $sql
     * @param array $bind
     * @return null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetchPairs($sql = '', $bind = [])
    {
        return null;
    }

    /**
     * @param string $table
     * @param array $data
     * @param array $fields
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function insertOnDuplicate($table, array $data, array $fields = [])
    {
        return $this;
    }

    /**
     * @param string $table
     * @param string $where
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function delete($table, $where = '')
    {
        return $this;
    }

    /**
     * @param string $text
     * @param mixed $value
     * @param mixed $type
     * @param mixed $count
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function quoteInto($text, $value, $type = null, $count = null)
    {
        return '';
    }

    /**
     * @param mixed $name
     * @param mixed $cond
     * @param mixed $cols
     * @param mixed $schema
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function joinLeft($name, $cond, $cols = '*', $schema = null)
    {
        return $this;
    }

    /**
     * @param mixed $select
     * @return $this
     */
    public function setSelect($select)
    {
        $this->selectResult = $select;
        return $this;
    }

    /**
     * @param mixed $result
     * @return $this
     */
    public function setFetchAll($result)
    {
        $this->fetchAllResult = $result;
        return $this;
    }
}

