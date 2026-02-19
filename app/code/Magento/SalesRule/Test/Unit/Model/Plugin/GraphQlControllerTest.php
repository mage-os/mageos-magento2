<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\SalesRule\Test\Unit\Model\Plugin;

use Magento\Framework\App\FrontControllerInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\SalesRule\Model\Plugin\GraphQlController;
use Magento\SalesRule\Model\Plugin\RequestTypeRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for GraphQlController plugin
 */
class GraphQlControllerTest extends TestCase
{
    /**
     * @var RequestTypeRegistry|MockObject
     */
    private $requestTypeRegistry;

    /**
     * @var GraphQlController
     */
    private $graphQlController;

    /**
     * @var FrontControllerInterface|MockObject
     */
    private $frontController;

    /**
     * @var ResponseInterface|MockObject
     */
    private $response;

    /**
     * Set up test environment
     */
    protected function setUp(): void
    {
        $this->requestTypeRegistry = $this->createMock(RequestTypeRegistry::class);
        $this->graphQlController = new GraphQlController($this->requestTypeRegistry);
        $this->frontController = $this->createMock(FrontControllerInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
    }

    /**
     * Test afterDispatch calls reset on RequestTypeRegistry
     */
    public function testAfterDispatchResetsRequestTypeRegistry(): void
    {
        // Expect reset to be called on the RequestTypeRegistry
        $this->requestTypeRegistry->expects($this->once())
            ->method('reset');

        // Call the afterDispatch method
        $result = $this->graphQlController->afterDispatch($this->frontController, $this->response);

        // Verify the response is returned unchanged
        $this->assertSame($this->response, $result);
    }
}
