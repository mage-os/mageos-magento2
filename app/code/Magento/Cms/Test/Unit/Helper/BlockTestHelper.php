<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Cms\Test\Unit\Helper;

use Magento\Cms\Model\Block;

class BlockTestHelper extends Block
{
    /**
     * @var mixed
     */
    private $storeId;
    
    /**
     * @var mixed
     */
    private $resource;
    
    /**
     * @var int
     */
    private $id = 1;
    
    public function __construct()
    {
        // Skip parent constructor for testing
    }
    
    public function getResource()
    {
        return $this->resource;
    }
    
    public function setResource($resource)
    {
        $this->resource = $resource;
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
