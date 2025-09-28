<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Model\ResourceModel\Test\Unit\Helper;

use Magento\Framework\Model\ResourceModel\AbstractResource;

class AbstractResourceTestHelper extends AbstractResource
{
    public function __construct()
    {
        // Skip parent constructor for testing
    }
    
    public function getIdFieldName()
    {
        return 'id';
    }
    
    public function _construct()
    {
        // Empty implementation for testing
    }
    
    public function getConnection()
    {
        return null;
    }
}
