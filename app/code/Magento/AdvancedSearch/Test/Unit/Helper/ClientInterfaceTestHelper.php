<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\AdvancedSearch\Test\Unit\Helper;

use Magento\AdvancedSearch\Model\Client\ClientInterface;

/**
 * Test helper for ClientInterface
 * Provides ping() and testConnection() methods that don't exist on the interface
 */
class ClientInterfaceTestHelper implements ClientInterface
{
    /**
     * @var bool
     */
    private $pingResult = true;

    /**
     * @var bool
     */
    private $testConnectionResult = true;

    /**
     * @var array
     */
    private $queryResults = [];
    
    /**
     * @var int
     */
    private $queryResultIndex = 0;

    /**
     * Skip constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Ping method for testing
     *
     * @return bool
     */
    public function ping(): bool
    {
        return $this->pingResult;
    }

    /**
     * Set ping result
     *
     * @param bool $result
     * @return $this
     */
    public function setPingResult(bool $result): self
    {
        $this->pingResult = $result;
        return $this;
    }

    /**
     * Test connection method for testing
     *
     * @return bool
     */
    public function testConnection(): bool
    {
        return $this->testConnectionResult;
    }

    /**
     * Set test connection result
     *
     * @param bool $result
     * @return $this
     */
    public function setTestConnectionResult(bool $result): self
    {
        $this->testConnectionResult = $result;
        return $this;
    }

    /**
     * Query method (from interface)
     *
     * @param array $query
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function query($query): array
    {
        if (empty($this->queryResults)) {
            return [];
        }
        
        $result = $this->queryResults[$this->queryResultIndex] ?? [];
        $this->queryResultIndex++;
        
        return $result;
    }

    /**
     * Set query results
     *
     * @param array $results
     * @return $this
     */
    public function setQueryResults(array $results): self
    {
        $this->queryResults = $results;
        $this->queryResultIndex = 0;
        return $this;
    }

    /**
     * Bulk query method (from interface)
     *
     * @param array $query
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function bulkQuery($query): array
    {
        return [];
    }
}
