<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Webapi\Test\Unit\Helper;

use Magento\Framework\Webapi\Request;

/**
 * Test helper for Magento\Framework\Webapi\Request
 * 
 * Provides test-specific methods for Request without complex constructor dependencies
 */
class RequestTestHelper extends Request
{
    /**
     * @var array
     */
    private $postData = [];
    
    public function __construct()
    {
        // Skip parent constructor for testing
    }
    
    /**
     * Get post value
     *
     * @param string|null $name
     * @param mixed $default
     * @return mixed
     */
    public function getPostValue($name = null, $default = null)
    {
        if ($name === null) {
            return $this->postData;
        }
        return $this->postData[$name] ?? $default;
    }
    
    /**
     * Set post data
     *
     * @param array $data
     * @return $this
     */
    public function setPostData($data)
    {
        $this->postData = $data;
        return $this;
    }
}

