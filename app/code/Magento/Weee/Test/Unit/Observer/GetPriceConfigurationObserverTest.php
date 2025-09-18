<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Weee\Test\Unit\Observer;

use Magento\Bundle\Model\Product\Type;
use Magento\Catalog\Model\Product\Type\Simple;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Weee\Helper\Data;
use Magento\Weee\Observer\GetPriceConfigurationObserver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Unit Tests to cover GetPriceConfigurationObserver
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class GetPriceConfigurationObserverTest extends TestCase
{
    /**
     * Tests the methods that rely on the ScopeConfigInterface object to provide their return values
     */
    #[DataProvider('getPriceConfigurationProvider')]
    /**
     * @param bool  $hasWeeeAttributes
     * @param array $testArray
     * @param array $expectedArray
     */
    public function testGetPriceConfiguration($hasWeeeAttributes, $testArray, $expectedArray)
    {
        $configObj = new DataObject(
            [
                'config' => $testArray,
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
                'amount' => '16.0000',
            ]
        );

        $weeeHelper=$this->createMock(Data::class);
        $weeeHelper->expects($this->any())
            ->method('isEnabled')
            ->willReturn(true);

        $observerObject=$this->createMock(Observer::class);
        $observerObject->expects($this->any())
            ->method('getData')
            ->with('configObj')
            ->willReturn($configObj);

        $productInstance=$this->createMock(Simple::class);

        $product = $this->createProductMock();
        $product->setTypeInstance($productInstance);
        $product->setTypeId('simple');
        $product->setStoreId(null);

        $registry=$this->createMock(Registry::class);
        $registry->expects($this->any())
            ->method('registry')
            ->with('current_product')
            ->willReturn($product);

        if ($hasWeeeAttributes) {
            $weeeHelper->expects($this->any())
                ->method('getWeeeAttributesForBundle')
                ->willReturn(
                    [
                    1 => ['fpt1' => $weeeObject1],
                    2 => [
                        'fpt1' => $weeeObject1,
                        'fpt2' => $weeeObject2
                    ]
                    ]
                );
        } else {
            $weeeHelper->expects($this->any())
                ->method('getWeeeAttributesForBundle')
                ->willReturn(null);
        }

        $objectManager = new ObjectManager($this);
        /** @var GetPriceConfigurationObserver $weeeObserverObject */
        $weeeObserverObject = $objectManager->getObject(
            GetPriceConfigurationObserver::class,
            [
                'weeeData' => $weeeHelper,
                'registry' => $registry,
            ]
        );
        $weeeObserverObject->execute($observerObject);

        $this->assertEquals($expectedArray, $configObj->getData('config'));
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public static function getPriceConfigurationProvider()
    {
        return [
            "basic" => [
                'hasWeeeAttributes' => true,
                'testArray' => [
                    [
                        [
                            'optionId' => 1,
                            'prices' => [
                                'finalPrice' => ['amount' => 31.50],
                                'basePrice' => ['amount' => 33.50],
                            ],
                        ],
                        [
                            'optionId' => 2,
                            'prices' => [
                                'finalPrice' =>['amount' => 331.50],
                                'basePrice' => ['amount' => 333.50],
                            ],
                        ],
                    ],
                ],
                'expectedArray' => [
                    [
                        [
                            'optionId' => 1,
                            'prices' => [
                                'finalPrice' => ['amount' => 31.50],
                                'basePrice' => ['amount' => 33.50],
                                'weeePrice' => ['amount' => 46.5],
                                'weeePricefpt1' => ['amount' => 15],
                            ],
                        ],
                        [
                            'optionId' => 2,
                            'prices' => [
                                'finalPrice' =>['amount' => 331.50],
                                'basePrice' => ['amount' => 333.50],
                                'weeePrice' => ['amount' => 362.5],
                                'weeePricefpt1' => ['amount' => 15],
                                'weeePricefpt2' => ['amount' => 16],
                            ],
                        ],
                    ],
                ],
            ],

            "layered, with extra keys" => [
                'hasWeeeAttributes' => true,
                'testArray' => [
                    [
                        [
                            'prices' => [
                                'finalPrice' => ['amount' => 31.50],
                            ],
                            'somekey' => 0,
                        ],
                        [
                            [
                                [
                                    'prices' => [
                                        'finalPrice' =>['amount' => 321.50],
                                    ],
                                ],
                                'otherkey' => [ 1, 2 , 3],
                            ]
                        ],
                    ],
                ],
                'expectedArray' => [
                    [
                        [
                            'prices' => [
                                'finalPrice' => ['amount' => 31.50],
                                'weeePrice' => ['amount' => 31.50],
                            ],
                            'somekey' => 0,
                        ],
                        [
                            [
                                [
                                    'prices' => [
                                        'finalPrice' =>['amount' => 321.50],
                                        'weeePrice' => ['amount' => 321.50],
                                    ],
                                ],
                                'otherkey' => [ 1, 2 , 3],
                            ]
                        ],
                    ],
                ],
            ],

            "no Weee attributes, expect WeeePrice to be same as FinalPrice" => [
                'hasWeeeAttributes' => false,
                'testArray' => [
                    [
                        [
                            'optionId' => 1,
                            'prices' => [
                                'basePrice' => ['amount' => 10],
                                'finalPrice' => ['amount' => 11],
                            ],
                        ],
                    ],
                ],
                'expectedArray' => [
                    [
                        [
                            'optionId' => 1,
                            'prices' => [
                                'basePrice' => ['amount' => 10],
                                'finalPrice' => ['amount' => 11],
                                'weeePrice' => ['amount' => 11],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Create a mock for Product Type
     *
     * @return Type
     */
    private function createProductMock(): Type
    {
        return new class extends Type {
            /**
             * @var mixed
             */
            private $typeInstance = null;
            /**
             * @var mixed
             */
            private $typeId = null;
            /**
             * @var mixed
             */
            private $storeId = null;

            public function __construct()
            {
            }

            public function getTypeInstance()
            {
                return $this->typeInstance;
            }

            public function setTypeInstance($instance)
            {
                $this->typeInstance = $instance;
                return $this;
            }

            public function getTypeId()
            {
                return $this->typeId;
            }

            public function setTypeId($id)
            {
                $this->typeId = $id;
                return $this;
            }

            public function getStoreId()
            {
                return $this->storeId;
            }

            public function setStoreId($id)
            {
                $this->storeId = $id;
                return $this;
            }
        };
    }
}
