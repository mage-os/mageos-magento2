<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Event\Test\Unit\Helper;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event;

/**
 * Test helper for creating Event mocks with getRequest and getObject methods
 *
 * This helper extends the concrete Event class directly, providing a clean
 * way to add test-specific methods without using anonymous classes.
 */
class EventTestHelper extends Event
{
    /**
     * @var RequestInterface|null
     */
    private $request = null;
    
    /**
     * @var mixed
     */
    private $object = null;
    
    /**
     * @var mixed
     */
    private $controllerAction = null;
    
    /**
     * @var mixed
     */
    private $layout = null;
    
    /**
     * @param RequestInterface|null $request
     */
    public function __construct($request = null)
    {
        $this->request = $request;
    }
    
    /**
     * Get request
     *
     * @return RequestInterface|null
     */
    public function getRequest()
    {
        return $this->request;
    }
    
    /**
     * Set request
     *
     * @param RequestInterface|null $request
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }
    
    /**
     * Get object
     *
     * @return mixed
     */
    public function getObject()
    {
        return $this->object;
    }
    
    /**
     * Set object
     *
     * @param mixed $object
     * @return $this
     */
    public function setObject($object)
    {
        $this->object = $object;
        return $this;
    }
    
    /**
     * Get data object
     *
     * @return mixed
     */
    public function getDataObject()
    {
        return $this->object;
    }
    
    /**
     * Set controller action
     *
     * @param mixed $action
     * @return $this
     */
    public function setControllerAction($action)
    {
        $this->controllerAction = $action;
        return $this;
    }
    
    /**
     * Get controller action
     *
     * @return mixed
     */
    public function getControllerAction()
    {
        return $this->controllerAction;
    }
    
    /**
     * Set layout
     *
     * @param mixed $layout
     * @return $this
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }
    
    /**
     * Get layout
     *
     * @return mixed
     */
    public function getLayout()
    {
        return $this->layout;
    }
}
