<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CheckoutAgreements\Test\Unit\Helper;

use Magento\CheckoutAgreements\Model\Agreement;

class AgreementTestHelper extends Agreement
{
    /**
     * @var int
     */
    private $id = 1;
    
    /**
     * @var array
     */
    private $storeId = [1];
    
    public function __construct()
    {
        // Skip parent constructor for testing
    }
    
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load($id, $field = null)
    {
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
