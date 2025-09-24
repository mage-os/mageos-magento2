<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Controller\Adminhtml\Category;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Backend\Model\Auth\Session;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Controller\Adminhtml\Category\Move;
use Magento\Catalog\Model\Category;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\Collection;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\Messages;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MoveTest extends TestCase
{
    /**
     * @var JsonFactory|MockObject
     */
    private $resultJsonFactoryMock;

    /**
     * @var LayoutFactory|MockObject
     */
    private $layoutFactoryMock;

    /**
     * @var LoggerInterface|MockObject
     */
    private $loggerMock;

    /**
     * @var Context|MockObject
     */
    private $context;

    /**
     * @var RequestInterface|MockObject
     */
    private $request;

    /**
     * @var Move
     */
    private $moveController;

    /**
     * @var ObjectManagerInterface|MockObject
     */
    private $objectManager;

    /**
     * @var ManagerInterface|MockObject
     */
    private $messageManager;

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
                Session::class,
                $this->createMock(Session::class)
            ]
        ];
        $this->objectManager->prepareObjectManager($objects);
        $this->resultJsonFactoryMock = $this->getMockBuilder(JsonFactory::class)
            ->onlyMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->layoutFactoryMock = $this->getMockBuilder(LayoutFactory::class)
            ->onlyMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->context = $this->createMock(Context::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->fillContext();

        $this->moveController = new Move(
            $this->context,
            $this->resultJsonFactoryMock,
            $this->layoutFactoryMock,
            $this->loggerMock
        );
        $this->initObjectManager();
    }

    private function fillContext()
    {
        $this->request = new class implements RequestInterface {
            private $getPostCallback = null;
            
            public function setGetPostCallback($callback)
            {
                $this->getPostCallback = $callback;
                return $this;
            }
            
            public function getPost($key = null, $defaultValue = null)
            {
                if ($this->getPostCallback) {
                    return call_user_func($this->getPostCallback, $key, $defaultValue);
                }
                return $defaultValue;
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
                return $defaultValue;
            }
            public function getParams()
            {
                return [];
            }
            public function getPostValue($key = null, $defaultValue = null)
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
        $this->context->expects($this->once())->method('getRequest')->willReturn($this->request);
        $this->messageManager = $this->createMock(ManagerInterface::class);
        $this->context->expects($this->once())->method('getMessageManager')->willReturn($this->messageManager);
    }

    private function initObjectManager()
    {
        $this->objectManager = $this->createMock(ObjectManagerInterface::class);
        $moveController = new \ReflectionClass($this->moveController);
        $objectManagerProp = $moveController->getProperty('_objectManager');
        $objectManagerProp->setAccessible(true);
        $objectManagerProp->setValue($this->moveController, $this->objectManager);
    }

    public function testExecuteWithGenericException()
    {
        $messagesCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $messageBlock = $this->getMockBuilder(Messages::class)
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock = $this->createMock(LayoutInterface::class);
        $this->layoutFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($layoutMock);
        $layoutMock->expects($this->once())
            ->method('getMessagesBlock')
            ->willReturn($messageBlock);
        $wysiwygConfig = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();
        $registry = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();
        $categoryMock = $this->getMockBuilder(Category::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request->setGetPostCallback(function ($arg1, $arg2) {
            if ($arg1 == 'pid' && $arg2 == false) {
                return 2;
            } elseif ($arg1 == 'aid' && $arg2 == false) {
                return 1;
            }
        });
        $this->objectManager->expects($this->once())
            ->method('create')
            ->with(Category::class)
            ->willReturn($categoryMock);
        $this->objectManager->expects($this->any())
            ->method('get')
            ->willReturnMap([[Registry::class, $registry], [Config::class, $wysiwygConfig]]);
        $categoryMock->expects($this->once())
            ->method('move')
            ->willThrowException(new \Exception('Some exception'));
        $this->messageManager->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('There was a category move error.'));
        $this->messageManager->expects($this->once())
            ->method('getMessages')
            ->with(true)
            ->willReturn($messagesCollection);
        $messageBlock->expects($this->once())
            ->method('setMessages')
            ->with($messagesCollection);
        $resultJsonMock = $this->getMockBuilder(Json::class)
            ->disableOriginalConstructor()
            ->getMock();
        $messageBlock->expects($this->once())
            ->method('getGroupedHtml')
            ->willReturn('<body></body>');
        $resultJsonMock->expects($this->once())
            ->method('setData')
            ->with(
                [
                    'messages' => '<body></body>',
                    'error' => true
                ]
            )
            ->willReturn(true);
        $this->resultJsonFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($resultJsonMock);
        $this->assertTrue($this->moveController->execute());
    }

    public function testExecuteWithLocalizedException()
    {
        $exceptionMessage = 'Sorry, but we can\'t find the new category you selected.';
        $messagesCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $messageBlock = $this->getMockBuilder(Messages::class)
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock = $this->createMock(LayoutInterface::class);
        $this->layoutFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($layoutMock);
        $layoutMock->expects($this->once())
            ->method('getMessagesBlock')
            ->willReturn($messageBlock);
        $wysiwygConfig = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();
        $registry = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();
        $categoryMock = $this->getMockBuilder(Category::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request->setGetPostCallback(function ($arg1, $arg2) {
            if ($arg1 == 'pid' && $arg2 == false) {
                return 2;
            } elseif ($arg1 == 'aid' && $arg2 == false) {
                return 1;
            }
        });
        $this->objectManager->expects($this->once())
            ->method('create')
            ->with(Category::class)
            ->willReturn($categoryMock);
        $this->objectManager->expects($this->any())
            ->method('get')
            ->willReturnMap([[Registry::class, $registry], [Config::class, $wysiwygConfig]]);
        $this->messageManager->expects($this->once())
            ->method('addExceptionMessage');
        $this->messageManager->expects($this->once())
            ->method('getMessages')
            ->with(true)
            ->willReturn($messagesCollection);
        $messageBlock->expects($this->once())
            ->method('setMessages')
            ->with($messagesCollection);
        $resultJsonMock = $this->getMockBuilder(Json::class)
            ->disableOriginalConstructor()
            ->getMock();
        $messageBlock->expects($this->once())
            ->method('getGroupedHtml')
            ->willReturn('<body></body>');
        $resultJsonMock->expects($this->once())
            ->method('setData')
            ->with(
                [
                    'messages' => '<body></body>',
                    'error' => true
                ]
            )
            ->willReturn(true);
        $categoryMock->expects($this->once())
            ->method('move')
            ->willThrowException(new LocalizedException(__($exceptionMessage)));
        $this->resultJsonFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($resultJsonMock);
        $this->assertTrue($this->moveController->execute());
    }

    public function testSuccessfulCategorySave()
    {
        $messagesCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $messageBlock = $this->getMockBuilder(Messages::class)
            ->disableOriginalConstructor()
            ->getMock();
        $layoutMock = $this->createMock(LayoutInterface::class);
        $this->layoutFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($layoutMock);
        $layoutMock->expects($this->once())
            ->method('getMessagesBlock')
            ->willReturn($messageBlock);
        $wysiwygConfig = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->getMock();
        $registry = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();
        $categoryMock = $this->getMockBuilder(Category::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->request->setGetPostCallback(function ($arg1, $arg2) {
            if ($arg1 == 'pid' && $arg2 == false) {
                return 2;
            } elseif ($arg1 == 'aid' && $arg2 == false) {
                return 1;
            }
        });
        $this->objectManager->expects($this->once())
            ->method('create')
            ->with(Category::class)
            ->willReturn($categoryMock);
        $this->objectManager->expects($this->any())
            ->method('get')
            ->willReturnMap([[Registry::class, $registry], [Config::class, $wysiwygConfig]]);
        $this->messageManager->expects($this->once())
            ->method('getMessages')
            ->with(true)
            ->willReturn($messagesCollection);
        $messageBlock->expects($this->once())
            ->method('setMessages')
            ->with($messagesCollection);
        $resultJsonMock = $this->getMockBuilder(Json::class)
            ->disableOriginalConstructor()
            ->getMock();
        $messageBlock->expects($this->once())
            ->method('getGroupedHtml')
            ->willReturn('<body></body>');
        $resultJsonMock->expects($this->once())
            ->method('setData')
            ->with(
                [
                    'messages' => '<body></body>',
                    'error' => false
                ]
            )
            ->willReturn(true);
        $this->messageManager->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('You moved the category.'));
        $categoryMock->expects($this->once())
            ->method('move')
            ->with(2, 1);
        $this->resultJsonFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($resultJsonMock);
        $this->assertTrue($this->moveController->execute());
    }
}
