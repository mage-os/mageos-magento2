<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\SalesRule\Test\Unit\Model\Plugin;

use Magento\Framework\App\FrontControllerInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\SalesRule\Model\Plugin\FrontController;
use Magento\SalesRule\Model\Plugin\RequestTypeRegistry;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FrontControllerTest extends TestCase
{
    use MockCreationTrait;

    /** @var RequestTypeRegistry|MockObject */
    private $requestTypeRegistry;

    /** @var FrontController */
    private $plugin;

    /**
     * @var RequestInterface|MockObject
     */
    private RequestInterface $request;

    /**
     * @var FrontControllerInterface|MockObject
     */
    private FrontControllerInterface $subject;

    protected function setUp(): void
    {
        $this->requestTypeRegistry = $this->createMock(RequestTypeRegistry::class);
        $this->plugin = new FrontController($this->requestTypeRegistry);
        $this->request = $this->createPartialMockWithReflection(
            Http::class,
            ['getMethod']
        );
        $this->subject = $this->createMock(FrontControllerInterface::class);
    }

    public function testBeforeDispatchSetsTrueForGetRequests(): void
    {
        $this->request->method('getMethod')->willReturn('GET');

        $this->requestTypeRegistry
            ->expects($this->once())
            ->method('setIsGetRequestOrQuery')
            ->with(true);

        $this->plugin->beforeDispatch($this->subject, $this->request);
    }

    public function testBeforeDispatchSetsFalseForPostRequests(): void
    {
        $this->request->method('getMethod')->willReturn('POST');

        $this->requestTypeRegistry
            ->expects($this->once())
            ->method('setIsGetRequestOrQuery')
            ->with(false);

        $this->plugin->beforeDispatch($this->subject, $this->request);
    }

    public function testBeforeDispatchIsCaseInsensitive(): void
    {

        $this->request->method('getMethod')->willReturn('get'); // lowercase

        $this->requestTypeRegistry
            ->expects($this->once())
            ->method('setIsGetRequestOrQuery')
            ->with(true);

        $this->plugin->beforeDispatch($this->subject, $this->request);
    }
}
