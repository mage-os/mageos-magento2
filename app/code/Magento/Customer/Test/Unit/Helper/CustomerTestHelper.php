<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\Customer;

class CustomerTestHelper extends Customer
{
    /**
     * @var int
     */
    private $storeId = 1;
    
    /**
     * @var int
     */
    private $callCount = 0;
    
    /**
     * @var mixed
     */
    private $websiteId = null;
    
    /**
     * @var mixed
     */
    private $id = null;
    
    public function __construct()
    {
        // Skip parent constructor for testing
    }
    
    public function getStoreId()
    {
        $this->callCount++;
        // Return store1 on first call, store2 on second call
        return $this->callCount === 1 ? 1 : 2;
    }
    
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        return $this;
    }
    
    public function getCallCount()
    {
        return $this->callCount;
    }
    
    public function resetCallCount()
    {
        $this->callCount = 0;
        return $this;
    }
    
    public function getWebsiteId()
    {
        return $this->websiteId;
    }
    
    public function setWebsiteId($websiteId)
    {
        $this->websiteId = $websiteId;
        return $this;
    }
    
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load($id, $field = null)
    {
        $this->id = $id;
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
}
