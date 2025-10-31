<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\App\Request\Http;

/**
 * Test helper for RequestInterface with custom methods
 * 
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class RequestInterfaceTestHelper extends Http
{
    /**
     * @var bool
     */
    protected $isPost = false;

    /**
     * @var mixed
     */
    protected $postValue = null;

    /**
     * @var bool
     */
    protected $isGet = false;

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Check if request is POST (custom method for tests)
     *
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->isPost;
    }

    /**
     * Set is post flag
     *
     * @param bool $isPost
     * @return $this
     */
    public function setIsPost(bool $isPost): self
    {
        $this->isPost = $isPost;
        return $this;
    }

    /**
     * Get post value (custom method for tests)
     *
     * @param string|null $name
     * @param mixed $default
     * @return mixed
     */
    public function getPostValue($name = null, $default = null)
    {
        return $this->postValue;
    }

    /**
     * Set post value (override parent method for testing)
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setPostValue($name, $value = null)
    {
        $this->postValue = $value;
        return $this;
    }

    /**
     * Set test post value (for test setup)
     *
     * @param mixed $value
     * @return $this
     */
    public function setTestPostValue($value): self
    {
        $this->postValue = $value;
        return $this;
    }

    /**
     * Check if request is GET (custom method for tests)
     *
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->isGet;
    }

    /**
     * Set is GET flag
     *
     * @param bool $isGet
     * @return $this
     */
    public function setIsGet(bool $isGet): self
    {
        $this->isGet = $isGet;
        return $this;
    }

    /**
     * Set parameter value (override parent method for testing)
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
        return $this;
    }
}
