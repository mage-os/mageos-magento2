<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\LoginAsCustomerFrontendUi\Test\Unit\Controller\Login;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\Collection;
use Magento\Framework\Message\ManagerInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\LoginAsCustomerApi\Api\AuthenticateCustomerBySecretInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Page\Title;
use Magento\LoginAsCustomerApi\Api\GetAuthenticationDataBySecretInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use Magento\LoginAsCustomerFrontendUi\Controller\Login\Index;

/**
 * Unit tests for Magento\LoginAsCustomerFrontendUi\Controller\Login\Index
 */
class IndexTest extends TestCase
{
    /**
     * @var Index
     */
    private $controller;

    /**
     * @var ResultFactory|MockObject
     */
    private $resultFactoryMock;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    /**
     * @var AuthenticateCustomerBySecretInterface|MockObject
     */
    private $authenticateCustomerBySecretMock;

    /**
     * @var Session|MockObject
     */
    private $customerSessionMock;

    /**
     * @var ManagerInterface|MockObject
     */
    private $messageManagerMock;

    /**
     * @var LoggerInterface|MockObject
     */
    private $loggerMock;

    /**
     * @var CustomerInterface|MockObject
     */
    private $customerMock;

    /**
     * @var Redirect|MockObject
     */
    private $resultRedirectMock;

    /**
     * @var Raw|MockObject
     */
    private $resultPageMock;

    /**
     * @var Config|MockObject
     */
    private $pageConfigMock;

    /**
     * @var Title|MockObject
     */
    private $pageTitleMock;

    /**
     * Set up the test environment
     */
    protected function setUp(): void
    {
        $this->resultFactoryMock = $this->createMock(ResultFactory::class);
        $this->requestMock = $this->createMock(RequestInterface::class);
        $this->authenticateCustomerBySecretMock =
            $this->getMockForAbstractClass(AuthenticateCustomerBySecretInterface::class);
        $getAuthenticationDataBySecretMock =
            $this->getMockForAbstractClass(GetAuthenticationDataBySecretInterface::class);
        $customerRepositoryMock = $this->getMockForAbstractClass(CustomerRepositoryInterface::class);
        $this->customerSessionMock = $this->createMock(Session::class);
        $this->messageManagerMock = $this->getMockForAbstractClass(ManagerInterface::class);
        $this->loggerMock = $this->getMockForAbstractClass(LoggerInterface::class);
        $this->customerMock = $this->getMockForAbstractClass(CustomerInterface::class);
        $this->pageConfigMock = $this->createMock(Config::class);
        $this->resultRedirectMock = $this->createMock(Redirect::class);
        $this->resultPageMock = $this->createMock(Page::class);
        $this->pageConfigMock = $this->createMock(Config::class);
        $this->pageTitleMock = $this->createMock(Title::class);
        $this->controller = new Index(
            $this->resultFactoryMock,
            $this->requestMock,
            $customerRepositoryMock,
            $getAuthenticationDataBySecretMock,
            $this->authenticateCustomerBySecretMock,
            $this->messageManagerMock,
            $this->loggerMock,
            $this->customerSessionMock
        );
    }

    /**
     * Test the success case where authentication succeeds
     */
    public function testExecuteSuccess(): void
    {
        $secret = 'valid_secret';
        $this->requestMock->expects($this->once())->method('getParam')->willReturn($secret);
        $collectionMock = $this->createMock(Collection::class);
        $this->messageManagerMock->expects($this->once())->method('getMessages')->willReturn($collectionMock);
        $this->authenticateCustomerBySecretMock->expects($this->once())->method('execute')->with($secret);
        $this->customerSessionMock->expects($this->once())
            ->method('getCustomer')
            ->willReturn($this->customerMock);
        $this->customerMock->expects($this->once())->method('getFirstname')->willReturn('Test');
        $this->customerMock->expects($this->once())->method('getLastname')->willReturn('Testing');
        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with('You are logged in as customer: Test Testing');
        $this->resultFactoryMock->expects($this->exactly(3))
            ->method('create')
            ->willReturnMap([
                [ResultFactory::TYPE_REDIRECT, [], $this->resultRedirectMock],
                [ResultFactory::TYPE_PAGE, [], $this->resultPageMock],
                [ResultFactory::TYPE_PAGE, [], $this->resultPageMock]
            ]);
        $this->resultPageMock->expects($this->once())->method('getConfig')->willReturn($this->pageConfigMock);
        $this->pageConfigMock->expects($this->once())->method('getTitle')->willReturn($this->pageTitleMock);
        $this->pageTitleMock->expects($this->once())->method('set')->with('You are logged in');
        $this->controller->execute();
    }

    /**
     * Test the failure case where a LocalizedException is thrown
     */
    public function testExecuteLocalizedException(): void
    {
        $secret = 'invalid_secret';
        $this->requestMock->expects($this->once())->method('getParam')->willReturn($secret);
        $this->messageManagerMock->expects($this->never())->method('getMessages');
        $exceptionMessage = 'Invalid secret provided';
        $this->authenticateCustomerBySecretMock->expects($this->once())
            ->method('execute')
            ->with($secret)
            ->willThrowException(new LocalizedException(__($exceptionMessage)));
        $this->messageManagerMock->expects($this->once())->method('addErrorMessage')->with($exceptionMessage);
        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_REDIRECT)
            ->willReturn($this->resultRedirectMock);
        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('/')
            ->willReturnSelf();
        $this->controller->execute();
    }

    /**
     * Test the failure case where a general Exception is thrown
     */
    public function testExecuteGeneralException(): void
    {
        $secret = 'invalid_secret';
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('secret')
            ->willReturn($secret);
        $this->messageManagerMock->expects($this->never())->method('getMessages');
        $exceptionMessage = 'Unexpected error';
        $this->authenticateCustomerBySecretMock->expects($this->once())
            ->method('execute')
            ->with($secret)
            ->willThrowException(new \Exception($exceptionMessage));
        $this->loggerMock->expects($this->once())->method('error')->with($exceptionMessage);
        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with('Cannot login to account.');
        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->with(ResultFactory::TYPE_REDIRECT)
            ->willReturn($this->resultRedirectMock);
        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('/')
            ->willReturnSelf();
        $this->controller->execute();
    }
}
