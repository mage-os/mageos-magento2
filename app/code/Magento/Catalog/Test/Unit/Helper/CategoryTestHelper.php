<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Category;

/**
 * TestHelper for Category with dynamic methods
 */
class CategoryTestHelper extends Category
{
    /** @var array */
    private $changedProductIds = [];
    
    /** @var array */
    private $data = [];

    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    public function getChangedProductIds()
    {
        return $this->changedProductIds;
    }

    public function setChangedProductIds($value)
    {
        $this->changedProductIds = $value;
        return $this;
    }
    
    public function setUrlPath($value)
    {
        $this->data['url_path'] = $value;
        return $this;
    }
    
    public function getUrlPath()
    {
        return $this->data['url_path'] ?? null;
    }
    
    public function unsUrlPath()
    {
        unset($this->data['url_path']);
        return $this;
    }
    
    public function setUrlKey($value)
    {
        $this->data['url_key'] = $value;
        return $this;
    }
    
    public function getUrlKey()
    {
        return $this->data['url_key'] ?? null;
    }
    
    public function getStore()
    {
        return $this->data['store'] ?? null;
    }
    
    public function setStore($value)
    {
        $this->data['store'] = $value;
        return $this;
    }

    public function __wakeUp()
    {
        // Implementation for __wakeUp method
    }
}