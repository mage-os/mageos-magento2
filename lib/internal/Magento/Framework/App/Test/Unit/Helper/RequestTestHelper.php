<?php
/**
 * ADOBE CONFIDENTIAL
 *
 * Copyright 2015 Adobe
 * All Rights Reserved.
 *
 * NOTICE: All information contained herein is, and remains
 * the property of Adobe and its suppliers, if any. The intellectual
 * and technical concepts contained herein are proprietary to Adobe
 * and its suppliers and are protected by all applicable intellectual
 * property laws, including trade secret and copyright laws.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Adobe.
 */
declare(strict_types=1);

namespace Magento\Framework\App\Test\Unit\Helper;

use Magento\Framework\App\RequestInterface;

/**
 * Test helper for creating RequestInterface mocks with isDispatched method
 *
 * This helper implements RequestInterface and adds the isDispatched method
 * for testing purposes.
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
    
    /**
     * @inheritDoc
     */
    public function getModuleName()
    {
        return '';
    }
    
    /**
     * @inheritDoc
     */
    public function setModuleName($name)
    {
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getControllerName()
    {
        return '';
    }
    
    /**
     * @inheritDoc
     */
    public function setControllerName($name)
    {
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getActionName()
    {
        return '';
    }
    
    /**
     * @inheritDoc
     */
    public function setActionName($name)
    {
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getRequestUri()
    {
        return '';
    }
    
    /**
     * @inheritDoc
     */
    public function getPathInfo()
    {
        return '';
    }
    
    /**
     * @inheritDoc
     */
    public function getOriginalPathInfo()
    {
        return '';
    }
    
    /**
     * @inheritDoc
     */
    public function getBasePath()
    {
        return '';
    }
    
    /**
     * @inheritDoc
     */
    public function getBaseUrl($type = null, $secure = null)
    {
        return '';
    }
    
    /**
     * @inheritDoc
     */
    public function getScheme()
    {
        return '';
    }
    
    /**
     * @inheritDoc
     */
    public function isSecure()
    {
        return false;
    }
    
    /**
     * @inheritDoc
     */
    public function getHttpHost()
    {
        return '';
    }
    
    /**
     * @inheritDoc
     */
    public function getClientIp($checkToUse = false)
    {
        return '';
    }
    
    /**
     * @inheritDoc
     */
    public function getServer($name = null, $default = null)
    {
        return $default;
    }
    
    /**
     * @inheritDoc
     */
    public function getEnv($name = null, $default = null)
    {
        return $default;
    }
    
    /**
     * @inheritDoc
     */
    public function getCookie($name = null, $default = null)
    {
        return $default;
    }
    
    /**
     * @inheritDoc
     */
    public function getFiles($name = null, $default = null)
    {
        return $default;
    }
    
    /**
     * @inheritDoc
     */
    public function getHeader($name = null, $default = null)
    {
        return $default;
    }
    
    /**
     * @inheritDoc
     */
    public function getParam($key, $default = null)
    {
        return $default;
    }
    
    /**
     * @inheritDoc
     */
    public function getParams()
    {
        return [];
    }
    
    /**
     * @inheritDoc
     */
    public function setParams(array $params)
    {
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function setParam($key, $value)
    {
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getPost($key = null, $default = null)
    {
        return $default;
    }
    
    /**
     * @inheritDoc
     */
    public function getQuery($key = null, $default = null)
    {
        return $default;
    }
    
    /**
     * @inheritDoc
     */
    public function getRequest()
    {
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function isPost()
    {
        return false;
    }
    
    /**
     * @inheritDoc
     */
    public function isPut()
    {
        return false;
    }
    
    /**
     * @inheritDoc
     */
    public function isGet()
    {
        return true;
    }
    
    /**
     * @inheritDoc
     */
    public function isDelete()
    {
        return false;
    }
    
    /**
     * @inheritDoc
     */
    public function isHead()
    {
        return false;
    }
    
    /**
     * @inheritDoc
     */
    public function isOptions()
    {
        return false;
    }
    
    /**
     * @inheritDoc
     */
    public function isAjax()
    {
        return false;
    }
    
    /**
     * @inheritDoc
     */
    public function isFlashValidatorRequest()
    {
        return false;
    }
    
    /**
     * @inheritDoc
     */
    public function getMethod()
    {
        return 'GET';
    }
    
    /**
     * @inheritDoc
     */
    public function isXmlHttpRequest()
    {
        return false;
    }
    
    /**
     * @inheritDoc
     */
    public function isConsole()
    {
        return false;
    }
    
    /**
     * @inheritDoc
     */
    public function isCli()
    {
        return false;
    }
    
    /**
     * @inheritDoc
     */
    public function getFullActionName($delimiter = '_')
    {
        return '';
    }
    
    /**
     * @inheritDoc
     */
    public function initForward()
    {
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function isForwarded()
    {
        return false;
    }
    
    /**
     * @inheritDoc
     */
    public function getRouteName()
    {
        return '';
    }
    
    /**
     * @inheritDoc
     */
    public function getControllerModule()
    {
        return '';
    }
    
    /**
     * @inheritDoc
     */
    public function getBeforeForwardInfo($name = null)
    {
        return $name ? null : [];
    }
    
    /**
     * @inheritDoc
     */
    public function setBeforeForwardInfo($name, $value = null)
    {
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getAlias($name)
    {
        return $name;
    }
    
    /**
     * @inheritDoc
     */
    public function getDistroBaseUrl()
    {
        return '';
    }
    
    /**
     * @inheritDoc
     */
    public function getRequestString()
    {
        return '';
    }
}
