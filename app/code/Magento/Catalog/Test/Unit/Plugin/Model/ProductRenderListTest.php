<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Plugin\Model;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Catalog\Model\ProductRenderList as Subject;
use Magento\Catalog\Plugin\Model\ProductRenderList;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Group;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Rest\Request;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Unit test for ProductRenderList plugin
 */
class ProductRenderListTest extends TestCase
{
    /**
     * @var ProductRenderList
     */
    private $plugin;

    /**
     * @var UserContextInterface|MockObject
     */
    private $userContextMock;

    /**
     * @var CustomerRepositoryInterface|MockObject
     */
    private $customerRepositoryMock;

    /**
     * @var CustomerSession|MockObject
     */
    private $customerSessionMock;

    /**
     * @var State|MockObject
     */
    private $appStateMock;

    /**
     * @var LoggerInterface|MockObject
     */
    private $loggerMock;

    /**
     * @var Request|MockObject
     */
    private $requestMock;

    /**
     * @var Subject|MockObject
     */
    private $subjectMock;

    /**
     * @var SearchCriteriaInterface|MockObject
     */
    private $searchCriteriaMock;

    /**
     * Set up test dependencies
     */
    protected function setUp(): void
    {
        $this->userContextMock = $this->createMock(UserContextInterface::class);
        $this->customerRepositoryMock = $this->createMock(CustomerRepositoryInterface::class);
        $this->customerSessionMock = $this->createMock(CustomerSession::class);
        $this->appStateMock = $this->createMock(State::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->requestMock = $this->createMock(Request::class);
        $this->subjectMock = $this->createMock(Subject::class);
        $this->searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);

        $this->plugin = new ProductRenderList(
            $this->userContextMock,
            $this->customerRepositoryMock,
            $this->customerSessionMock,
            $this->appStateMock,
            $this->loggerMock,
            $this->requestMock
        );
    }

    /**
     * Test plugin does nothing for non-API areas
     */
    public function testBeforeGetListSkipsNonApiAreas(): void
    {
        // Setup
        $this->appStateMock->expects($this->atLeastOnce())
            ->method('getAreaCode')
            ->willReturn('frontend');

        $this->customerSessionMock->expects($this->never())
            ->method('setCustomerGroupId');

        // Execute
        $result = $this->plugin->beforeGetList(
            $this->subjectMock,
            $this->searchCriteriaMock,
            1,
            'USD'
        );

        // Assert
        $this->assertEquals(
            [$this->searchCriteriaMock, 1, 'USD'],
            $result
        );
    }

    /**
     * Test plugin sets customer group for authenticated customer in REST API
     */
    public function testBeforeGetListSetsCustomerGroupForAuthenticatedCustomerRest(): void
    {
        // Setup
        $customerId = 123;
        $customerGroupId = 2;

        $customerMock = $this->createMock(CustomerInterface::class);
        $customerMock->expects($this->once())
            ->method('getGroupId')
            ->willReturn($customerGroupId);

        $this->appStateMock->expects($this->once())
            ->method('getAreaCode')
            ->willReturn('webapi_rest');

        $this->userContextMock->expects($this->once())
            ->method('getUserType')
            ->willReturn(UserContextInterface::USER_TYPE_CUSTOMER);

        $this->userContextMock->expects($this->once())
            ->method('getUserId')
            ->willReturn($customerId);

        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customerMock);

        $this->customerSessionMock->expects($this->once())
            ->method('setCustomerGroupId')
            ->with($customerGroupId);

        // Execute
        $result = $this->plugin->beforeGetList(
            $this->subjectMock,
            $this->searchCriteriaMock,
            1,
            'USD'
        );

