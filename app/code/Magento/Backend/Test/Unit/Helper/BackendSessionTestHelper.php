<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\Model\Session;
use Magento\Framework\DataObject;

class BackendSessionTestHelper extends Session
{
    private $sessionData;
    
    public function __construct(DataObject $sessionData = null)
    {
        // Skip parent constructor for testing
        $this->sessionData = $sessionData;
    }
    
    public function setData($key, $value = null)
    {
        if ($this->sessionData) {
            $this->sessionData->setData($key, $value);
        }
        return $this;
    }
    
    public function getData($key = '', $index = null)
    {
        if ($this->sessionData) {
            return $this->sessionData->getData($key, $index);
        }
        return null;
    }
}
