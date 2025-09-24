<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Controller\Adminhtml\Product;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Data;
use Magento\Backend\Model\Session;
use Magento\Catalog\Controller\Adminhtml\Product\Builder;
use Magento\Catalog\Controller\Adminhtml\Product\ShowUpdateResult;
use Magento\Catalog\Helper\Product\Composite;
use Magento\Catalog\Model\Product\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Manager;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Layout;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ShowUpdateResultTest extends TestCase
{
    /** @var Context|MockObject */
    protected $context;

    /** @var Layout|MockObject */
    protected $layout;

    /** @var Session|MockObject */
    protected $session;

    /** @var Http|MockObject */
    protected $request;

    /**
     * Init session object
     *
     * @return MockObject
     */
    protected function getSession()
    {
        $session = $this->getMockBuilder(Session::class)
            ->onlyMethods(['hasCompositeProductResult', 'getCompositeProductResult', 'unsCompositeProductResult'])
            ->disableOriginalConstructor()
            ->getMock();
        $session->expects($this->once())
            ->method('hasCompositeProductResult')
            ->willReturn(true);
        $session->expects($this->once())
            ->method('unsCompositeProductResult');
        $session->expects($this->atLeastOnce())
            ->method('getCompositeProductResult')
            ->willReturn(new DataObject());

        return $session;
    }

    /**
     * Init context object
     *
     * @return MockObject
     */
    protected function getContext()
    {
        $productActionMock = $this->createMock(Action::class);
        $objectManagerMock = $this->createMock(ObjectManagerInterface::class);
        $objectManagerMock->method('get')->willReturn($productActionMock);

        $eventManager = $this->getMockBuilder(Manager::class)
            ->onlyMethods(['dispatch'])
            ->disableOriginalConstructor()
            ->getMock();

        $eventManager->expects($this->any())
            ->method('dispatch')
            ->willReturnSelf();

        $this->request = $this->createPartialMock(
            Http::class,
            ['getParam', 'getPost', 'getFullActionName', 'getPostValue']
        );

        $responseInterfaceMock = $this->getMockBuilder(ResponseInterface::class)
            ->onlyMethods(['sendResponse', 'setRedirect'])
            ->getMock();

        $managerInterfaceMock = $this->createMock(ManagerInterface::class);
        $this->session = $this->getSession();
        $actionFlagMock = $this->createMock(ActionFlag::class);
        $helperDataMock = $this->createMock(Data::class);
        $this->context = $this->getMockBuilder(Context::class)
            ->onlyMethods(
                [
                    'getRequest',
                    'getResponse',
                    'getObjectManager',
                    'getEventManager',
                    'getMessageManager',
                    'getSession',
                    'getActionFlag',
                    'getHelper',
                    'getView',
                    'getResultRedirectFactory',
                    'getTitle'
                ]
            )
            ->disableOriginalConstructor()
            ->getMock();

        $this->context->method('getEventManager')->willReturn($eventManager);
        $this->context->method('getRequest')->willReturn($this->request);
        $this->context->method('getResponse')->willReturn($responseInterfaceMock);
        $this->context->method('getObjectManager')->willReturn($objectManagerMock);

        $this->context->method('getMessageManager')->willReturn($managerInterfaceMock);
        $this->context->method('getSession')->willReturn($this->session);
        $this->context->method('getActionFlag')->willReturn($actionFlagMock);
        $this->context->method('getHelper')->willReturn($helperDataMock);

        return $this->context;
    }

    public function testExecute()
    {
        $productCompositeHelper = $this->createMock(Composite::class);
        $productCompositeHelper->expects($this->once())
            ->method('renderUpdateResult');

        $productBuilder = $this->createMock(Builder::class);
        $context = $this->getContext();

        /** @var ShowUpdateResult $controller */
        $controller = new ShowUpdateResult($context, $productBuilder, $productCompositeHelper);
        $controller->execute();
    }
}
