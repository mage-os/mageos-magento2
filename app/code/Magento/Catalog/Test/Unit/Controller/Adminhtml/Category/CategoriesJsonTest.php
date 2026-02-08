<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Controller\Adminhtml\Category;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Catalog\Block\Adminhtml\Category\Tree;
use Magento\Catalog\Controller\Adminhtml\Category\CategoriesJson;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date as DateFilter;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutFactory;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for CategoriesJson controller.
 *
 * @covers \Magento\Catalog\Controller\Adminhtml\Category\CategoriesJson
 */
class CategoriesJsonTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var AuthSession|MockObject
     */
    private $authSessionMock;

    /**
     * @var HttpRequest|MockObject
     */
    private $requestMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @var Registry|MockObject
     */
    private $registryMock;

    /**
     * @var WysiwygConfig|MockObject
     */
    private $wysiwygConfigMock;

    /**
     * @var DateFilter|MockObject
     */
    private $dateFilterMock;

    /**
     * @var JsonFactory|MockObject
     */
    private $resultJsonFactoryMock;

    /**
     * @var ResultJson|MockObject
     */
    private $resultJsonMock;

    /**
     * @var LayoutFactory|MockObject
     */
    private $layoutFactoryMock;

    /**
     * @var Layout|MockObject
     */
    private $layoutMock;

    /**
     * @var RedirectFactory|MockObject
     */
    private $resultRedirectFactoryMock;

    /**
     * @var Redirect|MockObject
     */
    private $resultRedirectMock;

    /**
     * @var CategoriesJson
     */
    private CategoriesJson $controller;

    /**
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->createMock(Context::class);

        $this->authSessionMock = $this->getMockBuilder(AuthSession::class)
            ->disableOriginalConstructor()
            ->addMethods(['setIsTreeWasExpanded'])
            ->getMock();

        $this->resultJsonFactoryMock = $this->createMock(JsonFactory::class);
        $this->resultJsonMock         = $this->createMock(ResultJson::class);
        $this->layoutFactoryMock      = $this->createMock(LayoutFactory::class);
        $this->layoutMock             = $this->createMock(Layout::class);
        $this->requestMock            = $this->createMock(HttpRequest::class);

        $this->resultRedirectFactoryMock = $this->getMockBuilder(RedirectFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();

        $this->resultRedirectMock = $this->getMockBuilder(Redirect::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setPath'])
            ->getMock();

        $this->contextMock->method('getResultRedirectFactory')
            ->willReturn($this->resultRedirectFactoryMock);
        $this->contextMock->method('getRequest')
            ->willReturn($this->requestMock);

        $this->storeManagerMock   = $this->createMock(StoreManagerInterface::class);
        $this->registryMock       = $this->createMock(Registry::class);
        $this->wysiwygConfigMock  = $this->createMock(WysiwygConfig::class);
        $this->dateFilterMock     = $this->createMock(DateFilter::class);

        $this->objectManager->prepareObjectManager([
            [StoreManagerInterface::class, $this->storeManagerMock],
            [Registry::class, $this->registryMock],
            [WysiwygConfig::class, $this->wysiwygConfigMock],
            [AuthSession::class, $this->authSessionMock],
        ]);

        $this->controller = $this->objectManager->getObject(
            CategoriesJson::class,
            [
                'context'           => $this->contextMock,
                'resultJsonFactory' => $this->resultJsonFactoryMock,
                'layoutFactory'     => $this->layoutFactoryMock,
                'authSession'       => $this->authSessionMock,
            ]
        );
    }

    /**
     * Ensure execute() sets expanded flag true and returns JSON error when id is missing.
     *
     * @covers \Magento\Catalog\Controller\Adminhtml\Category\CategoriesJson::execute
     * @return void
     */
    public function testExecuteWithExpandAllTrueAndMissingIdReturnsJsonError(): void
    {
        $this->requestMock->method('getParam')->with('expand_all')->willReturn(1);
        $this->requestMock->method('getPost')->with('id')->willReturn(null);

        $this->authSessionMock->expects($this->once())
            ->method('setIsTreeWasExpanded')
            ->with(true);

        $this->resultJsonFactoryMock->method('create')->willReturn($this->resultJsonMock);
        $this->resultJsonMock->expects($this->once())
            ->method('setJsonData')
            ->with(json_encode(['error' => 'Category ID is required']))
            ->willReturnSelf();

        $result = $this->controller->execute();
        $this->assertSame($this->resultJsonMock, $result);
    }

    /**
     * Ensure execute() sets expanded flag false and returns JSON error when id is missing.
     *
     * @covers \Magento\Catalog\Controller\Adminhtml\Category\CategoriesJson::execute
     * @return void
     */
    public function testExecuteWithExpandAllFalseAndMissingIdReturnsJsonError(): void
    {
        $this->requestMock->method('getParam')->with('expand_all')->willReturn(null);
        $this->requestMock->method('getPost')->with('id')->willReturn(0);

        $this->authSessionMock->expects($this->once())
            ->method('setIsTreeWasExpanded')
            ->with(false);

        $this->resultJsonFactoryMock->method('create')->willReturn($this->resultJsonMock);
        $this->resultJsonMock->expects($this->once())
            ->method('setJsonData')
            ->with(json_encode(['error' => 'Category ID is required']))
            ->willReturnSelf();

        $result = $this->controller->execute();
        $this->assertSame($this->resultJsonMock, $result);
    }

    /**
     * Ensure redirect is returned when _initCategory() yields null.
     *
     * @covers \Magento\Catalog\Controller\Adminhtml\Category\CategoriesJson::execute
     */
    public function testExecuteWithValidIdAndNullInitCategoryReturnsRedirect(): void
    {
        $this->requestMock->method('getParam')->with('expand_all')->willReturn(null);
        $this->requestMock->method('getPost')->with('id')->willReturn(12);
        $this->requestMock->expects($this->once())
            ->method('setParam')
            ->with('id', 12);

        $this->authSessionMock->expects($this->once())
            ->method('setIsTreeWasExpanded')
            ->with(false);

        $this->resultRedirectFactoryMock->method('create')
            ->willReturn($this->resultRedirectMock);

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('catalog/*/', ['_current' => true, 'id' => null])
            ->willReturn($this->resultRedirectMock);

        // Build a controller stub that returns null from _initCategory()
        $controller = $this->getMockBuilder(CategoriesJson::class)
            ->setConstructorArgs([
                $this->contextMock,
                $this->resultJsonFactoryMock,
                $this->layoutFactoryMock,
                $this->authSessionMock,
            ])
            ->onlyMethods(['_initCategory'])
            ->getMock();

        $controller->method('_initCategory')->willReturn(null);

        $result = $controller->execute();
        $this->assertSame($this->resultRedirectMock, $result);
    }

    /**
     * Ensure JSON is returned with tree block payload when category is initialized.
     *
     * @covers \Magento\Catalog\Controller\Adminhtml\Category\CategoriesJson::execute
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testExecuteWithValidIdReturnsTreeJson(): void
    {
        $this->requestMock->method('getParam')->with('expand_all')->willReturn(0);
        $this->requestMock->method('getPost')->with('id')->willReturn(34);
        $this->requestMock->expects($this->once())
            ->method('setParam')
            ->with('id', 34);

        $this->authSessionMock->expects($this->once())
            ->method('setIsTreeWasExpanded')
            ->with(false);

        $categoryMock = $this->createMock(CategoryModel::class);

        $treeBlockMock = $this->getMockBuilder(Tree::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getTreeJson'])
            ->getMock();

        $this->layoutFactoryMock->method('create')
            ->willReturn($this->layoutMock);
        $this->layoutMock->method('createBlock')
            ->with(Tree::class)
            ->willReturn($treeBlockMock);

        $treeBlockMock->method('getTreeJson')
            ->with($categoryMock)
            ->willReturn('{"tree":"json"}');

        $this->resultJsonFactoryMock->method('create')
            ->willReturn($this->resultJsonMock);
        $this->resultJsonMock->expects($this->once())
            ->method('setJsonData')
            ->with('{"tree":"json"}')
            ->willReturnSelf();

        $controller = $this->getMockBuilder(CategoriesJson::class)
            ->setConstructorArgs([
                $this->contextMock,
                $this->resultJsonFactoryMock,
                $this->layoutFactoryMock,
                $this->authSessionMock,
            ])
            ->onlyMethods(['_initCategory'])
            ->getMock();

        $controller->method('_initCategory')
            ->willReturn($categoryMock);

        $result = $controller->execute();
        $this->assertSame($this->resultJsonMock, $result);
    }
}
