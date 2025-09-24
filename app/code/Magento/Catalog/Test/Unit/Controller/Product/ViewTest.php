<?php
/**
 * Copyright 2021 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Controller\Product;

use Magento\Catalog\Helper\Product;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\UrlInterface;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Controller\Product\View;
use Magento\Catalog\Helper\Product\View as ViewHelper;
use Magento\Catalog\Model\Design;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\ObjectManager\ObjectManager;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Responsible for testing product view action on a strorefront.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ViewTest extends TestCase
{
    /**
     * @var View
     */
    private $view;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    /**
     * @var PageFactory|MockObject
     */
    private $resultPageFactoryMock;

    /**
     * @var Design|MockObject
     */
    private $catalogDesignMock;

    /**
     * @var ProductRepository|MockObject
     */
    private $productRepositoryMock;

    /**
     * @var ProductInterface|MockObject
     */
    private $productInterfaceMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    protected $storeManagerMock;

    /**
     * @var Product|MockObject
     */
    protected $helperProduct;

    /**
     * @var Redirect|MockObject
     */
    protected $redirectMock;

    /**
     * @var UrlInterface|MockObject
     */
    protected $urlBuilder;

    /**
     * @var LoggerInterface|MockObject
     */
    protected $loggerMock;

    /**
     * @var Data|MockObject
     */
    protected $jsonHelperMock;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = new class implements RequestInterface {
            private $isPostReturn = false;
            private $isAjaxReturn = false;
            private $getParamCallback = null;
            private $getParamReturn = null;
            
            public function setReturnValues($isPost = false, $isAjax = false, $getParam = null)
            {
                $this->isPostReturn = $isPost;
                $this->isAjaxReturn = $isAjax;
                $this->getParamReturn = $getParam;
                return $this;
            }
            
            public function setGetParamCallback($callback)
            {
                $this->getParamCallback = $callback;
                return $this;
            }
            
            public function getParam($param, $defaultValue = null)
            {
                if ($this->getParamCallback) {
                    return call_user_func($this->getParamCallback, $param);
                }
                return $this->getParamReturn;
            }
            public function isAjax()
            {
                return $this->isAjaxReturn;
            }
            public function isPost()
            {
                return $this->isPostReturn;
            }
            // Required interface methods
            public function getModuleName()
            {
                return null;
            }
            public function setModuleName($moduleName)
            {
                return $this;
            }
            public function getActionName()
            {
                return null;
            }
            public function setActionName($actionName)
            {
                return $this;
            }
            public function getRequestUri()
            {
                return null;
            }
            public function getPathInfo()
            {
                return null;
            }
            public function getParams()
            {
                return [];
            }
            public function setParam($key, $value)
            {
                return $this;
            }
            public function setParams(array $params)
            {
                return $this;
            }
            public function getMethod()
            {
                return null;
            }
            public function isGet()
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
            public function isSecure()
            {
                return false;
            }
            public function getHttpHost()
            {
                return null;
            }
            public function getClientIp($checkProxy = true)
            {
                return null;
            }
            public function getScriptName()
            {
                return null;
            }
            public function getHttpReferer()
            {
                return null;
            }
            public function getRequestString()
            {
                return null;
            }
            public function getBaseUrl()
            {
                return null;
            }
            public function getDistroBaseUrl()
            {
                return null;
            }
            public function getRequestedRouteName()
            {
                return null;
            }
            public function getRequestedControllerName()
            {
                return null;
            }
            public function getRequestedActionName()
            {
                return null;
            }
            public function getRouteName()
            {
                return null;
            }
            public function getControllerName()
            {
                return null;
            }
            public function getFullActionName($delimiter = '_')
            {
                return null;
            }
            public function getForwarded()
            {
                return null;
            }
            public function setForwarded($flag)
            {
                return $this;
            }
            public function getDirectFrontNames()
            {
                return [];
            }
            public function setDirectFrontNames($directFrontNames)
            {
                return $this;
            }
            public function getBeforeForwardInfo()
            {
                return [];
            }
            public function setBeforeForwardInfo($beforeForwardInfo)
            {
                return $this;
            }
            public function getAlias($name)
            {
                return null;
            }
            public function setAlias($name, $alias)
            {
                return $this;
            }
            public function getAliases()
            {
                return [];
            }
            public function getCookie($name, $defaultValue = null)
            {
                return null;
            }
            public function getHeader($name)
            {
                return null;
            }
            public function getServer($name)
            {
                return null;
            }
            public function getEnv($name)
            {
                return null;
            }
            public function getQuery($name = null)
            {
                return null;
            }
            public function getPost($name = null)
            {
                return null;
            }
            public function getFiles($name = null)
            {
                return null;
            }
            public function getRawBody()
            {
                return null;
            }
            public function getContent()
            {
                return null;
            }
            public function getContentLength()
            {
                return null;
            }
            public function getContentType()
            {
                return null;
            }
            public function getAcceptTypes()
            {
                return null;
            }
            public function getAcceptLanguage()
            {
                return null;
            }
            public function getAcceptCharset()
            {
                return null;
            }
            public function getAcceptEncoding()
            {
                return null;
            }
            public function getAccept()
            {
                return null;
            }
            public function getExtensionAttributes()
            {
                return null;
            }
            public function setExtensionAttributes($extensionAttributes)
            {
                return $this;
            }
        };
        $contextMock->method('getRequest')->willReturn($this->requestMock);
        $objectManagerMock = $this->createMock(ObjectManager::class);
        $this->helperProduct = $this->createMock(Product::class);
        $objectManagerMock->expects($this->any())
            ->method('get')
            ->with(Product::class)
            ->willReturn($this->helperProduct);
        $contextMock->method('getObjectManager')->willReturn($objectManagerMock);
        $resultRedirectFactoryMock = $this->createPartialMock(
            RedirectFactory::class,
            ['create']
        );
        $this->redirectMock = $this->createMock(Redirect::class);
        $resultRedirectFactoryMock->method('create')->willReturn($this->redirectMock);
        $contextMock->method('getResultRedirectFactory')->willReturn($resultRedirectFactoryMock);
        $this->urlBuilder = $this->getMockBuilder(UrlInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->urlBuilder->method('getUrl')->willReturn('productUrl');
        $contextMock->method('getUrl')->willReturn($this->urlBuilder);
        $viewHelperMock = $this->getMockBuilder(ViewHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $resultForwardFactoryMock = $this->getMockBuilder(ForwardFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultPageFactoryMock = $this->getMockBuilder(PageFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultPageFactoryMock = $this->getMockBuilder(PageFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->catalogDesignMock = $this->getMockBuilder(Design::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->productRepositoryMock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->productInterfaceMock = $this->getMockBuilder(ProductInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $storeMock = $this->createMock(Store::class);
        $this->storeManagerMock->method('getStore')->willReturn($storeMock);

        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->jsonHelperMock = $this->createMock(Data::class);

        $this->view = new View(
            $contextMock,
            $viewHelperMock,
            $resultForwardFactoryMock,
            $this->resultPageFactoryMock,
            $this->loggerMock,
            $this->jsonHelperMock,
            $this->catalogDesignMock,
            $this->productRepositoryMock,
            $this->storeManagerMock
        );
    }

    /**
     * Verify that product custom design theme is applied before product rendering
     */
    public function testExecute(): void
    {
        $themeId = 3;
        $this->requestMock->setReturnValues(false, false, null);
        $this->productRepositoryMock->method('getById')
            ->willReturn($this->productInterfaceMock);
        $dataObjectMock = new class extends DataObject {
            public function __construct()
            {
                // Empty constructor
            }
            
            public function getCustomDesign()
            {
                return 3;
            }
        };
        $this->catalogDesignMock->method('getDesignSettings')
            ->willReturn($dataObjectMock);
        $this->catalogDesignMock->expects($this->once())
            ->method('applyCustomDesign')
            ->with($themeId);
        $viewResultPageMock = $this->getMockBuilder(Page::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultPageFactoryMock->method('create')
            ->willReturn($viewResultPageMock);
        $this->view->execute();
    }

    public function testExecuteRecentlyViewed(): void
    {
        $post = [
            'category' => '1',
            'id' => 1,
            'options' => false,
            View::PARAM_NAME_URL_ENCODED => 'some_param_url_encoded'
        ];

        // _initProduct
        $this->helperProduct->method('initProduct')
            ->willReturn('true');
        $this->redirectMock->method('setUrl')->with('productUrl')->willReturnSelf();

        $this->requestMock->setReturnValues(true, false, null);
        $this->requestMock->setGetParamCallback(
            function ($key) use ($post) {
                return $post[$key];
            }
        );

        $this->urlBuilder->method('getCurrentUrl')->willReturn('productUrl');
        $this->view->execute();
    }
}
