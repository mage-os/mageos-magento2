<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Search\Test\Unit\Helper;

use Magento\Search\Model\Query;

/**
 * Test helper for Query class
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class QueryTestHelper extends Query
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var string
     */
    private $queryText;

    /**
     * @var int
     */
    private $numResults;

    /**
     * @var bool
     */
    private $isQueryTextExceeded;

    /**
     * @var bool
     */
    private $isQueryTextShort;

    /**
     * @var bool
     */
    private $isProcessed;

    /**
     * @var int
     */
    private $popularity;

    /**
     * @var array
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
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get store ID
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * Set store ID
     *
     * @param int $id
     * @return $this
     */
    public function setStoreId($id)
    {
        $this->storeId = $id;
        return $this;
    }

    /**
     * Get query text
     *
     * @return string|null
     */
    public function getQueryText()
    {
        return $this->queryText;
    }

    /**
     * Set query text
     *
     * @param string $text
     * @return $this
     */
    public function setQueryText($text)
    {
        $this->queryText = $text;
        return $this;
    }

    /**
     * Get number of results
     *
     * @return int|null
     */
    public function getNumResults()
    {
        return $this->numResults;
    }

    /**
     * Set number of results
     *
     * @param int $num
     * @return $this
     */
    public function setNumResults($num)
    {
        $this->numResults = $num;
        return $this;
    }

    /**
     * Get is query text exceeded
     *
     * @return bool|null
     */
    public function getIsQueryTextExceeded()
    {
        return $this->isQueryTextExceeded;
    }

    /**
     * Set is query text exceeded
     *
     * @param bool $exceeded
     * @return $this
     */
    public function setIsQueryTextExceeded($exceeded)
    {
        $this->isQueryTextExceeded = $exceeded;
        return $this;
    }

    /**
     * Get is query text short
     *
     * @return bool|null
     */
    public function getIsQueryTextShort()
    {
        return $this->isQueryTextShort;
    }

    /**
     * Set is query text short
     *
     * @param bool $short
     * @return $this
     */
    public function setIsQueryTextShort($short)
    {
        $this->isQueryTextShort = $short;
        return $this;
    }

    /**
     * Get is processed
     *
     * @return bool|null
     */
    public function getIsProcessed()
    {
        return $this->isProcessed;
    }

    /**
     * Set is processed
     *
     * @param bool $processed
     * @return $this
     */
    public function setIsProcessed($processed)
    {
        $this->isProcessed = $processed;
        return $this;
    }

    /**
     * Get popularity
     *
     * @return int|null
     */
    public function getPopularity()
    {
        return $this->popularity;
    }

    /**
     * Set popularity
     *
     * @param int $pop
     * @return $this
     */
    public function setPopularity($pop)
    {
        $this->popularity = $pop;
        return $this;
    }

    /**
     * Load by query text
     *
     * @param string $text
     * @return $this
     */
    public function loadByQueryText($text)
    {
        $this->queryText = $text;
        return $this;
    }

    /**
     * Load by ID
     *
     * @param int $modelId
     * @param string|null $field
     * @return $this
     */
    public function load($modelId, $field = null)
    {
        $this->id = $modelId;
        return $this;
    }

    /**
     * Add data
     *
     * @param array $data
     * @return $this
     */
    public function addData($data)
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Set data
     *
     * @param array|string $key
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
     * Get data
     *
     * @param string $key
     * @param string|int|null $index
     * @return mixed
     */
    public function getData($key = '', $index = null)
    {
        if ($key === '') {
            return $this->data;
        }
        return $this->data[$key] ?? null;
    }

    /**
     * Save
     *
     * @return $this
     */
    public function save()
    {
        return $this;
    }
}
