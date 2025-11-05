<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Framework\App\Request\Http;

/**
 * Test helper for Request
 */
class RequestTestHelper extends Http
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Skip parent constructor
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * getUri (custom method for testing)
     *
     * @return mixed
     */
    public function getUri()
    {
        return $this->data['uri'] ?? null;
    }

    /**
     * getRequestUri (custom method for testing)
     *
     * @return mixed
     */
    public function getRequestUri()
    {
        return $this->data['requestUri'] ?? null;
    }

    /**
     * getPathInfo (custom method for testing)
     *
     * @return mixed
     */
    public function getPathInfo()
    {
        return $this->data['pathInfo'] ?? null;
    }

    /**
     * getPost (custom method for testing)
     *
     * @param string|null $name
     * @param mixed $default
     * @return mixed
     */
    public function getPost($name = null, $default = null)
    {
        if ($name === null) {
            return $this->data['post'] ?? [];
        }
        return $this->data['post'][$name] ?? $default;
    }

    /**
     * setPostValue (custom method for testing)
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setPostValue($key, $value = null)
    {
        if (!isset($this->data['post'])) {
            $this->data['post'] = [];
        }
        $this->data['post'][$key] = $value;
        return $this;
    }
}
