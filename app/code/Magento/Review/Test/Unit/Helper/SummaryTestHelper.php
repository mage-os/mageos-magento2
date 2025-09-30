<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Review\Test\Unit\Helper;

use Magento\Review\Model\Review\Summary;

/**
 * Test helper for Review Summary
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class SummaryTestHelper extends Summary
{
    /**
     * @var mixed
     */
    private $data = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Load method
     *
     * @param mixed $modelId
     * @param mixed $field
     * @return $this
     */
    public function load($modelId, $field = null)
    {
        return $this;
    }

    /**
     * Get data
     *
     * @param mixed $key
     * @param mixed $index
     * @return array
     */
    public function getData($key = '', $index = null)
    {
        return $this->data;
    }

    /**
     * Set data
     *
     * @param mixed $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = $key;
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * Wakeup method
     *
     * @return $this
     */
    public function __wakeup()
    {
        return $this;
    }

    /**
     * Set store ID
     *
     * @param mixed $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this;
    }
}
