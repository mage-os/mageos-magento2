<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Test\Unit\Helper;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Url\DecoderInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Wishlist\Helper\Rss;
use Magento\Wishlist\Model\Wishlist;
use Magento\Wishlist\Model\WishlistFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RssTest extends TestCase
{
    /**
     * @var Rss
     */
    protected $model;

    /**
     * @var WishlistFactory|MockObject
     */
    protected $wishlistFactoryMock;

    /**
     * @var RequestInterface|MockObject
     */
    protected $requestMock;

    /**
     * @var DecoderInterface|MockObject
     */
    protected $urlDecoderMock;

    /**
     * @var CustomerInterfaceFactory|MockObject
     */
    protected $customerFactoryMock;

    /**
     * @var Session|MockObject
     */
    protected $customerSessionMock;

    /**
     * @var CustomerRepositoryInterface|MockObject
     */
    protected $customerRepositoryMock;

    /**
     * @var Manager|MockObject
     */
    protected $moduleManagerMock;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    protected $scopeConfigMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->wishlistFactoryMock = $this->createPartialMock(WishlistFactory::class, ['create']);

        $this->requestMock = $this->createMock(RequestInterface::class);

        $this->urlDecoderMock = $this->createMock(DecoderInterface::class);

        $this->customerFactoryMock = $this->createPartialMock(CustomerInterfaceFactory::class, ['create']);

        $this->customerSessionMock = $this->createMock(Session::class);

        $this->customerRepositoryMock = $this->createMock(CustomerRepositoryInterface::class);

        $this->moduleManagerMock = $this->createMock(Manager::class);

        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);

        $contextMock = $this->createMock(\Magento\Framework\App\Helper\Context::class);
        $escaperMock = $this->createMock(\Magento\Framework\Escaper::class);
        
        $contextMock->method('getRequest')->willReturn($this->requestMock);
        $contextMock->method('getUrlDecoder')->willReturn($this->urlDecoderMock);
        $contextMock->method('getModuleManager')->willReturn($this->moduleManagerMock);
        $contextMock->method('getScopeConfig')->willReturn($this->scopeConfigMock);
        
        $objectManagerMock = $this->createMock(\Magento\Framework\App\ObjectManager::class);
        $objectManagerMock->method('get')
            ->willReturnMap([
                [\Magento\Framework\Escaper::class, $escaperMock],
            ]);
        
        \Magento\Framework\App\ObjectManager::setInstance($objectManagerMock);
        
        $objectManager = new ObjectManager($this);
        $wishlistProviderMock = $this->createMock(\Magento\Wishlist\Controller\WishlistProviderInterface::class);
        $productRepositoryMock = $this->createMock(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $coreRegistryMock = $this->createMock(\Magento\Framework\Registry::class);
        $storeManagerMock = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $postDataHelperMock = $this->createMock(\Magento\Framework\Data\Helper\PostHelper::class);
        $customerViewHelperMock = $this->createMock(\Magento\Customer\Helper\View::class);

        $this->model = $objectManager->getObject(
            Rss::class,
            [
                'context' => $contextMock,
                'coreRegistry' => $coreRegistryMock,
                'customerSession' => $this->customerSessionMock,
                'wishlistFactory' => $this->wishlistFactoryMock,
                'storeManager' => $storeManagerMock,
                'postDataHelper' => $postDataHelperMock,
                'customerViewHelper' => $customerViewHelperMock,
                'wishlistProvider' => $wishlistProviderMock,
                'productRepository' => $productRepositoryMock,
                'customerFactory' => $this->customerFactoryMock,
                'customerRepository' => $this->customerRepositoryMock,
                'escaper' => $escaperMock
            ]
        );
    }

    /**
     * @return void
     */
    public function testGetWishlistWithWishlistId(): void
    {
        $wishlistId = 1;

        $wishlist = $this->createMock(Wishlist::class);
        $this->wishlistFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($wishlist);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('wishlist_id', null)
            ->willReturn($wishlistId);

        $wishlist->expects($this->once())
            ->method('load')
            ->with($wishlistId, null)
            ->willReturnSelf();

        $this->assertEquals($wishlist, $this->model->getWishlist());
        // Check that wishlist is cached
        $this->assertSame($wishlist, $this->model->getWishlist());
    }

    /**
     * @return void
     */
    public function testGetWishlistWithCustomerId(): void
    {
        $customerId = 1;
        $data = $customerId . ',2';

        $wishlist = $this->createMock(Wishlist::class);
        $this->wishlistFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($wishlist);

        $this->urlDecoderMock->expects($this->any())
            ->method('decode')
            ->willReturnArgument(0);

        $this->requestMock
            ->method('getParam')
            ->willReturnCallback(function ($arg1, $arg2) use ($data) {
                if ($arg1 == 'wishlist_id' && empty($arg2)) {
                    return '';
                } elseif ($arg1 == 'data' && empty($arg2)) {
                    return $data;
                }
            });

        $this->customerSessionMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn(0);

        $customer = $this->getMockBuilder(CustomerInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->customerFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($customer);

        $this->customerRepositoryMock->expects($this->never())
            ->method('getById');

        $customer->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($customerId);

        $wishlist->expects($this->once())
            ->method('loadByCustomerId')
            ->with($customerId, false)
            ->willReturnSelf();

        $this->assertEquals($wishlist, $this->model->getWishlist());
    }

    /**
     * @return void
     */
    public function testGetCustomerWithSession(): void
    {
        $customerId = 1;
        $data = $customerId . ',2';

        $this->urlDecoderMock->expects($this->any())
            ->method('decode')
            ->willReturnArgument(0);

        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('data', null)
            ->willReturn($data);

        $this->customerSessionMock->expects($this->once())
            ->method('getCustomerId')
            ->willReturn($customerId);

        $customer = $this->getMockBuilder(CustomerInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->customerRepositoryMock->expects($this->once())
            ->method('getById')
            ->with($customerId)
            ->willReturn($customer);

        $this->customerFactoryMock->expects($this->never())
            ->method('create');

        $this->assertEquals($customer, $this->model->getCustomer());
        // Check that customer is cached
        $this->assertSame($customer, $this->model->getCustomer());
    }

    /**
     * @param bool $isModuleEnabled
     * @param bool $isWishlistActive
     * @param bool $result
     *
     * @return void
     * @dataProvider dataProviderIsRssAllow
     */
    public function testIsRssAllow(
        bool $isModuleEnabled,
        bool $isWishlistActive,
        bool $result
    ): void {
        $this->moduleManagerMock->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_Rss')
            ->willReturn($isModuleEnabled);

        $this->scopeConfigMock->expects($this->any())
            ->method('isSetFlag')
            ->with('rss/wishlist/active', ScopeInterface::SCOPE_STORE)
            ->willReturn($isWishlistActive);

        $this->assertEquals($result, $this->model->isRssAllow());
    }

    /**
     * @return array
     */
    public static function dataProviderIsRssAllow(): array
    {
        return [
            [false, false, false],
            [true, false, false],
            [false, true, false],
            [true, true, true]
        ];
    }
}
