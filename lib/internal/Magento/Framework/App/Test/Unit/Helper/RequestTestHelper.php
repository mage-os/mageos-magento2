<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\App\Test\Unit\Helper;

use Magento\Framework\App\Request\Http;

/**
 * Test helper for Request - extends concrete Http class
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class RequestTestHelper extends Http
{
    /**
     * @var mixed
     */
    private $paramReturn = null;

    /**
     * @var mixed
     */
    private $postReturn = null;

    /**
     * @var mixed
     */
    private $postValueReturn = null;

    /**
     * @var mixed
     */
    private $queryReturn = null;

    /**
     * @var callable|null
     */
    private $getPostCallback = null;

    /**
     * @var bool
     */
    private $isPostReturn = false;

    /**
     * @var bool
     */
    private $isAjaxReturn = false;

    /**
     * @var callable|null
     */
    private $getParamCallback = null;

    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    public function setParamReturn($return)
    {
        $this->paramReturn = $return;
        return $this;
    }

    public function setPostReturn($return)
    {
        $this->postReturn = $return;
        return $this;
    }

    public function setPostValueReturn($return)
    {
        $this->postValueReturn = $return;
        return $this;
    }

    public function setQueryReturn($return)
    {
        $this->queryReturn = $return;
        return $this;
    }

    public function setGetPostCallback($callback)
    {
        $this->getPostCallback = $callback;
        return $this;
    }

    public function setReturnValues($isPost = false, $isAjax = false, $getParam = null)
    {
        $this->isPostReturn = $isPost;
        $this->isAjaxReturn = $isAjax;
        $this->paramReturn = $getParam;
        return $this;
    }

    public function setGetParamCallback($callback)
    {
        $this->getParamCallback = $callback;
        return $this;
    }

    public function getParam($param, $defaultValue = null)
    {
        if ($this->getParamCallback !== null) {
            return call_user_func($this->getParamCallback, $param);
        }
        return $this->paramReturn !== null ? $this->paramReturn : $defaultValue;
    }

    public function isPost()
    {
        return $this->isPostReturn;
    }

    public function isAjax()
    {
        return $this->isAjaxReturn;
    }

    public function getPost($key = null, $defaultValue = null)
    {
        if ($this->getPostCallback !== null) {
            return call_user_func($this->getPostCallback, $key, $defaultValue);
        }
        return $this->postReturn !== null ? $this->postReturn : $defaultValue;
    }

    public function getPostValue($key = null, $defaultValue = null)
    {
        return $this->postValueReturn !== null ? $this->postValueReturn : $defaultValue;
    }

    public function getQuery($key = null, $defaultValue = null)
    {
        return $this->queryReturn !== null ? $this->queryReturn : $defaultValue;
    }
}

