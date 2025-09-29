<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\App\RequestInterface;

/**
 * Test helper class for RequestInterface used across Framework and related module tests
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
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
     * @var string
     */
    public string $moduleName = '';

    /**
     * @var string
     */
    public string $actionName = '';

    /**
     * @var string
     */
    public string $controllerName = '';

    /**
     * @var bool
     */
    public bool $isAjax = false;

    /**
     * @var string
     */
    public string $routeName = '';

    /**
     * @var string
     */
    public string $method = 'GET';

    /**
     * Constructor - skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get post data
     *
     * @return array
     */
    public function getPostData(): array
    {
        return $this->postData;
    }

    /**
     * Set post data
     *
     * @param array $postData
     * @return $this
     */
    public function setPostData(array $postData): self
    {
        $this->postData = $postData;
        return $this;
    }

    /**
     * Get param
     *
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getParam($key, $defaultValue = null)
    {
        return $this->params[$key] ?? $defaultValue;
    }

    /**
     * Set param
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setParam($key, $value): self
    {
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * Get module name
     *
     * @return string
     */
    public function getModuleName(): string
    {
        return $this->moduleName;
    }

    /**
     * Set module name
     *
     * @param mixed $moduleName
     * @return $this
     */
    public function setModuleName($moduleName): self
    {
        $this->moduleName = (string)$moduleName;
        return $this;
    }

    /**
     * Get action name
     *
     * @return string
     */
    public function getActionName(): string
    {
        return $this->actionName;
    }

    /**
     * Set action name
     *
     * @param mixed $actionName
     * @return $this
     */
    public function setActionName($actionName): self
    {
        $this->actionName = (string)$actionName;
        return $this;
    }

    /**
     * Get controller name
     *
     * @return string
     */
    public function getControllerName(): string
    {
        return $this->controllerName;
    }

    /**
     * Set controller name
     *
     * @param mixed $controllerName
     * @return $this
     */
    public function setControllerName($controllerName): self
    {
        $this->controllerName = (string)$controllerName;
        return $this;
    }

    /**
     * Get route name
     *
     * @return string
     */
    public function getRouteName(): string
    {
        return $this->routeName;
    }

    /**
     * Set route name
     *
     * @param string $routeName
     * @return $this
     */
    public function setRouteName(string $routeName): self
    {
        $this->routeName = $routeName;
        return $this;
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Set method
     *
     * @param string $method
     * @return $this
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Is ajax
     *
     * @return bool
     */
    public function isAjax(): bool
    {
        return $this->isAjax;
    }

    /**
     * Is secure
     *
     * @return bool
     */
    public function isSecure(): bool
    {
        return false;
    }

    /**
     * Get server
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getServer($key = null, $default = null)
    {
        return $default;
    }

    /**
     * Get header
     *
     * @param string $name
     * @return string|null
     */
    public function getHeader($name): ?string
    {
        return null;
    }

    /**
     * Get request URI
     *
     * @return string
     */
    public function getRequestUri(): string
    {
        return '';
    }

    /**
     * Get path info
     *
     * @return string
     */
    public function getPathInfo(): string
    {
        return '';
    }

    /**
     * Get base URL
     *
     * @param string $type
     * @return string
     */
    public function getBaseUrl($type = null): string
    {
        return '';
    }

    /**
     * Get base path
     *
     * @return string
     */
    public function getBasePath(): string
    {
        return '';
    }

    /**
     * Get full action name
     *
     * @param string $delimiter
     * @return string
     */
    public function getFullActionName($delimiter = '_'): string
    {
        return $this->moduleName . $delimiter . $this->controllerName . $delimiter . $this->actionName;
    }

    /**
     * Get post data
     *
     * @param string|null $key
     * @return mixed
     */
    public function getPost($key = null)
    {
        return $key ? ($this->postData[$key] ?? null) : $this->postData;
    }

    /**
     * Get post value
     *
     * @param string|null $key
     * @return mixed
     */
    public function getPostValue($key = null)
    {
        return $key ? ($this->postValue[$key] ?? null) : $this->postValue;
    }

    /**
     * Get params
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Get query
     *
     * @param string|null $key
     * @return mixed
     */
    public function getQuery($key = null)
    {
        return $key;
    }

    /**
     * Is post
     *
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    /**
     * Is get
     *
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    /**
     * Is put
     *
     * @return bool
     */
    public function isPut(): bool
    {
        return $this->method === 'PUT';
    }

    /**
     * Is delete
     *
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->method === 'DELETE';
    }

    /**
     * Is head
     *
     * @return bool
     */
    public function isHead(): bool
    {
        return $this->method === 'HEAD';
    }

    /**
     * Is patch
     *
     * @return bool
     */
    public function isPatch(): bool
    {
        return $this->method === 'PATCH';
    }

    /**
     * Is options
     *
     * @return bool
     */
    public function isOptions(): bool
    {
        return $this->method === 'OPTIONS';
    }

    /**
     * Is xml http request
     *
     * @return bool
     */
    public function isXmlHttpRequest(): bool
    {
        return false;
    }

    /**
     * Is flash
     *
     * @return bool
     */
    public function isFlash(): bool
    {
        return false;
    }

    /**
     * Set params
     *
     * @param array $params
     * @return $this
     */
    public function setParams(array $params): self
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Get cookie
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getCookie($name, $default = null)
    {
        return $default;
    }
}
