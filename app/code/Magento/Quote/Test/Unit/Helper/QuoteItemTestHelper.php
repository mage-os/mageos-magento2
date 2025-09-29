<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Item;

class QuoteItemTestHelper extends Item
{
    /**
     * @var mixed
     */
    private $productId;

    /**
     * @var mixed
     */
    private $buyRequest;

    /**
     * @var mixed
     */
    private $product;
    
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }
    
    public function setProductId($productId)
    {
        $this->productId = $productId;
        return $this;
    }

    public function getProductId()
    {
        return $this->productId;
    }
    
    public function setBuyRequest($buyRequest)
    {
        $this->buyRequest = $buyRequest;
        return $this;
    }
    
    public function getBuyRequest()
    {
        return $this->buyRequest;
    }
    
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }
    
    public function getProduct()
    {
        return $this->product;
    }
}
