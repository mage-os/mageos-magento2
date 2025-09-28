<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Webapi\Test\Unit\Helper;

use Magento\Framework\Webapi\Request;

class RequestTestHelper extends Request
{
    private $postData = [];
    
    public function __construct()
    {
        // Skip parent constructor for testing
    }
    
    public function getPostValue($name = null, $default = null)
    {
        if ($name === null) {
            return $this->postData;
        }
        return $this->postData[$name] ?? $default;
    }
    
    public function setPostData($data)
    {
        $this->postData = $data;
        return $this;
    }
}
