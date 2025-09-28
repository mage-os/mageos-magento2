<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Review\Test\Unit\Helper;

use Magento\Review\Model\Review;

class ReviewTestHelper extends Review
{
    /**
     * @var array
     */
    private $stores = [];
    
    public function __construct($stores = [])
    {
        // Skip parent constructor for testing
        $this->stores = $stores;
    }
    
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load($id, $field = null)
    {
        return $this;
    }
    
    public function getStores()
    {
        return $this->stores;
    }
    
    public function setStores($stores)
    {
        $this->stores = $stores;
        return $this;
    }
}
