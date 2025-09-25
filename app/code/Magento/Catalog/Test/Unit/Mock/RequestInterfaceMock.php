<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Mock;

use Magento\Framework\App\RequestInterface;

/**
 * Mock class for RequestInterface with additional methods
 */
class RequestInterfaceMock implements RequestInterface
{
    /**
     * Mock method for getPostValue
     *
     * @param string|null $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getPostValue($key = null, $defaultValue = null)
    {
        return $defaultValue;
    }

    /**
     * Mock method for has
     *
     * @param string $key
     * @return bool
     */
    public function has($key): bool
    {
        return false;
    }

    // Required methods from RequestInterface
    public function getModuleName()
    {
        return '';
    }

    public function setModuleName($name)
    {
        return $this;
    }

    public function getActionName()
    {
        return '';
    }

    public function setActionName($name)
    {
        return $this;
    }

    public function getParam($key, $defaultValue = null)
    {
        return $defaultValue;
    }

    public function setParams(array $params)
    {
        return $this;
    }

    public function getParams(): array
    {
        return [];
    }

    public function getCookie($name, $default)
    {
        return $default;
    }

    public function isSecure(): bool
    {
        return false;
    }
}
