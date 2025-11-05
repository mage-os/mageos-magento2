<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Config\Test\Unit\Helper;

use Magento\Backend\Model\Session;

/**
 * Test helper for Session
 */
class SessionTestHelper extends Session
{
    private $data = [];

    public function __construct()
    {
        // Skip parent constructor
    }

    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = $key;
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    public function getData($key = null, $default = null)
    {
        if ($key === null) {
            return $this->data;
        }
        return $this->data[$key] ?? $default;
    }
}
