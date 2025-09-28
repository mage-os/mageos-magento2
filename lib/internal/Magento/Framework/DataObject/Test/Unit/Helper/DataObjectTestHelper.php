<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\DataObject\Test\Unit\Helper;

use Magento\Framework\DataObject;

class DataObjectTestHelper extends DataObject
{
    public function getCustomerLoggedIn()
    {
        return $this->getData('customer_logged_in');
    }
    
    public function getShouldProceed()
    {
        return $this->getData('should_proceed');
    }
}
