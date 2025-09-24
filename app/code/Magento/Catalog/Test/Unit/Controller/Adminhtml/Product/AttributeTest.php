<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Controller\Adminhtml\Product;

use Magento\Backend\App\Action\Context;
use Magento\Catalog\Controller\Adminhtml\Product\Attribute;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\View\Result\PageFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    protected $objectManager;

    /**
     * @var Context|MockObject
     */
    protected $contextMock;

    /**
     * @var Registry|MockObject
     */
    protected $coreRegistryMock;

    /**
     * @var FrontendInterface|MockObject
     */
    protected $attributeLabelCacheMock;

    /**
     * @var PageFactory|MockObject
     */
    protected $resultPageFactoryMock;

    /**
     * @var RequestInterface|MockObject
     */
    protected $requestMock;

    /**
     * @var ResultFactory|MockObject
     */
    protected $resultFactoryMock;

    /**
     * @var ManagerInterface|MockObject
     */
    protected $messageManager;

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManagerHelper($this);
        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->attributeLabelCacheMock = $this->getMockBuilder(FrontendInterface::class)
            ->getMock();
        $this->coreRegistryMock = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultPageFactoryMock = $this->getMockBuilder(PageFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestMock = new class implements RequestInterface {
            private $getPostValueReturn = null;
            private $hasReturn = null;
            private $getParamReturnMap = [];
            private $hasReturnMap = [];
            
            public function setGetPostValueReturn($return)
            {
                $this->getPostValueReturn = $return;
                return $this;
            }
            
            public function setHasReturn($return)
            {
                $this->hasReturn = $return;
                return $this;
            }
            
            public function setGetParamReturnMap($returnMap)
            {
                $this->getParamReturnMap = $returnMap;
                return $this;
            }
            
            public function setHasReturnMap($returnMap)
            {
                $this->hasReturnMap = $returnMap;
                return $this;
            }
            
            public function getPostValue($key = null, $defaultValue = null)
            {
                if ($this->getPostValueReturn !== null) {
                    return $this->getPostValueReturn;
                }
                return $defaultValue;
            }
            
            public function has($key)
            {
                if (isset($this->hasReturnMap[$key])) {
                    return $this->hasReturnMap[$key];
                }
                if ($this->hasReturn !== null) {
                    return $this->hasReturn;
                }
                return false;
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
            public function getParam($param, $defaultValue = null)
            {
                foreach ($this->getParamReturnMap as $mapEntry) {
                    if ($mapEntry[0] === $param && $mapEntry[1] === $defaultValue) {
                        return $mapEntry[2];
                    }
                }
                return $defaultValue;
            }
            public function getParams()
            {
                return [];
            }
            public function getPost($key = null, $defaultValue = null)
            {
                return $defaultValue;
            }
            public function getQuery($key = null, $defaultValue = null)
            {
                return $defaultValue;
            }
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
                return $this;
            }
        };
        $this->resultFactoryMock = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->messageManager = $this->getMockBuilder(ManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contextMock->method('getRequest')->willReturn($this->requestMock);
        $this->contextMock->method('getResultFactory')->willReturn($this->resultFactoryMock);
        $this->contextMock
            ->method('getMessageManager')
            ->willReturn($this->messageManager);
    }

    /**
     * @return Attribute
     */
    protected function getModel()
    {
        return $this->objectManager->getObject(Attribute::class, [
            'context' => $this->contextMock,
            'attributeLabelCache' => $this->attributeLabelCacheMock,
            'coreRegistry' => $this->coreRegistryMock,
            'resultPageFactory' => $this->resultPageFactoryMock,
        ]);
    }

    public function testDispatch()
    {
        $this->markTestSkipped('Should be dispatched in parent');
    }
}
