<?php
/***
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\WebapiAsync\Test\Unit\Controller;

use Magento\AsynchronousOperations\Api\Data\AsyncResponseInterfaceFactory;
use Magento\AsynchronousOperations\Model\ConfigInterface as WebApiAsyncConfig;
use Magento\AsynchronousOperations\Model\MassSchedule;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Webapi\Controller\Rest\Router;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\Webapi\Rest\Response;
use Magento\WebapiAsync\Controller\Rest\Asynchronous\InputParamsResolver;
use Magento\WebapiAsync\Controller\Rest\AsynchronousRequestProcessor;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Test for Magento\WebapiAsync\Controller\AsynchronousRequestProcessor class.
 */
class AsynchronousRequestProcessorTest extends TestCase
{
    /**
     * @var Response|MockObject
     */
    private $responseMock;

    /**
     * @var InputParamsResolver|MockObject
     */
    private $inputParamsResolverMock;

    /**
     * @var MassSchedule|MockObject
     */
    private $asyncBulkPublisher;

    /**
     * @var WebApiAsyncConfig|MockObject
     */
    private $webapiAsyncConfig;

    /**
     * @var DataObjectProcessor|MockObject
     */
    private $dataObjectProcessor;

    /**
     * @var AsyncResponseInterfaceFactory|MockObject
     */
    private $asyncResponseFactory;

    /**
     * @var string Regex pattern
     */
    private $processorPath;

    /**
     * @var Request|MockObject
     */
    private $requestMock;

    /**
     * @var AsynchronousRequestProcessor
     */
    private $subject;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->requestMock = $this->getRequestMock();

        $this->responseMock = $this->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->inputParamsResolverMock = $this->getMockBuilder(InputParamsResolver::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->asyncBulkPublisher = $this->getMockBuilder(MassSchedule::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->webapiAsyncConfig = $this->getMockBuilder(WebApiAsyncConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->dataObjectProcessor = $this->getMockBuilder(DataObjectProcessor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->asyncResponseFactory = $this->getMockBuilder(AsyncResponseInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->processorPath = AsynchronousRequestProcessor::PROCESSOR_PATH;

        $this->subject = new AsynchronousRequestProcessor(
            $this->responseMock,
            $this->inputParamsResolverMock,
            $this->asyncBulkPublisher,
            $this->webapiAsyncConfig,
            $this->dataObjectProcessor,
            $this->asyncResponseFactory,
            $this->processorPath
        );
    }

    public function testCanNotProcess(): void
    {
        $this->requestMock->expects($this->any())
            ->method('getPathInfo')
            ->willReturn("/async/bulk/V1/configurable-products/bySku/options");

        $this->assertFalse($this->subject->canProcess($this->requestMock));
    }

    public function testCanProcess(): void
    {
        $this->requestMock->expects($this->any())
            ->method('getPathInfo')
            ->willReturn("/async/V1/configurable-products/bySku/options");

        $route = $this->getMockBuilder(Router\Route::class)
            ->disableOriginalConstructor()
            ->getMock();

        $route->expects($this->once())
            ->method('getAclResources')
            ->willReturn(['Magento_Catalog::products']);

        $this->inputParamsResolverMock->expects($this->once())
            ->method('getRoute')
            ->willReturn($route);

        $this->assertTrue($this->subject->canProcess($this->requestMock));
    }

    public function testCanProcessSelfResourceRequest(): void
    {
        $this->requestMock->expects($this->any())
            ->method('getPathInfo')
            ->willReturn("/async/V1/configurable-products/bySku/options");

        $route = $this->getMockBuilder(Router\Route::class)
            ->disableOriginalConstructor()
            ->getMock();

        $route->expects($this->once())
            ->method('getAclResources')
            ->willReturn(['self']);

        $this->inputParamsResolverMock->expects($this->once())
            ->method('getRoute')
            ->willReturn($route);

        $this->assertFalse($this->subject->canProcess($this->requestMock));
    }

    /**
     * @return Request|MockObject
     */
    private function getRequestMock()
    {
        return $this->getMockBuilder(Request::class)
            ->onlyMethods(
                [
                    'isSecure',
                    'getRequestData',
                    'getParams',
                    'getParam',
                    'getRequestedServices',
                    'getPathInfo',
                    'getHttpHost',
                    'getMethod',
                ]
            )->disableOriginalConstructor()
            ->getMock();
    }
}
