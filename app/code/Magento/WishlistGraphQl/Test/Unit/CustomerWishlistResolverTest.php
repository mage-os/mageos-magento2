<?php
/**
 * Copyright 2019 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\WishlistGraphQl\Test\Unit;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\GraphQl\Model\Query\Context;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextExtensionInterface;
use Magento\Wishlist\Model\Wishlist;
use Magento\Wishlist\Model\Wishlist\Config;
use Magento\Wishlist\Model\WishlistFactory;
use Magento\WishlistGraphQl\Model\Resolver\CustomerWishlistResolver;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CustomerWishlistResolverTest extends TestCase
{
    use MockCreationTrait;

    private const STUB_CUSTOMER_ID = 1;

    /**
     * @var MockObject|ContextInterface
     */
    private $contextMock;

    /**
     * @var MockObject|ContextExtensionInterface
     */
    private $extensionAttributesMock;

    /**
     * @var MockObject|WishlistFactory
     */
    private $wishlistFactoryMock;

    /**
     * @var MockObject|Wishlist
     */
    private $wishlistMock;

    /**
     * @var CustomerWishlistResolver
     */
    private $resolver;

    /**
     * @var Config|MockObject
     */
    private $wishlistConfigMock;

    /**
     * Build the Testing Environment
     */
    protected function setUp(): void
    {
        $isCustomer = false;

        $this->extensionAttributesMock = $this->createPartialMockWithReflection(
            ContextExtensionInterface::class,
            ['setIsCustomer', 'getIsCustomer', 'getStore', 'setStore', 'getCustomerGroupId', 'setCustomerGroupId']
        );
        
        $this->extensionAttributesMock->method('getStore')->willReturn(null);
        $this->extensionAttributesMock->method('setStore')->willReturnSelf();
        $this->extensionAttributesMock->method('getCustomerGroupId')->willReturn(null);
        $this->extensionAttributesMock->method('setCustomerGroupId')->willReturnSelf();
        
        $this->extensionAttributesMock->method('setIsCustomer')
            ->willReturnCallback(function ($value) use (&$isCustomer) {
                $isCustomer = $value;
                return $this->extensionAttributesMock;
            });
        $this->extensionAttributesMock->method('getIsCustomer')
            ->willReturnCallback(function () use (&$isCustomer) {
                return $isCustomer;
            });

        $this->contextMock = $this->createMock(Context::class);
        $this->contextMock->method('getUserId')->willReturn(self::STUB_CUSTOMER_ID);
        $this->contextMock->method('getUserType')->willReturn(null);
        $this->contextMock->method('getExtensionAttributes')->willReturn($this->extensionAttributesMock);
        $this->wishlistFactoryMock = $this->createPartialMock(WishlistFactory::class, ['create']);
        $this->wishlistMock = $this->createPartialMock(Wishlist::class, ['loadByCustomerId', 'getItemsCount']);
        $reflection = new \ReflectionClass($this->wishlistMock);
        $property = $reflection->getProperty('_data');
        $property->setValue($this->wishlistMock, []);
        $this->wishlistMock->method('getItemsCount')->willReturn(0);
        $this->wishlistConfigMock = $this->createMock(Config::class);

        $this->resolver = new CustomerWishlistResolver(
            $this->wishlistFactoryMock,
            $this->wishlistConfigMock
        );
    }

    /**
     * Verify if Authorization exception is being thrown when User not logged in
     */
    public function testThrowExceptionWhenUserNotAuthorized(): void
    {
        $this->wishlistConfigMock->method('isEnabled')->willReturn(true);

        $this->extensionAttributesMock->setIsCustomer(false);

        $this->expectException(GraphQlAuthorizationException::class);
        $this->wishlistFactoryMock->expects($this->never())
            ->method('create');
        $this->resolver->resolve(
            $this->getFieldStub(),
            $this->contextMock,
            $this->getResolveInfoStub()
        );
    }

    /**
     * Verify if Wishlist instance is created for currently Authorized user
     */
    public function testFactoryCreatesWishlistByAuthorizedCustomerId(): void
    {
        $this->wishlistConfigMock->method('isEnabled')->willReturn(true);

        $this->extensionAttributesMock->setIsCustomer(true);

        $this->wishlistFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->wishlistMock);
        $this->resolver->resolve(
            $this->getFieldStub(),
            $this->contextMock,
            $this->getResolveInfoStub()
        );
    }

    /**
     * Returns stub for Field
     *
     * @return MockObject|Field
     */
    private function getFieldStub(): Field
    {
        /** @var MockObject|Field $fieldMock */
        $fieldMock = $this->createMock(Field::class);

        return $fieldMock;
    }

    /**
     * Returns stub for ResolveInfo
     *
     * @return MockObject|ResolveInfo
     */
    private function getResolveInfoStub(): ResolveInfo
    {
        /** @var MockObject|ResolveInfo $resolveInfoMock */
        $resolveInfoMock = $this->createMock(ResolveInfo::class);

        return $resolveInfoMock;
    }
}
