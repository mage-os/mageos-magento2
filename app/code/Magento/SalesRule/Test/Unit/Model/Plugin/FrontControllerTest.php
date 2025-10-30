<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\SalesRule\Test\Unit\Model\Plugin;

use Magento\Framework\App\FrontControllerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\SalesRule\Model\Plugin\FrontController;
use Magento\SalesRule\Model\Plugin\RequestTypeRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FrontControllerTest extends TestCase
{
    /** @var RequestTypeRegistry|MockObject */
    private $requestTypeRegistry;

    /** @var FrontController */
    private $plugin;

    protected function setUp(): void
    {
        $this->requestTypeRegistry = $this->createMock(RequestTypeRegistry::class);
        $this->plugin = new FrontController($this->requestTypeRegistry);
    }

    public function testBeforeDispatchSetsTrueForGetRequests(): void
    {
        $subject = $this->createMock(FrontControllerInterface::class);
        $request = $this->createMock(RequestInterface::class);
        $request->method('getMethod')->willReturn('GET');

        $this->requestTypeRegistry
            ->expects($this->once())
            ->method('setIsGetRequestOrQuery')
            ->with(true);

        $this->plugin->beforeDispatch($subject, $request);
    }

    public function testBeforeDispatchSetsFalseForPostRequests(): void
    {
        $subject = $this->createMock(FrontControllerInterface::class);
        $request = $this->createMock(RequestInterface::class);
        $request->method('getMethod')->willReturn('POST');

        $this->requestTypeRegistry
            ->expects($this->once())
            ->method('setIsGetRequestOrQuery')
            ->with(false);

        $this->plugin->beforeDispatch($subject, $request);
    }

    public function testBeforeDispatchIsCaseInsensitive(): void
    {
        $subject = $this->createMock(FrontControllerInterface::class);
        $request = $this->createMock(RequestInterface::class);
        $request->method('getMethod')->willReturn('get'); // lowercase

        $this->requestTypeRegistry
            ->expects($this->once())
            ->method('setIsGetRequestOrQuery')
            ->with(true);

        $this->plugin->beforeDispatch($subject, $request);
    }
}
