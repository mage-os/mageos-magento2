<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Weee\Test\Unit\Observer;

use Magento\Bundle\Model\Product\Type;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Tax\Model\Config as TaxConfig;
use Magento\Weee\Helper\Data;
use Magento\Weee\Model\Tax as WeeeDisplayConfig;
use Magento\Weee\Observer\UpdateProductOptionsObserver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Unit Tests to cover UpdateProductOptionsObserver
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class UpdateProductOptionsObserverTest extends TestCase
{
    /**
     * Tests the methods that rely on the ScopeConfigInterface object to provide their return values
     *
     * @param array $initialArray  The initial array that specifies the set of additional options
     * @param bool  $weeeEnabled   Whether the Weee module is assumed to be enabled
     * @param int   $weeeDisplay   Which Weee display is configured
     * @param int   $priceDisplay  Values are: including tax, excluding tax, or both including and excluding tax
     * @param array $expectedArray The revised array of the additional options
     */
    #[DataProvider('updateProductOptionsProvider')]
    public function testUpdateProductOptions($initialArray, $weeeEnabled, $weeeDisplay, $priceDisplay, $expectedArray)
    {
        $configObj = new DataObject(
            [
                'additional_options' => $initialArray,
            ]
        );

        $weeeObject1 = new DataObject(
            [
                'code' => 'fpt1',
                'amount' => '15.0000',
            ]
        );

        $weeeObject2 = new DataObject(
            [
                'code' => 'fpt2',
                'amount' => '7.0000',
            ]
        );

        $weeeHelper=$this->createMock(Data::class);
        $weeeHelper->method('isEnabled')->willReturn($weeeEnabled);
        $weeeHelper->method('isDisplayIncl')
            ->willReturn($weeeDisplay == WeeeDisplayConfig::DISPLAY_INCL);
        $weeeHelper->method('isDisplayExclDescIncl')
            ->willReturn($weeeDisplay == WeeeDisplayConfig::DISPLAY_EXCL_DESCR_INCL);
        $weeeHelper->method('isDisplayExcl')
            ->willReturn($weeeDisplay == WeeeDisplayConfig::DISPLAY_EXCL);
        $weeeHelper->method('getWeeeAttributesForBundle')
            ->willReturn([['fpt1' => $weeeObject1], ['fpt1'=>$weeeObject1, 'fpt2'=>$weeeObject2]]);

        $taxHelper=$this->createMock(\Magento\Tax\Helper\Data::class);
        $taxHelper->method('displayPriceExcludingTax')
            ->willReturn($priceDisplay == TaxConfig::DISPLAY_TYPE_EXCLUDING_TAX);
        $taxHelper->method('priceIncludesTax')->willReturn(true);

        $responseObject = $this->createResponseObjectMock();
        $responseObject->setResponseObject($configObj);

        $observerObject=$this->createPartialMock(Observer::class, ['getEvent']);
        $observerObject->expects($this->any())
            ->method('getEvent')
            ->willReturn($responseObject);

        $product = $this->createPartialMock(Product::class, ['getStoreId', 'getTypeId']);
        $product->method('getStoreId')->willReturn(1);
        $product->method('getTypeId')->willReturn('bundle');

        $registry=$this->createMock(Registry::class);
        $registry->expects($this->any())
            ->method('registry')
            ->with('current_product')
            ->willReturn($product);

        $objectManager = new ObjectManager($this);
        /** @var UpdateProductOptionsObserver $weeeObserverObject */
        $weeeObserverObject = $objectManager->getObject(
            UpdateProductOptionsObserver::class,
            [
                'weeeData' => $weeeHelper,
                'taxData' => $taxHelper,
                'registry' => $registry,
            ]
        );
        $weeeObserverObject->execute($observerObject);

        $this->assertEquals($expectedArray, $configObj->getData('additional_options'));
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public static function updateProductOptionsProvider()
    {
        return [
            'weee not enabled' => [
                'initialArray' => [
                    'TOTAL_BASE_CALCULATION' => 'TOTAL_BASE_CALCULATION',
                    'optionTemplate' => '<%= data.label %><% if (data.finalPrice.value) '
                        . '{ %> +<%- data.finalPrice.formatted %><% } %>',
                ],
                'weeeEnabled' => false,
                'weeeDisplay' => WeeeDisplayConfig::DISPLAY_INCL,         // has no effect for this scenario
                'priceDisplay' => TaxConfig::DISPLAY_TYPE_EXCLUDING_TAX,  // has no effect for this scenario
                'expectedArray' => [
                    'TOTAL_BASE_CALCULATION' => 'TOTAL_BASE_CALCULATION',
                    'optionTemplate' => '<%= data.label %><% if (data.finalPrice.value) '
                        . '{ %> +<%- data.finalPrice.formatted %><% } %>',
                ],
            ],

            'weee enabled, and display with Weee included in the price' => [
                'initialArray' => [
                    'TOTAL_BASE_CALCULATION' => 'TOTAL_BASE_CALCULATION',
                    'optionTemplate' => '<%= data.label %><% if (data.basePrice.value) '
                        . '{ %> +<%- data.basePrice.formatted %><% } %>',
                ],
                'weeeEnabled' => true,
                'weeeDisplay' => WeeeDisplayConfig::DISPLAY_INCL,
                'priceDisplay' => TaxConfig::DISPLAY_TYPE_INCLUDING_TAX,
                'expectedArray' => [
                    'TOTAL_BASE_CALCULATION' => 'TOTAL_BASE_CALCULATION',
                    'optionTemplate' => '<%= data.label %><% if (data.basePrice.value) '
                        . '{ %> +<%- data.basePrice.formatted %><% } %>',
                ],
            ],

            'weee enabled, and display with Weee included in the price, and include the Weee descriptions' => [
                'initialArray' => [
                    'TOTAL_BASE_CALCULATION' => 'TOTAL_BASE_CALCULATION',
                    'optionTemplate' => '<%= data.label %><% if (data.basePrice.value) '
                        . '{ %> +<%- data.basePrice.formatted %><% } %>',
                ],
                'weeeEnabled' => true,
                'weeeDisplay' => WeeeDisplayConfig::DISPLAY_INCL_DESCR,
                'priceDisplay' => TaxConfig::DISPLAY_TYPE_INCLUDING_TAX,
                'expectedArray' => [
                    'TOTAL_BASE_CALCULATION' => 'TOTAL_BASE_CALCULATION',
                    'optionTemplate' => '<%= data.label %><% if (data.basePrice.value) '
                        . '{ %> +<%- data.basePrice.formatted %><% } %> <% if (data.weeePricefpt1) '
                        . '{ %>  (: <%- data.weeePricefpt1.formatted %>)<% } %> '
                        . '<% if (data.weeePricefpt2) { %>  (: <%- data.weeePricefpt2.formatted %>)<% } %>',
                ],
            ],

            'weee enabled, and display with ExclDescIncl' => [
                'initialArray' => [
                    'TOTAL_BASE_CALCULATION' => 'TOTAL_BASE_CALCULATION',
                    'optionTemplate' => '<%= data.label %><% if (data.basePrice.value) '
                        . '{ %> +<%- data.basePrice.formatted %><% } %>',
                ],
                'weeeEnabled' => true,
                'weeeDisplay' => WeeeDisplayConfig::DISPLAY_EXCL_DESCR_INCL,
                'priceDisplay' => TaxConfig::DISPLAY_TYPE_INCLUDING_TAX,
                'expectedArray' => [
                    'TOTAL_BASE_CALCULATION' => 'TOTAL_BASE_CALCULATION',
                    'optionTemplate' => '<%= data.label %><% if (data.basePrice.value) '
                        . '{ %> +<%- data.basePrice.formatted %><% } %> <% if (data.weeePricefpt1) '
                        . '{ %>  (: <%- data.weeePricefpt1.formatted %>)<% } %> '
                        . '<% if (data.weeePricefpt2) { %>  (: <%- data.weeePricefpt2.formatted %>)<% } %> '
                        . '<% if (data.weeePrice) { %><%- data.weeePrice.formatted %><% } %>',
                ],
            ],

            'weee enabled, and display prices including tax but without Weee' => [
                'initialArray' => [
                    'TOTAL_BASE_CALCULATION' => 'TOTAL_BASE_CALCULATION',
                ],
                'weeeEnabled' => true,
                'weeeDisplay' => WeeeDisplayConfig::DISPLAY_EXCL,
                'priceDisplay' => TaxConfig::DISPLAY_TYPE_INCLUDING_TAX,
                'expectedArray' => [
                    'TOTAL_BASE_CALCULATION' => 'TOTAL_BASE_CALCULATION',
                    'optionTemplate' => '<%- data.label %><% if (data.finalPrice.value) '
                        . '{ %> +<%- data.finalPrice.formatted %><% } %>',
                ],
            ],

            'weee enabled, and display prices excluding tax but without Weee' => [
                'initialArray' => [
                    'TOTAL_BASE_CALCULATION' => 'TOTAL_BASE_CALCULATION',
                ],
                'weeeEnabled' => true,
                'weeeDisplay' => WeeeDisplayConfig::DISPLAY_EXCL,
                'priceDisplay' => TaxConfig::DISPLAY_TYPE_EXCLUDING_TAX,
                'expectedArray' => [
                    'TOTAL_BASE_CALCULATION' => 'TOTAL_BASE_CALCULATION',
                    'optionTemplate' => '<%- data.label %><% if (data.basePrice.value) '
                        . '{ %> +<%- data.basePrice.formatted %><% } %>',
                ],
            ],
        ];
    }

    /**
     * Create a mock for Response Object
     *
     * @return Observer
     */
    private function createResponseObjectMock(): Observer
    {
        $observer = $this->createPartialMock(Observer::class, []);
        $reflection = new \ReflectionClass($observer);
        $property = $reflection->getProperty('_data');
        $property->setValue($observer, []);
        return $observer;
    }
}
