<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Test\Unit\Controller\Index;

use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Exception as ProductException;
use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Checkout\Model\Cart as CheckoutCart;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\DataObject;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Quote\Model\Quote;
use Magento\Wishlist\Controller\Index\Cart;
use Magento\Wishlist\Controller\WishlistProviderInterface;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Model\Item;
use Magento\Wishlist\Model\Item\Option;
use Magento\Wishlist\Model\Item\OptionFactory;
use Magento\Wishlist\Model\ItemFactory;
use Magento\Wishlist\Model\LocaleQuantityProcessor;
use Magento\Wishlist\Model\ResourceModel\Item\Option\Collection;
use Magento\Wishlist\Model\Wishlist;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Checkout\Test\Unit\Helper\CheckoutCartTestHelper;
use Magento\Framework\Test\Unit\Helper\RequestInterfaceTestHelper;
use Magento\Wishlist\Test\Unit\Helper\ItemTestHelper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CartTest extends TestCase
{
    /**
     * @var Cart
     */
    protected $model;

    /**
     * @var Context|MockObject
     */
    protected $contextMock;

    /**
     * @var WishlistProviderInterface|MockObject
     */
    protected $wishlistProviderMock;

    /**
     * @var LocaleQuantityProcessor|MockObject
     */
    protected $quantityProcessorMock;

    /**
     * @var ItemFactory|MockObject
     */
    protected $itemFactoryMock;

    /**
     * @var CheckoutCart|MockObject
     */
    protected $checkoutCartMock;

    /**
     * @var OptionFactory|MockObject
     */
    protected $optionFactoryMock;

    /**
     * @var ProductHelper|MockObject
     */
    protected $productHelperMock;

    /**
     * @var Escaper|MockObject
     */
    protected $escaperMock;

    /**
     * @var Data|MockObject
     */
    protected $helperMock;

    /**
     * @var RequestInterface|MockObject
     */
    protected $requestMock;

    /**
     * @var RedirectInterface|MockObject
     */
    protected $redirectMock;

    /**
     * @var ObjectManagerInterface|MockObject
     */
    protected $objectManagerMock;

    /**
     * @var ManagerInterface|MockObject
     */
    protected $messageManagerMock;

    /**
     * @var UrlInterface|MockObject
     */
    protected $urlMock;

    /**
     * @var CartHelper|MockObject
     */
    protected $cartHelperMock;

    /**
     * @var ResultFactory|MockObject
     */
    protected $resultFactoryMock;

    /**
     * @var Redirect|MockObject
     */
    protected $resultRedirectMock;

    /**
     * @var Json|MockObject
     */
    protected $resultJsonMock;

    /**
     * @var Validator|MockObject
     */
    protected $formKeyValidator;

    /**
     * @var CookieManagerInterface|MockObject
     */
    private $cookieManagerMock;

    /**
     * @var CookieMetadataFactory|MockObject
     */
    private $cookieMetadataFactoryMock;

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function setUp(): void
    {
        $this->wishlistProviderMock = $this->createPartialMock(WishlistProviderInterface::class, ['getWishlist']);

        $this->quantityProcessorMock = $this->createMock(LocaleQuantityProcessor::class);

        $this->itemFactoryMock = $this->createPartialMock(ItemFactory::class, ['create']);

        $this->checkoutCartMock = new CheckoutCartTestHelper();

        $this->optionFactoryMock = $this->createPartialMock(OptionFactory::class, ['create']);

        $this->productHelperMock = $this->createMock(ProductHelper::class);

        $this->escaperMock = $this->createMock(Escaper::class);

        $this->helperMock = $this->createMock(Data::class);

        $this->requestMock = new RequestInterfaceTestHelper();

        $this->redirectMock = $this->createMock(RedirectInterface::class);

        $this->objectManagerMock = $this->createMock(ObjectManagerInterface::class);

        $this->messageManagerMock = $this->createPartialMock(
            \Magento\Framework\Message\Manager::class,
            [
                'addSuccessMessage',
                'addComplexSuccessMessage',
                'addErrorMessage',
                'addNoticeMessage'
            ]
        );

        $this->urlMock = $this->createMock(UrlInterface::class);
        $this->cartHelperMock = $this->createMock(CartHelper::class);
        $this->resultFactoryMock = $this->createMock(ResultFactory::class);
        $this->resultRedirectMock = $this->createMock(Redirect::class);
        $this->resultJsonMock = $this->createMock(Json::class);

        $this->contextMock = $this->createMock(Context::class);
        $this->contextMock->method('getRequest')->willReturn($this->requestMock);
        $this->contextMock->method('getRedirect')->willReturn($this->redirectMock);
        $this->contextMock->method('getObjectManager')->willReturn($this->objectManagerMock);
        $this->contextMock->method('getMessageManager')->willReturn($this->messageManagerMock);
        $this->contextMock->method('getUrl')->willReturn($this->urlMock);
        $this->contextMock->method('getResultFactory')->willReturn($this->resultFactoryMock);
        $this->resultFactoryMock->expects($this->any())
            ->method('create')
            ->willReturnMap(
                [
                    [ResultFactory::TYPE_REDIRECT, [], $this->resultRedirectMock],
                    [ResultFactory::TYPE_JSON, [], $this->resultJsonMock]
                ]
            );

        $this->formKeyValidator = $this->createMock(Validator::class);

        $this->cookieManagerMock = $this->createMock(CookieManagerInterface::class);

        $cookieMetadataMock = $this->createMock(PublicCookieMetadata::class);

        $this->cookieMetadataFactoryMock = $this->createPartialMock(
            CookieMetadataFactory::class,
            ['createPublicCookieMetadata']
        );
        $this->cookieMetadataFactoryMock->method('createPublicCookieMetadata')->willReturn($cookieMetadataMock);
        $cookieMetadataMock->expects($this->any())
            ->method('setDuration')
            ->willReturnSelf();
        $cookieMetadataMock->expects($this->any())
            ->method('setPath')
            ->willReturnSelf();
        $cookieMetadataMock->expects($this->any())
            ->method('setSameSite')
            ->willReturnSelf();
        $cookieMetadataMock->expects($this->any())
            ->method('setHttpOnly')
            ->willReturnSelf();

        $this->model = new Cart(
            $this->contextMock,
            $this->wishlistProviderMock,
            $this->quantityProcessorMock,
            $this->itemFactoryMock,
            $this->checkoutCartMock,
            $this->optionFactoryMock,
            $this->productHelperMock,
            $this->escaperMock,
            $this->helperMock,
            $this->cartHelperMock,
            $this->formKeyValidator,
            $this->cookieManagerMock,
            $this->cookieMetadataFactoryMock
        );
    }

    /**
     * @return void
     */
    public function testExecuteWithInvalidFormKey(): void
    {
        $this->formKeyValidator->expects($this->once())
            ->method('validate')
            ->with($this->requestMock)
            ->willReturn(false);

        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*/')
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->model->execute());
    }

    /**
     * @return void
     */
    public function testExecuteWithNoItem(): void
    {
        $itemId = false;

        $this->formKeyValidator->expects($this->once())
            ->method('validate')
            ->with($this->requestMock)
            ->willReturn(true);

        $itemMock = new ItemTestHelper();

        $this->requestMock->params['item'] = $itemId;
        $this->requestMock->params['qty'] = null;
        $this->itemFactoryMock->method('create')->willReturn($itemMock);
        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*', [])
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->model->execute());
    }

    /**
     * @return void
     */
    public function testExecuteWithNoWishlist(): void
    {
        $itemId = 2;
        $wishlistId = 1;

        $this->formKeyValidator->expects($this->once())
            ->method('validate')
            ->with($this->requestMock)
            ->willReturn(true);

        $itemMock = new ItemTestHelper();

        $this->requestMock->params['item'] = $itemId;
        $this->requestMock->params['qty'] = null;
        $this->itemFactoryMock->method('create')->willReturn($itemMock);

        $this->wishlistProviderMock->expects($this->once())
            ->method('getWishlist')
            ->with($wishlistId)
            ->willReturn(null);
        $this->resultRedirectMock->expects($this->once())
            ->method('setPath')
            ->with('*/*', [])
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->model->execute());
    }

    /**
     * @return void
     */
    public function testExecuteWithQuantityArray(): void
    {
        $refererUrl = $this->prepareExecuteWithQuantityArray();

        $this->formKeyValidator->expects($this->once())
            ->method('validate')
            ->with($this->requestMock)
            ->willReturn(true);

        // Configure cart helper to not redirect to cart
        $this->cartHelperMock->expects($this->once())
            ->method('getShouldRedirectToCart')
            ->willReturn(false);

        // Configure redirect mock to return referer URL
        $this->redirectMock->expects($this->once())
            ->method('getRefererUrl')
            ->willReturn($refererUrl);

        $this->resultRedirectMock->expects($this->once())
            ->method('setUrl')
            ->with($refererUrl)
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->model->execute());
    }

    /**
     * @return void
     */
    public function testExecuteWithQuantityArrayAjax(): void
    {
        $refererUrl = $this->prepareExecuteWithQuantityArray(true);

        // Set AJAX request
        $this->requestMock->isAjax = true;

        $this->formKeyValidator->expects($this->once())
            ->method('validate')
            ->with($this->requestMock)
            ->willReturn(true);

        $this->resultFactoryMock->expects($this->exactly(2))
            ->method('create')
            ->willReturnMap([
                [\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT, $this->resultRedirectMock],
                [\Magento\Framework\Controller\ResultFactory::TYPE_JSON, $this->resultJsonMock]
            ]);

        $this->helperMock->expects($this->once())
            ->method('calculate')
            ->willReturnSelf();

        $this->resultJsonMock->expects($this->once())
            ->method('setData')
            ->with(['backUrl' => $refererUrl])
            ->willReturn($this->resultJsonMock);

        $this->assertSame($this->resultJsonMock, $this->model->execute());
    }

    /**
     * @param bool $isAjax
     *
     * @return string
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function prepareExecuteWithQuantityArray($isAjax = false): string
    {
        $itemId = 2;
        $wishlistId = 1;
        $qty = [$itemId => 3];
        $productId = 4;
        $productName = 'product_name';
        $configureUrl = 'configure_url';
        $options = [5 => 'option'];
        $params = ['item' => $itemId, 'qty' => $qty];
        $refererUrl = 'referer_url';

        $itemMock = new ItemTestHelper();

        $this->itemFactoryMock->method('create')->willReturn($itemMock);

        $productMock = $this->createMock(\Magento\Catalog\Model\Product::class);
        $productMock->method('getId')->willReturn($productId);
        $buyRequestMock = $this->createMock(DataObject::class);
        $itemMock->setProductMock($productMock);
        $itemMock->setBuyRequestMock($buyRequestMock);

        $productMock->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn($productName);

        $wishlistMock = $this->createMock(Wishlist::class);

        $this->wishlistProviderMock->expects($this->once())
            ->method('getWishlist')
            ->with($wishlistId)
            ->willReturn($wishlistMock);

        $this->requestMock->params['item'] = $itemId;
        $this->requestMock->params['qty'] = null;

        $this->quantityProcessorMock->expects($this->once())
            ->method('process')
            ->with($qty[$itemId])
            ->willReturnArgument(0);

        $this->urlMock
            ->method('getUrl')
            ->willReturnCallback(function ($arg1, $arg2) use ($refererUrl, $configureUrl, $itemId, $productId) {
                if ($arg1 == '*/*' && is_null($arg2)) {
                    return $refererUrl; // Return referer URL for successful flow
                } elseif ($arg1 == '*/*/configure/' && $arg2['id'] == $itemId && $arg2['product_id'] == $productId) {
                    return $configureUrl;
                }
            });

        // Don't throw exception for AJAX test - it should work normally
        // Only throw exception for specific configurable product tests
        if ($isAjax) {
            // AJAX test should work without exceptions
        }

        $optionMock = $this->createMock(Option::class);

        $this->optionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($optionMock);

        $optionsMock = $this->createMock(Collection::class);
        $optionMock->expects($this->once())
            ->method('getCollection')
            ->willReturn($optionsMock);

        $optionsMock->expects($this->once())
            ->method('addItemFilter')
            ->with([$itemId])
            ->willReturnSelf();
        $optionsMock->expects($this->once())
            ->method('getOptionsByItem')
            ->with($itemId)
            ->willReturn($options);

        $this->requestMock->params = $params;

        $this->productHelperMock->expects($this->once())
            ->method('addParamsToBuyRequest')
            ->with($params, ['current_config' => $buyRequestMock])
            ->willReturn($buyRequestMock);

        $quoteMock = $this->createMock(\Magento\Quote\Model\Quote::class);
        $quoteMock->method('collectTotals')->willReturnSelf();

        $this->checkoutCartMock->setQuote($quoteMock);

        $wishlistMock->expects($this->once())
            ->method('save')
            ->willReturnSelf();

        $this->messageManagerMock->expects($this->once())
            ->method('addComplexSuccessMessage')
            ->willReturnSelf();

        $this->cartHelperMock->expects($this->once())
            ->method('getShouldRedirectToCart')
            ->willReturn(false);

        $this->redirectMock->expects($this->once())
            ->method('getRefererUrl')
            ->willReturn($refererUrl);

        $this->helperMock->expects($this->once())
            ->method('calculate')
            ->willReturnSelf();

        return $refererUrl;
    }

    /**
     * @return void
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testExecuteWithoutQuantityArrayAndOutOfStock(): void
    {
        $itemId = 2;
        $wishlistId = 1;
        $qty = [];
        $productId = 4;
        $indexUrl = 'index_url';
        $configureUrl = 'configure_url';
        $options = [5 => 'option'];
        $params = ['item' => $itemId, 'qty' => $qty];

        $this->formKeyValidator->expects($this->once())
            ->method('validate')
            ->with($this->requestMock)
            ->willReturn(true);

        $itemMock = new ItemTestHelper();

        $this->itemFactoryMock->method('create')->willReturn($itemMock);

        $productMock = $this->createMock(\Magento\Catalog\Model\Product::class);
        $productMock->method('getId')->willReturn($productId);
        $buyRequestMock = $this->createMock(DataObject::class);
        $itemMock->setProductMock($productMock);
        $itemMock->setBuyRequestMock($buyRequestMock);

        $wishlistMock = $this->createMock(Wishlist::class);

        $this->wishlistProviderMock->expects($this->once())
            ->method('getWishlist')
            ->with($wishlistId)
            ->willReturn($wishlistMock);

        $this->requestMock->params['item'] = $itemId;
        $this->requestMock->params['qty'] = null;

        $this->quantityProcessorMock->expects($this->once())
            ->method('process')
            ->with(1)
            ->willReturnArgument(0);

        $this->urlMock
            ->method('getUrl')
            ->willReturnCallback(function ($arg1, $arg2) use ($indexUrl, $configureUrl, $itemId, $productId) {
                if ($arg1 == '*/*' && is_null($arg2)) {
                    return $indexUrl;
                } elseif ($arg1 == '*/*/configure/' && $arg2['id'] == $itemId && $arg2['product_id'] == $productId) {
                    return $configureUrl;
                }
            });

        $optionMock = $this->createMock(Option::class);

        $this->optionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($optionMock);

        $optionsMock = $this->createMock(Collection::class);
        $optionMock->expects($this->once())
            ->method('getCollection')
            ->willReturn($optionsMock);

        $optionsMock->expects($this->once())
            ->method('addItemFilter')
            ->with([$itemId])
            ->willReturnSelf();
        $optionsMock->expects($this->once())
            ->method('getOptionsByItem')
            ->with($itemId)
            ->willReturn($options);

        $this->requestMock->params = $params;

        $buyRequestMock = $this->createMock(DataObject::class);
        $productMock = $this->createMock(\Magento\Catalog\Model\Product::class);
        $itemMock->setBuyRequestMock($buyRequestMock);
        $itemMock->setProductMock($productMock);

        // Configure product to throw ProductException for out of stock
        $productMock->method('getName')->willThrowException(
            new \Magento\Catalog\Model\Product\Exception(__('This product(s) is out of stock.'))
        );

        $this->productHelperMock->expects($this->once())
            ->method('addParamsToBuyRequest')
            ->with($params, ['current_config' => $buyRequestMock])
            ->willReturn($buyRequestMock);

        // Set up quote for checkout cart
        $quoteMock = $this->createMock(\Magento\Quote\Model\Quote::class);
        $quoteMock->method('collectTotals')->willReturnSelf();
        $this->checkoutCartMock->setQuote($quoteMock);

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with('This product(s) is out of stock.', null)
            ->willReturnSelf();

        $this->helperMock->expects($this->once())
            ->method('calculate')
            ->willReturnSelf();

        $this->resultRedirectMock->expects($this->once())
            ->method('setUrl')
            ->with($indexUrl)
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->model->execute());
    }

    /**
     * @return void
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testExecuteWithoutQuantityArrayAndConfigurable(): void
    {
        $itemId = 2;
        $wishlistId = 1;
        $qty = [];
        $productId = 4;
        $indexUrl = 'index_url';
        $configureUrl = 'configure_url';
        $options = [5 => 'option'];
        $params = ['item' => $itemId, 'qty' => $qty];

        $this->formKeyValidator->expects($this->once())
            ->method('validate')
            ->with($this->requestMock)
            ->willReturn(true);

        $itemMock = new ItemTestHelper();
        $itemMock->throwLocalizedException = true;
        $itemMock->setProductId($productId);

        $this->itemFactoryMock->method('create')->willReturn($itemMock);

        $productMock = $this->createMock(\Magento\Catalog\Model\Product::class);
        $productMock->method('getId')->willReturn($productId);
        $buyRequestMock = $this->createMock(DataObject::class);
        $itemMock->setProductMock($productMock);
        $itemMock->setBuyRequestMock($buyRequestMock);

        $wishlistMock = $this->createMock(Wishlist::class);

        $this->wishlistProviderMock->expects($this->once())
            ->method('getWishlist')
            ->with($wishlistId)
            ->willReturn($wishlistMock);

        $this->requestMock->params['item'] = $itemId;
        $this->requestMock->params['qty'] = null;

        $this->quantityProcessorMock->expects($this->once())
            ->method('process')
            ->with(1)
            ->willReturnArgument(0);

        $this->urlMock
            ->method('getUrl')
            ->willReturnCallback(function ($arg1, $arg2) use ($indexUrl, $configureUrl, $itemId, $productId) {
                if ($arg1 == '*/*' && is_null($arg2)) {
                    return $indexUrl;
                } elseif ($arg1 == '*/*/configure/' && $arg2['id'] == $itemId && $arg2['product_id'] == $productId) {
                    return $configureUrl;
                }
            });

        $optionMock = $this->createMock(Option::class);

        $this->optionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($optionMock);

        $optionsMock = $this->createMock(Collection::class);
        $optionMock->expects($this->once())
            ->method('getCollection')
            ->willReturn($optionsMock);

        $optionsMock->expects($this->once())
            ->method('addItemFilter')
            ->with([$itemId])
            ->willReturnSelf();
        $optionsMock->expects($this->once())
            ->method('getOptionsByItem')
            ->with($itemId)
            ->willReturn($options);

        $this->requestMock->params = $params;

        $buyRequestMock = $this->createMock(DataObject::class);
        $productMock = $this->createMock(\Magento\Catalog\Model\Product::class);
        $itemMock->setBuyRequestMock($buyRequestMock);
        $itemMock->setProductMock($productMock);

        $this->productHelperMock->expects($this->once())
            ->method('addParamsToBuyRequest')
            ->with($params, ['current_config' => $buyRequestMock])
            ->willReturn($buyRequestMock);

        // Set up quote for checkout cart
        $quoteMock = $this->createMock(\Magento\Quote\Model\Quote::class);
        $quoteMock->method('collectTotals')->willReturnSelf();
        $this->checkoutCartMock->setQuote($quoteMock);

        $this->messageManagerMock->expects($this->once())
            ->method('addNoticeMessage')
            ->with('message', null)
            ->willReturnSelf();

        $this->helperMock->expects($this->once())
            ->method('calculate')
            ->willReturnSelf();

        $this->resultRedirectMock->expects($this->once())
            ->method('setUrl')
            ->with($configureUrl)
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->model->execute());
    }

    /**
     * @return void
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testExecuteWithEditQuantity(): void
    {
        $itemId = 2;
        $wishlistId = 1;
        $qty = 1;
        $postQty = 2;
        $productId = 4;
        $indexUrl = 'index_url';
        $configureUrl = 'configure_url';
        $options = [5 => 'option'];
        $params = ['item' => $itemId, 'qty' => $qty];

        $this->formKeyValidator->expects($this->once())
            ->method('validate')
            ->with($this->requestMock)
            ->willReturn(true);

        $itemMock = new ItemTestHelper();
        $itemMock->throwLocalizedException = true;
        $itemMock->setProductId($productId);

        $this->itemFactoryMock->method('create')->willReturn($itemMock);

        $productMock = $this->createMock(\Magento\Catalog\Model\Product::class);
        $productMock->method('getId')->willReturn($productId);
        $buyRequestMock = $this->createMock(DataObject::class);
        $itemMock->setProductMock($productMock);
        $itemMock->setBuyRequestMock($buyRequestMock);

        $wishlistMock = $this->createMock(Wishlist::class);

        $this->wishlistProviderMock->expects($this->once())
            ->method('getWishlist')
            ->with($wishlistId)
            ->willReturn($wishlistMock);

        $this->requestMock->params['item'] = $itemId;
        $this->requestMock->params['qty'] = null;
        $this->requestMock->postValue = ['qty' => $postQty];

        $this->quantityProcessorMock->expects($this->once())
            ->method('process')
            ->with($postQty)
            ->willReturnArgument(0);
        $this->urlMock
            ->method('getUrl')
            ->willReturnCallback(function ($arg1, $arg2) use ($indexUrl, $configureUrl, $itemId, $productId) {
                if ($arg1 == '*/*' && is_null($arg2)) {
                    return $indexUrl;
                } elseif ($arg1 == '*/*/configure/' && $arg2['id'] == $itemId && $arg2['product_id'] == $productId) {
                    return $configureUrl;
                }
            });

        $optionMock = $this->createMock(Option::class);

        $this->optionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($optionMock);

        $optionsMock = $this->createMock(Collection::class);
        $optionMock->expects($this->once())
            ->method('getCollection')
            ->willReturn($optionsMock);

        $optionsMock->expects($this->once())
            ->method('addItemFilter')
            ->with([$itemId])
            ->willReturnSelf();
        $optionsMock->expects($this->once())
            ->method('getOptionsByItem')
            ->with($itemId)
            ->willReturn($options);

        $this->requestMock->params = $params;

        $buyRequestMock = $this->createMock(DataObject::class);
        $productMock = $this->createMock(\Magento\Catalog\Model\Product::class);
        $itemMock->setBuyRequestMock($buyRequestMock);
        $itemMock->setProductMock($productMock);

        $this->productHelperMock->expects($this->once())
            ->method('addParamsToBuyRequest')
            ->with($params, ['current_config' => $buyRequestMock])
            ->willReturn($buyRequestMock);

        // Set up quote for checkout cart
        $quoteMock = $this->createMock(\Magento\Quote\Model\Quote::class);
        $quoteMock->method('collectTotals')->willReturnSelf();
        $this->checkoutCartMock->setQuote($quoteMock);

        $this->messageManagerMock->expects($this->once())
            ->method('addNoticeMessage')
            ->with('message', null)
            ->willReturnSelf();

        $this->helperMock->expects($this->once())
            ->method('calculate')
            ->willReturnSelf();

        $this->resultRedirectMock->expects($this->once())
            ->method('setUrl')
            ->with($configureUrl)
            ->willReturnSelf();

        $this->assertSame($this->resultRedirectMock, $this->model->execute());
    }
}
