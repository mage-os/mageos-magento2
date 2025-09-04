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


namespace Magento\Wishlist\Test\Unit\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\Catalog\Model\ResourceModel\Url;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Wishlist\Model\Item;
use Magento\Wishlist\Model\Item\Option;
use Magento\Wishlist\Model\Item\OptionFactory;
use Magento\Wishlist\Model\ResourceModel\Item\Collection;
use Magento\Wishlist\Model\ResourceModel\Item\Option\CollectionFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class ItemTest extends TestCase
{
    /**
     * @var Item
     */
    protected $model;

    /**
     * @var array
     */
    protected $mocks;

    protected function setUp(): void
    {
        $context = $this->createMock(Context::class);
        $this->mocks = [
            'registry' => $this->createMock(Registry::class),
            'storeManager' => $this->createMock(StoreManagerInterface::class),
            'date' => $this->createMock(DateTime::class),
            'catalogUrl' => $this->createMock(Url::class),
            'optionFactory' => $this->createMock(OptionFactory::class), // @phpstan-ignore-line
            'itemOptFactory' => $this->createPartialMock(CollectionFactory::class, ['create']), // @phpstan-ignore-line
            'productTypeConfig' => $this->createMock(ConfigInterface::class),
            'productRepository' => $this->createMock(ProductRepositoryInterface::class),
            'resource' => $this->createMock(\Magento\Wishlist\Model\ResourceModel\Item::class),
            'collection' => $this->createMock(Collection::class),
            'serializer' => $this->createMock(Json::class)
        ];

        $this->model = new Item(
            $context,
            $this->mocks['registry'],
            $this->mocks['storeManager'],
            $this->mocks['date'],
            $this->mocks['catalogUrl'],
            $this->mocks['optionFactory'],
            $this->mocks['itemOptFactory'],
            $this->mocks['productTypeConfig'],
            $this->mocks['productRepository'],
            $this->mocks['resource'],
            $this->mocks['collection'],
            [],
            $this->mocks['serializer']
        );
    }

    /**
     * @dataProvider getOptionsDataProvider
     */
    public function testAddGetOptions($code, $option)
    {
        $this->assertEmpty($this->model->getOptions());
        
        if (is_callable($option)) {
            $option = $option($this);
        }
        
        $optionMock = $this->createMock(Option::class);
        $optionMock->expects($this->any())
            ->method('setData')
            ->willReturnSelf();
        $optionMock->method('setItem')
            ->willReturnSelf();
        $optionMock->method('getData')
            ->with('code')
            ->willReturn($code);
        $optionMock->method('isDeleted')
            ->willReturn(false);

        $this->mocks['optionFactory']->method('create')->willReturn($optionMock);
        $this->model->addOption($option);
        $this->assertCount(1, $this->model->getOptions());
    }

    /**
     * @dataProvider getOptionsDataProvider
     */
    public function testRemoveOptionByCode($code, $option)
    {
        $this->assertEmpty($this->model->getOptions());
        
        if (is_callable($option)) {
            $option = $option($this);
        }
        
        $optionMock = new class($code) extends Option {
            /**
             * @var string
             */
            /**
             * @var string
             */
            private $code;
            
            public function __construct($code)
            {
                $this->code = $code;
            }
            
            public function getCode()
            {
                return $this->code;
            }
            
            public function setData($key, $value = null)
            {
                return $this;
            }
            
            public function setItem($item)
            {
                return $this;
            }
            
            public function isDeleted($isDeleted = null)
            {
                $_ = [$isDeleted];
                unset($_);
                if ($isDeleted !== null) {
                    return $this;
                }
                return true;
            }
        };
        $this->mocks['optionFactory']->method('create')->willReturn($optionMock);
        $this->model->addOption($option);
        $this->assertCount(1, $this->model->getOptions());
        $this->model->removeOption($code);
        $this->assertTrue(true);
    }

    protected function getMockForOptionClass()
    {
        $optionMock = new class() extends Option {
            public function __construct()
            {
            }
            
            public function getCode()
            {
                return 'second_key';
            }
            
            public function setItem($item)
            {
                return $this;
            }
            
            public function isDeleted($isDeleted = null)
            {
                $_ = [$isDeleted];
                unset($_);
                if ($isDeleted !== null) {
                    return $this;
                }
                return false;
            }
        };
        return $optionMock;
    }

    protected function getMockForProductClass()
    {
        $optionMock = new class() extends Option {
            public function __construct()
            {
            }
            
            public function getCode()
            {
                return 'third_key';
            }
            
            public function setItem($item)
            {
                return $this;
            }
            
            public function isDeleted($isDeleted = null)
            {
                $_ = [$isDeleted];
                unset($_);
                if ($isDeleted !== null) {
                    return $this;
                }
                return false;
            }
        };
        return $optionMock;
    }

    /**
     * @return array
     */
    public static function getOptionsDataProvider()
    {
        $optionMock = static fn (self $testCase) => $testCase->getMockForOptionClass();

        $productMock = static fn (self $testCase) => $testCase->getMockForProductClass();
        return [
            ['first_key', ['code' => 'first_key', 'value' => 'first_data']],
            ['second_key', $optionMock],
            ['third_key', $productMock],
        ];
    }

    public function testCompareOptionsPositive()
    {
        $code = 'someOption';
        $optionValue = 100;
        
        $optionsOneMock = new class($code, $optionValue) extends Option {
            /**
             * @var string
             */
            private $code;
            /**
             * @var mixed
             */
            private $value;
            
            public function __construct($code, $value)
            {
                $this->code = $code;
                $this->value = $value;
            }
            
            public function getCode()
            {
                return $this->code;
            }
            
            public function getValue()
            {
                return $this->value;
            }
        };
        
        $optionsTwoMock = new class($code, $optionValue) extends Option {
            /**
             * @var string
             */
            private $code;
            /**
             * @var mixed
             */
            private $value;
            
            public function __construct($code, $value)
            {
                $this->code = $code;
                $this->value = $value;
            }
            
            public function getCode()
            {
                return $this->code;
            }
            
            public function getValue()
            {
                return $this->value;
            }
        };

        $result = $this->model->compareOptions(
            [$optionsOneMock],
            [$code => $optionsTwoMock]
        );

        $this->assertTrue($result);
    }

    public function testCompareOptionsNegative()
    {
        $code = 'someOption';
        $optionOneValue = 100;
        $optionTwoValue = 200;
        
        $optionsOneMock = new class($code, $optionOneValue) extends Option {
            /**
             * @var string
             */
            private $code;
            /**
             * @var mixed
             */
            private $value;
            
            public function __construct($code, $value)
            {
                $this->code = $code;
                $this->value = $value;
            }
            
            public function getCode()
            {
                return $this->code;
            }
            
            public function getValue()
            {
                return $this->value;
            }
        };
        
        $optionsTwoMock = new class($code, $optionTwoValue) extends Option {
            /**
             * @var string
             */
            private $code;
            /**
             * @var mixed
             */
            private $value;
            
            public function __construct($code, $value)
            {
                $this->code = $code;
                $this->value = $value;
            }
            
            public function getCode()
            {
                return $this->code;
            }
            
            public function getValue()
            {
                return $this->value;
            }
        };

        $result = $this->model->compareOptions(
            [$optionsOneMock],
            [$code => $optionsTwoMock]
        );

        $this->assertFalse($result);
    }

    public function testCompareOptionsNegativeOptionsTwoHaveNotOption()
    {
        $code = 'someOption';
        
        $optionsOneMock = new class($code) extends Option {
            /**
             * @var string
             */
            private $code;
            
            public function __construct($code)
            {
                $this->code = $code;
            }
            
            public function getCode()
            {
                return $this->code;
            }
        };
        
        $optionsTwoMock = new class() extends Option {
            public function __construct()
            {
            }
        };

        $result = $this->model->compareOptions(
            [$optionsOneMock],
            ['someOneElse' => $optionsTwoMock]
        );

        $this->assertFalse($result);
    }

    public function testSetAndSaveItemOptions()
    {
        $this->assertEmpty($this->model->getOptions());
        $firstOptionMock = $this->createFirstOptionMock('first_code', true);
        $secondOptionMock = $this->createSecondOptionMock('second_code', false);

        $this->model->setOptions([$firstOptionMock, $secondOptionMock]);
        $this->assertNull($this->model->isOptionsSaved());
        $this->model->saveItemOptions();
        $this->assertTrue($this->model->isOptionsSaved());
        $this->assertEquals(1, $firstOptionMock->getDeleteCount());
        $this->assertEquals(1, $secondOptionMock->getSaveCount());
    }

    public function testGetProductWithException()
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Cannot specify product.');
        $this->model->getProduct();
    }

    public function testGetProduct()
    {
        $productId = 1;
        $storeId = 0;
        $this->model->setData('product_id', $productId);
        $this->model->setData('store_id', $storeId);
        $productMock = $this->createPartialMock(Product::class, [
            'setCustomOptions',
            'setFinalPrice'
        ]);
        $productMock->expects($this->any())
            ->method('setFinalPrice')
            ->with(null);
        $productMock->expects($this->any())
            ->method('setCustomOptions')
            ->with([]);
        $this->mocks['productRepository']->expects($this->once())
            ->method('getById')
            ->with($productId, false, $storeId, true)
            ->willReturn($productMock);
        $this->assertEquals($productMock, $this->model->getProduct());
    }

    private function createFirstOptionMock($code, $deleted)
    {
        return new class($code, $deleted) extends Option {
            /**
             * @var string
             */
            private $code;
            /**
             * @var bool
             */
            private $deleted;
            /**
             * @var int
             */
            private $deleteCount = 0;
            
            public function __construct($code, $deleted)
            {
                $this->code = $code;
                $this->deleted = $deleted;
            }
            
            public function getCode()
            {
                return $this->code;
            }
            
            public function setItem($item)
            {
                return $this;
            }
            
            public function isDeleted($isDeleted = null)
            {
                if ($isDeleted !== null) {
                    $this->deleted = $isDeleted;
                }
                return $this->deleted;
            }
            
            public function delete()
            {
                $this->deleteCount++;
                return $this;
            }
            
            public function getDeleteCount()
            {
                return $this->deleteCount;
            }
        };
    }

    private function createSecondOptionMock($code, $deleted)
    {
        return new class($code, $deleted) extends Option {
            /**
             * @var string
             */
            private $code;
            /**
             * @var bool
             */
            private $deleted;
            /**
             * @var int
             */
            private $saveCount = 0;
            
            public function __construct($code, $deleted)
            {
                $this->code = $code;
                $this->deleted = $deleted;
            }
            
            public function getCode()
            {
                return $this->code;
            }
            
            public function setItem($item)
            {
                return $this;
            }
            
            public function isDeleted($isDeleted = null)
            {
                if ($isDeleted !== null) {
                    $this->deleted = $isDeleted;
                }
                return $this->deleted;
            }
            
            public function save()
            {
                $this->saveCount++;
                return $this;
            }
            
            public function getSaveCount()
            {
                return $this->saveCount;
            }
        };
    }
}
