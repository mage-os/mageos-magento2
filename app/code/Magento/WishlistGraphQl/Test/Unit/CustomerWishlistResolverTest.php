<?php
/**
 * Copyright 2018 Adobe
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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CustomerWishlistResolverTest extends TestCase
{
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
        $this->extensionAttributesMock = $this->createExtensionAttributesMock();
        $this->contextMock = $this->createMock(Context::class);
        $this->contextMock->method('getUserId')->willReturn(self::STUB_CUSTOMER_ID);
        $this->contextMock->method('getUserType')->willReturn(null);
        $this->contextMock->method('getExtensionAttributes')->willReturn($this->extensionAttributesMock);
        $this->wishlistFactoryMock = $this->createPartialMock(WishlistFactory::class, ['create']);
        $this->wishlistMock = $this->createWishlistMock();
        $this->wishlistConfigMock = $this->createMock(Config::class);

        $this->resolver = new CustomerWishlistResolver(
            $this->wishlistFactoryMock,
            $this->wishlistConfigMock
        );
    }

    /**
     * Create extension attributes mock
     */
    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    private function createExtensionAttributesMock()
    {
        return new class implements \Magento\GraphQl\Model\Query\ContextExtensionInterface {
            /**
             * @var bool
             */
            private $isCustomer = false;
            
            public function setIsCustomer($value)
            {
                $this->isCustomer = $value;
                return $this;
            }
            
            public function getIsCustomer()
            {
                return $this->isCustomer;
            }
        };
    }

    /**
     * Create wishlist mock
     */
    private function createWishlistMock(): Wishlist
    {
        return new class extends Wishlist {
            /**
             * @var int
             */
            private $id = 1;
            
            /**
             * @var int
             */
            private $itemsCount = 0;
            
            /**
             * @var string
             */
            private $sharingCode = 'test-sharing-code';
            
            /**
             * @var string
             */
            private $updatedAt = '2024-01-01 00:00:00';

            public function __construct()
            {
            }

            public function loadByCustomerId($customerId, $create = false)
            {
                return $this;
            }

            public function getId()
            {
                return $this->id;
            }

            public function getItemsCount()
            {
                return $this->itemsCount;
            }

            public function getSharingCode()
            {
                return $this->sharingCode;
            }

            public function getUpdatedAt()
            {
                return $this->updatedAt;
            }
        };
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
