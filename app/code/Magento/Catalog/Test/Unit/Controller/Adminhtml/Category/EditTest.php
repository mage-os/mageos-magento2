<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Controller\Adminhtml\Category;

use Magento\Framework\Registry;
use Magento\Cms\Model\Wysiwyg\Config;
use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Session;
use Magento\Backend\Test\Unit\Helper\ContextTestHelper;
use Magento\Framework\App\Test\Unit\Helper\RequestTestHelper;
use Magento\Framework\View\Test\Unit\Helper\ResultPageTestHelper;
use Magento\Store\Test\Unit\Helper\StoreManagerTestHelper;
use Magento\Store\Test\Unit\Helper\StoreTestHelper;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Catalog\Controller\Adminhtml\Category\Edit;
use Magento\Catalog\Model\Category;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Page\Title;
use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class EditTest extends TestCase
{
    /**
     * @var RedirectFactory|MockObject
     */
    protected $resultRedirectFactoryMock;

    /**
     * @var \Magento\Backend\Model\View\Result\PageFactory|MockObject
     */
    protected $resultPageFactoryMock;

    /**
     * @var \Magento\Backend\Model\View\Result\Page|MockObject
     */
    protected $resultPageMock;

    /**
     * @var JsonFactory|MockObject
     */
    protected $resultJsonFactoryMock;

    /**
     * @var LayoutFactory|MockObject
     */
    protected $storeManagerInterfaceMock;

    /**
     * @var Context|MockObject
     */
    protected $contextMock;

    /**
     * @var Title|MockObject
     */
    protected $titleMock;

    /**
     * @var RequestInterface|MockObject
     */
    protected $requestMock;

    /**
     * @var ObjectManagerInterface|MockObject
     */
    protected $objectManagerMock;

    /**
     * @var ManagerInterface|MockObject
     */
    protected $eventManagerMock;

    /**
     * @var ResponseInterface|MockObject
     */
    protected $responseMock;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var Edit
     */
    protected $edit;

    /**
     * @var Session|MockObject
     */
    protected $sessionMock;

    /**
     * @var Category|MockObject
     */
    protected $categoryMock;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|MockObject
     */
    protected $messageManagerMock;

    /**
     * Set up
     *
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $objects = [
            [
                StoreManagerInterface::class,
                $this->createMock(StoreManagerInterface::class)
            ],
            [
                Registry::class,
                $this->createMock(Registry::class)
            ],
            [
                Config::class,
                $this->createMock(Config::class)
            ],
            [
                \Magento\Backend\Model\Auth\Session::class,
                $this->createMock(\Magento\Backend\Model\Auth\Session::class)
            ]
        ];
        $this->objectManager->prepareObjectManager($objects);

        $this->categoryMock = $this->createPartialMock(
            Category::class,
            [
                'getPath',
                'addData',
                'getId',
                'getName',
                'getResource',
                'setStoreId',
                'toArray'
            ]
        );

        $this->contextMock = new ContextTestHelper();

        $this->resultRedirectFactoryMock = $this->createPartialMock(
            RedirectFactory::class,
            ['create']
        );

        $this->resultPageMock = new ResultPageTestHelper();

        $this->resultPageFactoryMock = $this->createPartialMock(
            PageFactory::class,
            ['create']
        );
        $this->resultPageFactoryMock->method('create')->willReturn($this->resultPageMock);

        $this->resultJsonFactoryMock = $this->createPartialMock(
            JsonFactory::class,
            ['create']
        );
        $this->storeManagerInterfaceMock = new StoreManagerTestHelper();
        $this->requestMock = new RequestTestHelper();
        $this->objectManagerMock = $this->createMock(ObjectManagerInterface::class);
        $this->eventManagerMock = $this->createMock(
            ManagerInterface::class,
            [],
            '',
            false,
            true,
            true,
            ['dispatch']
        );
        $this->messageManagerMock = $this->createMock(\Magento\Framework\Message\ManagerInterface::class);
        $this->titleMock = $this->createMock(Title::class);
        $this->sessionMock = $this->createPartialMock(Session::class, ['__call']);

        $this->contextMock->setMocks(
            $this->requestMock,
            $this->objectManagerMock,
            $this->eventManagerMock,
            $this->responseMock,
            $this->messageManagerMock,
            $this->resultRedirectFactoryMock,
            $this->sessionMock,
            $this->titleMock
        );

        $this->edit = $this->objectManager->getObject(
            Edit::class,
            [
                'context' => $this->contextMock,
                'resultPageFactory' => $this->resultPageFactoryMock,
                'resultJsonFactory' => $this->resultJsonFactoryMock,
                'storeManager' => $this->storeManagerInterfaceMock
            ]
        );
    }

    /**
     * Run test execute method
     *
     * @param int|bool $categoryId
     * @param int $storeId
     * @return void
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    #[DataProvider('dataProviderExecute')]
    public function testExecute($categoryId, $storeId)
    {
        $rootCategoryId = 2;

        // Configure the request mock
        $this->requestMock->setParamReturn($categoryId);

        $this->mockInitCategoryCall();

        $this->sessionMock->expects($this->once())
            ->method('__call')
            ->willReturn([]);

        // Create a store object
        $storeObject = new StoreTestHelper();
        $storeObject->setCode('default');
        $storeObject->setRootCategoryId(2);
        
        // Configure the store manager mock
        $this->storeManagerInterfaceMock->setStoreReturn($storeObject);
        $this->storeManagerInterfaceMock->setDefaultStoreViewReturn($storeObject);

        if (!$categoryId) {
            $categoryId = $rootCategoryId;
        }

        // No mock expectations needed for anonymous class

        $this->categoryMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($categoryId);

        $this->edit->execute();
    }

    /**
     * Data provider for execute
     *
     * @return array
     */
    public static function dataProviderExecute()
    {
        return [
            [
                'categoryId' => null,
                'storeId' => null,
            ],
            [
                'categoryId' => null,
                'storeId' => 7,
            ]
        ];
    }

    /**
     * Mock for method "_initCategory"
     */
    private function mockInitCategoryCall()
    {
        $this->objectManagerMock->expects($this->atLeastOnce())
            ->method('create')
            ->willReturn($this->categoryMock);
    }
}
