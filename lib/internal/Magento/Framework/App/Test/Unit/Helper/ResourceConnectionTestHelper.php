<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\App\Test\Unit\Helper;

use Magento\Framework\App\ResourceConnection;

class ResourceConnectionTestHelper extends ResourceConnection
{
    /**
     * @var mixed
     */
    private $deleteResult = null;

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
     * @param string|array $where
     * @param mixed $adapter
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function delete($table, $where = '', $adapter = null)
    {
        return $this->deleteResult;
    }

    /**
     * @param mixed $result
     * @return $this
     */
    public function setDeleteResult($result)
    {
        $this->deleteResult = $result;
        return $this;
    }

    /**
     * @param string $connectionName
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getConnection($connectionName = ResourceConnection::DEFAULT_CONNECTION)
    {
        return $this->connection;
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

