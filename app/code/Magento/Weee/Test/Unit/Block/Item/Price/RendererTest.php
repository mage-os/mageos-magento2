<?php
/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Weee\Test\Unit\Block\Item\Price;

use Magento\Directory\Model\PriceCurrency;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Model\Quote\Item;
use Magento\Weee\Block\Item\Price\Renderer;
use Magento\Weee\Helper\Data;
use Magento\Weee\Model\Tax as WeeeDisplayConfig;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit Tests to cover Renderer
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class RendererTest extends TestCase
{
    use MockCreationTrait;

    /**
     * @var Renderer
     */
    protected $renderer;

    /**
     * @var Data|MockObject
     */
    protected $weeeHelper;

    /**
     * @var PriceCurrency|MockObject
     */
    protected $priceCurrency;

    /**
     * @var Item|MockObject
     */
    protected $item;

    private const STORE_ID = 'store_id';
    private const ZONE = 'zone';

    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->weeeHelper = $this->getMockBuilder(Data::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                'isEnabled',
                'typeOfDisplay',
                'getWeeeTaxInclTax',
                'getRowWeeeTaxInclTax',
                'getBaseRowWeeeTaxInclTax',
                'getBaseWeeeTaxInclTax',
                ]
            )
            ->getMock();

        $this->priceCurrency = $this->getMockBuilder(PriceCurrency::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['format'])
            ->getMock();

        $this->item = $this->createItemMock();
        $this->item->setStoreId(self::STORE_ID);

        $this->renderer = $objectManager->getObject(
            Renderer::class,
            [
                'weeeHelper' => $this->weeeHelper,
                'priceCurrency' => $this->priceCurrency,
            ]
        );
        $this->renderer->setItem($this->item);
        $this->renderer->setZone(self::ZONE);
    }

    /**
     * Create item mock with all required methods
     *
     * @return Item|MockObject
     */
    private function createItemMock()
    {
        $methods = [
            'setStoreId', 'getStoreId',
            'setWeeeTaxAppliedAmount', 'getWeeeTaxAppliedAmount',
            'setPriceInclTax', 'getPriceInclTax',
            'setBasePriceInclTax', 'getBasePriceInclTax',
            'setCalculationPrice', 'getCalculationPrice',
            'setBaseWeeeTaxAppliedAmount', 'getBaseWeeeTaxAppliedAmount',
            'setBaseRowTotal', 'getBaseRowTotal',
            'setQtyOrdered', 'getQtyOrdered',
            'setWeeeTaxAppliedRowAmount', 'getWeeeTaxAppliedRowAmount',
            'setRowTotal', 'getRowTotal',
            'setBaseWeeeTaxAppliedRowAmnt', 'getBaseWeeeTaxAppliedRowAmnt',
            'setRowTotalInclTax', 'getRowTotalInclTax',
            'setBaseRowTotalInclTax', 'getBaseRowTotalInclTax',
            'setPrice', 'getPrice',
            'getData', 'setData'
        ];
        
        $itemMock = $this->createPartialMockWithReflection(Item::class, $methods);
        
        // Configure data storage
        $data = ['store_id' => 1];
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
        
        // Configure all setters to store data and return self
        $itemMock->method('setStoreId')->willReturnCallback(function ($val) use (&$data, $itemMock) {
            $data['store_id'] = $val;
            return $itemMock;
        });
        $itemMock->method('getStoreId')->willReturnCallback(function () use (&$data) {
            return $data['store_id'] ?? null;
        });
        $itemMock->method('setWeeeTaxAppliedAmount')->willReturnCallback(function ($val) use (&$data, $itemMock) {
            $data['weee_tax_applied_amount'] = $val;
            return $itemMock;
        });
        $itemMock->method('setPriceInclTax')->willReturnCallback(function ($val) use (&$data, $itemMock) {
            $data['price_incl_tax'] = $val;
            return $itemMock;
        });
        $itemMock->method('setBasePriceInclTax')->willReturnCallback(function ($val) use (&$data, $itemMock) {
            $data['base_price_incl_tax'] = $val;
            return $itemMock;
        });
        $itemMock->method('setCalculationPrice')->willReturnCallback(function ($val) use (&$data, $itemMock) {
            $data['calculation_price'] = $val;
            return $itemMock;
        });
        $itemMock->method('setBaseWeeeTaxAppliedAmount')->willReturnCallback(function ($val) use (&$data, $itemMock) {
            $data['base_weee_tax_applied_amount'] = $val;
            return $itemMock;
        });
        $itemMock->method('setBaseRowTotal')->willReturnCallback(function ($val) use (&$data, $itemMock) {
            $data['base_row_total'] = $val;
            return $itemMock;
        });
        $itemMock->method('setQtyOrdered')->willReturnCallback(function ($val) use (&$data, $itemMock) {
            $data['qty_ordered'] = $val;
            return $itemMock;
        });
        $itemMock->method('setWeeeTaxAppliedRowAmount')->willReturnCallback(function ($val) use (&$data, $itemMock) {
            $data['weee_tax_applied_row_amount'] = $val;
            return $itemMock;
        });
        $itemMock->method('setRowTotal')->willReturnCallback(function ($val) use (&$data, $itemMock) {
            $data['row_total'] = $val;
            return $itemMock;
        });
        $itemMock->method('setBaseWeeeTaxAppliedRowAmnt')->willReturnCallback(function ($val) use (&$data, $itemMock) {
            $data['base_weee_tax_applied_row_amnt'] = $val;
            return $itemMock;
        });
        $itemMock->method('setRowTotalInclTax')->willReturnCallback(function ($val) use (&$data, $itemMock) {
            $data['row_total_incl_tax'] = $val;
            return $itemMock;
        });
        $itemMock->method('setBaseRowTotalInclTax')->willReturnCallback(function ($val) use (&$data, $itemMock) {
            $data['base_row_total_incl_tax'] = $val;
            return $itemMock;
        });
        $itemMock->method('setPrice')->willReturnCallback(function ($val) use (&$data, $itemMock) {
            $data['price'] = $val;
            return $itemMock;
        });
        
        // Configure all getters to retrieve data
        $itemMock->method('getWeeeTaxAppliedAmount')->willReturnCallback(function () use (&$data) {
            return $data['weee_tax_applied_amount'] ?? null;
        });
        $itemMock->method('getPriceInclTax')->willReturnCallback(function () use (&$data) {
            return $data['price_incl_tax'] ?? null;
        });
        $itemMock->method('getBasePriceInclTax')->willReturnCallback(function () use (&$data) {
            return $data['base_price_incl_tax'] ?? null;
        });
        $itemMock->method('getCalculationPrice')->willReturnCallback(function () use (&$data) {
            return $data['calculation_price'] ?? null;
        });
        $itemMock->method('getBaseWeeeTaxAppliedAmount')->willReturnCallback(function () use (&$data) {
            return $data['base_weee_tax_applied_amount'] ?? null;
        });
        $itemMock->method('getBaseRowTotal')->willReturnCallback(function () use (&$data) {
            return $data['base_row_total'] ?? null;
        });
        $itemMock->method('getQtyOrdered')->willReturnCallback(function () use (&$data) {
            return $data['qty_ordered'] ?? 1;  // Default to 1 to avoid division by zero
        });
        $itemMock->method('getWeeeTaxAppliedRowAmount')->willReturnCallback(function () use (&$data) {
            return $data['weee_tax_applied_row_amount'] ?? null;
        });
        $itemMock->method('getRowTotal')->willReturnCallback(function () use (&$data) {
            return $data['row_total'] ?? null;
        });
        $itemMock->method('getBaseWeeeTaxAppliedRowAmnt')->willReturnCallback(function () use (&$data) {
            return $data['base_weee_tax_applied_row_amnt'] ?? null;
        });
        $itemMock->method('getRowTotalInclTax')->willReturnCallback(function () use (&$data) {
            return $data['row_total_incl_tax'] ?? null;
        });
        $itemMock->method('getBaseRowTotalInclTax')->willReturnCallback(function () use (&$data) {
            return $data['base_row_total_incl_tax'] ?? null;
        });
        $itemMock->method('getPrice')->willReturnCallback(function () use (&$data) {
            // getPrice should return calculation_price if set, otherwise price
            return $data['calculation_price'] ?? $data['price'] ?? null;
        });
        
        return $itemMock;
    }

    /**
     * @param bool $isWeeeEnabled
     * @param bool $showWeeeDetails
     * @param bool $hasWeeeAmount
     * @param bool $expectedValue
     */
    #[DataProvider('displayPriceWithWeeeDetailsDataProvider')]
    public function testDisplayPriceWithWeeeDetails(
        $isWeeeEnabled,
        $showWeeeDetails,
        $hasWeeeAmount,
        $expectedValue
    ) {
        $this->weeeHelper->expects($this->once())
            ->method('isEnabled')
            ->willReturn($isWeeeEnabled);

        $this->weeeHelper->expects($this->any())
            ->method('typeOfDisplay')
            ->with(
                [WeeeDisplayConfig::DISPLAY_INCL_DESCR, WeeeDisplayConfig::DISPLAY_EXCL_DESCR_INCL],
                self::ZONE,
                self::STORE_ID
            )->willReturn($showWeeeDetails);

        $this->item->setWeeeTaxAppliedAmount($hasWeeeAmount);

        $this->assertEquals($expectedValue, $this->renderer->displayPriceWithWeeeDetails());
    }

    /**
     * @return array
     */
    public static function displayPriceWithWeeeDetailsDataProvider()
    {
        $data = [
            'weee_disabled_true_true' => [
                'isWeeeEnabled' => false,
                'showWeeeDetails' => true,
                'hasWeeeAmount' => true,
                'expectedValue' => false,
            ],
            'weee_disabled_true_false' => [
                'isWeeeEnabled' => false,
                'showWeeeDetails' => true,
                'hasWeeeAmount' => false,
                'expectedValue' => false,
            ],
            'weee_disabled_false_true' => [
                'isWeeeEnabled' => false,
                'showWeeeDetails' => false,
                'hasWeeeAmount' => true,
                'expectedValue' => false,
            ],
            'weee_disabled_false_false' => [
                'isWeeeEnabled' => false,
                'showWeeeDetails' => false,
                'hasWeeeAmount' => false,
                'expectedValue' => false,
            ],
            'weee_enabled_showdetail_true' => [
                'isWeeeEnabled' => true,
                'showWeeeDetails' => true,
                'hasWeeeAmount' => true,
                'expectedValue' => true,
            ],
            'weee_enabled_showdetail_string_zero_false' => [
                'isWeeeEnabled' => true,
                'showWeeeDetails' => true,
                'hasWeeeAmount' => "0.0000",
                'expectedValue' => false,
            ],
            'weee_enabled_showdetail_false' => [
                'isWeeeEnabled' => true,
                'showWeeeDetails' => true,
                'hasWeeeAmount' => false,
                'expectedValue' => false,
            ],
            'weee_enabled_not_showing_detail_true' => [
                'isWeeeEnabled' => true,
                'showWeeeDetails' => false,
                'hasWeeeAmount' => true,
                'expectedValue' => false,
            ],
            'weee_enabled_not_showing_detail_false' => [
                'isWeeeEnabled' => true,
                'showWeeeDetails' => false,
                'hasWeeeAmount' => false,
                'expectedValue' => false,
            ],
        ];

        return $data;
    }

    /**
     * @param int  $price
     * @param int  $weeeTax
     * @param bool $weeeEnabled
     * @param bool $includeWeee
     * @param int  $expectedValue
     */
    #[DataProvider('getDisplayPriceDataProvider')]
    public function testGetUnitDisplayPriceInclTax(
        int $price,
        int $weeeTax,
        bool $weeeEnabled,
        bool $includeWeee,
        int $expectedValue
    ) {
        $this->weeeHelper->expects($this->once())
            ->method('isEnabled')
            ->willReturn($weeeEnabled);

        $this->weeeHelper->expects($this->any())
            ->method('getWeeeTaxInclTax')
            ->with($this->item)
            ->willReturn($weeeTax);

        $this->item->setPriceInclTax($price);

        $this->weeeHelper->expects($this->any())
            ->method('typeOfDisplay')
            ->with([WeeeDisplayConfig::DISPLAY_INCL_DESCR, WeeeDisplayConfig::DISPLAY_INCL], self::ZONE)
            ->willReturn($includeWeee);

        $this->assertEquals($expectedValue, $this->renderer->getUnitDisplayPriceInclTax());
    }

    /**
     * @param int  $price
     * @param int  $weeeTax
     * @param bool $weeeEnabled
     * @param bool $includeWeee
     * @param int  $expectedValue
     */
    #[DataProvider('getDisplayPriceDataProvider')]
    public function testGetBaseUnitDisplayPriceInclTax(
        int $price,
        int $weeeTax,
        bool $weeeEnabled,
        bool $includeWeee,
        int $expectedValue
    ) {
        $this->weeeHelper->expects($this->once())
            ->method('isEnabled')
            ->willReturn($weeeEnabled);

        $this->weeeHelper->expects($this->any())
            ->method('getBaseWeeeTaxInclTax')
            ->with($this->item)
            ->willReturn($weeeTax);

        $this->item->setBasePriceInclTax($price);

        $this->weeeHelper->expects($this->any())
            ->method('typeOfDisplay')
            ->with([WeeeDisplayConfig::DISPLAY_INCL_DESCR, WeeeDisplayConfig::DISPLAY_INCL], self::ZONE)
            ->willReturn($includeWeee);

        $this->assertEquals($expectedValue, $this->renderer->getBaseUnitDisplayPriceInclTax());
    }

    /**
     * @param int  $price
     * @param int  $weeeTax
     * @param bool $weeeEnabled
     * @param bool $includeWeee
     * @param int  $expectedValue
     */
    #[DataProvider('getDisplayPriceDataProvider')]
    public function testGetUnitDisplayPriceExclTax(
        int $price,
        int $weeeTax,
        bool $weeeEnabled,
        bool $includeWeee,
        int $expectedValue
    ) {
        $this->weeeHelper->expects($this->once())
            ->method('isEnabled')
            ->willReturn($weeeEnabled);

        $this->item->setWeeeTaxAppliedAmount($weeeTax);

        $this->item->setCalculationPrice($price);

        $this->weeeHelper->expects($this->any())
            ->method('typeOfDisplay')
            ->with([WeeeDisplayConfig::DISPLAY_INCL_DESCR, WeeeDisplayConfig::DISPLAY_INCL], self::ZONE)
            ->willReturn($includeWeee);

        $this->assertEquals($expectedValue, $this->renderer->getUnitDisplayPriceExclTax());
    }

    /**
     * @param int  $price
     * @param int  $weeeTax
     * @param bool $weeeEnabled
     * @param bool $includeWeee
     * @param int  $expectedValue
     */
    #[DataProvider('getDisplayPriceDataProvider')]
    public function testGetBaseUnitDisplayPriceExclTax(
        int $price,
        int $weeeTax,
        bool $weeeEnabled,
        bool $includeWeee,
        int $expectedValue
    ) {
        $this->weeeHelper->expects($this->once())
            ->method('isEnabled')
            ->willReturn($weeeEnabled);

        $this->item->setBaseWeeeTaxAppliedAmount($weeeTax);

        $this->item->setBaseRowTotal($price);

        $this->item->setQtyOrdered(1);

        $this->weeeHelper->expects($this->any())
            ->method('typeOfDisplay')
            ->with([WeeeDisplayConfig::DISPLAY_INCL_DESCR, WeeeDisplayConfig::DISPLAY_INCL], self::ZONE)
            ->willReturn($includeWeee);

        $this->assertEquals($expectedValue, $this->renderer->getBaseUnitDisplayPriceExclTax());
    }

    /**
     * @param int  $price
     * @param int  $weeeTax
     * @param bool $weeeEnabled
     * @param bool $includeWeee
     * @param int  $expectedValue
     */
    #[DataProvider('getDisplayPriceDataProvider')]
    public function testGetRowDisplayPriceExclTax(
        int $price,
        int $weeeTax,
        bool $weeeEnabled,
        bool $includeWeee,
        int $expectedValue
    ) {
        $this->weeeHelper->expects($this->once())
            ->method('isEnabled')
            ->willReturn($weeeEnabled);

        $this->item->setWeeeTaxAppliedRowAmount($weeeTax);

        $this->item->setRowTotal($price);

        $this->weeeHelper->expects($this->any())
            ->method('typeOfDisplay')
            ->with([WeeeDisplayConfig::DISPLAY_INCL_DESCR, WeeeDisplayConfig::DISPLAY_INCL], self::ZONE)
            ->willReturn($includeWeee);

        $this->assertEquals($expectedValue, $this->renderer->getRowDisplayPriceExclTax());
    }

    /**
     * @param int  $price
     * @param int  $weeeTax
     * @param bool $weeeEnabled
     * @param bool $includeWeee
     * @param int  $expectedValue
     */
    #[DataProvider('getDisplayPriceDataProvider')]
    public function testGetBaseRowDisplayPriceExclTax(
        int $price,
        int $weeeTax,
        bool $weeeEnabled,
        bool $includeWeee,
        int $expectedValue
    ) {
        $this->weeeHelper->expects($this->once())
            ->method('isEnabled')
            ->willReturn($weeeEnabled);

        $this->item->setBaseWeeeTaxAppliedRowAmnt($weeeTax);

        $this->item->setBaseRowTotal($price);

        $this->weeeHelper->expects($this->any())
            ->method('typeOfDisplay')
            ->with([WeeeDisplayConfig::DISPLAY_INCL_DESCR, WeeeDisplayConfig::DISPLAY_INCL], self::ZONE)
            ->willReturn($includeWeee);

        $this->assertEquals($expectedValue, $this->renderer->getBaseRowDisplayPriceExclTax());
    }

    /**
     * @param int  $price
     * @param int  $weeeTax
     * @param bool $weeeEnabled
     * @param bool $includeWeee
     * @param int  $expectedValue
     */
    #[DataProvider('getDisplayPriceDataProvider')]
    public function testGetRowDisplayPriceInclTax(
        int $price,
        int $weeeTax,
        bool $weeeEnabled,
        bool $includeWeee,
        int $expectedValue
    ) {
        $this->weeeHelper->expects($this->once())
            ->method('isEnabled')
            ->willReturn($weeeEnabled);

        $this->weeeHelper->expects($this->any())
            ->method('getRowWeeeTaxInclTax')
            ->with($this->item)
            ->willReturn($weeeTax);

        $this->item->setRowTotalInclTax($price);

        $this->weeeHelper->expects($this->any())
            ->method('typeOfDisplay')
            ->with([WeeeDisplayConfig::DISPLAY_INCL_DESCR, WeeeDisplayConfig::DISPLAY_INCL], self::ZONE)
            ->willReturn($includeWeee);

        $this->assertEquals($expectedValue, $this->renderer->getRowDisplayPriceInclTax());
    }

    /**
     * @param int  $price
     * @param int  $weeeTax
     * @param bool $weeeEnabled
     * @param bool $includeWeee
     * @param int  $expectedValue
     */
    #[DataProvider('getDisplayPriceDataProvider')]
    public function testGetBaseRowDisplayPriceInclTax(
        int $price,
        int $weeeTax,
        bool $weeeEnabled,
        bool $includeWeee,
        int $expectedValue
    ) {
        $this->weeeHelper->expects($this->once())
            ->method('isEnabled')
            ->willReturn($weeeEnabled);

        $this->weeeHelper->expects($this->any())
            ->method('getBaseRowWeeeTaxInclTax')
            ->with($this->item)
            ->willReturn($weeeTax);

        $this->item->setBaseRowTotalInclTax($price);

        $this->weeeHelper->expects($this->any())
            ->method('typeOfDisplay')
            ->with([WeeeDisplayConfig::DISPLAY_INCL_DESCR, WeeeDisplayConfig::DISPLAY_INCL], self::ZONE)
            ->willReturn($includeWeee);

        $this->assertEquals($expectedValue, $this->renderer->getBaseRowDisplayPriceInclTax());
    }

    /**
     * @return array
     */
    public static function getDisplayPriceDataProvider()
    {
        $data = [
            'weee_disabled_true' => [
                'price' => 100,
                'weeeTax' => 10,
                'weeeEnabled' => false,
                'includeWeee' => true,
                'expectedValue' => 100,
            ],
            'weee_disabled_false' => [
                'price' => 100,
                'weeeTax' => 10,
                'weeeEnabled' => false,
                'includeWeee' => false,
                'expectedValue' => 100,
            ],
            'weee_enabled_include_weee' => [
                'price' => 100,
                'weeeTax' => 10,
                'weeeEnabled' => true,
                'includeWeee' => true,
                'expectedValue' => 110,
            ],
            'weee_enabled_not_include_weee' => [
                'price' => 100,
                'weeeTax' => 10,
                'weeeEnabled' => true,
                'includeWeee' => false,
                'expectedValue' => 100,
            ],
        ];
        return $data;
    }

    /**
     * @param int  $rowTotal
     * @param int  $weeeTax
     * @param bool $weeeEnabled
     * @param int  $expectedValue
     */
    #[DataProvider('getFinalDisplayPriceDataProvider')]
    public function testGetFinalUnitDisplayPriceInclTax(
        int $rowTotal,
        int $weeeTax,
        bool $weeeEnabled,
        int $expectedValue
    ) {
        $this->weeeHelper->expects($this->once())
            ->method('isEnabled')
            ->willReturn($weeeEnabled);

        $this->weeeHelper->expects($this->any())
            ->method('getWeeeTaxInclTax')
            ->with($this->item)
            ->willReturn($weeeTax);

        $this->item->setPriceInclTax($rowTotal);

        $this->assertEquals($expectedValue, $this->renderer->getFinalUnitDisplayPriceInclTax());
    }

    /**
     * @param int  $rowTotal
     * @param int  $weeeTax
     * @param bool $weeeEnabled
     * @param int  $expectedValue
     */
    #[DataProvider('getFinalDisplayPriceDataProvider')]
    public function testGetBaseFinalUnitDisplayPriceInclTax(
        int $rowTotal,
        int $weeeTax,
        bool $weeeEnabled,
        int $expectedValue
    ) {
        $this->weeeHelper->expects($this->once())
            ->method('isEnabled')
            ->willReturn($weeeEnabled);

        $this->weeeHelper->expects($this->any())
            ->method('getBaseWeeeTaxInclTax')
            ->with($this->item)
            ->willReturn($weeeTax);

        $this->item->setBasePriceInclTax($rowTotal);

        $this->assertEquals($expectedValue, $this->renderer->getBaseFinalUnitDisplayPriceInclTax());
    }

    /**
     * @param int  $rowTotal
     * @param int  $weeeTax
     * @param bool $weeeEnabled
     * @param int  $expectedValue
     */
    #[DataProvider('getFinalDisplayPriceDataProvider')]
    public function testGetFinalUnitDisplayPriceExclTax(
        int $rowTotal,
        int $weeeTax,
        bool $weeeEnabled,
        int $expectedValue
    ) {
        $this->weeeHelper->expects($this->once())
            ->method('isEnabled')
            ->willReturn($weeeEnabled);

        $this->item->setWeeeTaxAppliedAmount($weeeTax);
      
        $this->item->setCalculationPrice($rowTotal);

        $this->assertEquals($expectedValue, $this->renderer->getFinalUnitDisplayPriceExclTax());
    }

    /**
     * @param int  $rowTotal
     * @param int  $weeeTax
     * @param bool $weeeEnabled
     * @param int  $expectedValue
     */
    #[DataProvider('getFinalDisplayPriceDataProvider')]
    public function testGetBaseFinalUnitDisplayPriceExclTax(
        int $rowTotal,
        int $weeeTax,
        bool $weeeEnabled,
        int $expectedValue
    ) {
        $this->weeeHelper->expects($this->once())
            ->method('isEnabled')
            ->willReturn($weeeEnabled);

        $this->item->setBaseWeeeTaxAppliedAmount($weeeTax);

        $this->item->setBaseRowTotal($rowTotal);

        $this->item->setQtyOrdered(1);

        $this->assertEquals($expectedValue, $this->renderer->getBaseFinalUnitDisplayPriceExclTax());
    }

    /**
     * @param int  $rowTotal
     * @param int  $weeeTax
     * @param bool $weeeEnabled
     * @param int  $expectedValue
     */
    #[DataProvider('getFinalDisplayPriceDataProvider')]
    public function testGetFianlRowDisplayPriceExclTax(
        int $rowTotal,
        int $weeeTax,
        bool $weeeEnabled,
        int $expectedValue
    ) {
        $this->weeeHelper->expects($this->once())
            ->method('isEnabled')
            ->willReturn($weeeEnabled);

        $this->item->setWeeeTaxAppliedRowAmount($weeeTax);

        $this->item->setRowTotal($rowTotal);

        $this->assertEquals($expectedValue, $this->renderer->getFinalRowDisplayPriceExclTax());
    }

    /**
     * @param int  $rowTotal
     * @param int  $weeeTax
     * @param bool $weeeEnabled
     * @param int  $expectedValue
     */
    #[DataProvider('getFinalDisplayPriceDataProvider')]
    public function testGetBaseFianlRowDisplayPriceExclTax(
        int $rowTotal,
        int $weeeTax,
        bool $weeeEnabled,
        int $expectedValue
    ) {
        $this->weeeHelper->expects($this->once())
            ->method('isEnabled')
            ->willReturn($weeeEnabled);

        $this->item->setBaseWeeeTaxAppliedRowAmnt($weeeTax);

        $this->item->setBaseRowTotal($rowTotal);

        $this->assertEquals($expectedValue, $this->renderer->getBaseFinalRowDisplayPriceExclTax());
    }

    /**
     * @param int  $rowTotal
     * @param int  $weeeTax
     * @param bool $weeeEnabled
     * @param int  $expectedValue
     */
    #[DataProvider('getFinalDisplayPriceDataProvider')]
    public function testGetFinalRowDisplayPriceInclTax(
        int $rowTotal,
        int $weeeTax,
        bool $weeeEnabled,
        int $expectedValue
    ) {
        $this->weeeHelper->expects($this->once())
            ->method('isEnabled')
            ->willReturn($weeeEnabled);

        $this->weeeHelper->expects($this->any())
            ->method('getRowWeeeTaxInclTax')
            ->with($this->item)
            ->willReturn($weeeTax);

        $this->item->setRowTotalInclTax($rowTotal);

        $this->assertEquals($expectedValue, $this->renderer->getFinalRowDisplayPriceInclTax());
    }

    /**
     * @param int  $rowTotal
     * @param int  $weeeTax
     * @param bool $weeeEnabled
     * @param int  $expectedValue
     */
    #[DataProvider('getFinalDisplayPriceDataProvider')]
    public function testGetBaseFinalRowDisplayPriceInclTax(
        int $rowTotal,
        int $weeeTax,
        bool $weeeEnabled,
        int $expectedValue
    ) {
        $this->weeeHelper->expects($this->once())
            ->method('isEnabled')
            ->willReturn($weeeEnabled);

        $this->weeeHelper->expects($this->any())
            ->method('getBaseRowWeeeTaxInclTax')
            ->with($this->item)
            ->willReturn($weeeTax);

        $this->item->setBaseRowTotalInclTax($rowTotal);

        $this->assertEquals($expectedValue, $this->renderer->getBaseFinalRowDisplayPriceInclTax());
    }

    /**
     * @return array
     */
    public static function getFinalDisplayPriceDataProvider()
    {
        $data = [
            'weee_disabled_true' => [
                'rowTotal' => 100,
                'weeeTax' => 10,
                'weeeEnabled' => false,
                'expectedValue' => 100,
            ],
            'weee_enabled_include_weee' => [
                'rowTotal' => 100,
                'weeeTax' => 10,
                'weeeEnabled' => true,
                'expectedValue' => 110,
            ],
        ];
        return $data;
    }

    public function testGetTotalAmount()
    {
        $rowTotal = 100;
        $taxAmount = 10;
        $discountTaxCompensationAmount = 2;
        $discountAmount = 20;
        $weeeAmount = 5;

        $expectedValue = 97;

        $itemMock = $this->getMockBuilder(\Magento\Sales\Model\Order\Item::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'getRowTotal',
                    'getTaxAmount',
                    'getDiscountTaxCompensationAmount',
                    'getDiscountAmount'
                ]
            )
            ->getMock();

        $itemMock->expects($this->once())
            ->method('getRowTotal')
            ->willReturn($rowTotal);

        $itemMock->expects($this->once())
            ->method('getTaxAmount')
            ->willReturn($taxAmount);

        $itemMock->expects($this->once())
            ->method('getDiscountTaxCompensationAmount')
            ->willReturn($discountTaxCompensationAmount);

        $itemMock->expects($this->once())
            ->method('getDiscountAmount')
            ->willReturn($discountAmount);

        $this->weeeHelper->expects($this->once())
            ->method('getRowWeeeTaxInclTax')
            ->with($itemMock)
            ->willReturn($weeeAmount);

        $this->assertEquals($expectedValue, $this->renderer->getTotalAmount($itemMock));
    }

    public function testGetBaseTotalAmount()
    {
        $baseRowTotal = 100;
        $baseTaxAmount = 10;
        $baseDiscountTaxCompensationAmount = 2;
        $baseDiscountAmount = 20;
        $baseWeeeAmount = 5;

        $expectedValue = $baseRowTotal + $baseTaxAmount + $baseDiscountTaxCompensationAmount -
            $baseDiscountAmount + $baseWeeeAmount;

        $itemMock = $this->getMockBuilder(\Magento\Sales\Model\Order\Item::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'getBaseRowTotal',
                    'getBaseTaxAmount',
                    'getBaseDiscountTaxCompensationAmount',
                    'getBaseDiscountAmount'
                ]
            )
            ->getMock();

        $itemMock->expects($this->once())
            ->method('getBaseRowTotal')
            ->willReturn($baseRowTotal);

        $itemMock->expects($this->once())
            ->method('getBaseTaxAmount')
            ->willReturn($baseTaxAmount);

        $itemMock->expects($this->once())
            ->method('getBaseDiscountTaxCompensationAmount')
            ->willReturn($baseDiscountTaxCompensationAmount);

        $itemMock->expects($this->once())
            ->method('getBaseDiscountAmount')
            ->willReturn($baseDiscountAmount);

        $this->weeeHelper->expects($this->once())
            ->method('getBaseRowWeeeTaxInclTax')
            ->with($itemMock)
            ->willReturn($baseWeeeAmount);

        $this->assertEquals($expectedValue, $this->renderer->getBaseTotalAmount($itemMock));
    }
}
