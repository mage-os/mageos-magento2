<?php
/**
 * Copyright 2011 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Weee\Test\Unit\Helper;

use Magento\Bundle\Model\Product\Type;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\Simple;
use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Sales\Model\Order\Item;
use Magento\Store\Model\Store;
use Magento\Tax\Helper\Data;
use Magento\Weee\Helper\Data as WeeeHelper;
use Magento\Weee\Model\Config;
use Magento\Weee\Model\Tax;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class DataTest extends TestCase
{
    private const ROW_AMOUNT_INVOICED = '200';
    private const BASE_ROW_AMOUNT_INVOICED = '400';
    private const TAX_AMOUNT_INVOICED = '20';
    private const BASE_TAX_AMOUNT_INVOICED = '40';
    private const ROW_AMOUNT_REFUNDED = '100';
    private const BASE_ROW_AMOUNT_REFUNDED = '201';
    private const TAX_AMOUNT_REFUNDED = '10';
    private const BASE_TAX_AMOUNT_REFUNDED = '21';

    /**
     * @var Product
     */
    protected $product;

    /**
     * @var Tax
     */
    protected $weeeTax;

    /**
     * @var Data
     */
    protected $taxData;

    /**
     * @var WeeeHelper
     */
    protected $helperData;

    /**
     * @var Json|MockObject
     */
    private $serializerMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->product = $this->createMock(Product::class);
        $weeeConfig = $this->createMock(Config::class);
        $weeeConfig->method('isEnabled')->willReturn(true);
        $weeeConfig->method('getListPriceDisplayType')->willReturn(1);
        $this->weeeTax = $this->createMock(Tax::class);
        $this->weeeTax->method('getWeeeAmount')->willReturn('11.26');
        $this->taxData = $this->createPartialMock(
            Data::class,
            ['getPriceDisplayType', 'priceIncludesTax']
        );

        $this->serializerMock = $this->createMock(Json::class);

        $this->helperData = new WeeeHelper(
            $this->createMock(\Magento\Framework\App\Helper\Context::class),
            $this->createPartialMock(\Magento\Store\Model\StoreManager::class, []),
            $this->weeeTax,
            $weeeConfig,
            $this->taxData,
            $this->createMock(Registry::class),
            $this->serializerMock
        );
    }

    /**
     * @return void
     */
    public function testGetAmount(): void
    {
        $this->product->method('hasData')->willReturn(false);
        $this->product->method('getData')->willReturn(11.26);

        $this->assertEquals('11.26', $this->helperData->getAmountExclTax($this->product));
    }

    /**
     * @return Item|MockObject
     */
    private function setupOrderItem(): Item
    {
        $orderItem = $this->createPartialMock(Item::class, ['__wakeup']);

        $weeeTaxApplied = [
            [
                WeeeHelper::KEY_WEEE_AMOUNT_INVOICED => self::ROW_AMOUNT_INVOICED,
                WeeeHelper::KEY_BASE_WEEE_AMOUNT_INVOICED => self::BASE_ROW_AMOUNT_INVOICED,
                WeeeHelper::KEY_WEEE_TAX_AMOUNT_INVOICED => self::TAX_AMOUNT_INVOICED,
                WeeeHelper::KEY_BASE_WEEE_TAX_AMOUNT_INVOICED => self::BASE_TAX_AMOUNT_INVOICED,
                WeeeHelper::KEY_WEEE_AMOUNT_REFUNDED => self::ROW_AMOUNT_REFUNDED,
                WeeeHelper::KEY_BASE_WEEE_AMOUNT_REFUNDED => self::BASE_ROW_AMOUNT_REFUNDED,
                WeeeHelper::KEY_WEEE_TAX_AMOUNT_REFUNDED => self::TAX_AMOUNT_REFUNDED,
                WeeeHelper::KEY_BASE_WEEE_TAX_AMOUNT_REFUNDED => self::BASE_TAX_AMOUNT_REFUNDED
            ],
            [
                WeeeHelper::KEY_WEEE_AMOUNT_INVOICED => self::ROW_AMOUNT_INVOICED,
                WeeeHelper::KEY_BASE_WEEE_AMOUNT_INVOICED => self::BASE_ROW_AMOUNT_INVOICED,
                WeeeHelper::KEY_WEEE_TAX_AMOUNT_INVOICED => self::TAX_AMOUNT_INVOICED,
                WeeeHelper::KEY_BASE_WEEE_TAX_AMOUNT_INVOICED => self::BASE_TAX_AMOUNT_INVOICED,
                WeeeHelper::KEY_WEEE_AMOUNT_REFUNDED => self::ROW_AMOUNT_REFUNDED,
                WeeeHelper::KEY_BASE_WEEE_AMOUNT_REFUNDED => self::BASE_ROW_AMOUNT_REFUNDED,
                WeeeHelper::KEY_WEEE_TAX_AMOUNT_REFUNDED => self::TAX_AMOUNT_REFUNDED,
                WeeeHelper::KEY_BASE_WEEE_TAX_AMOUNT_REFUNDED => self::BASE_TAX_AMOUNT_REFUNDED
            ],
        ];

        $orderItem->setData(
            'weee_tax_applied',
            json_encode($weeeTaxApplied)
        );

        $this->serializerMock
            ->method('unserialize')
            ->willReturn($weeeTaxApplied);

        return $orderItem;
    }

    /**
     * @return void
     */
    public function testGetWeeeAmountInvoiced(): void
    {
        $orderItem = $this->setupOrderItem();
        $value = $this->helperData->getWeeeAmountInvoiced($orderItem);
        $this->assertEquals(self::ROW_AMOUNT_INVOICED, $value);
    }

    /**
     * @return void
     */
    public function testGetBaseWeeeAmountInvoiced(): void
    {
        $orderItem = $this->setupOrderItem();
        $value = $this->helperData->getBaseWeeeAmountInvoiced($orderItem);
        $this->assertEquals(self::BASE_ROW_AMOUNT_INVOICED, $value);
    }

    /**
     * @return void
     */
    public function testGetWeeeTaxAmountInvoiced(): void
    {
        $orderItem = $this->setupOrderItem();
        $value = $this->helperData->getWeeeTaxAmountInvoiced($orderItem);
        $this->assertEquals(self::TAX_AMOUNT_INVOICED, $value);
    }

    /**
     * @return void
     */
    public function testGetWeeeBaseTaxAmountInvoiced(): void
    {
        $orderItem = $this->setupOrderItem();
        $value = $this->helperData->getBaseWeeeTaxAmountInvoiced($orderItem);
        $this->assertEquals(self::BASE_TAX_AMOUNT_INVOICED, $value);
    }

    /**
     * @return void
     */
    public function testGetWeeeAmountRefunded(): void
    {
        $orderItem = $this->setupOrderItem();
        $value = $this->helperData->getWeeeAmountRefunded($orderItem);
        $this->assertEquals(self::ROW_AMOUNT_REFUNDED, $value);
    }

    /**
     * @return void
     */
    public function testGetBaseWeeeAmountRefunded(): void
    {
        $orderItem = $this->setupOrderItem();
        $value = $this->helperData->getBaseWeeeAmountRefunded($orderItem);
        $this->assertEquals(self::BASE_ROW_AMOUNT_REFUNDED, $value);
    }

    /**
     * @return void
     */
    public function testGetWeeeTaxAmountRefunded(): void
    {
        $orderItem = $this->setupOrderItem();
        $value = $this->helperData->getWeeeTaxAmountRefunded($orderItem);
        $this->assertEquals(self::TAX_AMOUNT_REFUNDED, $value);
    }

    /**
     * @return void
     */
    public function testGetBaseWeeeTaxAmountRefunded(): void
    {
        $orderItem = $this->setupOrderItem();
        $value = $this->helperData->getBaseWeeeTaxAmountRefunded($orderItem);
        $this->assertEquals(self::BASE_TAX_AMOUNT_REFUNDED, $value);
    }

    /**
     * @param int $priceDisplay
     * @param bool $priceIncludesTax
     * @param array $expectedAmount
     *
     * @return void
     * @dataProvider dataProviderGetWeeeAttributesForBundle
     */
    public function testGetWeeeAttributesForBundle(
        int $priceDisplay,
        bool $priceIncludesTax,
        array $expectedAmount
    ): void {
        $prodId1 = 1;
        $prodId2 = 2;
        $fptCode1 = 'fpt' . $prodId1;
        $fptCode2 = 'fpt' . $prodId2;

        $weeeObject1 = new DataObject(
            [
                'code' => $fptCode1,
                'amount' => '15.00',
                'amount_excl_tax' => '15.0000',
                'tax_amount' => '1'
            ]
        );
        $weeeObject2 = new DataObject(
            [
                'code' => $fptCode2,
                'amount' => '10.00',
                'amount_excl_tax' => '10.0000',
                'tax_amount' => '5'
            ]
        );
        $expectedObject1 = new DataObject(
            [
                'code' => $fptCode1,
                'amount' => $expectedAmount[0],
                'amount_excl_tax' => '15.0000',
                'tax_amount' => '1'
            ]
        );
        $expectedObject2 = new DataObject(
            [
                'code' => $fptCode2,
                'amount' => $expectedAmount[1],
                'amount_excl_tax' => '10.0000',
                'tax_amount' => '5'
            ]
        );

        $expectedArray = [$prodId1 => [$fptCode1 => $expectedObject1], $prodId2 => [$fptCode2 => $expectedObject2]];
        $this->weeeTax
            ->method('getProductWeeeAttributes')
            ->willReturn([$weeeObject1, $weeeObject2]);
        $this->taxData
            ->method('getPriceDisplayType')
            ->willReturn($priceDisplay);
        $this->taxData
            ->method('priceIncludesTax')
            ->willReturn($priceIncludesTax);

        $productSimple = $this->createProductSimpleMock($prodId1, $prodId2);

        $productInstance = $this->createMock(Type::class);
        $productInstance
            ->method('getSelectionsCollection')
            ->willReturn([$productSimple]);

        $store=$this->createMock(Store::class);
        /** @var Product $product */
        $product = $this->createPartialMock(
            Product::class,
            ['getTypeInstance', 'getStoreId', 'getStore', 'getTypeId']
        );
        $product
            ->method('getTypeInstance')
            ->willReturn($productInstance);
        $product
            ->method('getStoreId')
            ->willReturn(1);
        $product
            ->method('getStore')
            ->willReturn($store);
        $product
            ->method('getTypeId')
            ->willReturn('bundle');

        $registry = $this->createMock(Registry::class);
        $registry
            ->method('registry')
            ->with('current_product')
            ->willReturn($product);

        $result = $this->helperData->getWeeeAttributesForBundle($product);
        $this->assertEquals($expectedArray, $result);
    }

    /**
     * @return array
     */
    public static function dataProviderGetWeeeAttributesForBundle(): array
    {
        return [
            [2, false, ["16.00", "15.00"]],
            [2, true, ["15.00", "10.00"]],
            [1, false, ["15.00", "10.00"]],
            [1, true, ["15.0000", "10.0000"]],
            [3, false, ["16.00", "15.00"]],
            [3, true, ["15.00", "10.00"]]
        ];
    }

    /**
     * @return void
     */
    public function testGetAppliedSimple(): void
    {
        $testArray = ['key' => 'value'];
        $itemProductSimple = new class extends QuoteItem {
            /**
             * @var mixed
             */
            private $weeeTaxApplied = null;
            /**
             * @var mixed
             */
            private $hasChildren = null;

            public function __construct()
            {
            }

            public function getWeeeTaxApplied()
            {
                return $this->weeeTaxApplied;
            }

            public function setWeeeTaxApplied($value)
            {
                $this->weeeTaxApplied = $value;
                return $this;
            }

            public function getHasChildren()
            {
                return $this->hasChildren;
            }

            public function setHasChildren($value)
            {
                $this->hasChildren = $value;
                return $this;
            }
        };
        $itemProductSimple->setHasChildren(false);
        $itemProductSimple->setWeeeTaxApplied(json_encode($testArray));

        $this->serializerMock
            ->method('unserialize')
            ->willReturn($testArray);

        $this->assertEquals($testArray, $this->helperData->getApplied($itemProductSimple));
    }

    /**
     * @return void
     */
    public function testGetAppliedBundle(): void
    {
        $testArray1 = ['key1' => 'value1'];
        $testArray2 = ['key2' => 'value2'];

        $testArray = array_merge($testArray1, $testArray2);

        $itemProductSimple1 = $this->createQuoteItemMock();
        $itemProductSimple2 = $this->createQuoteItemMock();

        $itemProductSimple1->setWeeeTaxApplied(json_encode($testArray1));
        $itemProductSimple2->setWeeeTaxApplied(json_encode($testArray2));

        $itemProductBundle = $this->createBundleQuoteItemMock();
        $itemProductBundle->setHasChildren(true);
        $itemProductBundle->setChildren([$itemProductSimple1, $itemProductSimple2]);

        $this->serializerMock
            ->method('unserialize')
            ->willReturn($testArray);

        $this->assertEquals($testArray, $this->helperData->getApplied($itemProductBundle));
    }

    /**
     * @return void
     */
    public function testGetRecursiveAmountSimple(): void
    {
        $testAmountUnit = 2;
        $testAmountRow = 34;

        $itemProductSimple = new class extends QuoteItem {
            /**
             * @var mixed
             */
            private $hasChildren = null;
            /**
             * @var mixed
             */
            private $weeeTaxAppliedAmount = null;
            /**
             * @var mixed
             */
            private $weeeTaxAppliedRowAmount = null;

            public function __construct()
            {
            }

            public function getHasChildren()
            {
                return $this->hasChildren;
            }

            public function setHasChildren($value)
            {
                $this->hasChildren = $value;
                return $this;
            }

            public function getWeeeTaxAppliedAmount()
            {
                return $this->weeeTaxAppliedAmount;
            }

            public function setWeeeTaxAppliedAmount($value)
            {
                $this->weeeTaxAppliedAmount = $value;
                return $this;
            }

            public function getWeeeTaxAppliedRowAmount()
            {
                return $this->weeeTaxAppliedRowAmount;
            }

            public function setWeeeTaxAppliedRowAmount($value)
            {
                $this->weeeTaxAppliedRowAmount = $value;
                return $this;
            }
        };
        $itemProductSimple->setHasChildren(false);
        $itemProductSimple->setWeeeTaxAppliedAmount($testAmountUnit);
        $itemProductSimple->setWeeeTaxAppliedRowAmount($testAmountRow);

        $this->assertEquals($testAmountUnit, $this->helperData->getWeeeTaxAppliedAmount($itemProductSimple));
        $this->assertEquals($testAmountRow, $this->helperData->getWeeeTaxAppliedRowAmount($itemProductSimple));
    }

    /**
     * @return void
     */
    public function testGetRecursiveAmountBundle(): void
    {
        $testAmountUnit1 = 1;
        $testAmountUnit2 = 2;
        $testTotalUnit = $testAmountUnit1 + $testAmountUnit2;

        $testAmountRow1 = 33;
        $testAmountRow2 = 444;
        $testTotalRow = $testAmountRow1 + $testAmountRow2;

        $itemProductSimple1 = $this->createQuoteItemWithAmountsMock();
        $itemProductSimple2 = $this->createQuoteItemWithAmountsMock();

        $itemProductSimple1->setWeeeTaxAppliedAmount($testAmountUnit1);
        $itemProductSimple1->setWeeeTaxAppliedRowAmount($testAmountRow1);

        $itemProductSimple2->setWeeeTaxAppliedAmount($testAmountUnit2);
        $itemProductSimple2->setWeeeTaxAppliedRowAmount($testAmountRow2);

        $itemProductBundle = new class extends QuoteItem {
            /**
             * @var mixed
             */
            private $hasChildren = null;
            /**
             * @var mixed
             */
            private $children = null;

            public function __construct()
            {
            }

            public function getHasChildren()
            {
                return $this->hasChildren;
            }

            public function setHasChildren($value)
            {
                $this->hasChildren = $value;
                return $this;
            }

            public function getChildren()
            {
                return $this->children;
            }

            public function setChildren($value)
            {
                $this->children = $value;
                return $this;
            }
        };
        $itemProductBundle->setHasChildren(true);
        $itemProductBundle->setChildren([$itemProductSimple1, $itemProductSimple2]);

        $this->assertEquals($testTotalUnit, $this->helperData->getWeeeTaxAppliedAmount($itemProductBundle));
        $this->assertEquals($testTotalRow, $this->helperData->getWeeeTaxAppliedRowAmount($itemProductBundle));
    }

    /**
     * @return void
     */
    public function testGetProductWeeeAttributesForDisplay(): void
    {
        $store = $this->createMock(Store::class);
        $this->product
            ->method('getStore')
            ->willReturn($store);

        $result = $this->helperData->getProductWeeeAttributesForDisplay($this->product);
        $this->assertNull($result);
    }

    /**
     * @return void
     */
    public function testGetTaxDisplayConfig(): void
    {
        $expected = 1;
        $taxData = $this->createPartialMock(Data::class, ['getPriceDisplayType']);
        $taxData->method('getPriceDisplayType')->willReturn($expected);
        $helperData = new WeeeHelper(
            $this->createMock(\Magento\Framework\App\Helper\Context::class),
            $this->createPartialMock(\Magento\Store\Model\StoreManager::class, []),
            $this->createMock(Tax::class),
            $this->createMock(Config::class),
            $taxData,
            $this->createMock(Registry::class),
            $this->serializerMock
        );

        $this->assertEquals($expected, $helperData->getTaxDisplayConfig());
    }

    /**
     * @return void
     */
    public function testGetTotalAmounts(): void
    {
        $item1Weee = 5;
        $item2Weee = 7;
        $expected = $item1Weee + $item2Weee;
        $itemProductSimple1 = $this->createQuoteItemWithRowAmountMock();
        $itemProductSimple2 = $this->createQuoteItemWithRowAmountMock();
        $items = [$itemProductSimple1, $itemProductSimple2];

        $itemProductSimple1->setWeeeTaxAppliedRowAmount($item1Weee);
        $itemProductSimple2->setWeeeTaxAppliedRowAmount($item2Weee);

        $this->assertEquals($expected, $this->helperData->getTotalAmounts($items));
    }

    /**
     * @return void
     */
    public function testGetBaseTotalAmounts(): void
    {
        $item1BaseWeee = 4;
        $item2BaseWeee = 3;
        $expected = $item1BaseWeee + $item2BaseWeee;
        $itemProductSimple1 = $this->createQuoteItemWithBaseRowAmountMock();
        $itemProductSimple2 = $this->createQuoteItemWithBaseRowAmountMock();
        $items = [$itemProductSimple1, $itemProductSimple2];

        $itemProductSimple1->setBaseWeeeTaxAppliedRowAmnt($item1BaseWeee);
        $itemProductSimple2->setBaseWeeeTaxAppliedRowAmnt($item2BaseWeee);

        $this->assertEquals($expected, $this->helperData->getBaseTotalAmounts($items));
    }

    /**
     * Create a mock for Product Simple with ID tracking
     *
     * @param int $prodId1
     * @param int $prodId2
     * @return Simple
     */
    private function createProductSimpleMock(int $prodId1, int $prodId2): Simple
    {
        return new class($prodId1, $prodId2) extends Simple {
            /**
             * @var array
             */
            private $ids = [];
            /**
             * @var int
             */
            private $callCount = 0;

            public function __construct(int $prodId1, int $prodId2)
            {
                $this->ids = [$prodId1, $prodId2];
            }

            public function getId()
            {
                $id = $this->ids[$this->callCount] ?? null;
                $this->callCount++;
                return $id;
            }

            public function setId($id)
            {
                $this->ids[] = $id;
                return $this;
            }
        };
    }

    /**
     * Create a mock for QuoteItem with weeeTaxApplied
     *
     * @return QuoteItem
     */
    private function createQuoteItemMock(): QuoteItem
    {
        return new class extends QuoteItem {
            /**
             * @var mixed
             */
            private $weeeTaxApplied = null;

            public function __construct()
            {
            }

            public function getWeeeTaxApplied()
            {
                return $this->weeeTaxApplied;
            }

            public function setWeeeTaxApplied($value)
            {
                $this->weeeTaxApplied = $value;
                return $this;
            }
        };
    }

    /**
     * Create a mock for Bundle QuoteItem with children
     *
     * @return QuoteItem
     */
    private function createBundleQuoteItemMock(): QuoteItem
    {
        return new class extends QuoteItem {
            /**
             * @var mixed
             */
            private $hasChildren = null;
            /**
             * @var mixed
             */
            private $children = null;

            public function __construct()
            {
            }

            public function getHasChildren()
            {
                return $this->hasChildren;
            }

            public function setHasChildren($value)
            {
                $this->hasChildren = $value;
                return $this;
            }

            public function getChildren()
            {
                return $this->children;
            }

            public function setChildren($value)
            {
                $this->children = $value;
                return $this;
            }
        };
    }

    /**
     * Create a mock for QuoteItem with weee amounts
     *
     * @return QuoteItem
     */
    private function createQuoteItemWithAmountsMock(): QuoteItem
    {
        return new class extends QuoteItem {
            /**
             * @var mixed
             */
            private $weeeTaxAppliedAmount = null;
            /**
             * @var mixed
             */
            private $weeeTaxAppliedRowAmount = null;

            public function __construct()
            {
            }

            public function getWeeeTaxAppliedAmount()
            {
                return $this->weeeTaxAppliedAmount;
            }

            public function setWeeeTaxAppliedAmount($value)
            {
                $this->weeeTaxAppliedAmount = $value;
                return $this;
            }

            public function getWeeeTaxAppliedRowAmount()
            {
                return $this->weeeTaxAppliedRowAmount;
            }

            public function setWeeeTaxAppliedRowAmount($value)
            {
                $this->weeeTaxAppliedRowAmount = $value;
                return $this;
            }
        };
    }

    /**
     * Create a mock for QuoteItem with row amount
     *
     * @return QuoteItem
     */
    private function createQuoteItemWithRowAmountMock(): QuoteItem
    {
        return new class extends QuoteItem {
            /**
             * @var mixed
             */
            private $weeeTaxAppliedRowAmount = null;

            public function __construct()
            {
            }

            public function getWeeeTaxAppliedRowAmount()
            {
                return $this->weeeTaxAppliedRowAmount;
            }

            public function setWeeeTaxAppliedRowAmount($value)
            {
                $this->weeeTaxAppliedRowAmount = $value;
                return $this;
            }
        };
    }

    /**
     * Create a mock for QuoteItem with base row amount
     *
     * @return QuoteItem
     */
    private function createQuoteItemWithBaseRowAmountMock(): QuoteItem
    {
        return new class extends QuoteItem {
            /**
             * @var mixed
             */
            private $baseWeeeTaxAppliedRowAmnt = null;

            public function __construct()
            {
            }

            public function getBaseWeeeTaxAppliedRowAmnt()
            {
                return $this->baseWeeeTaxAppliedRowAmnt;
            }

            public function setBaseWeeeTaxAppliedRowAmnt($value)
            {
                $this->baseWeeeTaxAppliedRowAmnt = $value;
                return $this;
            }
        };
    }
}
