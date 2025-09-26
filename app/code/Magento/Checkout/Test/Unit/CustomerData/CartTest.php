<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Unit\CustomerData;

use Magento\Catalog\Block\ShortcutButtons;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Url;
use Magento\Checkout\CustomerData\Cart;
use Magento\Checkout\CustomerData\ItemPoolInterface;
use Magento\Checkout\Helper\Data;
use Magento\Checkout\Model\Session;
use Magento\Framework\DataObject;
use Magento\Framework\View\LayoutInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\Option;
use Magento\Store\Model\System\Store;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CartTest extends TestCase
{
    /**
     * @var Cart
     */
    protected $model;

    /**
     * @var MockObject
     */
    protected $checkoutSessionMock;

    /**
     * @var MockObject
     */
    protected $catalogUrlMock;

    /**
     * @var MockObject
     */
    protected $checkoutCartMock;

    /**
     * @var MockObject
     */
    protected $checkoutHelperMock;

    /**
     * @var MockObject
     */
    protected $itemPoolInterfaceMock;

    /**
     * @var MockObject
     */
    protected $layoutMock;

    protected function setUp(): void
    {
        $this->checkoutSessionMock = $this->createMock(Session::class);
        $this->catalogUrlMock = $this->createPartialMock(
            Url::class,
            ['getRewriteByProductStore']
        );
        $this->checkoutCartMock = $this->createMock(\Magento\Checkout\Model\Cart::class);
        $this->checkoutHelperMock = $this->createMock(Data::class);
        $this->layoutMock = $this->createMock(LayoutInterface::class);
        $this->itemPoolInterfaceMock = $this->createMock(ItemPoolInterface::class);

        $this->model = new Cart(
            $this->checkoutSessionMock,
            $this->catalogUrlMock,
            $this->checkoutCartMock,
            $this->checkoutHelperMock,
            $this->itemPoolInterfaceMock,
            $this->layoutMock
        );
    }

    public function testIsGuestCheckoutAllowed()
    {
        $quoteMock = $this->createMock(Quote::class);
        $this->checkoutSessionMock->expects($this->once())->method('getQuote')->willReturn($quoteMock);
        $this->checkoutHelperMock->expects($this->once())->method('isAllowedGuestCheckout')->with($quoteMock)
            ->willReturn(true);

        $this->assertTrue($this->model->isGuestCheckoutAllowed());
    }

    public function testGetSectionData()
    {
        $summaryQty = 100;
        $subtotalValue = 200;
        $productId = 10;
        $storeId = 20;
        $productRewrite = [$productId => ['rewrite' => 'product']];
        $itemData = ['item' => 'data'];
        $shortcutButtonsHtml = '<span>Buttons</span>';
        $websiteId = 100;

        $subtotalMock = new class($subtotalValue) extends DataObject { private $v; public function __construct($v){$this->v=$v;} public function getValue(){return $this->v;} };
        $totals = ['subtotal' => $subtotalMock];

        $quoteMock = new class extends Quote {
            private $totals; private $items; private $store;
            public function __construct() {}
            public function setFixtureTotals($t){ $this->totals = $t; return $this; }
            public function setFixtureItems($i){ $this->items = $i; return $this; }
            public function setFixtureStore($s){ $this->store = $s; return $this; }
            public function getTotals(){ return $this->totals; }
            public function getAllVisibleItems(){ return $this->items; }
            public function getStore(){ return $this->store; }
            public function getHasError(){ return false; }
        };
        $this->checkoutSessionMock->expects($this->exactly(2))->method('getQuote')->willReturn($quoteMock);
        $quoteMock->setFixtureTotals($totals);

        $this->checkoutCartMock->expects($this->once())->method('getSummaryQty')->willReturn($summaryQty);
        $this->checkoutHelperMock->expects($this->once())
            ->method('formatPrice')
            ->with($subtotalValue)
            ->willReturn($subtotalValue);
        $this->checkoutHelperMock->expects($this->once())->method('canOnepageCheckout')->willReturn(true);

        $quoteItemMock = new class($storeId) extends Item {
            private $storeId; private $product;
            public function __construct($storeId){ $this->storeId = $storeId; }
            public function getStoreId(){ return $this->storeId; }
            public function setProduct($p){ $this->product = $p; return $this; }
            public function getProduct(){ return $this->product; }
        };
        $quoteMock->setFixtureItems([$quoteItemMock]);

        $storeMock = new class($websiteId) extends Store { private $id; public function __construct($id){ $this->id=$id; } public function getWebsiteId(){ return $this->id; } };
        $quoteMock->setFixtureStore($storeMock);

        $productMock = new class($productId) extends Product { private $id; private $urlData; public function __construct($id){$this->id=$id;} public function isVisibleInSiteVisibility(){ return false; } public function getId(){ return $this->id; } public function setUrlDataObject($data){ $this->urlData = $data; return $this; } public function getUrlDataObject(){ return $this->urlData; } };
        $quoteItemMock->setProduct($productMock);

        $this->catalogUrlMock->expects($this->once())
            ->method('getRewriteByProductStore')
            ->with([$productId => $storeId])
            ->willReturn($productRewrite);

        $this->itemPoolInterfaceMock->expects($this->once())
            ->method('getItemData')
            ->with($quoteItemMock)
            ->willReturn($itemData);

        $shortcutButtonsMock = $this->createMock(ShortcutButtons::class);
        $this->layoutMock->expects($this->once())
            ->method('createBlock')
            ->with(ShortcutButtons::class)
            ->willReturn($shortcutButtonsMock);

        $shortcutButtonsMock->expects($this->once())->method('toHtml')->willReturn($shortcutButtonsHtml);
        $this->checkoutHelperMock->expects($this->once())
            ->method('isAllowedGuestCheckout')
            ->with($quoteMock)
            ->willReturn(true);

        $expectedResult = [
            'summary_count' => 100,
            'subtotal' => 200,
            'possible_onepage_checkout' => 1,
            'items' => [
                ['item' => 'data']
            ],
            'extra_actions' => '<span>Buttons</span>',
            'isGuestCheckoutAllowed' => 1,
            'website_id' => $websiteId,
            'subtotalAmount' => 200,
            'storeId' => null
        ];
        $this->assertEquals($expectedResult, $this->model->getSectionData());
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testGetSectionDataWithCompositeProduct()
    {
        $summaryQty = 100;
        $subtotalValue = 200;
        $productId = 10;
        $storeId = 20;
        $websiteId = 100;

        $productRewrite = [$productId => ['rewrite' => 'product']];
        $itemData = ['item' => 'data'];
        $shortcutButtonsHtml = '<span>Buttons</span>';
        $subtotalMock = new class($subtotalValue) extends DataObject { private $v; public function __construct($v){$this->v=$v;} public function getValue(){return $this->v;} };
        $totals = ['subtotal' => $subtotalMock];

        $quoteMock = new class extends Quote {
            private $totals; private $items; private $store;
            public function __construct() {}
            public function setFixtureTotals($t){ $this->totals = $t; return $this; }
            public function setFixtureItems($i){ $this->items = $i; return $this; }
            public function setFixtureStore($s){ $this->store = $s; return $this; }
            public function getTotals(){ return $this->totals; }
            public function getAllVisibleItems(){ return $this->items; }
            public function getStore(){ return $this->store; }
            public function getHasError(){ return false; }
        };
        $quoteItemMock = new class($storeId) extends Item {
            private $storeId; private $product; private $options;
            public function __construct($storeId){ $this->storeId = $storeId; }
            public function getStoreId(){ return $this->storeId; }
            public function setProduct($p){ $this->product = $p; return $this; }
            public function getProduct(){ return $this->product; }
            public function setOption($code, $option){ $this->options[$code] = $option; return $this; }
            public function getOptionByCode($code){ return $this->options[$code] ?? null; }
        };

        $this->checkoutSessionMock->expects($this->exactly(2))->method('getQuote')->willReturn($quoteMock);
        $quoteMock->setFixtureTotals($totals);

        $storeMock = new class($websiteId) extends Store { private $id; public function __construct($id){ $this->id=$id; } public function getWebsiteId(){ return $this->id; } };
        $quoteMock->setFixtureStore($storeMock);

        $this->checkoutCartMock->expects($this->once())->method('getSummaryQty')->willReturn($summaryQty);
        $this->checkoutHelperMock->expects($this->once())
            ->method('formatPrice')
            ->with($subtotalValue)
            ->willReturn($subtotalValue);
        $this->checkoutHelperMock->expects($this->once())->method('canOnepageCheckout')->willReturn(true);

        $quoteMock->setFixtureItems([$quoteItemMock]);

        $productMock = new class($productId) extends Product { private $id; private $urlData; public function __construct($id){$this->id=$id;} public function isVisibleInSiteVisibility(){ return false; } public function getId(){ return $this->id; } public function setUrlDataObject($data){ $this->urlData = $data; return $this; } public function getUrlDataObject(){ return $this->urlData; } };

        $optionsMock = $this->createMock(Option::class);
        $optionsMock->expects($this->once())->method('getProduct')->willReturn($productMock);

        $quoteItemMock->setProduct($productMock)->setOption('product_type', $optionsMock);

        $this->catalogUrlMock->expects($this->once())
            ->method('getRewriteByProductStore')
            ->with([$productId => $storeId])
            ->willReturn($productRewrite);

        $shortcutButtonsMock = $this->createMock(ShortcutButtons::class);
        $this->layoutMock->expects($this->once())
            ->method('createBlock')
            ->with(ShortcutButtons::class)
            ->willReturn($shortcutButtonsMock);

        $shortcutButtonsMock->expects($this->once())->method('toHtml')->willReturn($shortcutButtonsHtml);
        $this->checkoutHelperMock->expects($this->once())
            ->method('isAllowedGuestCheckout')
            ->with($quoteMock)
            ->willReturn(true);

        $this->itemPoolInterfaceMock->expects($this->once())
            ->method('getItemData')
            ->with($quoteItemMock)
            ->willReturn($itemData);

        $expectedResult = [
            'summary_count' => 100,
            'subtotal' => 200,
            'possible_onepage_checkout' => 1,
            'items' => [
                ['item' => 'data']
            ],
            'extra_actions' => '<span>Buttons</span>',
            'isGuestCheckoutAllowed' => 1,
            'website_id' => $websiteId,
            'subtotalAmount' => 200,
            'storeId' => null
        ];
        $this->assertEquals($expectedResult, $this->model->getSectionData());
    }
}
