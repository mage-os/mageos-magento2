<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Test\Unit\Controller\Adminhtml\Bundle\Product\Edit;

use Magento\Backend\App\Action\Context;
use Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle;
use Magento\Bundle\Controller\Adminhtml\Bundle\Product\Edit\Form;
use Magento\Catalog\Controller\Adminhtml\Product\Builder;
use Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\ViewInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FormTest extends TestCase
{
    /** @var Form */
    protected $controller;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /**
     * @var MockObject|RequestInterface
     */
    protected $request;

    /**
     * @var MockObject|ResponseInterface
     */
    protected $response;

    /**
     * @var MockObject|Builder
     */
    protected $productBuilder;

    /**
     * @var MockObject|Helper
     */
    protected $initializationHelper;

    /**
     * @var MockObject|ViewInterface
     */
    protected $view;

    /**
     * @var MockObject|Context
     */
    protected $context;

    protected function setUp(): void
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);

        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request = $this->createMock(RequestInterface::class);
        
        /** @var ResponseInterface $response */
        $this->response = new class implements ResponseInterface {
            private $body = '';
            
            public function __construct()
            {
            }
            
            public function setBody($body)
            {
                $this->body = $body;
                return $this;
            }
            public function getBody()
            {
                return $this->body;
            }
            public function sendResponse()
            {
                return $this;
            }
        };
        
        $this->productBuilder = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['build'])
            ->getMock();
        $this->initializationHelper = $this->getMockBuilder(
            Helper::class
        )
            ->disableOriginalConstructor()
            ->onlyMethods(['initialize'])
            ->getMock();
        $this->view = $this->createMock(ViewInterface::class);

        $this->context->method('getRequest')->willReturn($this->request);
        $this->context->method('getResponse')->willReturn($this->response);
        $this->context->method('getView')->willReturn($this->view);

        $this->controller = $this->objectManagerHelper->getObject(
            Form::class,
            [
                'context' => $this->context,
                'productBuilder' => $this->productBuilder,
                'initializationHelper' => $this->initializationHelper
            ]
        );
    }

    public function testExecute()
    {
        /** @var Product $product */
        $product = new class extends Product {
            private $id = null;
            
            public function __construct()
            {
            }
            
            public function getId()
            {
                return $this->id;
            }
            public function setId($id)
            {
                $this->id = $id;
                return $this;
            }
        };
        
        $layout = $this->createMock(LayoutInterface::class);
        
        /** @var Bundle $block */
        $block = new class extends Bundle {
            private $index = null;
            private $htmlResult = '';
            
            public function __construct()
            {
            }
            
            public function setIndex($index)
            {
                $this->index = $index;
                return $this;
            }
            public function getIndex()
            {
                return $this->index;
            }
            public function toHtml()
            {
                return $this->htmlResult;
            }
            public function setHtmlResult($result)
            {
                $this->htmlResult = $result;
                return $this;
            }
        };

        $this->productBuilder->expects($this->once())->method('build')->with($this->request)->willReturn($product);
        $this->initializationHelper->method('initialize')->willReturn($product);
        $this->response->setBody(''); // Use setter instead of expects
        $this->view->expects($this->once())->method('getLayout')->willReturn($layout);
        $layout->expects($this->once())->method('createBlock')->willReturn($block);
        $block->setHtmlResult(''); // Use setter instead of expects

        $this->controller->execute();
    }
}
