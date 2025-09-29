<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\Session;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CustomerSessionTestHelper extends Session
{
    /**
     * @var bool
     */
    public $beforeWishlistUrl = false;

    /**
     * @var array
     */
    public $beforeWishlistRequest = [];

    /**
     * @var array
     */
    public $beforeRequestParams = [];

    /**
     * @var string
     */
    public $beforeModuleName = '';

    /**
     * @var string
     */
    public $beforeControllerName = '';

    /**
     * @var string
     */
    public $beforeAction = '';
    
    public function __construct()
    {
    }
    
    public function authenticate($loginUrl = null)
    {
        return false;
    }
    
    public function getBeforeWishlistUrl()
    {
        return $this->beforeWishlistUrl;
    }
    
    public function getBeforeWishlistRequest()
    {
        return $this->beforeWishlistRequest;
    }
    
    public function setBeforeWishlistUrl($url)
    {
        $this->beforeWishlistUrl = $url;
        return $this;
    }
    
    public function setBeforeWishlistRequest($request)
    {
        $this->beforeWishlistRequest = $request;
        return $this;
    }
    
    public function setBeforeRequestParams($params)
    {
        $this->beforeRequestParams = $params;
        return $this;
    }
    
    public function setBeforeModuleName($moduleName)
    {
        $this->beforeModuleName = $moduleName;
        return $this;
    }
    
    public function setBeforeControllerName($controllerName)
    {
        $this->beforeControllerName = $controllerName;
        return $this;
    }
    
    public function setBeforeAction($action)
    {
        $this->beforeAction = $action;
        return $this;
    }
}
