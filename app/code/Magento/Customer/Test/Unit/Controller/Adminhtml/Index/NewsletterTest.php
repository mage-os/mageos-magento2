<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Data;
use Magento\Backend\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Controller\Adminhtml\Index;
use Magento\Customer\Controller\Adminhtml\Index\Newsletter;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\FrontController;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\ViewInterface;
use Magento\Framework\Message\Manager;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use Magento\Framework\View\Result\Layout;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Newsletter\Model\Subscriber;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\Backend\Test\Unit\Helper\BackendSessionTestHelper;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for \Magento\Customer\Controller\Adminhtml\Index controller
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class NewsletterTest extends TestCase
{
    /**
     * Request mock instance
     *
     * @var MockObject|RequestInterface
     */
    protected $_request;

    /**
     * Response mock instance
     *
     * @var MockObject|ResponseInterface
     */
    protected $_response;

    /**
     * Instance of mocked tested object
     *
     * @var MockObject|Index
     */
    protected $_testedObject;

    /**
     * ObjectManager mock instance
     *
     * @var MockObject|\Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var MockObject|AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * Session mock instance
     *
     * @var MockObject|\Magento\Backend\Model\Session
     */
    protected $_session;

    /**
     * Backend helper mock instance
     *
     * @var MockObject|Data
     */
    protected $_helper;

    /**
     * @var MockObject|ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Layout|MockObject
     */
    protected $resultLayoutMock;

    /**
     * @var MockObject
     */
    protected $pageConfigMock;

    /**
     * @var MockObject
     */
    protected $titleMock;

    /**
     * @var MockObject
     */
    protected $layoutInterfaceMock;

    /**
     * @var MockObject
     */
    protected $viewInterfaceMock;

    /**
     * @var LayoutFactory|MockObject
     */
    protected $resultLayoutFactoryMock;

    /**
     * Prepare required values
     *
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function setUp(): void
    {
        $this->_request = $this->createMock(Http::class);

        $this->_response = $this->createPartialMock(
            \Magento\Framework\App\Response\Http::class,
            ['setRedirect', 'getHeader', '__wakeup']
        );

        $this->_response->expects(
            $this->any()
        )->method(
            'getHeader'
        )->with(
            'X-Frame-Options'
        )->willReturn(
            true
        );

        $this->_objectManager = $this->createPartialMock(
            ObjectManager::class,
            ['get', 'create']
        );
        $frontControllerMock = $this->createMock(FrontController::class);

        $actionFlagMock = $this->createMock(ActionFlag::class);

        $this->_session = new BackendSessionTestHelper();
        // setIsUrlNotice is now available through the helper

        $this->_helper = $this->createPartialMock(
            Data::class,
            ['getUrl']
        );

        $this->messageManager = $this->createPartialMock(
            Manager::class,
            ['addSuccess', 'addMessage', 'addException']
        );

        $contextMock = new \Magento\Backend\Test\Unit\Helper\ContextTestHelper();
        $contextMock->setRequest($this->_request);
        $contextMock->setResponse($this->_response);
        $contextMock->setObjectManager($this->_objectManager);
        $contextMock->setFrontController($frontControllerMock);
        $contextMock->setActionFlag($actionFlagMock);
        $contextMock->setHelper($this->_helper);
        $contextMock->setSession($this->_session);
        $contextMock->setMessageManager($this->messageManager);
        $this->titleMock = $this->createMock(Title::class);
        $contextMock->setTitle($this->titleMock);
        $this->viewInterfaceMock = $this->createMock(ViewInterface::class);

        $this->viewInterfaceMock->expects($this->any())->method('loadLayout')->willReturnSelf();
        $contextMock->setView($this->viewInterfaceMock);
        $this->resultLayoutMock = $this->createMock(Layout::class);
        $this->pageConfigMock = $this->createMock(Config::class);
        $this->customerAccountManagement = $this->createMock(AccountManagementInterface::class);
        $this->resultLayoutFactoryMock = $this->createMock(LayoutFactory::class);

        $args = [
            'context' => $contextMock,
            'customerAccountManagement' => $this->customerAccountManagement,
            'resultLayoutFactory' => $this->resultLayoutFactoryMock
        ];

        $helperObjectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->_testedObject = $helperObjectManager->getObject(
            Newsletter::class,
            $args
        );
    }

    public function testNewsletterAction()
    {
        $subscriberMock = $this->createMock(Subscriber::class);
        $this->resultLayoutFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->resultLayoutMock);
        $subscriberMock->expects($this->once())
            ->method('loadByCustomerId');
        $this->_objectManager
            ->expects($this->any())
            ->method('create')
            ->with(Subscriber::class)
            ->willReturn($subscriberMock);

        $this->assertInstanceOf(
            Layout::class,
            $this->_testedObject->execute()
        );
    }
}
