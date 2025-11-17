<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Weee\Test\Unit\Model\Total\Quote;

use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Serialize\Serializer\Json;

use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Api\Data\ShippingInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\Store;
use Magento\Tax\Helper\Data;
use Magento\Tax\Model\Calculation;
use Magento\Weee\Helper\Data as WeeeHelperData;
use Magento\Weee\Model\Total\Quote\Weee;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class WeeeTest extends TestCase
{
    use MockCreationTrait;
    /**
     * @var MockObject|PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var Weee
     */
    protected $weeeCollector;

    /**
     * @var Json
     */
    private $serializerMock;

    /**
     * Setup tax helper with an array of methodName, returnValue.
     *
     * @param array $taxConfig
     *
     * @return MockObject|Data
     */
    protected function setupTaxHelper(array $taxConfig): Data
    {
        $taxHelper = $this->createMock(Data::class);

        foreach ($taxConfig as $method => $value) {
            $taxHelper->expects($this->any())->method($method)->willReturn($value);
        }

        return $taxHelper;
    }

    /**
     * Setup calculator to return tax rates.
     *
     * @param array $taxRates
     *
     * @return MockObject|Calculation
     */
    protected function setupTaxCalculation(array $taxRates): Calculation
    {
        $storeTaxRate = $taxRates['store_tax_rate'];
        $customerTaxRate = $taxRates['customer_tax_rate'];

        $taxCalculation = $this->createPartialMock(
            Calculation::class,
            ['getRateOriginRequest', 'getRateRequest', 'getRate']
        );

        $rateRequest = new DataObject();
        $defaultRateRequest = new DataObject();

        $taxCalculation->expects($this->any())->method('getRateRequest')->willReturn($rateRequest);
        $taxCalculation
            ->expects($this->any())
            ->method('getRateOriginRequest')
            ->willReturn($defaultRateRequest);

        $callCount = 0;
        $taxCalculation
            ->expects($this->any())
            ->method('getRate')
            ->willReturnCallback(
                function () use (&$callCount, $storeTaxRate, $customerTaxRate) {
                    $callCount++;
                    return $callCount === 1 ? $storeTaxRate : $customerTaxRate;
                }
            );

        return $taxCalculation;
    }

    /**
     * Setup weee helper with an array of methodName, returnValue.
     *
     * @param  array $weeeConfig
     * @return MockObject|WeeeHelperData
     */
    protected function setupWeeeHelper($weeeConfig): WeeeHelperData
    {
        $this->serializerMock = $this->getMockBuilder(Json::class)
            ->getMock();

        $weeeHelper = $this->getMockBuilder(WeeeHelperData::class)
            ->setConstructorArgs(['serializer' => $this->serializerMock])
            ->disableOriginalConstructor()
            ->getMock();

        foreach ($weeeConfig as $method => $value) {
            $weeeHelper->expects($this->any())->method($method)->willReturn($value);
        }

        return $weeeHelper;
    }

    /**
     * Create item mock with all required methods
     *
     * @return Item|MockObject
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function createItemMock()
    {
        $methods = [
            'getProduct', 'setProduct', 'getTotalQty', 'setTotalQty',
            'getParentItem', 'setParentItem', 'getHasChildren', 'setHasChildren',
            'getChildren', 'setChildren', 'setIsChildrenCalculated',
            'getAssociatedTaxables', 'setAssociatedTaxables',
            'setWeeeTaxAppliedAmount', 'setBaseWeeeTaxAppliedAmount',
            'setWeeeTaxAppliedRowAmount', 'setBaseWeeeTaxAppliedRowAmnt',
            'setWeeeTaxAppliedAmountInclTax', 'setBaseWeeeTaxAppliedAmountInclTax',
            'setWeeeTaxAppliedRowAmountInclTax', 'setBaseWeeeTaxAppliedRowAmntInclTax',
            'getData', 'setData', 'getStoreId', 'setStoreId'
        ];
        
        $itemMock = $this->createPartialMockWithReflection(Item::class, $methods);
        
        // Configure data storage
        $data = [];
        $itemMock->method('setData')->willReturnCallback(function ($key, $value = null) use (&$data, $itemMock) {
            if (is_array($key)) {
                $data = array_merge($data, $key);
            } else {
                $data[$key] = $value;
            }
            return $itemMock;
        });
        $itemMock->method('getData')->willReturnCallback(function ($key = '', $index = null) use (&$data) {
            if ($key === '') {
                return $data;
            }
            $value = $data[$key] ?? null;
            if ($index !== null && is_array($value)) {
                return $value[$index] ?? null;
            }
            return $value;
        });
        
        // Configure chainable setters
        $itemMock->method('setWeeeTaxAppliedAmount')->willReturnCallback(function ($val) use (&$data, $itemMock) {
            $data['weee_tax_applied_amount'] = $val;
            return $itemMock;
        });
        $itemMock->method('setBaseWeeeTaxAppliedAmount')->willReturnCallback(function ($val) use (&$data, $itemMock) {
            $data['base_weee_tax_applied_amount'] = $val;
            return $itemMock;
        });
        $itemMock->method('setWeeeTaxAppliedRowAmount')->willReturnCallback(function ($val) use (&$data, $itemMock) {
            $data['weee_tax_applied_row_amount'] = $val;
            return $itemMock;
        });
        $itemMock->method('setBaseWeeeTaxAppliedRowAmnt')->willReturnCallback(function ($val) use (&$data, $itemMock) {
            $data['base_weee_tax_applied_row_amnt'] = $val;
            return $itemMock;
        });
        $itemMock->method('setWeeeTaxAppliedAmountInclTax')->willReturnCallback(
            function ($val) use (&$data, $itemMock) {
                $data['weee_tax_applied_amount_incl_tax'] = $val;
                return $itemMock;
            }
        );
        $itemMock->method('setBaseWeeeTaxAppliedAmountInclTax')->willReturnCallback(
            function ($val) use (&$data, $itemMock) {
                $data['base_weee_tax_applied_amount_incl_tax'] = $val;
                return $itemMock;
            }
        );
        $itemMock->method('setWeeeTaxAppliedRowAmountInclTax')->willReturnCallback(
            function ($val) use (&$data, $itemMock) {
                $data['weee_tax_applied_row_amount_incl_tax'] = $val;
                return $itemMock;
            }
        );
        $itemMock->method('setBaseWeeeTaxAppliedRowAmntInclTax')->willReturnCallback(
            function ($val) use (&$data, $itemMock) {
                $data['base_weee_tax_applied_row_amnt_incl_tax'] = $val;
                return $itemMock;
            }
        );
        
        return $itemMock;
    }

    /**
     * Setup the basics of an item mock.
     *
     * @param float $itemTotalQty
     *
     * @return MockObject|Item
     */
    protected function setupItemMockBasics($itemTotalQty): Item
    {
        $itemMock = $this->createItemMock();

        // Create product mock with proper extension attributes configuration
        $productMock = $this->createPartialMockWithReflection(
            Product::class,
            [
                'getSku', 'getWeight', 'getName', 'getTaxClassId',
                'getCost', 'getId', 'getTypeId', 'getExtensionAttributes'
            ]
        );
        $productMock->method('getSku')->willReturn('test-sku');
        $productMock->method('getWeight')->willReturn(1.0);
        $productMock->method('getName')->willReturn('Test Product');
        $productMock->method('getTaxClassId')->willReturn(1);
        $productMock->method('getCost')->willReturn(10.0);
        $productMock->method('getId')->willReturn(1);
        $productMock->method('getTypeId')->willReturn('simple');
        
        $stockItem = $this->createMock(\Magento\CatalogInventory\Api\Data\StockItemInterface::class);
        $stockItem->method('getIsQtyDecimal')->willReturn(false);
        
        $extensionAttributes = $this->createPartialMockWithReflection(
            \Magento\Catalog\Api\Data\ProductExtensionInterface::class,
            [
                'getStockItem', 'setStockItem',
                'getWebsiteIds', 'setWebsiteIds',
                'getCategoryLinks', 'setCategoryLinks',
                'getConfigurableProductOptions', 'setConfigurableProductOptions',
                'getConfigurableProductLinks', 'setConfigurableProductLinks',
                'getBundleProductOptions', 'setBundleProductOptions',
                'getDownloadableProductLinks', 'setDownloadableProductLinks',
                'getDownloadableProductSamples', 'setDownloadableProductSamples',
                'getGiftcardAmounts', 'setGiftcardAmounts',
                'getDiscounts', 'setDiscounts'
            ]
        );
        
        $extensionAttributes->method('getWebsiteIds')->willReturn(null);
        $extensionAttributes->method('setWebsiteIds')->willReturnSelf();
        $extensionAttributes->method('getCategoryLinks')->willReturn(null);
        $extensionAttributes->method('setCategoryLinks')->willReturnSelf();
        $extensionAttributes->method('getConfigurableProductOptions')->willReturn(null);
        $extensionAttributes->method('setConfigurableProductOptions')->willReturnSelf();
        $extensionAttributes->method('getConfigurableProductLinks')->willReturn(null);
        $extensionAttributes->method('setConfigurableProductLinks')->willReturnSelf();
        $extensionAttributes->method('getBundleProductOptions')->willReturn(null);
        $extensionAttributes->method('setBundleProductOptions')->willReturnSelf();
        $extensionAttributes->method('getDownloadableProductLinks')->willReturn(null);
        $extensionAttributes->method('setDownloadableProductLinks')->willReturnSelf();
        $extensionAttributes->method('getDownloadableProductSamples')->willReturn(null);
        $extensionAttributes->method('setDownloadableProductSamples')->willReturnSelf();
        $extensionAttributes->method('getGiftcardAmounts')->willReturn(null);
        $extensionAttributes->method('setGiftcardAmounts')->willReturnSelf();
        $extensionAttributes->method('getDiscounts')->willReturn(null);
        $extensionAttributes->method('setDiscounts')->willReturnSelf();
        $extensionAttributes->method('setStockItem')->willReturnSelf();
        
        $extensionAttributes->method('getStockItem')->willReturn($stockItem);
        
        $productMock->method('getExtensionAttributes')->willReturn($extensionAttributes);
        
        // Configure product and quantity on item
        $product = $productMock;
        $totalQty = $itemTotalQty;
        $itemMock->method('getProduct')->willReturnCallback(function () use (&$product) {
            return $product;
        });
        $itemMock->method('setProduct')->willReturnCallback(function ($p) use (&$product, $itemMock) {
            $product = $p;
            return $itemMock;
        });
        $itemMock->method('getTotalQty')->willReturnCallback(function () use (&$totalQty) {
            return $totalQty;
        });
        $itemMock->method('setTotalQty')->willReturnCallback(function ($qty) use (&$totalQty, $itemMock) {
            $totalQty = $qty;
            return $itemMock;
        });
        $itemMock->method('getStoreId')->willReturn(1);
        $itemMock->method('setStoreId')->willReturnSelf();

        return $itemMock;
    }

    /**
     * Setup an item mock.
     *
     * @param float $itemQty
     *
     * @return MockObject|Item
     */
    protected function setupItemMock(float $itemQty): Item
    {
        $itemMock = $this->setupItemMockBasics($itemQty);

        // Configure parent, children, and associated taxables
        $parentItem = false;
        $hasChildren = false;
        $children = [];
        $associatedTaxables = null;
        
        $itemMock->method('getParentItem')->willReturnCallback(function () use (&$parentItem) {
            return $parentItem;
        });
        $itemMock->method('setParentItem')->willReturnCallback(function ($p) use (&$parentItem, $itemMock) {
            $parentItem = $p;
            return $itemMock;
        });
        $itemMock->method('getHasChildren')->willReturnCallback(function () use (&$hasChildren) {
            return $hasChildren;
        });
        $itemMock->method('setHasChildren')->willReturnCallback(function ($h) use (&$hasChildren, $itemMock) {
            $hasChildren = $h;
            return $itemMock;
        });
        $itemMock->method('getChildren')->willReturnCallback(function () use (&$children) {
            return $children;
        });
        $itemMock->method('setChildren')->willReturnCallback(function ($c) use (&$children, $itemMock) {
            $children = $c;
            return $itemMock;
        });
        $itemMock->method('setIsChildrenCalculated')->willReturnSelf();
        $itemMock->method('getAssociatedTaxables')->willReturnCallback(function () use (&$associatedTaxables) {
            return $associatedTaxables;
        });
        $itemMock->method('setAssociatedTaxables')->willReturnCallback(
            function ($t) use (&$associatedTaxables, $itemMock) {
                $associatedTaxables = $t;
                return $itemMock;
            }
        );

        return $itemMock;
    }

    /**
     * Setup an item mock as a parent of a child item mock.  Return both.
     *
     * @param float $parentQty
     * @param float $itemQty
     *
     * @return MockObject[]|Item[]
     */
    protected function setupParentItemWithChildrenMock($parentQty, $itemQty): array
    {
        $items = [];

        // Create parent and child using the base setup
        $parentItemMock = $this->setupItemMockBasics($parentQty);
        $childItemMock = $this->setupItemMockBasics($parentQty * $itemQty);
        
        // Set up parent-child relationship and other required methods
        $parentItem = false;
        $hasChildrenForParent = true;
        $childrenForParent = [$childItemMock];
        $associatedTaxablesForParent = null;
        
        $parentItemMock->method('getParentItem')->willReturnCallback(function () use (&$parentItem) {
            return $parentItem;
        });
        $parentItemMock->method('getHasChildren')->willReturnCallback(function () use (&$hasChildrenForParent) {
            return $hasChildrenForParent;
        });
        $parentItemMock->method('getChildren')->willReturnCallback(function () use (&$childrenForParent) {
            return $childrenForParent;
        });
        $parentItemMock->method('getAssociatedTaxables')->willReturnCallback(
            function () use (&$associatedTaxablesForParent) {
                return $associatedTaxablesForParent;
            }
        );
        $parentItemMock->method('setAssociatedTaxables')->willReturnCallback(
            function ($t) use (&$associatedTaxablesForParent, $parentItemMock) {
                $associatedTaxablesForParent = $t;
                return $parentItemMock;
            }
        );
        $parentItemMock->method('setParentItem')->willReturnSelf();
        $parentItemMock->method('setHasChildren')->willReturnSelf();
        $parentItemMock->method('setChildren')->willReturnSelf();
        $parentItemMock->method('setIsChildrenCalculated')->willReturnSelf();
        
        // For child item
        $parentItemForChild = $parentItemMock;
        $hasChildrenForChild = false;
        $childrenForChild = [];
        $associatedTaxablesForChild = null;
        
        $childItemMock->method('getParentItem')->willReturnCallback(function () use (&$parentItemForChild) {
            return $parentItemForChild;
        });
        $childItemMock->method('getHasChildren')->willReturnCallback(function () use (&$hasChildrenForChild) {
            return $hasChildrenForChild;
        });
        $childItemMock->method('getChildren')->willReturnCallback(function () use (&$childrenForChild) {
            return $childrenForChild;
        });
        $childItemMock->method('getAssociatedTaxables')->willReturnCallback(
            function () use (&$associatedTaxablesForChild) {
                return $associatedTaxablesForChild;
            }
        );
        $childItemMock->method('setAssociatedTaxables')->willReturnCallback(
            function ($t) use (&$associatedTaxablesForChild, $childItemMock) {
                $associatedTaxablesForChild = $t;
                return $childItemMock;
            }
        );
        $childItemMock->method('setParentItem')->willReturnSelf();
        $childItemMock->method('setHasChildren')->willReturnSelf();
        $childItemMock->method('setChildren')->willReturnSelf();
        $childItemMock->method('setIsChildrenCalculated')->willReturnSelf();

        $items[] = $parentItemMock;
        $items[] = $childItemMock;

        return $items;
    }

    /**
     * Setup address mock.
     *
     * @param Item[]|MockObject[] $items
     *
     * @return MockObject
     */
    protected function setupAddressMock(array $items): MockObject
    {
        $addressMock = $this->createPartialMock(
            Address::class,
            [
            'getAllItems',
            'getQuote',
            'getCustomAttributesCodes'
            ]
        );

        $quoteMock = $this->createMock(Quote::class);
        $storeMock = $this->createMock(Store::class);
        $this->priceCurrency = $this->getMockBuilder(
            PriceCurrencyInterface::class
        )->getMock();
        $this->priceCurrency->expects($this->any())->method('round')->willReturnArgument(0);
        $this->priceCurrency->expects($this->any())->method('convert')->willReturnArgument(0);
        $quoteMock->expects($this->any())->method('getStore')->willReturn($storeMock);

        $addressMock->expects($this->any())->method('getAllItems')->willReturn($items);
        $addressMock->expects($this->any())->method('getQuote')->willReturn($quoteMock);
        $addressMock->expects($this->any())->method('getCustomAttributesCodes')->willReturn([]);

        return $addressMock;
    }

    /**
     * Setup shipping assignment mock.
     *
     * @param MockObject $addressMock
     * @param MockObject $itemMock
     *
     * @return MockObject
     */
    protected function setupShippingAssignmentMock($addressMock, $itemMock): MockObject
    {
        $shippingMock = $this->createMock(ShippingInterface::class);
        $shippingMock->expects($this->any())->method('getAddress')->willReturn($addressMock);
        $shippingAssignmentMock = $this->createMock(ShippingAssignmentInterface::class);
        $shippingAssignmentMock->expects($this->any())->method('getItems')->willReturn($itemMock);
        $shippingAssignmentMock->expects($this->any())->method('getShipping')->willReturn($shippingMock);

        return $shippingAssignmentMock;
    }

    /**
     * Verify that correct fields of item has been set.
     *
     * @param MockObject|Item $item
     * @param $itemData
     *
     * @return void
     */
    public function verifyItem(Item $item, $itemData): void
    {
        foreach ($itemData as $key => $value) {
            $this->assertEquals($value, $item->getData($key), 'item ' . $key . ' is incorrect');
        }
    }

    /**
     * Verify that correct fields of address has been set
     *
     * @param MockObject|Address $address
     * @param $addressData
     *
     * @return void
     */
    public function verifyAddress($address, $addressData): void
    {
        foreach ($addressData as $key => $value) {
            $this->assertEquals($value, $address->getData($key), 'address ' . $key . ' is incorrect');
        }
    }

    /**
     * Test the collect function of the weee collector.
     *
     * @param array $taxConfig
     * @param array $weeeConfig
     * @param array $taxRates
     * @param array $itemData
     * @param float $itemQty
     * @param float $parentQty
     * @param array $addressData
     * @param bool  $assertSetApplied
     *
     * @return void
     */
    #[DataProvider('collectDataProvider')]
    public function testCollect(
        $taxConfig,
        $weeeConfig,
        $taxRates,
        $itemData,
        $itemQty,
        $parentQty,
        $addressData,
        $assertSetApplied = false
    ): void {
        $items = [];
        $shippingItems = [];

        if ($parentQty > 0) {
            $items = $this->setupParentItemWithChildrenMock($parentQty, $itemQty);
            // For shipping assignment, only pass the parent item (collector discovers children via getChildren())
            $shippingItems = [$items[0]];  // Only parent
        } else {
            $itemMock = $this->setupItemMock($itemQty);
            $items[] = $itemMock;
            $shippingItems = $items;
        }
        $quoteMock = $this->createMock(Quote::class);
        $storeMock = $this->createMock(Store::class);
        $quoteMock->expects($this->any())->method('getStore')->willReturn($storeMock);
        $addressMock = $this->setupAddressMock($shippingItems);
        $totalMock = new Total(
            [],
            $this->getMockBuilder(Json::class)
                ->getMock()
        );
        $shippingAssignmentMock = $this->setupShippingAssignmentMock($addressMock, $shippingItems);

        $taxHelper = $this->setupTaxHelper($taxConfig);
        $weeeHelper = $this->setupWeeeHelper($weeeConfig);
        $calculator = $this->setupTaxCalculation($taxRates);

        if ($assertSetApplied) {
            $weeeHelper
                ->method('setApplied')
                ->willReturnCallback(
                    function ($arg1, $arg2) use ($items) {
                        if ($arg1 === reset($items) && empty($arg2)) {
                            return null;
                        } elseif ($arg1 === end($items) && empty($arg2)) {
                            return null;
                        } elseif ($arg1 === end($items) && is_array($arg2)) {
                            return null;
                        }
                    }
                );
        }

        $arguments = [
            'taxData' => $taxHelper,
            'calculation' => $calculator,
            'weeeData' => $weeeHelper,
            'priceCurrency' => $this->priceCurrency
        ];

        $helper = new ObjectManager($this);
        $this->weeeCollector = $helper->getObject(Weee::class, $arguments);

        $this->weeeCollector->collect($quoteMock, $shippingAssignmentMock, $totalMock);

        $this->verifyItem(end($items), $itemData);          // verify the (child) item
        $this->verifyAddress($totalMock, $addressData);
    }

    /**
     * Data provider for testCollect
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * Multiple datasets
     *
     * @return array
     */
    public static function collectDataProvider(): array
    {
        $data = [];

        // 1. This collector never computes tax.  Instead it sets up various fields for the tax calculation.
        // 2. When the Weee is not taxable, this collector will change the address data as follows:
        //     accumulate the totals into 'weee_total_excl_tax' and 'weee_base_total_excl_tax'

        $data['price_incl_tax_weee_taxable_unit_included_in_subtotal'] = [
            'taxConfig' => [
                'priceIncludesTax' => true,
                'getCalculationAlgorithm' => Calculation::CALC_UNIT_BASE
            ],
            'weeeConfig' => [
                'isEnabled' => true,
                'includeInSubtotal' => true,
                'isTaxable' => true,
                'getApplied' => [],
                'getProductWeeeAttributes' => [
                    new DataObject(
                        [
                            'name' => 'Recycling Fee',
                            'amount' => 10
                        ]
                    )
                ]
            ],
            'taxRates' => [
                'store_tax_rate' => 8.25,
                'customer_tax_rate' => 8.25
            ],
            'itemData' => [
                'weee_tax_applied_amount' => 10,
                'base_weee_tax_applied_amount' => 10,
                'weee_tax_applied_row_amount' => 20,
                'base_weee_tax_applied_row_amnt' => 20,
                'weee_tax_applied_amount_incl_tax' => 10,
                'base_weee_tax_applied_amount_incl_tax' => 10,
                'weee_tax_applied_row_amount_incl_tax' => 20,
                'base_weee_tax_applied_row_amnt_incl_tax' => 20
            ],
            'itemQty' => 2,
            'parentQty' => 0,
            'addressData' => [
                'subtotal_incl_tax' => 20,
                'base_subtotal_incl_tax' => 20
            ]
        ];

        $data['price_incl_tax_weee_taxable_unit_not_included_in_subtotal'] = [
            'taxConfig' => [
                'priceIncludesTax' => true,
                'getCalculationAlgorithm' => Calculation::CALC_UNIT_BASE
            ],
            'weeeConfig' => [
                'isEnabled' => true,
                'includeInSubtotal' => false,
                'isTaxable' => true,
                'getApplied' => [],
                'getProductWeeeAttributes' => [
                    new DataObject(
                        [
                            'name' => 'Recycling Fee',
                            'amount' => 10,
                        ]
                    )
                ]
            ],
            'taxRates' => [
                'store_tax_rate' => 8.25,
                'customer_tax_rate' => 8.25
            ],
            'itemData' => [
                'weee_tax_applied_amount' => 10,
                'base_weee_tax_applied_amount' => 10,
                'weee_tax_applied_row_amount' => 20,
                'base_weee_tax_applied_row_amnt' => 20,
                'weee_tax_applied_amount_incl_tax' => 10,
                'base_weee_tax_applied_amount_incl_tax' => 10,
                'weee_tax_applied_row_amount_incl_tax' => 20,
                'base_weee_tax_applied_row_amnt_incl_tax' => 20
            ],
            'itemQty' => 2,
            'parentQty' => 0,
            'addressData' => [
                'subtotal_incl_tax' => 20,
                'base_subtotal_incl_tax' => 20
            ]
        ];

        $data['price_excl_tax_weee_taxable_unit_included_in_subtotal'] = [
            'taxConfig' => [
                'priceIncludesTax' => false,
                'getCalculationAlgorithm' => Calculation::CALC_UNIT_BASE
            ],
            'weeeConfig' => [
                'isEnabled' => true,
                'includeInSubtotal' => true,
                'isTaxable' => true,
                'getApplied' => [],
                'getProductWeeeAttributes' => [
                    new DataObject(
                        [
                            'name' => 'Recycling Fee',
                            'amount' => 10
                        ]
                    )
                ]
            ],
            'taxRates' => [
                'store_tax_rate' => 8.25,
                'customer_tax_rate' => 8.25
            ],
            'itemData' => [
                'weee_tax_applied_amount' => 10,
                'base_weee_tax_applied_amount' => 10,
                'weee_tax_applied_row_amount' => 20,
                'base_weee_tax_applied_row_amnt' => 20,
                'weee_tax_applied_amount_incl_tax' => 10,
                'base_weee_tax_applied_amount_incl_tax' => 10,
                'weee_tax_applied_row_amount_incl_tax' => 20,
                'base_weee_tax_applied_row_amnt_incl_tax' => 20
            ],
            'itemQty' => 2,
            'parentQty' => 0,
            'addressData' => [
                'subtotal_incl_tax' => 20,
                'base_subtotal_incl_tax' => 20
            ]
        ];

        $data['price_incl_tax_weee_non_taxable_unit_included_in_subtotal'] = [
            'taxConfig' => [
                'priceIncludesTax' => true,
                'getCalculationAlgorithm' => Calculation::CALC_UNIT_BASE
            ],
            'weeeConfig' => [
                'isEnabled' => true,
                'includeInSubtotal' => true,
                'isTaxable' => false,
                'getApplied' => [],
                'getProductWeeeAttributes' => [
                    new DataObject(
                        [
                            'name' => 'Recycling Fee',
                            'amount' => 10
                        ]
                    )
                ]
            ],
            'taxRates' => [
                'store_tax_rate' => 8.25,
                'customer_tax_rate' => 8.25
            ],
            'itemData' => [
                'weee_tax_applied_amount' => 10,
                'base_weee_tax_applied_amount' => 10,
                'weee_tax_applied_row_amount' => 20,
                'base_weee_tax_applied_row_amnt' => 20,
                'weee_tax_applied_amount_incl_tax' => 10,
                'base_weee_tax_applied_amount_incl_tax' => 10,
                'weee_tax_applied_row_amount_incl_tax' => 20,
                'base_weee_tax_applied_row_amnt_incl_tax' => 20
            ],
            'itemQty' => 2,
            'parentQty' => 0,
            'addressData' => [
                'subtotal_incl_tax' => 20,
                'base_subtotal_incl_tax' => 20,
                'weee_total_excl_tax' => 20,
                'weee_base_total_excl_tax' => 20
            ]
        ];

        $data['price_excl_tax_weee_non_taxable_unit_included_in_subtotal'] = [
            'taxConfig' => [
                'priceIncludesTax' => false,
                'getCalculationAlgorithm' => Calculation::CALC_UNIT_BASE
            ],
            'weeeConfig' => [
                'isEnabled' => true,
                'includeInSubtotal' => true,
                'isTaxable' => false,
                'getApplied' => [],
                'getProductWeeeAttributes' => [
                    new DataObject(
                        [
                            'name' => 'Recycling Fee',
                            'amount' => 10
                        ]
                    )
                ]
            ],
            'taxRates' => [
                'store_tax_rate' => 8.25,
                'customer_tax_rate' => 8.25
            ],
            'itemData' => [
                'weee_tax_applied_amount' => 10,
                'base_weee_tax_applied_amount' => 10,
                'weee_tax_applied_row_amount' => 20,
                'base_weee_tax_applied_row_amnt' => 20,
                'weee_tax_applied_amount_incl_tax' => 10,
                'base_weee_tax_applied_amount_incl_tax' => 10,
                'weee_tax_applied_row_amount_incl_tax' => 20,
                'base_weee_tax_applied_row_amnt_incl_tax' => 20
            ],
            'itemQty' => 2,
            'parentQty' => 0,
            'addressData' => [
                'subtotal_incl_tax' => 20,
                'base_subtotal_incl_tax' => 20,
                'weee_total_excl_tax' => 20,
                'weee_base_total_excl_tax' => 20
            ]
        ];

        $data['price_incl_tax_weee_taxable_row_included_in_subtotal'] = [
            'taxConfig' => [
                'priceIncludesTax' => true,
                'getCalculationAlgorithm' => Calculation::CALC_ROW_BASE
            ],
            'weeeConfig' => [
                'isEnabled' => true,
                'includeInSubtotal' => true,
                'isTaxable' => true,
                'getApplied' => [],
                'getProductWeeeAttributes' => [
                    new DataObject(
                        [
                            'name' => 'Recycling Fee',
                            'amount' => 10
                        ]
                    )
                ]
            ],
            'taxRates' => [
                'store_tax_rate' => 8.25,
                'customer_tax_rate' => 8.25
            ],
            'itemData' => [
                'weee_tax_applied_amount' => 10,
                'base_weee_tax_applied_amount' => 10,
                'weee_tax_applied_row_amount' => 20,
                'base_weee_tax_applied_row_amnt' => 20,
                'weee_tax_applied_amount_incl_tax' => 10,
                'base_weee_tax_applied_amount_incl_tax' => 10,
                'weee_tax_applied_row_amount_incl_tax' => 20,
                'base_weee_tax_applied_row_amnt_incl_tax' => 20
            ],
            'itemQty' => 2,
            'parentQty' => 0,
            'addressData' => [
                'subtotal_incl_tax' => 20,
                'base_subtotal_incl_tax' => 20
            ]
        ];

        $data['price_excl_tax_weee_taxable_row_included_in_subtotal'] = [
            'taxConfig' => [
                'priceIncludesTax' => false,
                'getCalculationAlgorithm' => Calculation::CALC_ROW_BASE
            ],
            'weeeConfig' => [
                'isEnabled' => true,
                'includeInSubtotal' => true,
                'isTaxable' => true,
                'getApplied' => [],
                'getProductWeeeAttributes' => [
                    new DataObject(
                        [
                            'name' => 'Recycling Fee',
                            'amount' => 10
                        ]
                    )
                ]
            ],
            'taxRates' => [
                'store_tax_rate' => 8.25,
                'customer_tax_rate' => 8.25
            ],
            'itemData' => [
                'weee_tax_applied_amount' => 10,
                'base_weee_tax_applied_amount' => 10,
                'weee_tax_applied_row_amount' => 20,
                'base_weee_tax_applied_row_amnt' => 20,
                'weee_tax_applied_amount_incl_tax' => 10,
                'base_weee_tax_applied_amount_incl_tax' => 10,
                'weee_tax_applied_row_amount_incl_tax' => 20,
                'base_weee_tax_applied_row_amnt_incl_tax' => 20
            ],
            'itemQty' => 2,
            'parentQty' => 0,
            'addressData' => [
                'subtotal_incl_tax' => 20,
                'base_subtotal_incl_tax' => 20
            ],
        ];

        $data['price_incl_tax_weee_non_taxable_row_included_in_subtotal'] = [
            'taxConfig' => [
                'priceIncludesTax' => true,
                'getCalculationAlgorithm' => Calculation::CALC_ROW_BASE
            ],
            'weeeConfig' => [
                'isEnabled' => true,
                'includeInSubtotal' => true,
                'isTaxable' => false,
                'getApplied' => [],
                'getProductWeeeAttributes' => [
                    new DataObject(
                        [
                            'name' => 'Recycling Fee',
                            'amount' => 10
                        ]
                    )
                ]
            ],
            'taxRates' => [
                'store_tax_rate' => 8.25,
                'customer_tax_rate' => 8.25
            ],
            'itemData' => [
                'weee_tax_applied_amount' => 10,
                'base_weee_tax_applied_amount' => 10,
                'weee_tax_applied_row_amount' => 20,
                'base_weee_tax_applied_row_amnt' => 20,
                'weee_tax_applied_amount_incl_tax' => 10,
                'base_weee_tax_applied_amount_incl_tax' => 10,
                'weee_tax_applied_row_amount_incl_tax' => 20,
                'base_weee_tax_applied_row_amnt_incl_tax' => 20
            ],
            'itemQty' => 2,
            'parentQty' => 0,
            'addressData' => [
                'subtotal_incl_tax' => 20,
                'base_subtotal_incl_tax' => 20,
                'weee_total_excl_tax' => 20,
                'weee_base_total_excl_tax' => 20
            ]
        ];

        $data['price_excl_tax_weee_non_taxable_row_included_in_subtotal'] = [
            'taxConfig' => [
                'priceIncludesTax' => false,
                'getCalculationAlgorithm' => Calculation::CALC_ROW_BASE
            ],
            'weeeConfig' => [
                'isEnabled' => true,
                'includeInSubtotal' => true,
                'isTaxable' => false,
                'getApplied' => [],
                'getProductWeeeAttributes' => [
                    new DataObject(
                        [
                            'name' => 'Recycling Fee',
                            'amount' => 10
                        ]
                    )
                ]
            ],
            'taxRates' => [
                'store_tax_rate' => 8.25,
                'customer_tax_rate' => 8.25
            ],
            'itemData' => [
                'weee_tax_applied_amount' => 10,
                'base_weee_tax_applied_amount' => 10,
                'weee_tax_applied_row_amount' => 20,
                'base_weee_tax_applied_row_amnt' => 20,
                'weee_tax_applied_amount_incl_tax' => 10,
                'base_weee_tax_applied_amount_incl_tax' => 10,
                'weee_tax_applied_row_amount_incl_tax' => 20,
                'base_weee_tax_applied_row_amnt_incl_tax' => 20
            ],
            'itemQty' => 2,
            'parentQty' => 0,
            'addressData' => [
                'subtotal_incl_tax' => 20,
                'base_subtotal_incl_tax' => 20,
                'weee_total_excl_tax' => 20,
                'weee_base_total_excl_tax' => 20
            ]
        ];

        $data['price_excl_tax_weee_non_taxable_row_not_included_in_subtotal'] = [
            'taxConfig' => [
                'priceIncludesTax' => false,
                'getCalculationAlgorithm' => Calculation::CALC_ROW_BASE
            ],
            'weeeConfig' => [
                'isEnabled' => true,
                'includeInSubtotal' => false,
                'isTaxable' => false,
                'getApplied' => [],
                'getProductWeeeAttributes' => [
                    new DataObject(
                        [
                            'name' => 'Recycling Fee',
                            'amount' => 10
                        ]
                    )
                ]
            ],
            'taxRates' => [
                'store_tax_rate' => 8.25,
                'customer_tax_rate' => 8.25
            ],
            'itemData' => [
                'weee_tax_applied_amount' => 10,
                'base_weee_tax_applied_amount' => 10,
                'weee_tax_applied_row_amount' => 20,
                'base_weee_tax_applied_row_amnt' => 20,
                'weee_tax_applied_amount_incl_tax' => 10,
                'base_weee_tax_applied_amount_incl_tax' => 10,
                'weee_tax_applied_row_amount_incl_tax' => 20,
                'base_weee_tax_applied_row_amnt_incl_tax' => 20
            ],
            'itemQty' => 2,
            'parentQty' => 0,
            'addressData' => [
                'subtotal_incl_tax' => 20,
                'base_subtotal_incl_tax' => 20,
                'weee_total_excl_tax' => 20,
                'weee_base_total_excl_tax' => 20
            ]
        ];

        $data['price_excl_tax_weee_taxable_unit_not_included_in_subtotal_PARENT_ITEM'] = [
            'taxConfig' => [
                'priceIncludesTax' => false,
                'getCalculationAlgorithm' => Calculation::CALC_UNIT_BASE
            ],
            'weeeConfig' => [
                'isEnabled' => true,
                'includeInSubtotal' => false,
                'isTaxable' => true,
                'getApplied' => [],
                'getProductWeeeAttributes' => [
                    new DataObject(
                        [
                            'name' => 'Recycling Fee',
                            'amount' => 10
                        ]
                    )
                ]
            ],
            'taxRates' => [
                'store_tax_rate' => 8.25,
                'customer_tax_rate' => 8.25
            ],
            'itemData' => [
                'weee_tax_applied_amount' => 10,
                'base_weee_tax_applied_amount' => 10,
                'weee_tax_applied_row_amount' => 60,
                'base_weee_tax_applied_row_amnt' => 60,
                'weee_tax_applied_amount_incl_tax' => 10,
                'base_weee_tax_applied_amount_incl_tax' => 10,
                'weee_tax_applied_row_amount_incl_tax' => 60,
                'base_weee_tax_applied_row_amnt_incl_tax' => 60
            ],
            'itemQty' => 2,
            'parentQty' => 3,
            'addressData' => [
                'subtotal_incl_tax' => 60,
                'base_subtotal_incl_tax' => 60,
                'weee_total_excl_tax' => 0,
                'weee_base_total_excl_tax' => 0
            ]
        ];

        $data['price_excl_tax_weee_taxable_unit_included_in_subtotal_PARENT_ITEM'] = [
            'taxConfig' => [
                'priceIncludesTax' => true,
                'getCalculationAlgorithm' => Calculation::CALC_UNIT_BASE
            ],
            'weeeConfig' => [
                'isEnabled' => true,
                'includeInSubtotal' => true,
                'isTaxable' => true,
                'getApplied' => [],
                'getProductWeeeAttributes' => [
                    new DataObject(
                        [
                            'name' => 'FPT',
                            'amount' => 10
                        ]
                    )
                ]
            ],
            'taxRates' => [
                'store_tax_rate' => 8.25,
                'customer_tax_rate' => 8.25
            ],
            'itemData' => [
                'weee_tax_applied_amount' => 10,
                'base_weee_tax_applied_amount' => 10,
                'weee_tax_applied_row_amount' => 20,
                'base_weee_tax_applied_row_amnt' => 20,
                'weee_tax_applied_amount_incl_tax' => 10,
                'base_weee_tax_applied_amount_incl_tax' => 10,
                'weee_tax_applied_row_amount_incl_tax' => 20,
                'base_weee_tax_applied_row_amnt_incl_tax' => 20
            ],
            'itemQty' => 2,
            'parentQty' => 1,
            'addressData' => [
                'subtotal_incl_tax' => 20,
                'base_subtotal_incl_tax' => 20,
                'weee_total_excl_tax' => 0,
                'weee_base_total_excl_tax' => 0
            ]
        ];

        $data['price_excl_tax_weee_non_taxable_row_not_included_in_subtotal_dynamic_multiple_weee'] = [
            'taxConfig' => [
                'priceIncludesTax' => false,
                'getCalculationAlgorithm' => Calculation::CALC_ROW_BASE
            ],
            'weeeConfig' => [
                'isEnabled' => true,
                'includeInSubtotal' => false,
                'isTaxable' => false,
                'getApplied' => [],
                'getProductWeeeAttributes' => [
                    new DataObject(
                        [
                            'name' => 'Recycling Fee',
                            'amount' => 10
                        ]
                    ),
                    new DataObject(
                        [
                            'name' => 'FPT Fee',
                            'amount' => 5
                        ]
                    )
                ]
            ],
            'taxRates' => [
                'store_tax_rate' => 8.25,
                'customer_tax_rate' => 8.25
            ],
            'itemData' => [
                'weee_tax_applied_amount' => 15,
                'base_weee_tax_applied_amount' => 15,
                'weee_tax_applied_row_amount' => 30,
                'base_weee_tax_applied_row_amnt' => 30,
                'weee_tax_applied_amount_incl_tax' => 15,
                'base_weee_tax_applied_amount_incl_tax' => 15,
                'weee_tax_applied_row_amount_incl_tax' => 30,
                'base_weee_tax_applied_row_amnt_incl_tax' => 30
            ],
            'itemQty' => 2,
            'parentQty' => 0,
            'addressData' => [
                'subtotal_incl_tax' => 30,
                'base_subtotal_incl_tax' => 30,
                'weee_total_excl_tax' => 30,
                'weee_base_total_excl_tax' => 30
            ],
            'assertSetApplied' => true
        ];

        return $data;
    }
}
