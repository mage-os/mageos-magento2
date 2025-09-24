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

        $this->contextMock = new class extends Context {
            private $requestMock;
            private $objectManagerMock;
            private $eventManagerMock;
            private $responseMock;
            private $messageManagerMock;
            private $resultRedirectFactoryMock;
            private $sessionMock;
            private $titleMock;

            public function __construct()
            {
 /* Empty constructor */
            }
            
            public function setMocks($requestMock, $objectManagerMock, $eventManagerMock, $responseMock, $messageManagerMock, $resultRedirectFactoryMock, $sessionMock, $titleMock)
            {
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
        };

        $this->resultRedirectFactoryMock = $this->createPartialMock(
            RedirectFactory::class,
            ['create']
        );

        $this->resultPageMock = new class extends ResultPage {
            public function __construct()
            {
 /* Empty constructor */
            }
            
            public function getConfig()
            {
                return $this;
            }
            public function getLayout()
            {
                return $this;
            }
            public function setActiveMenu($menuId)
            {
                return $this;
            }
            public function addBreadcrumb($label, $title, $link = null)
            {
                return $this;
            }
            public function getBlock($name)
            {
                return $this;
            }
            public function getTitle()
            {
                return $this;
            }
            public function prepend($element)
            {
                return $this;
            }
        };

        $this->resultPageFactoryMock = $this->createPartialMock(
            PageFactory::class,
            ['create']
        );
        $this->resultPageFactoryMock->method('create')->willReturn($this->resultPageMock);

        $this->resultJsonFactoryMock = $this->createPartialMock(
            JsonFactory::class,
            ['create']
        );
        $this->storeManagerInterfaceMock = new class implements StoreManagerInterface {
            private $getStoreReturn = null;
            private $getDefaultStoreViewReturn = null;
            private $getRootCategoryIdReturn = null;
            private $getCodeReturn = null;

            public function setReturnValues($getStore = null, $getDefaultStoreView = null, $getRootCategoryId = null, $getCode = null)
            {
                $this->getStoreReturn = $getStore;
                $this->getDefaultStoreViewReturn = $getDefaultStoreView;
                $this->getRootCategoryIdReturn = $getRootCategoryId;
                $this->getCodeReturn = $getCode;
                return $this;
            }

            public function getStore($storeId = null)
            {
                if ($this->getStoreReturn) {
                    return $this->getStoreReturn;
                }
                // Return a store object that has getCode() method
                return new class {
                    public function getCode()
                    {
                        return 'default';
                    }
                    public function getRootCategoryId()
                    {
                        return 2;
                    }
                };
            }
            public function getDefaultStoreView()
            {
                if ($this->getDefaultStoreViewReturn) {
                    return $this->getDefaultStoreViewReturn;
                }
                // Return a store object that has getRootCategoryId() method
                return new class {
                    public function getRootCategoryId()
                    {
                        return 2;
                    }
                };
            }
            public function getRootCategoryId()
            {
                return $this->getRootCategoryIdReturn ?: 2;
            }
            public function getCode()
            {
                return $this->getCodeReturn ?: '';
            }
            
            // Required StoreManagerInterface methods
            public function getStores($withDefault = false, $codeKey = false)
            {
                return [];
            }
            public function getWebsites($withDefault = false, $codeKey = false)
            {
                return [];
            }
            public function getWebsite($websiteId = null)
            {
                return null;
            }
            public function getCurrentStore()
            {
                return $this;
            }
            public function getCurrentStoreId()
            {
                return 1;
            }
            public function getCurrentWebsiteId()
            {
                return 1;
            }
            public function getCurrentWebsite()
            {
                return null;
            }
            public function getCurrentGroup()
            {
                return null;
            }
            public function getCurrentGroupId()
            {
                return 1;
            }
            
            // Additional required abstract methods
            public function setIsSingleStoreModeAllowed($value)
            {
                return $this;
            }
            public function hasSingleStore()
            {
                return false;
            }
            public function isSingleStoreMode()
            {
                return false;
            }
            public function reinitStores()
            {
                return $this;
            }
            public function getGroup($groupId = null)
            {
                return null;
            }
            public function getGroups($withDefault = false, $codeKey = false)
            {
                return [];
            }
            public function setCurrentStore($store)
            {
                return $this;
            }
        };
        $this->requestMock = new class implements RequestInterface {
            private $getParamReturn = null;
            private $getPostReturn = null;
            private $getPostValueReturn = null;
            private $getQueryReturn = null;
            private $setParamReturn = null;

            public function setReturnValues($getParam = null, $getPost = null, $getPostValue = null, $getQuery = null, $setParam = null)
            {
                $this->getParamReturn = $getParam;
                $this->getPostReturn = $getPost;
                $this->getPostValueReturn = $getPostValue;
                $this->getQueryReturn = $getQuery;
                $this->setParamReturn = $setParam;
                return $this;
            }

            public function getParam($param, $defaultValue = null)
            {
                return $this->getParamReturn;
            }
            public function getParams()
            {
                return [];
            }
            public function getPost($key = null, $defaultValue = null)
            {
                return $this->getPostReturn;
            }
            public function getPostValue($key = null, $defaultValue = null)
            {
                return $this->getPostValueReturn;
            }
            public function getQuery($key = null, $defaultValue = null)
            {
                return $this->getQueryReturn;
            }
            
            // Required RequestInterface methods
            public function getModuleName()
            {
                return '';
            }
            public function getControllerName()
            {
                return '';
            }
            public function getActionName()
            {
                return '';
            }
            public function getRequestUri()
            {
                return '';
            }
            public function getMethod()
            {
                return '';
            }
            public function isGet()
            {
                return false;
            }
            public function isPost()
            {
                return false;
            }
            public function isPut()
            {
                return false;
            }
            public function isDelete()
            {
                return false;
            }
            public function isHead()
            {
                return false;
            }
            public function isOptions()
            {
                return false;
            }
            public function isXmlHttpRequest()
            {
                return false;
            }
            public function isFlashRequest()
            {
                return false;
            }
            public function getServer($key = null, $default = null)
            {
                return $default;
            }
            public function getCookie($key, $default = null)
            {
                return $default;
            }
            public function getHeader($name)
            {
                return '';
            }
            public function getScheme()
            {
                return '';
            }
            public function getHttpHost()
            {
                return '';
            }
            public function getClientIp($checkProxy = true)
            {
                return '';
            }
            public function getScriptName()
            {
                return '';
            }
            public function getPathInfo()
            {
                return '';
            }
            public function getBasePath()
            {
                return '';
            }
            public function getBaseUrl()
            {
                return '';
            }
            public function getUri()
            {
                return '';
            }
            public function getUrl($uri = null)
            {
                return '';
            }
            public function getFullActionName($delimiter = '_')
            {
                return '';
            }
            public function isSecure()
            {
                return false;
            }
            public function getHttpUserAgent()
            {
                return '';
            }
            public function getHttpAccept()
            {
                return '';
            }
            public function getHttpAcceptCharset()
            {
                return '';
            }
            public function getHttpAcceptLanguage()
            {
                return '';
            }
            public function getHttpAcceptEncoding()
            {
                return '';
            }
            public function getHttpConnection()
            {
                return '';
            }
            public function getHttpReferer()
            {
                return '';
            }
            public function getRequestString()
            {
                return '';
            }
            public function getDistroBaseUrl()
            {
                return '';
            }
            public function getRequestedRouteName()
            {
                return '';
            }
            public function getRequestedControllerName()
            {
                return '';
            }
            public function getRequestedActionName()
            {
                return '';
            }
            public function getRouteName()
            {
                return '';
            }
            public function getControllerModule()
            {
                return '';
            }
            public function getFrontName()
            {
                return '';
            }
            public function getBeforeForwardInfo()
            {
                return [];
            }
            public function getAfterForwardInfo()
            {
                return [];
            }
            public function isStraight()
            {
                return false;
            }
            public function getAlias($name)
            {
                return '';
            }
            public function getOriginalPathInfo()
            {
                return '';
            }
            public function getOriginalRequest()
            {
                return null;
            }
            
            // Additional required abstract methods
            public function setModuleName($moduleName)
            {
                return $this;
            }
            public function setActionName($actionName)
            {
                return $this;
            }
            public function setParams(array $params)
            {
                return $this;
            }
            public function setParam($key, $value)
            {
                return $this->setParamReturn;
            }
        };
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

        // Configure the anonymous class request mock
        $this->requestMock->setReturnValues($categoryId, null, null, false, null);

        $this->mockInitCategoryCall();

        $this->sessionMock->expects($this->once())
            ->method('__call')
            ->willReturn([]);

        // Create a store object with getCode() method
        $storeObject = new class {
            public function getCode()
            {
                return 'default';
            }
            public function getRootCategoryId()
            {
                return 2;
            }
        };
        
        // Configure the anonymous class store manager mock
        $this->storeManagerInterfaceMock->setReturnValues($storeObject, $storeObject, $rootCategoryId, '');

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
