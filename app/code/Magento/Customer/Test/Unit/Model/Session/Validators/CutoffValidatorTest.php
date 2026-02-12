<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Model\Session\Validators;

use Magento\Customer\Model\ResourceModel\Customer as ResourceCustomer;
use Magento\Customer\Model\ResourceModel\Visitor as ResourceVisitor;
use Magento\Customer\Model\Session\Storage as CustomerSessionStorage;
use Magento\Customer\Model\Session\Validators\CutoffValidator;
use Magento\Framework\Exception\SessionException;
use Magento\Framework\Session\SessionManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for CutoffValidator
 */
class CutoffValidatorTest extends TestCase
{
    /**
     * @var ResourceCustomer|MockObject
     */
    private $customerResource;

    /**
     * @var ResourceVisitor|MockObject
     */
    private $visitorResource;

    /**
     * @var \Magento\Framework\Session\Generic|MockObject
     */
    private $visitorSession;

    /**
     * @var CustomerSessionStorage|MockObject
     */
    private $customerSessionStorage;

    /**
     * @var CutoffValidator
     */
    private $validator;

    /**
     * @var SessionManagerInterface|MockObject
     */
    private $session;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->customerResource = $this->createMock(ResourceCustomer::class);
        $this->visitorResource = $this->createMock(ResourceVisitor::class);
        $this->visitorSession = $this->getMockBuilder(\Magento\Framework\Session\Generic::class)
            ->addMethods(['getVisitorData'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->customerSessionStorage = $this->createMock(CustomerSessionStorage::class);
        $this->session = $this->getMockForAbstractClass(SessionManagerInterface::class);

        $this->customerSessionStorage->method('getNamespace')
            ->willReturn('customer');

        $this->validator = new CutoffValidator(
            $this->customerResource,
            $this->visitorResource,
            $this->visitorSession,
            $this->customerSessionStorage
        );
    }

    /**
     * Guest session does not throw even when cutoff > sessionCreationTime.
     *
     * @return void
     */
    public function testValidateDoesNotThrowWhenGuestSession(): void
    {
        $cutoffTime = time();
        $sessionCreationTime = $cutoffTime - 3600;

        $this->visitorSession->method('getVisitorData')
            ->willReturn(['customer_id' => '1', 'visitor_id' => '100']);
        $this->customerResource->method('findSessionCutOff')
            ->with(1)
            ->willReturn($cutoffTime);
        $this->visitorResource->method('fetchCreatedAt')
            ->with(100)
            ->willReturn($sessionCreationTime);

        $this->customerSessionStorage->method('getData')
            ->with('customer_id')
            ->willReturn(null);

        $this->validator->validate($this->session);
        $this->addToAssertionCount(1);
    }

    /**
     * Logged-in session with stale cutoff must throw SessionException.
     *
     * @return void
     */
    public function testValidateThrowsWhenLoggedInSessionAndCutoffAfterCreation(): void
    {
        $cutoffTime = time();
        $sessionCreationTime = $cutoffTime - 3600;

        $this->visitorSession->method('getVisitorData')
            ->willReturn(['customer_id' => '1', 'visitor_id' => '100']);
        $this->customerResource->method('findSessionCutOff')
            ->with(1)
            ->willReturn($cutoffTime);
        $this->visitorResource->method('fetchCreatedAt')
            ->with(100)
            ->willReturn($sessionCreationTime);

        $this->customerSessionStorage->method('getData')
            ->with('customer_id')
            ->willReturn(1);

        $this->expectException(SessionException::class);
        $this->expectExceptionMessage('The session has expired, please login again.');

        $this->validator->validate($this->session);
    }

    /**
     * Visitor has no customer_id validation passes (no cutoff check).
     *
     * @return void
     */
    public function testValidatePassesWhenVisitorHasNoCustomerId(): void
    {
        $this->visitorSession->method('getVisitorData')
            ->willReturn(['visitor_id' => '100']);

        $this->customerResource->expects($this->never())->method('findSessionCutOff');

        $this->validator->validate($this->session);
    }
}
