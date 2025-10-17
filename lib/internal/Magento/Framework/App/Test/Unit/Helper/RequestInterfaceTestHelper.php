<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\App\Test\Unit\Helper;

use Magento\Framework\App\Console\Request as ConsoleRequest;
use Magento\Framework\App\RequestInterface;

/**
 * Test helper for RequestInterface
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class RequestInterfaceTestHelper implements RequestInterface
{
    /**
     * @var array
     */
    public array $postData = [];

    /**
     * @var array
     */
    public array $postValue = [];

    /**
     * @var array
     */
    public array $params = [];

    /**
     * @var bool
     */
    public bool $isAjax = false;

    /**
     * @var ConsoleRequest
     */
    private ConsoleRequest $delegate;

    public function __construct()
    {
        $this->delegate = new ConsoleRequest([]);
    }

    public function getModuleName()
    {
        return $this->delegate->getModuleName();
    }

    public function setModuleName($name)
    {
        return $this->delegate->setModuleName($name);
    }

    public function getActionName()
    {
        return $this->delegate->getActionName();
    }

    public function setActionName($name)
    {
        return $this->delegate->setActionName($name);
    }

    public function getCookie($name, $default)
    {
        return $this->delegate->getCookie($name, $default);
    }

    public function isSecure()
    {
        return $this->delegate->isSecure();
    }

    public function getParam($key, $defaultValue = null)
    {
        return $this->params[$key] ?? $defaultValue;
    }

    public function setParams(array $params)
    {
        $this->params = $params;
        return $this;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getPost($key = null)
    {
        return $key ? ($this->postData[$key] ?? null) : $this->postData;
    }

    public function getPostValue($key = null)
    {
        return $key ? ($this->postValue[$key] ?? null) : $this->postValue;
    }

    public function isAjax()
    {
        return $this->isAjax;
    }
}
