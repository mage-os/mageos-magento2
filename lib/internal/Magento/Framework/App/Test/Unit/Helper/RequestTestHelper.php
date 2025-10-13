<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\App\Test\Unit\Helper;

use Magento\Framework\App\RequestInterface;

/**
 * Test helper for creating RequestInterface mocks with isDispatched method
 *
 * This helper implements RequestInterface and adds the isDispatched method
 * for testing purposes.
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class RequestTestHelper implements RequestInterface
{
    /**
     * @var bool
     */
    private $isDispatched = false;
    
    /**
     * Check if request is dispatched
     *
     * @return bool
     */
    public function isDispatched(): bool
    {
        return $this->isDispatched;
    }
    
    /**
     * Set dispatched flag
     *
     * @param bool $value
     * @return void
     */
    public function setIsDispatched(bool $value): void
    {
        $this->isDispatched = $value;
    }
    
    // Implement all required methods from RequestInterface (stubs)
    
    public function getModuleName()
    {
        return '';
    }
    
    public function setModuleName($name)
    {
        return $this;
    }
    
    public function getControllerName()
    {
        return '';
    }
    
    public function setControllerName($name)
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
    
    public function getRequestUri()
    {
        return '';
    }
    
    public function getPathInfo()
    {
        return '';
    }
    
    public function getOriginalPathInfo()
    {
        return '';
    }
    
    public function getBasePath()
    {
        return '';
    }
    
    public function getBaseUrl($type = null, $secure = null)
    {
        return '';
    }
    
    public function getScheme()
    {
        return '';
    }
    
    public function isSecure()
    {
        return false;
    }
    
    public function getHttpHost()
    {
        return '';
    }
    
    public function getClientIp($checkToUse = false)
    {
        return '';
    }
    
    public function getServer($name = null, $default = null)
    {
        return $default;
    }
    
    public function getEnv($name = null, $default = null)
    {
        return $default;
    }
    
    public function getCookie($name = null, $default = null)
    {
        return $default;
    }
    
    public function getFiles($name = null, $default = null)
    {
        return $default;
    }
    
    public function getHeader($name = null, $default = null)
    {
        return $default;
    }
    
    public function getParam($key, $default = null)
    {
        return $default;
    }
    
    public function getParams()
    {
        return [];
    }
    
    public function setParams(array $params)
    {
        return $this;
    }
    
    public function setParam($key, $value)
    {
        return $this;
    }
    
    public function getPost($key = null, $default = null)
    {
        return $default;
    }
    
    public function getQuery($key = null, $default = null)
    {
        return $default;
    }
    
    public function getRequest()
    {
        return $this;
    }
    
    public function isPost()
    {
        return false;
    }
    
    public function isPut()
    {
        return false;
    }
    
    public function isGet()
    {
        return true;
    }
    
    public function isDelete()
    {
        return false;
    }
    
    public function isHead()
    {
        return false;
    }
    
    public function isOptions()
    {
        return false;
    }
    
    public function isAjax()
    {
        return false;
    }
    
    public function isFlashValidatorRequest()
    {
        return false;
    }
    
    public function getMethod()
    {
        return 'GET';
    }
    
    public function isXmlHttpRequest()
    {
        return false;
    }
    
    public function isConsole()
    {
        return false;
    }
    
    public function isCli()
    {
        return false;
    }
    
    public function getFullActionName($delimiter = '_')
    {
        return '';
    }
    
    public function initForward()
    {
        return $this;
    }
    
    public function isForwarded()
    {
        return false;
    }
    
    public function getRouteName()
    {
        return '';
    }
    
    public function getControllerModule()
    {
        return '';
    }
    
    public function getBeforeForwardInfo($name = null)
    {
        return $name ? null : [];
    }
    
    public function setBeforeForwardInfo($name, $value = null)
    {
        return $this;
    }
    
    public function getAlias($name)
    {
        return $name;
    }
    
    public function getDistroBaseUrl()
    {
        return '';
    }
    
    public function getRequestString()
    {
        return '';
    }
}

