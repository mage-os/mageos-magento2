<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Store\Test\Unit\Helper;

use Magento\Store\Model\Store;

class StoreTestHelper extends Store
{
    /**
     * @var string
     */
    private $code = 'default';

    /**
     * @var int
     */
    private $rootCategoryId = 2;

    /**
     * @var mixed
     */
    private $filters = null;

    /**
     * @var mixed
     */
    private $website = null;

    /**
     * @var mixed
     */
    private $baseCurrency = null;

    /**
     * @var mixed
     */
    private $id = null;

    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setRootCategoryId($id)
    {
        $this->rootCategoryId = $id;
        return $this;
    }

    public function getRootCategoryId()
    {
        return $this->rootCategoryId;
    }

    public function setFilters($filters)
    {
        $this->filters = $filters;
        return $this;
    }

    public function getFilters()
    {
        return $this->filters;
    }

    public function getWebsite()
    {
        return $this->website;
    }

    public function setWebsite($website)
    {
        $this->website = $website;
        return $this;
    }

    public function getBaseCurrency()
    {
        return $this->baseCurrency;
    }

    public function setBaseCurrency($value)
    {
        $this->baseCurrency = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setIdReturn($id)
    {
        $this->id = $id;
        return $this;
    }
}

