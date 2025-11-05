<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Indexer\Test\Unit\Helper;

use Magento\Framework\Indexer\IndexerRegistry;

class IndexerRegistryTestHelper extends IndexerRegistry
{
    /**
     * @var mixed
     */
    private $getResult = null;

    /**
     * @var mixed
     */
    private $loadResult = null;

    /**
     * @var mixed
     */
    private $setScheduledResult = null;

    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @param string $indexerId
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function get($indexerId)
    {
        return $this->getResult;
    }

    /**
     * @param mixed $result
     * @return $this
     */
    public function setGetResult($result)
    {
        $this->getResult = $result;
        return $this;
    }

    /**
     * @param string $indexerId
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load($indexerId)
    {
        return $this->loadResult;
    }

    /**
     * @param mixed $result
     * @return $this
     */
    public function setLoadResult($result)
    {
        $this->loadResult = $result;
        return $this;
    }

    /**
     * @param bool $scheduled
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setScheduled($scheduled)
    {
        return $this->setScheduledResult;
    }

    /**
     * @param mixed $result
     * @return $this
     */
    public function setSetScheduledResult($result)
    {
        $this->setScheduledResult = $result;
        return $this;
    }
}

