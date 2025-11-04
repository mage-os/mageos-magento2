<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\App\Action\Context;

/**
 * Test helper for Backend Context with custom methods
 */
class ContextTestHelper extends Context
{
    /**
     * @var mixed
     */
    private $translator = null;

    /**
     * @var mixed
     */
    private $layoutFactory = null;

    /**
     * @var mixed
     */
    private $title = null;

    /**
     * @var mixed
     */
    protected $helper = null;

    /**
     * @var mixed
     */
    protected $session = null;

    /**
     * @var mixed
     */
    protected $authorization = null;

    /**
     * @var mixed
     */
    protected $objectManager = null;

    /**
     * @var mixed
     */
    protected $actionFlag = null;

    /**
     * @var mixed
     */
    protected $messageManager = null;

    /**
     * @var mixed
     */
    protected $eventManager = null;

    /**
     * @var mixed
     */
    protected $request = null;

    /**
     * @var mixed
     */
    protected $response = null;

    /**
     * @var mixed
     */
    protected $view = null;

    /**
     * @var mixed
     */
    protected $frontController = null;

    /**
     * @var mixed
     */
    protected $resultRedirectFactory = null;

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get translator
     *
     * @return mixed
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Set translator
     *
     * @param mixed $translator
     * @return $this
     */
    public function setTranslator($translator): self
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * Get front controller
     *
     * @return mixed
     */
    public function getFrontController()
    {
        return $this->frontController;
    }

    /**
     * Set front controller
     *
     * @param mixed $frontController
     * @return $this
     */
    public function setFrontController($frontController): self
    {
        $this->frontController = $frontController;
        return $this;
    }

    /**
     * Get layout factory
     *
     * @return mixed
     */
    public function getLayoutFactory()
    {
        return $this->layoutFactory;
    }

    /**
     * Set layout factory
     *
     * @param mixed $layoutFactory
     * @return $this
     */
    public function setLayoutFactory($layoutFactory): self
    {
        $this->layoutFactory = $layoutFactory;
        return $this;
    }

    /**
     * Get title
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param mixed $title
     * @return $this
     */
    public function setTitle($title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get helper
     *
     * @return mixed
     */
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * Set helper
     *
     * @param mixed $helper
     * @return $this
     */
    public function setHelper($helper): self
    {
        $this->helper = $helper;
        return $this;
    }

    /**
     * Get session
     *
     * @return mixed
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Set session
     *
     * @param mixed $session
     * @return $this
     */
    public function setSession($session): self
    {
        $this->session = $session;
        return $this;
    }

    /**
     * Get authorization
     *
     * @return mixed
     */
    public function getAuthorization()
    {
        return $this->authorization;
    }

    /**
     * Set authorization
     *
     * @param mixed $authorization
     * @return $this
     */
    public function setAuthorization($authorization): self
    {
        $this->authorization = $authorization;
        return $this;
    }

    /**
     * Get object manager
     *
     * @return mixed
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * Set object manager
     *
     * @param mixed $objectManager
     * @return $this
     */
    public function setObjectManager($objectManager): self
    {
        $this->objectManager = $objectManager;
        return $this;
    }

    /**
     * Get action flag
     *
     * @return mixed
     */
    public function getActionFlag()
    {
        return $this->actionFlag;
    }

    /**
     * Set action flag
     *
     * @param mixed $actionFlag
     * @return $this
     */
    public function setActionFlag($actionFlag): self
    {
        $this->actionFlag = $actionFlag;
        return $this;
    }

    /**
     * Get message manager
     *
     * @return mixed
     */
    public function getMessageManager()
    {
        return $this->messageManager;
    }

    /**
     * Set message manager
     *
     * @param mixed $messageManager
     * @return $this
     */
    public function setMessageManager($messageManager): self
    {
        $this->messageManager = $messageManager;
        return $this;
    }

    /**
     * Get event manager
     *
     * @return mixed
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * Set event manager
     *
     * @param mixed $eventManager
     * @return $this
     */
    public function setEventManager($eventManager): self
    {
        $this->eventManager = $eventManager;
        return $this;
    }

    /**
     * Get request
     *
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set request
     *
     * @param mixed $request
     * @return $this
     */
    public function setRequest($request): self
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Get response
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set response
     *
     * @param mixed $response
     * @return $this
     */
    public function setResponse($response): self
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Get view
     *
     * @return mixed
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Set view
     *
     * @param mixed $view
     * @return $this
     */
    public function setView($view): self
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Get result redirect factory
     *
     * @return mixed
     */
    public function getResultRedirectFactory()
    {
        return $this->resultRedirectFactory;
    }

    /**
     * Set result redirect factory
     *
     * @param mixed $resultRedirectFactory
     * @return $this
     */
    public function setResultRedirectFactory($resultRedirectFactory): self
    {
        $this->resultRedirectFactory = $resultRedirectFactory;
        return $this;
    }
}
