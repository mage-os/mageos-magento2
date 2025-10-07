<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Elasticsearch\Test\Unit\Helper;

use Magento\Elasticsearch\Model\Adapter\Elasticsearch;

/**
 * Test helper for Elasticsearch adapter class
 */
class ElasticsearchTestHelper extends Elasticsearch
{
    /**
     * @var array
     */
    private $queryResult = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Query method
     *
     * @param array $query
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function query($query)
    {
        return $this->queryResult;
    }

    /**
     * Set query result
     *
     * @param array|null $result
     * @return $this
     */
    public function setQueryResult($result)
    {
        $this->queryResult = $result;
        return $this;
    }
}
