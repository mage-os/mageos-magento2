<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\UrlRewrite\Test\Unit\Helper;

use Magento\UrlRewrite\Model\UrlRewrite;

class UrlRewriteTestHelper extends UrlRewrite
{
    /**
     * @var int
     */
    private $id = 1;
    
    /**
     * @var int
     */
    private $storeId = 1;
    
    public function __construct()
    {
        // Skip parent constructor for testing
    }
    
    public function load($id, $field = null)
    {
        // Suppress unused parameter warnings for test helper
        unset($id, $field);
        return $this;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
    
    public function getStoreId()
    {
        return $this->storeId;
    }
    
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        return $this;
    }
}
