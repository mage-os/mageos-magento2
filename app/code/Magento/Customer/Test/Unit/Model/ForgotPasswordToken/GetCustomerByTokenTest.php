<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Model\ForgotPasswordToken;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ForgotPasswordToken\GetCustomerByToken;
use Magento\Customer\Model\ResourceModel\Customer\Collection;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\ExpiredException;
use Magento\Framework\Phrase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GetCustomerByTokenTest extends TestCase
{
    private const RESET_PASSWORD = 'resetPassword';

    /**
     * @var CollectionFactory|MockObject
     */
    private $collectionFactoryMock;

    /**
     * @var Collection|MockObject
     */
    private $collectionMock;

    /**
     * Typed to the interface execute() declares. Note that the real collection yields
     * \Magento\Customer\Model\Customer, which does NOT implement CustomerInterface - see
     * the upstream note in the PR description.
     *
     * @var CustomerInterface|MockObject
     */
    private $customerMock;

    /**
     * @var GetCustomerByToken
     */
    private $model;

    protected function setUp(): void
    {
        $this->collectionFactoryMock = $this->createMock(CollectionFactory::class);
        $this->collectionMock = $this->createMock(Collection::class);
        $this->customerMock = $this->createMock(CustomerInterface::class);

        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->model = $objectManagerHelper->getObject(
            GetCustomerByToken::class,
            [
                'collectionFactory' => $this->collectionFactoryMock
            ]
        );

        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->collectionMock);
        $this->collectionMock->expects($this->once())
            ->method('addAttributeToFilter')
            ->with('rp_token', self::RESET_PASSWORD)
            ->willReturnSelf();
    }

    public function testExecuteReturnWhenOneItemAvailable(): void
    {
        $this->collectionMock->method('getSize')->willReturn(1);
        $this->collectionMock->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($this->customerMock);

        $this->assertSame(
            $this->customerMock,
            $this->model->execute(self::RESET_PASSWORD)
        );
    }

    public function testExecuteWithNoSuchEntityException(): void
    {
        $this->collectionMock->method('getSize')->willReturn(0);
        $this->collectionMock->expects($this->never())->method('getFirstItem');

        $this->expectExceptionObject(new NoSuchEntityException(
            new Phrase(
                'No such entity with rp_token = %value',
                ['value' => self::RESET_PASSWORD]
            )
        ));

        $this->model->execute(self::RESET_PASSWORD);
    }

    public function testExecuteWithExpireException(): void
    {
        $this->collectionMock->method('getSize')->willReturn(2);
        $this->collectionMock->expects($this->never())->method('getFirstItem');

        $this->expectExceptionObject(new ExpiredException(
            new Phrase(
                'Reset password token expired.'
            )
        ));

        $this->model->execute(self::RESET_PASSWORD);
    }
}
