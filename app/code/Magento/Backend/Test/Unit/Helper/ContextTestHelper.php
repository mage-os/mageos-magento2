<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\App\Action\Context;

class ContextTestHelper extends Context
{
    /**
     * @var mixed
     */
    private $requestMock;

    /**
     * @var mixed
     */
    private $objectManagerMock;

    /**
     * @var mixed
     */
    private $eventManagerMock;

    /**
     * @var mixed
     */
    private $responseMock;

    /**
     * @var mixed
     */
    private $messageManagerMock;

    /**
     * @var mixed
     */
    private $resultRedirectFactoryMock;

    /**
     * @var mixed
     */
    private $sessionMock;

    /**
     * @var mixed
     */
    private $titleMock;

    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    public function setMocks(
        $requestMock,
        $objectManagerMock,
        $eventManagerMock,
        $responseMock,
        $messageManagerMock,
        $resultRedirectFactoryMock,
        $sessionMock,
        $titleMock
    ) {
        $this->requestMock = $requestMock;
        $this->objectManagerMock = $objectManagerMock;
        $this->eventManagerMock = $eventManagerMock;
        $this->responseMock = $responseMock;
        $this->messageManagerMock = $messageManagerMock;
        $this->resultRedirectFactoryMock = $resultRedirectFactoryMock;
        $this->sessionMock = $sessionMock;
        $this->titleMock = $titleMock;
        return $this;
    }

    public function getRequest()
    {
        return $this->requestMock;
    }

    public function getObjectManager()
    {
        return $this->objectManagerMock;
    }

    public function getEventManager()
    {
        return $this->eventManagerMock;
    }

    public function getResponse()
    {
        return $this->responseMock;
    }

    public function getMessageManager()
    {
        return $this->messageManagerMock;
    }

    public function getResultRedirectFactory()
    {
        return $this->resultRedirectFactoryMock;
    }

    public function getSession()
    {
        return $this->sessionMock;
    }

    public function getTitle()
    {
        return $this->titleMock;
    }
}