        // Assert
        $this->assertEquals(
            [$this->searchCriteriaMock, 1, 'USD'],
            $result
        );
    }

    /**
     * Test plugin sets NOT_LOGGED_IN group for guest users
     */
    public function testBeforeGetListSetsNotLoggedInGroupForGuests(): void
    {
        // Setup
        $this->appStateMock->expects($this->once())
            ->method('getAreaCode')
            ->willReturn('webapi_rest');

        $this->userContextMock->expects($this->once())
            ->method('getUserType')
            ->willReturn(UserContextInterface::USER_TYPE_GUEST);

        $this->customerSessionMock->expects($this->once())
            ->method('setCustomerGroupId')
            ->with(Group::NOT_LOGGED_IN_ID);

        // Execute
        $result = $this->plugin->beforeGetList(
            $this->subjectMock,
            $this->searchCriteriaMock,
            1,
            'USD'
        );

        // Assert
        $this->assertEquals(
            [$this->searchCriteriaMock, 1, 'USD'],
            $result
        );
    }

    /**
     * Test plugin handles customer not found exception gracefully
     */
    public function testBeforeGetListHandlesCustomerNotFound(): void
    {
        // Setup
        $customerId = 999;

        $this->appStateMock->expects($this->once())
            ->method('getAreaCode')
            ->willReturn('webapi_rest');

        $this->userContextMock->expects($this->once())
            ->method('getUserType')
            ->willReturn(UserContextInterface::USER_TYPE_CUSTOMER);

        $this->userContextMock->expects($this->once())
            ->method('getUserId')
            ->willReturn($customerId);

        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willThrowException(new NoSuchEntityException(__('Customer not found')));

        $this->loggerMock->expects($this->once())
            ->method('warning')
            ->with($this->stringContains('Customer not found'));

        $this->customerSessionMock->expects($this->once())
            ->method('setCustomerGroupId')
            ->with(Group::NOT_LOGGED_IN_ID);

        // Execute
        $result = $this->plugin->beforeGetList(
            $this->subjectMock,
            $this->searchCriteriaMock,
            1,
            'USD'
        );

        // Assert
        $this->assertEquals(
            [$this->searchCriteriaMock, 1, 'USD'],
            $result
        );
    }

    /**
     * Test plugin handles generic exceptions gracefully
     */
    public function testBeforeGetListHandlesGenericException(): void
    {
        // Setup
        $this->appStateMock->expects($this->once())
            ->method('getAreaCode')
            ->willThrowException(new LocalizedException(__('Generic error')));

        $this->loggerMock->expects($this->once())
            ->method('error')
            ->with(
                $this->stringContains('Error in ProductRenderList plugin'),
                $this->arrayHasKey('exception')
            );

        $this->customerSessionMock->expects($this->never())
            ->method('setCustomerGroupId');

        // Execute
        $result = $this->plugin->beforeGetList(
            $this->subjectMock,
            $this->searchCriteriaMock,
            1,
            'USD'
        );

        // Assert
        $this->assertEquals(
            [$this->searchCriteriaMock, 1, 'USD'],
            $result
        );
    }

    /**
     * Test plugin handles customer with null user ID
     */
    public function testBeforeGetListHandlesNullUserId(): void
    {
        // Setup
        $this->appStateMock->expects($this->once())
            ->method('getAreaCode')
            ->willReturn('webapi_rest');

        $this->userContextMock->expects($this->once())
            ->method('getUserType')
            ->willReturn(UserContextInterface::USER_TYPE_CUSTOMER);

        $this->userContextMock->expects($this->once())
            ->method('getUserId')
            ->willReturn(null);

        $this->customerRepositoryMock->expects($this->never())
            ->method('getById');

        $this->customerSessionMock->expects($this->once())
            ->method('setCustomerGroupId')
            ->with(Group::NOT_LOGGED_IN_ID);

        // Execute
        $result = $this->plugin->beforeGetList(
            $this->subjectMock,
            $this->searchCriteriaMock,
            1,
            'USD'
        );

        // Assert
        $this->assertEquals(
            [$this->searchCriteriaMock, 1, 'USD'],
            $result
        );
    }

    /**
     * Test plugin handles admin user type
     */
    public function testBeforeGetListHandlesAdminUserType(): void
    {
        // Setup
        $this->appStateMock->expects($this->once())
            ->method('getAreaCode')
            ->willReturn('webapi_rest');

        $this->userContextMock->expects($this->once())
            ->method('getUserType')
            ->willReturn(UserContextInterface::USER_TYPE_ADMIN);

        $this->customerRepositoryMock->expects($this->never())
            ->method('getById');

        $this->customerSessionMock->expects($this->once())
            ->method('setCustomerGroupId')
            ->with(Group::NOT_LOGGED_IN_ID);

        // Execute
        $result = $this->plugin->beforeGetList(
            $this->subjectMock,
            $this->searchCriteriaMock,
            1,
            'USD'
        );

        // Assert
        $this->assertEquals(
            [$this->searchCriteriaMock, 1, 'USD'],
            $result
        );
    }
}
