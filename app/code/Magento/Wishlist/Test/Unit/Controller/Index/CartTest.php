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
        $this->wishlistProviderMock = $this->getMockBuilder(WishlistProviderInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getWishlist'])
            ->getMockForAbstractClass();

        $this->quantityProcessorMock = $this->createMock(LocaleQuantityProcessor::class);

        $this->itemFactoryMock = $this->createPartialMock(ItemFactory::class, ['create']); // @phpstan-ignore-line

        $this->checkoutCartMock = new class extends CheckoutCart {
            /**
             * @var Quote
             */
            private $quote;
            public function __construct()
            {
 /* Skip parent */
            }
            public function save()
            {
                return $this;
            }
            public function getQuote()
            {
                return $this->quote;
            }
            public function getShouldRedirectToCart()
            {
                return false;
            }
            public function getCartUrl()
            {
                return 'cart_url';
            }
            public function setQuote($quote)
            {
                $this->quote = $quote;
                $_ = [$quote];
                unset($_);
                return $this;
            }
        };

        $this->optionFactoryMock = $this->createPartialMock(OptionFactory::class, ['create']); // @phpstan-ignore-line

        $this->productHelperMock = $this->createMock(ProductHelper::class);

        $this->escaperMock = $this->createMock(Escaper::class);

        $this->helperMock = $this->createMock(Data::class);

        $this->requestMock = new class implements RequestInterface {
            /**
             * @var array
             */
            public $params = [];
            /**
             * @var array
             */
            public $postValue = [];
            /**
             * @var bool
             */
            public $isAjax = false;
            
            public function getParams()
            {
                return $this->params;
            }
            public function getParam($key, $defaultValue = null)
            {
                $_ = [$key, $defaultValue];
                unset($_);
                return $this->params[$key] ?? $defaultValue;
            }
            public function isAjax()
            {
                return $this->isAjax;
            }
            public function getPostValue($key = null)
            {
                return $key ? ($this->postValue[$key] ?? null) : $this->postValue;
            }
            public function getPost($key = null)
            {
                return $key ? ($this->postValue[$key] ?? null) : $this->postValue;
            }
            public function getQuery($key = null)
            {
                return $key;
            }
            public function isPost()
            {
                return true;
            }
            public function isGet()
            {
                return false;
            }
            public function isPut()
            {
                return false;
            }
            public function isDelete()
            {
                return false;
            }
            public function isHead()
            {
                return false;
            }
            public function isOptions()
            {
                return false;
            }
            public function isPatch()
            {
                return false;
            }
            public function getMethod()
            {
                return 'POST';
            }
            public function getHeader($name)
            {
                return null;
            }
            public function getHeaders()
            {
                return [];
            }
            public function getUri()
            {
                return null;
            }
            public function getRequestUri()
            {
                return '/';
            }
            public function getPathInfo()
            {
                return '/';
            }
            public function getBasePath()
            {
                return '/';
            }
            public function getBaseUrl()
            {
                return 'http://example.com/';
            }
            public function getServer($key = null)
            {
                return $key;
            }
            public function getServerValue($key, $default = null)
            {
                return $default;
            }
            public function getHttpHost($trimPort = true)
            {
                return 'example.com';
            }
            public function getClientIp($checkToProxy = true)
            {
                return '127.0.0.1';
            }
            public function getScriptName()
            {
                return '/index.php';
            }
            public function getRequestString()
            {
                return '';
            }
            public function getFullActionName($delimiter = '_')
            {
                return 'index_index';
            }
            public function isSecure()
            {
                return false;
            }
            public function getHttpReferer()
            {
                return null;
            }
            public function getRequestedRouteName()
            {
                return 'index';
            }
            public function getRequestedControllerName()
            {
                return 'index';
            }
            public function getRequestedActionName()
            {
                return 'index';
            }
            public function getRouteName()
            {
                return 'index';
            }
            public function getControllerName()
            {
                return 'index';
            }
            public function getActionName()
            {
                return 'index';
            }
            public function getModuleName()
            {
                return 'Magento';
            }
            public function setModuleName($name)
            {
                return $this;
            }
            public function setActionName($name)
            {
                return $this;
            }
            public function setParam($key, $value)
            {
                return $this;
            }
            public function setParams(array $params)
            {
                return $this;
            }
            public function getCookie($name, $default = null)
            {
                return $default;
            }
            public function getCookies()
            {
                return [];
            }
            public function isXmlHttpRequest()
            {
                return false;
            }
        };

        $this->redirectMock = $this->getMockBuilder(RedirectInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->objectManagerMock = $this->getMockBuilder(ObjectManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->messageManagerMock = $this->getMockBuilder(ManagerInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['addSuccessMessage'])
            ->getMockForAbstractClass();

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

        $this->cookieManagerMock = $this->getMockForAbstractClass(CookieManagerInterface::class);

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

        $itemMock = new class extends Item {
            public function __construct()
            {
 /* Skip parent */
            }
            public function load($id, $field = null)
            {
                return $this;
            }
            public function getId()
            {
                return null;
            }
        };

        $this->requestMock->params['item'] = $itemId;
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

        $itemMock = new class extends Item {
            public function __construct()
            {
 /* Skip parent */
            }
            public function load($id, $field = null)
            {
                return $this;
            }
            public function getId()
            {
                return 2;
            }
            public function getWishlistId()
            {
                return 1;
            }
        };

        $this->requestMock->params['item'] = $itemId;
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

        $this->formKeyValidator->expects($this->once())
            ->method('validate')
            ->with($this->requestMock)
            ->willReturn(true);

        $this->resultJsonMock->expects($this->once())
            ->method('setData')
            ->with(['backUrl' => $refererUrl])
            ->willReturnSelf();

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
        $indexUrl = 'index_url';
        $configureUrl = 'configure_url';
        $options = [5 => 'option'];
        $params = ['item' => $itemId, 'qty' => $qty];
        $refererUrl = 'referer_url';

        $itemMock = new class extends Item {
            /**
             * @var BuyRequest
             */
            private $buyRequestMock;
            /**
             * @var Product
             */
            private $productMock;
            public function __construct()
            {
 /* Skip parent */
            }
            public function load($id, $field = null)
            {
                return $this;
            }
            public function getId()
            {
                return 2;
            }
            public function setQty($qty)
            {
                return $this;
            }
            public function setOptions($options)
            {
                return $this;
            }
            public function getBuyRequest()
            {
                return $this->buyRequestMock;
            }
            public function mergeBuyRequest($buyRequest)
            {
                return $this;
            }
            public function addToCart($cart, $delete = false)
            {
                return true;
            }
            public function getProduct()
            {
                return $this->productMock;
            }
            public function getWishlistId()
            {
                return 1;
            }
            public function getProductId()
            {
                return 4;
            }
            public function setBuyRequestMock($mock)
            {
                $this->buyRequestMock = $mock;
                return $this;
            }
            public function setProductMock($mock)
            {
                $this->productMock = $mock;
                return $this;
            }
        };

        $this->itemFactoryMock->method('create')->willReturn($itemMock);
        
        $productMock = $this->createMock(\Magento\Catalog\Model\Product::class);
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
        $this->requestMock->params['qty'] = $qty;

        $this->quantityProcessorMock->expects($this->once())
            ->method('process')
            ->with($qty[$itemId])
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
        $this->requestMock->isAjax = $isAjax;

        $this->productHelperMock->expects($this->once())
            ->method('addParamsToBuyRequest')
            ->with($params, ['current_config' => $buyRequestMock])
            ->willReturn($buyRequestMock);

        $quoteMock = new class extends Quote {
            public function __construct()
            {
 /* Skip parent */
            }
            public function collectTotals()
            {
                return $this;
            }
            public function getHasError()
            {
                return false;
            }
        };

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

        $itemMock = new class extends Item {
            /**
             * @var Product
             */
            private $productMock;
            /**
             * @var BuyRequest
             */
            private $buyRequestMock;
            public function __construct()
            {
 /* Skip parent */
            }
            public function load($id, $field = null)
            {
                return $this;
            }
            public function getId()
            {
                return 2;
            }
            public function setQty($qty)
            {
                return $this;
            }
            public function setOptions($options)
            {
                return $this;
            }
            public function getBuyRequest()
            {
                return $this->buyRequestMock;
            }
            public function mergeBuyRequest($buyRequest)
            {
                return $this;
            }
            public function addToCart($cart, $delete = false)
            {
                throw new ProductException(__('Test Phrase'));
            }
            public function getProduct()
            {
                return $this->productMock;
            }
            public function getWishlistId()
            {
                return 1;
            }
            public function getProductId()
            {
                return 4;
            }
            public function setProductMock($mock)
            {
                $this->productMock = $mock;
                return $this;
            }
            public function setBuyRequestMock($mock)
            {
                $this->buyRequestMock = $mock;
                return $this;
            }
        };

        $this->itemFactoryMock->method('create')->willReturn($itemMock);

        $productMock = $this->createMock(\Magento\Catalog\Model\Product::class);
        $buyRequestMock = $this->createMock(DataObject::class);
        $itemMock->setProductMock($productMock);
        $itemMock->setBuyRequestMock($buyRequestMock);

        $wishlistMock = $this->createMock(Wishlist::class);

        $this->wishlistProviderMock->expects($this->once())
            ->method('getWishlist')
            ->with($wishlistId)
            ->willReturn($wishlistMock);

        $this->requestMock->params['item'] = $itemId;
        $this->requestMock->params['qty'] = $qty;

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

        $itemMock = new class extends Item {
            /**
             * @var Product
             */
            private $productMock;
            /**
             * @var BuyRequest
             */
            private $buyRequestMock;
            public function __construct()
            {
 /* Skip parent */
            }
            public function load($id, $field = null)
            {
                return $this;
            }
            public function getId()
            {
                return 2;
            }
            public function setQty($qty)
            {
                return $this;
            }
            public function setOptions($options)
            {
                return $this;
            }
            public function getBuyRequest()
            {
                return $this->buyRequestMock;
            }
            public function mergeBuyRequest($buyRequest)
            {
                return $this;
            }
            public function addToCart($cart, $delete = false)
            {
                throw new LocalizedException(__('message'));
            }
            public function getProduct()
            {
                return $this->productMock;
            }
            public function getWishlistId()
            {
                return 1;
            }
            public function getProductId()
            {
                return 4;
            }
            public function setProductMock($mock)
            {
                $this->productMock = $mock;
                return $this;
            }
            public function setBuyRequestMock($mock)
            {
                $this->buyRequestMock = $mock;
                return $this;
            }
        };

        $this->itemFactoryMock->method('create')->willReturn($itemMock);

        $productMock = $this->createMock(\Magento\Catalog\Model\Product::class);
        $buyRequestMock = $this->createMock(DataObject::class);
        $itemMock->setProductMock($productMock);
        $itemMock->setBuyRequestMock($buyRequestMock);

        $wishlistMock = $this->createMock(Wishlist::class);

        $this->wishlistProviderMock->expects($this->once())
            ->method('getWishlist')
            ->with($wishlistId)
            ->willReturn($wishlistMock);

        $this->requestMock->params['item'] = $itemId;
        $this->requestMock->params['qty'] = $qty;

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

        $itemMock = new class extends Item {
            /**
             * @var Product
             */
            private $productMock;
            /**
             * @var BuyRequest
             */
            private $buyRequestMock;
            public function __construct()
            {
 /* Skip parent */
            }
            public function load($id, $field = null)
            {
                return $this;
            }
            public function getId()
            {
                return 2;
            }
            public function setQty($qty)
            {
                return $this;
            }
            public function setOptions($options)
            {
                return $this;
            }
            public function getBuyRequest()
            {
                return $this->buyRequestMock;
            }
            public function mergeBuyRequest($buyRequest)
            {
                return $this;
            }
            public function addToCart($cart, $delete = false)
            {
                throw new LocalizedException(__('message'));
            }
            public function getProduct()
            {
                return $this->productMock;
            }
            public function getWishlistId()
            {
                return 1;
            }
            public function getProductId()
            {
                return 4;
            }
            public function setProductMock($mock)
            {
                $this->productMock = $mock;
                return $this;
            }
            public function setBuyRequestMock($mock)
            {
                $this->buyRequestMock = $mock;
                return $this;
            }
        };

        $this->itemFactoryMock->method('create')->willReturn($itemMock);

        $productMock = $this->createMock(\Magento\Catalog\Model\Product::class);
        $buyRequestMock = $this->createMock(DataObject::class);
        $itemMock->setProductMock($productMock);
        $itemMock->setBuyRequestMock($buyRequestMock);

        $wishlistMock = $this->createMock(Wishlist::class);

        $this->wishlistProviderMock->expects($this->once())
            ->method('getWishlist')
            ->with($wishlistId)
            ->willReturn($wishlistMock);

        $this->requestMock->params['item'] = $itemId;
        $this->requestMock->params['qty'] = $qty;
        $this->requestMock->postValue['qty'] = $postQty;

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
