<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper\Product;

use Magento\Catalog\Helper\Product\Compare;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Compare\Item\Collection;
use Magento\Catalog\Model\ResourceModel\Product\Compare\Item\CollectionFactory;
use Magento\Catalog\Model\Session;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Model\Visitor;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Url;
use Magento\Framework\Url\EncoderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CompareTest extends TestCase
{
    use MockCreationTrait;
    /**
     * @var Compare
     */
    protected $compareHelper;

    /**
     * @var Context|MockObject
     */
    protected $context;

    /**
     * @var Url|MockObject
     */
    protected $urlBuilder;

    /**
     * @var PostHelper|MockObject
     */
    protected $postDataHelper;

    /**
     * @var Http|MockObject
     */
    protected $request;

    /**
     * @var EncoderInterface|MockObject
     */
    protected $urlEncoder;

    /**
     * @var Session|MockObject
     */
    protected $catalogSessionMock;

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->urlBuilder = $this->createPartialMock(Url::class, ['getUrl', 'getCurrentUrl']);
        $this->request = $this->createPartialMock(
            Http::class,
            ['getServer', 'isSecure']
        );
        $this->request->method('getServer')->willReturnMap([
            [null, ['HTTP_HOST' => 'magento.com', 'SERVER_PORT' => 80]],
            ['HTTP_HOST', 'magento.com'],
            ['SERVER_PORT', 80],
        ]);
        $this->request->method('isSecure')->willReturn(false);
        /** @var Context $context */
        $this->context = $this->createPartialMock(
            Context::class,
            ['getUrlBuilder', 'getRequest', 'getUrlEncoder']
        );
        $this->urlEncoder = $this->createMock(EncoderInterface::class);
        $this->urlEncoder->expects($this->any())
            ->method('encode')
            ->willReturnCallback(
                function ($url) {
                    return strtr(base64_encode($url), '+/=', '-_,');
                }
            );
        $this->context->expects($this->once())
            ->method('getUrlBuilder')
            ->willReturn($this->urlBuilder);
        $this->context->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->request);
        $this->context->expects($this->once())
            ->method('getUrlEncoder')
            ->willReturn($this->urlEncoder);
        $this->postDataHelper = $this->createPartialMock(
            PostHelper::class,
            ['getPostData']
        );
        $this->catalogSessionMock = $this->createPartialMockWithReflection(
            Session::class,
            ['getBeforeCompareUrl']
        );

        $this->compareHelper = $objectManager->getObject(
            Compare::class,
            [
                'context' => $this->context,
                'postHelper' => $this->postDataHelper,
                'catalogSession' => $this->catalogSessionMock
            ]
        );
    }

    public function testGetPostDataRemove()
    {
        //Data
        $productId = 1;
        $removeUrl = 'catalog/product_compare/remove';
        $postParams = [
            Action::PARAM_NAME_URL_ENCODED => '',
            'product' => $productId,
            'confirmation' => true,
            'confirmationMessage' => __('Are you sure you want to remove this item from your Compare Products list?'),
        ];

        //Verification
        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with($removeUrl)
            ->willReturn($removeUrl);
        $this->postDataHelper->expects($this->once())
            ->method('getPostData')
            ->with($removeUrl, $postParams)
            ->willReturn(true);

        /** @var Product|MockObject $product */
        $product = $this->createPartialMock(Product::class, ['getId']);
        $product->expects($this->once())
            ->method('getId')
            ->willReturn($productId);

        $this->assertTrue($this->compareHelper->getPostDataRemove($product));
    }

    public function testGetClearListUrl()
    {
        //Data
        $url = 'catalog/product_compare/clear';

        //Verification
        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with($url)
            ->willReturn($url);

        $this->assertEquals($url, $this->compareHelper->getClearListUrl());
    }

    public function testGetPostDataClearList()
    {
        //Data
        $clearUrl = 'catalog/product_compare/clear';
        $postParams = [
            Action::PARAM_NAME_URL_ENCODED => '',
            'confirmation' => true,
            'confirmationMessage' => __('Are you sure you want to remove all items from your Compare Products list?'),
        ];

        //Verification
        $this->urlBuilder->expects($this->once())
            ->method('getUrl')
            ->with($clearUrl)
            ->willReturn($clearUrl);

        $this->postDataHelper->expects($this->once())
            ->method('getPostData')
            ->with($clearUrl, $postParams)
            ->willReturn(true);

        $this->assertTrue($this->compareHelper->getPostDataClearList());
    }

    public function testGetAddToCartUrl()
    {
        $productId = 42;
        $isRequestSecure = false;
        $beforeCompareUrl = 'http://magento.com/compare/before';
        $encodedCompareUrl = strtr(base64_encode($beforeCompareUrl), '+/=', '-_,');
        $expectedResult = [
            'product' => $productId,
            Action::PARAM_NAME_URL_ENCODED => $encodedCompareUrl,
            '_secure' => $isRequestSecure
        ];

        $productMock = $this->createMock(Product::class);
    
        $productMock->expects($this->once())->method('getId')->willReturn($productId);
        $this->catalogSessionMock->expects($this->once())->method('getBeforeCompareUrl')
            ->willReturn($beforeCompareUrl);
        $this->urlEncoder->expects($this->once())->method('encode')->with($beforeCompareUrl)
            ->willReturn($encodedCompareUrl);
        $this->request->expects($this->once())->method('isSecure')->willReturn($isRequestSecure);

        $this->urlBuilder->expects($this->once())->method('getUrl')
            ->with('checkout/cart/add', $expectedResult);
        $this->compareHelper->getAddToCartUrl($productMock);
    }

    public function testGetItemCollectionOrdersByCompareItemIdAscending(): void
    {
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['useProductItem', 'setStoreId', 'setVisitorId', 'setVisibility',
                'addPriceData', 'addAttributeToSelect', 'addUrlRewrite', 'addOrder', 'load'])
            ->getMock();

        $collectionMock->method('useProductItem')->willReturnSelf();
        $collectionMock->method('setStoreId')->willReturnSelf();
        $collectionMock->method('setVisitorId')->willReturnSelf();
        $collectionMock->method('setVisibility')->willReturnSelf();
        $collectionMock->method('addPriceData')->willReturnSelf();
        $collectionMock->method('addAttributeToSelect')->willReturnSelf();
        $collectionMock->method('addUrlRewrite')->willReturnSelf();
        $collectionMock->method('load')->willReturnSelf();

        $collectionMock->expects($this->once())
            ->method('addOrder')
            ->with('catalog_compare_item_id', 'ASC')
            ->willReturnSelf();

        $collectionFactory = $this->createMock(CollectionFactory::class);
        $collectionFactory->method('create')->willReturn($collectionMock);

        $customerSession = $this->createMock(CustomerSession::class);
        $customerSession->method('isLoggedIn')->willReturn(false);

        $visitor = $this->createMock(Visitor::class);
        $visitor->method('getId')->willReturn(1);

        $storeMock = $this->createMock(\Magento\Store\Model\Store::class);
        $storeMock->method('getId')->willReturn(1);
        $storeManagerMock = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $storeManagerMock->method('getStore')->willReturn($storeMock);

        $objectManager = new ObjectManager($this);
        $helper = $objectManager->getObject(
            Compare::class,
            [
                'itemCollectionFactory' => $collectionFactory,
                'customerSession'       => $customerSession,
                'customerVisitor'       => $visitor,
                'storeManager'          => $storeManagerMock,
            ]
        );

        $helper->getItemCollection();
    }
}
