<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Test\Unit\Pricing\Adjustment;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Bundle\Model\Option;
use Magento\Bundle\Model\Product\Price as ProductPrice;
use Magento\Bundle\Pricing\Adjustment\Calculator;
use Magento\Bundle\Pricing\Adjustment\SelectionPriceListProviderInterface;
use Magento\Bundle\Pricing\Price;
use Magento\Bundle\Pricing\Price\BundleOptionPrice;
use Magento\Bundle\Pricing\Price\BundleSelectionFactory;
use Magento\Bundle\Pricing\Price\BundleSelectionPrice;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Test\Unit\Helper\ProductTestHelper;
use Magento\Framework\Pricing\Adjustment\Calculator as PricingAdjustmentCalculator;
use Magento\Framework\Pricing\Amount\AmountFactory;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Pricing\PriceInfo\Base;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\Store;
use Magento\Tax\Helper\Data;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\Bundle\Pricing\Adjustment\Calculator
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CalculatorTest extends TestCase
{
    /**
     * @var SaleableInterface
     */
    protected $saleableItem;

    /**
     * @var PriceInterface[]|MockObject[]
     */
    protected $priceMocks = [];

    /**
     * @var float
     */
    protected $baseAmount = 50.;

    /**
     * @var PricingAdjustmentCalculator|MockObject
     */
    protected $baseCalculator;

    /**
     * @var MockObject
     */
    protected $amountFactory;

    /**
     * @var MockObject
     */
    protected $selectionFactory;

    /**
     * @var MockObject
     */
    protected $taxData;

    /**
     * @var MockObject
     */
    private $selectionPriceListProvider;

    /**
     * @var Calculator
     */
    protected $model;

    protected function setUp(): void
    {
        /** @var ProductTestHelper $saleableItem */
        $this->saleableItem = new ProductTestHelper();

        $priceCurrency = $this->createMock(PriceCurrencyInterface::class);
        $priceInfo = $this->createMock(Base::class);
        $priceInfo->expects($this->any())->method('getPrice')->willReturnCallback(
            function ($type) {
                if (!isset($this->priceMocks[$type])) {
                    throw new ExpectationFailedException('Unexpected type of price model');
                }
                return $this->priceMocks[$type];
            }
        );
        $this->saleableItem->setPriceInfo($priceInfo);

        $store = $this->createMock(Store::class);
        $priceCurrency->expects($this->any())->method('round')->willReturnArgument(0);

        $this->saleableItem->setStore($store);

        $this->baseCalculator = $this->createMock(PricingAdjustmentCalculator::class);
        $this->amountFactory = $this->createMock(AmountFactory::class);

        $this->selectionFactory = $this->getMockBuilder(BundleSelectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->selectionFactory->expects($this->any())->method('create')->willReturnArgument(1);

        $this->taxData = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->selectionPriceListProvider = $this->getMockBuilder(
            SelectionPriceListProviderInterface::class
        )->getMock();

        $this->model = (new ObjectManager($this))->getObject(
            Calculator::class,
            [
                'calculator' => $this->baseCalculator,
                'amountFactory' => $this->amountFactory,
                'bundleSelectionFactory' => $this->selectionFactory,
                'taxHelper' => $this->taxData,
                'priceCurrency' => $priceCurrency,
                'selectionPriceListProvider' => $this->selectionPriceListProvider
            ]
        );
    }

    public function testEmptySelectionPriceList()
    {
        /** @var Option $option */
        $option = new class extends Option {
            private $selections;
            
            public function __construct() {}
            
            public function getSelections() { return $this->selections; }
            public function setSelections($selections) { $this->selections = $selections; return $this; }
        };
        $option->setSelections(null);
        $bundleProduct = $this->createMock(Product::class);
        $this->assertSame([], $this->model->createSelectionPriceList($option, $bundleProduct));
    }

    #[DataProvider('dataProviderForGetterAmount')]
    public function testGetterAmount($amountForBundle, $optionList, $expectedResult)
    {
        $searchMin = $expectedResult['isMinAmount'];
        $this->baseCalculator->expects($this->atLeastOnce())->method('getAmount')
            ->with($this->baseAmount, $this->saleableItem)
            ->willReturn($this->createAmountMock($amountForBundle));

        $options = [];
        foreach ($optionList as $optionData) {
            $options[] = $this->createOptionMock($optionData);
        }

        $optionSelections = [];
        foreach ($options as $option) {
            $optionSelections[] = $option->getSelections();
        }
        $optionSelections = array_merge([], ...$optionSelections);

        $this->selectionPriceListProvider->method('getPriceList')->willReturn($optionSelections);

        $price = $this->createMock(BundleOptionPrice::class);
        $this->priceMocks[BundleOptionPrice::PRICE_CODE] = $price;

        // Price type of saleable items
        $this->saleableItem->setPriceType(ProductPrice::PRICE_TYPE_DYNAMIC);

        $this->amountFactory->expects($this->atLeastOnce())->method('create')
            ->with($expectedResult['fullAmount'], $expectedResult['adjustments']);
        if ($searchMin) {
            $this->model->getAmount($this->baseAmount, $this->saleableItem);
        } else {
            $this->model->getMaxAmount($this->baseAmount, $this->saleableItem);
        }
    }

    /**
     * @return array
     */
    public static function dataProviderForGetterAmount()
    {
        return [
            // first case with minimal amount
            'case with getting minimal amount' => self::getCaseWithMinAmount(),
            // second case with maximum amount
            'case with getting maximum amount' => self::getCaseWithMaxAmount(),
            // third case without saleable items
            'case without saleable items' => self::getCaseWithoutSaleableItems(),
            // fourth case without require options
            'case without required options' => self::getCaseMinAmountWithoutRequiredOptions(),
        ];
    }

    protected function tearDown(): void
    {
        $this->priceMocks = [];
    }

    /**
     * Create amount mock
     *
     * @param array $amountData
     * @return \Magento\Framework\Pricing\Amount\Base|MockObject
     */
    protected function createAmountMock($amountData)
    {
        /** @var \Magento\Framework\Pricing\Amount\Base $amount */
        $amount = new class extends \Magento\Framework\Pricing\Amount\Base {
            protected $adjustmentAmounts;
            protected $value;
            
            public function __construct() {}
            
            public function __wakeup() {}
            public function getAdjustmentAmounts() { return $this->adjustmentAmounts; }
            public function setAdjustmentAmounts($adjustmentAmounts) { $this->adjustmentAmounts = $adjustmentAmounts; return $this; }
            public function getValue($exclude = null) { return $this->value; }
            public function setValue($value) { $this->value = $value; return $this; }
        };
        $amount->setAdjustmentAmounts($amountData['adjustmentsAmounts']);
        $amount->setValue($amountData['amount']);
        return $amount;
    }

    /**
     * Create option mock
     *
     * @param array $optionData
     * @return Option|MockObject
     */
    protected function createOptionMock($optionData)
    {
        /** @var MockObject|Option $option */
        $option = $this->createPartialMock(Option::class, ['isMultiSelection', '__wakeup']);
        $option->method('isMultiSelection')->willReturn($optionData['isMultiSelection']);
        $selections = [];
        foreach ($optionData['selections'] as $selectionData) {
            $selections[] = $this->createSelectionMock($selectionData);
        }
        foreach ($optionData['data'] as $key => $value) {
            $option->setData($key, $value);
        }
        $option->setData('selections', $selections);
        return $option;
    }

    /**
     * Create selection product mock
     *
     * @param array $selectionData
     * @return Product|MockObject
     */
    protected function createSelectionMock($selectionData)
    {
        /** @var ProductTestHelper $selection */
        $selection = new ProductTestHelper();

        // All items are saleable
        $selection->setIsSaleable(true);
        foreach ($selectionData['data'] as $key => $value) {
            $selection->setData($key, $value);
        }
        $amountMock = $this->createAmountMock($selectionData['amount']);
        $selection->setAmount($amountMock);
        $selection->setQuantity(1);

        /** @var Product $innerProduct */
        $innerProduct = new class extends Product {
            private $selectionCanChangeQty;
            
            public function __construct() {}
            
            public function __wakeup() {}
            public function getSelectionCanChangeQty() { return $this->selectionCanChangeQty; }
            public function setSelectionCanChangeQty($selectionCanChangeQty) { $this->selectionCanChangeQty = $selectionCanChangeQty; return $this; }
        };
        $innerProduct->setSelectionCanChangeQty(false);
        $selection->setProduct($innerProduct);

        return $selection;
    }

    /**
     * Array for data provider dataProviderForGetterAmount for case 'case with getting minimal amount'
     *
     * @return array
     */
    protected static function getCaseWithMinAmount()
    {
        return [
            'amountForBundle' => [
                'adjustmentsAmounts' => ['tax' => 102],
                'amount' => 782,
            ],
            'optionList' => [
                // first option with single choice of product
                [
                    'isMultiSelection' => false,
                    'data' => [
                        'title' => 'test option 1',
                        'default_title' => 'test option 1',
                        'type' => 'select',
                        'option_id' => '1',
                        'position' => '0',
                        'required' => '1',
                    ],
                    'selections' => [
                        'selection with the lowest price' => [
                            'data' => ['price' => 50.],
                            'amount' => [
                                'adjustmentsAmounts' => ['tax' => 8, 'weee' => 10],
                                'amount' => 8,
                            ],
                        ],
                    ]
                ],
            ],
            'expectedResult' => [
                'isMinAmount' => true,
                'fullAmount' => 790.,
                'adjustments' => ['tax' => 110, 'weee' => 10],
            ]
        ];
    }

    /**
     * Array for data provider dataProviderForGetterAmount for case 'case with getting maximum amount'
     *
     * @return array
     */
    protected static function getCaseWithMaxAmount()
    {
        return [
            'amountForBundle' => [
                'adjustmentsAmounts' => ['tax' => 102],
                'amount' => 782,
            ],
            'optionList' => [
                // first option with single choice of product
                [
                    'isMultiSelection' => false,
                    'data' => [
                        'title' => 'test option 1',
                        'default_title' => 'test option 1',
                        'type' => 'select',
                        'option_id' => '1',
                        'position' => '0',
                        'required' => '1',
                    ],
                    'selections' => [
                        'first product selection' => [
                            'data' => ['price' => 50.],
                            'amount' => [
                                'adjustmentsAmounts' => ['tax' => 8, 'weee' => 10],
                                'amount' => 8,
                            ],
                        ],
                    ]
                ],
                // second option with multiselection
                [
                    'isMultiSelection' => true,
                    'data' => [
                        'title' => 'test option 2',
                        'default_title' => 'test option 2',
                        'type' => 'select',
                        'option_id' => '2',
                        'position' => '1',
                        'required' => '1',
                    ],
                    'selections' => [
                        'first product selection' => [
                            'data' => ['price' => 20.],
                            'amount' => [
                                'adjustmentsAmounts' => ['tax' => 8],
                                'amount' => 8,
                            ],
                        ],
                        'second product selection' => [
                            'data' => ['price' => 110.],
                            'amount' => [
                                'adjustmentsAmounts' => ['tax' => 28],
                                'amount' => 28,
                            ],
                        ],
                        'third product selection' => [
                            'data' => ['price' => 50.],
                            'amount' => [
                                'adjustmentsAmounts' => ['tax' => 18],
                                'amount' => 18,
                            ],
                        ],
                    ]
                ],
            ],
            'expectedResult' => [
                'isMinAmount' => false,
                'fullAmount' => 844.,
                'adjustments' => ['tax' => 164, 'weee' => 10],
            ]
        ];
    }

    /**
     * Array for data provider dataProviderForGetterAmount for case 'case without saleable items'
     *
     * @return array
     */
    protected static function getCaseWithoutSaleableItems()
    {
        return [
            'amountForBundle' => [
                'adjustmentsAmounts' => ['tax' => 102],
                'amount' => 782,
            ],
            'optionList' => [
                // first option with single choice of product
                [
                    'isMultiSelection' => false,
                    'data' => [
                        'title' => 'test option 1',
                        'default_title' => 'test option 1',
                        'type' => 'select',
                        'option_id' => '1',
                        'position' => '0',
                        'required' => '1',
                    ],
                    'selections' => []
                ],
            ],
            'expectedResult' => [
                'isMinAmount' => true,
                'fullAmount' => 782.,
                'adjustments' => ['tax' => 102],
            ]
        ];
    }

    /**
     * Array for data provider dataProviderForGetterAmount for case 'case without required options'
     *
     * @return array
     */
    protected static function getCaseMinAmountWithoutRequiredOptions()
    {
        return [
            'amountForBundle' => [
                'adjustmentsAmounts' => [],
                'amount' => null,
            ],
            'optionList' => [
                // first option
                [
                    'isMultiSelection' => false,
                    'data' => [
                        'title' => 'test option 1',
                        'default_title' => 'test option 1',
                        'type' => 'select',
                        'option_id' => '1',
                        'position' => '0',
                        'required' => '0',
                    ],
                    'selections' => [
                        'first product selection' => [
                            'data' => ['price' => 20.],
                            'amount' => [
                                'adjustmentsAmounts' => ['tax' => 8],
                                'amount' => 8,
                            ],
                        ],
                    ]
                ],
                // second option
                [
                    'isMultiSelection' => false,
                    'data' => [
                        'title' => 'test option 2',
                        'default_title' => 'test option 2',
                        'type' => 'select',
                        'option_id' => '2',
                        'position' => '1',
                        'required' => '0',
                    ],
                    'selections' => [
                    ]
                ],
            ],
            'expectedResult' => [
                'isMinAmount' => true,
                'fullAmount' => 8.,
                'adjustments' => ['tax' => 8],
            ]
        ];
    }

    public function testGetAmountWithoutOption()
    {
        $amount = 1;
        $result = 5;

        /** @var Calculator|MockObject $calculatorMock */
        $calculatorMock = $this->getMockBuilder(Calculator::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['calculateBundleAmount'])
            ->getMock();

        $calculatorMock->expects($this->once())
            ->method('calculateBundleAmount')
            ->with($amount, $this->saleableItem, [])
            ->willReturn($result);

        $this->assertEquals($result, $calculatorMock->getAmountWithoutOption($amount, $this->saleableItem));
    }

    public function testGetMinRegularAmount()
    {
        $amount = 1;
        $expectedResult = 5;

        $exclude = 'false';

        /** @var Calculator|MockObject $calculatorMock */
        $calculatorMock = $this->getMockBuilder(Calculator::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getOptionsAmount'])
            ->getMock();

        $calculatorMock->expects($this->once())
            ->method('getOptionsAmount')
            ->with($this->saleableItem, $exclude, true, $amount, true)
            ->willReturn($expectedResult);

        $result = $calculatorMock->getMinRegularAmount($amount, $this->saleableItem, $exclude);

        $this->assertEquals($expectedResult, $result, 'Incorrect result');
    }

    public function testGetMaxRegularAmount()
    {
        $amount = 1;
        $expectedResult = 5;

        $exclude = 'false';

        /** @var Calculator|MockObject $calculatorMock */
        $calculatorMock = $this->getMockBuilder(Calculator::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getOptionsAmount'])
            ->getMock();

        $calculatorMock->expects($this->once())
            ->method('getOptionsAmount')
            ->with($this->saleableItem, $exclude, false, $amount, true)
            ->willReturn($expectedResult);

        $result = $calculatorMock->getMaxRegularAmount($amount, $this->saleableItem, $exclude);

        $this->assertEquals($expectedResult, $result, 'Incorrect result');
    }

    #[DataProvider('getOptionsAmountDataProvider')]
    public function testGetOptionsAmount($searchMin, $useRegularPrice)
    {
        $amount = 1;
        $expectedResult = 5;

        $exclude = 'false';

        /** @var Calculator|MockObject $calculatorMock */
        $calculatorMock = $this->getMockBuilder(Calculator::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['calculateBundleAmount', 'getSelectionAmounts'])
            ->getMock();

        $selections[] = $this->getMockBuilder(BundleSelectionPrice::class)
            ->disableOriginalConstructor()
            ->getMock();
        $calculatorMock->expects($this->once())
            ->method('getSelectionAmounts')
            ->with($this->saleableItem, $searchMin, $useRegularPrice)
            ->willReturn($selections);

        $calculatorMock->expects($this->once())
            ->method('calculateBundleAmount')
            ->with($amount, $this->saleableItem, $selections, $exclude)
            ->willReturn($expectedResult);

        $result = $calculatorMock->getOptionsAmount(
            $this->saleableItem,
            $exclude,
            $searchMin,
            $amount,
            $useRegularPrice
        );

        $this->assertEquals($expectedResult, $result, 'Incorrect result');
    }

    /**
     * @return array
     */
    public static function getOptionsAmountDataProvider()
    {
        return [
            'true, true' => [
                'searchMin' => true,
                'useRegularPrice' => true,
            ],
            'true, false' => [
                'searchMin' => true,
                'useRegularPrice' => false,
            ],
            'false, true' => [
                'searchMin' => false,
                'useRegularPrice' => true,
            ],
            'false, false' => [
                'searchMin' => false,
                'useRegularPrice' => false,
            ],
        ];
    }
}
