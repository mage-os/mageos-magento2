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
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Controller\Adminhtml\Index;
use Magento\Customer\Controller\Adminhtml\Index\ResetPassword;
use Magento\Customer\Model\AccountManagement;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\FrontController;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\ViewInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\SecurityViolationException;
use Magento\Framework\Message\Error;
use Magento\Framework\Message\Manager;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Message\Warning;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\Backend\Test\Unit\Helper\BackendSessionTestHelper;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for \Magento\Customer\Controller\Adminhtml\Index controller
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ResetPasswordTest extends TestCase
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
    protected $_customerAccountManagementMock;

    /**
     * @var MockObject|CustomerRepositoryInterface
     */
    protected $_customerRepositoryMock;

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
     * @var RedirectFactory|MockObject
     */
    protected $resultRedirectFactoryMock;

    /**
     * @var Redirect|MockObject
     */
    protected $resultRedirectMock;

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
            ['addSuccessMessage', 'addMessage', 'addExceptionMessage', 'addErrorMessage']
        );

        $this->resultRedirectFactoryMock = $this->createPartialMock(
            RedirectFactory::class,
            ['create']
        );
        $this->resultRedirectMock = $this->createMock(Redirect::class);

        $this->resultRedirectFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->resultRedirectMock);

        $contextMock = new \Magento\Backend\Test\Unit\Helper\ContextTestHelper();
        $contextMock->setRequest($this->_request);
        $contextMock->setResponse($this->_response);
        $contextMock->setObjectManager($this->_objectManager);
        $contextMock->setFrontController($frontControllerMock);
        $contextMock->setActionFlag($actionFlagMock);
        $contextMock->setHelper($this->_helper);
        $contextMock->setSession($this->_session);
        $contextMock->setMessageManager($this->messageManager);
        
        $viewMock = $this->createMock(ViewInterface::class);
        $viewMock->expects($this->any())->method('loadLayout')->willReturnSelf();
        $contextMock->setView($viewMock);
        $contextMock->setResultRedirectFactory($this->resultRedirectFactoryMock);

        $this->_customerAccountManagementMock = $this->createMock(AccountManagementInterface::class);

        $this->_customerRepositoryMock = $this->createMock(CustomerRepositoryInterface::class);

        $args = [
            'context' => $contextMock,
            'customerAccountManagement' => $this->_customerAccountManagementMock,
            'customerRepository' => $this->_customerRepositoryMock,
        ];

        $helperObjectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->_testedObject = $helperObjectManager->getObject(
            ResetPassword::class,
            $args
        );
    }

    public function testResetPasswordActionNoCustomer()
    {
        $redirectLink = 'customer/index';
        $this->_request->expects(
            $this->once()
        )->method(
            'getParam'
        )->with(
            'customer_id',
            0
        )->willReturn(
            false
        );

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with($redirectLink);

        $this->assertInstanceOf(
            Redirect::class,
            $this->_testedObject->execute()
        );
    }

    public function testResetPasswordActionInvalidCustomerId()
    {
        $redirectLink = 'customer/index';
        $customerId = 1;

        $this->_request->expects(
            $this->once()
        )->method(
            'getParam'
        )->with(
            'customer_id',
            0
        )->willReturn(
            $customerId
        );

        $this->_customerRepositoryMock->expects(
            $this->once()
        )->method(
            'getById'
        )->with(
            $customerId
        )->willThrowException(
            new NoSuchEntityException(
                __(
                    'No such entity with %fieldName = %fieldValue',
                    ['fieldName' => 'customerId', 'fieldValue' => $customerId]
                )
            )
        );

        $this->_helper->expects(
            $this->any()
        )->method(
            'getUrl'
        )->with(
            'customer/index',
            []
        )->willReturn(
            $redirectLink
        );

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with($redirectLink);

        $this->assertInstanceOf(
            Redirect::class,
            $this->_testedObject->execute()
        );
    }

    public function testResetPasswordActionCoreException()
    {
        $customerId = 1;

        $this->_request->expects(
            $this->once()
        )->method(
            'getParam'
        )->with(
            'customer_id',
            0
        )->willReturn(
            $customerId
        );

        // Setup a core exception to return
        $exception = new \Magento\Framework\Validator\Exception();
        $error = new Error('Something Bad happened');
        $exception->addMessage($error);

        $this->_customerRepositoryMock->expects(
            $this->once()
        )->method(
            'getById'
        )->with(
            $customerId
        )->willThrowException(
            $exception
        );

        // Verify error message is set
        $this->messageManager->expects($this->once())
            ->method('addMessage')
            ->with($error);

        $this->_testedObject->execute();
    }

    public function testResetPasswordActionSecurityException()
    {
        $securityText = 'Security violation.';
        $exception = new SecurityViolationException(__($securityText));
        $customerId = 1;
        $email = 'some@example.com';
        $websiteId = 1;

        $this->_request->expects(
            $this->once()
        )->method(
            'getParam'
        )->with(
            'customer_id',
            0
        )->willReturn(
            $customerId
        );
        $customer = $this->createMock(CustomerInterface::class);
        $customer->expects($this->once())->method('getEmail')->willReturn($email);
        $customer->expects($this->once())->method('getWebsiteId')->willReturn($websiteId);
        $this->_customerRepositoryMock->expects(
            $this->once()
        )->method(
            'getById'
        )->with(
            $customerId
        )->willReturn(
            $customer
        );
        $this->_customerAccountManagementMock->expects(
            $this->once()
        )->method(
            'initiatePasswordReset'
        )->willThrowException($exception);

        $this->messageManager->expects(
            $this->once()
        )->method(
            'addErrorMessage'
        )->with(
            $exception->getMessage()
        );

        $this->_testedObject->execute();
    }

    public function testResetPasswordActionCoreExceptionWarn()
    {
        $warningText = 'Warning';
        $customerId = 1;

        $this->_request->expects($this->once())
            ->method('getParam')
            ->with('customer_id', 0)
            ->willReturn($customerId);

        // Setup a core exception to return
        $exception = new \Magento\Framework\Validator\Exception(__($warningText));

        $error = new Warning('Something Not So Bad happened');
        $exception->addMessage($error);

        $this->_customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willThrowException($exception);

        // Verify Warning is converted to an Error and message text is set to exception text
        $this->messageManager->expects($this->once())
            ->method('addMessage')
            ->with(new Error($warningText));

        $this->_testedObject->execute();
    }

    public function testResetPasswordActionException()
    {
        $customerId = 1;

        $this->_request->expects(
            $this->once()
        )->method(
            'getParam'
        )->with(
            'customer_id',
            0
        )->willReturn(
            $customerId
        );

        // Setup a core exception to return
        $exception = new \Exception('Something Really Bad happened');

        $this->_customerRepositoryMock->expects(
            $this->once()
        )->method(
            'getById'
        )->with(
            $customerId
        )->willThrowException(
            $exception
        );

        // Verify error message is set
        $this->messageManager->expects(
            $this->once()
        )->method(
            'addExceptionMessage'
        )->with(
            $exception,
            'Something went wrong while resetting customer password.'
        );

        $this->_testedObject->execute();
    }

    public function testResetPasswordActionSendEmail()
    {
        $customerId = 1;
        $email = 'test@example.com';
        $websiteId = 1;
        $redirectLink = 'customer/*/edit';

        $this->_request->expects(
            $this->once()
        )->method(
            'getParam'
        )->with(
            'customer_id',
            0
        )->willReturn(
            $customerId
        );

        $customer = $this->createMock(CustomerInterface::class);

        $customer->expects($this->once())->method('getEmail')->willReturn($email);
        $customer->expects($this->once())->method('getWebsiteId')->willReturn($websiteId);

        $this->_customerRepositoryMock->expects(
            $this->once()
        )->method(
            'getById'
        )->with(
            $customerId
        )->willReturn(
            $customer
        );

        // verify initiatePasswordReset() is called
        $this->_customerAccountManagementMock->expects(
            $this->once()
        )->method(
            'initiatePasswordReset'
        )->with(
            $email,
            AccountManagement::EMAIL_REMINDER,
            $websiteId
        );

        // verify success message
        $this->messageManager->expects(
            $this->once()
        )->method(
            'addSuccessMessage'
        )->with(
            'The customer will receive an email with a link to reset password.'
        );

        // verify redirect
        $this->_helper->expects(
            $this->any()
        )->method(
            'getUrl'
        )->with(
            'customer/*/edit',
            ['id' => $customerId, '_current' => true]
        )->willReturn(
            $redirectLink
        );

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with(
                $redirectLink,
                ['id' => $customerId, '_current' => true]
            );

        $this->assertInstanceOf(
            Redirect::class,
            $this->_testedObject->execute()
        );
    }
}
