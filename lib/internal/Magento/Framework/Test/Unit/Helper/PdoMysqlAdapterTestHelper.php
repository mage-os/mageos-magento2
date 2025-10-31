<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\Select;

/**
 * Test helper for Pdo Mysql adapter
 * 
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PdoMysqlAdapterTestHelper extends Mysql
{
    /**
     * @var Select|null
     */
    private $selectMock = null;

    /**
     * @var mixed
     */
    private $queryResult = null;

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get select instance
     *
     * @return Select|null
     */
    public function select()
    {
        return $this->selectMock;
    }

    /**
     * Set select mock
     *
     * @param Select $select
     * @return $this
     */
    public function setSelectMock(Select $select): self
    {
        $this->selectMock = $select;
        return $this;
    }

    /**
     * Execute query
     *
     * @param mixed $sql
     * @param array $bind
     * @return mixed
     */
    public function query($sql, $bind = [])
    {
        return $this->queryResult;
    }

    /**
     * Set query result
     *
     * @param mixed $result
     * @return $this
     */
    public function setQueryResult($result): self
    {
        $this->queryResult = $result;
        return $this;
    }

    /**
     * From method (custom for tests)
     *
     * @param mixed $table
     * @return $this
     */
    public function from($table): self
    {
        return $this;
    }

    /**
     * Order method (custom for tests)
     *
     * @param mixed $order
     * @return $this
     */
    public function order($order): self
    {
        return $this;
    }
}
