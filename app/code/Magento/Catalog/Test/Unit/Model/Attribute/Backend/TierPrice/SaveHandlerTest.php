<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model\Attribute\Backend\TierPrice;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Framework\DataObject;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Backend\TierPrice\SaveHandler;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Tierprice;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for \Magento\Catalog\Model\Product\Attribute\Backend\TierPrice\SaveHandler
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SaveHandlerTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var SaveHandler|MockObject
     */
    private $saveHandler;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManager;

    /**
     * @var ProductAttributeRepositoryInterface|MockObject
     */
    private $attributeRepository;

    /**
     * @var GroupManagementInterface|MockObject
     */
    private $groupManagement;

    /**
     * @var MetadataPool|MockObject
     */
    private $metadataPoll;

    /**
     * @var Tierprice|MockObject
     */
    private $tierPriceResource;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->attributeRepository = $this->createMock(ProductAttributeRepositoryInterface::class);
        $this->groupManagement = $this->createMock(GroupManagementInterface::class);
        $this->metadataPoll = $this->getMockBuilder(MetadataPool::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMetadata'])
            ->getMock();
        $this->tierPriceResource = $this->getMockBuilder(Tierprice::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['savePriceData', 'loadPriceData'])
            ->getMock();

        $this->saveHandler = $this->objectManager->getObject(
            SaveHandler::class,
            [
                'storeManager' => $this->storeManager,
                'attributeRepository' => $this->attributeRepository,
                'groupManagement' => $this->groupManagement,
                'metadataPoll' => $this->metadataPoll,
                'tierPriceResource' => $this->tierPriceResource
            ]
        );
    }

    /**
     * @return void
     */
    public function testExecute(): void
    {
        $tierPrices = [
            ['website_id' => 0, 'price_qty' => 2, 'cust_group' => 0, 'price' => 10],
            ['website_id' => 0, 'price_qty' => 3, 'cust_group' => 3200, 'price' => null, 'percentage_value' => 20]
        ];
        $linkField = 'entity_id';
        $productId = 10;

        /** @var ProductInterface $product */
        $product = new class {
            private $data = [];
            private $storeId = 0;
            
            public function __construct()
            {
            }
            
            public function getData($key = null)
            {
                if ($key === null) {
                    return $this->data;
                }
                return $this->data[$key] ?? null;
            }
            
            public function setData($key, $value = null)
            {
                if (is_array($key)) {
                    $this->data = array_merge($this->data, $key);
                } else {
                    $this->data[$key] = $value;
                }
                return $this;
            }
            
            public function getStoreId()
            {
                return $this->storeId;
            }
            
            public function setStoreId($storeId)
            {
                $this->storeId = $storeId;
                return $this;
            }
            
            public function setGetDataResult($key, $value)
            {
                $this->data[$key] = $value;
                return $this;
            }
        };
        $product->setGetDataResult('tier_price', $tierPrices);
        $product->setGetDataResult('entity_id', $productId);
        $product->setStoreId(0);
        $product->setData('tier_price_changed', 1);
        $store = $this->createMock(StoreInterface::class);
        $store->expects($this->atLeastOnce())->method('getWebsiteId')->willReturn(0);
        $this->storeManager->expects($this->atLeastOnce())->method('getStore')->willReturn($store);
        /** @var ProductAttributeInterface $attribute */
        $attribute = new class {
            private $name = '';
            private $isScopeGlobal = false;
            
            public function __construct()
            {
            }
            
            public function getName()
            {
                return $this->name;
            }
            
            public function setName($name)
            {
                $this->name = $name;
                return $this;
            }
            
            public function isScopeGlobal()
            {
                return $this->isScopeGlobal;
            }
            
            public function setIsScopeGlobal($isScopeGlobal)
            {
                $this->isScopeGlobal = $isScopeGlobal;
                return $this;
            }
        };
        $attribute->setName('tier_price');
        $attribute->setIsScopeGlobal(true);
        $this->attributeRepository->expects($this->atLeastOnce())->method('get')->with('tier_price')
            ->willReturn($attribute);
        $productMetadata = $this->createMock(EntityMetadataInterface::class);
        $productMetadata->expects($this->atLeastOnce())->method('getLinkField')->willReturn($linkField);
        $this->metadataPoll->expects($this->atLeastOnce())->method('getMetadata')
            ->with(ProductInterface::class)
            ->willReturn($productMetadata);
        $customerGroup = $this->createMock(GroupInterface::class);
        $customerGroup->expects($this->atLeastOnce())->method('getId')->willReturn(3200);
        $this->groupManagement->expects($this->atLeastOnce())->method('getAllCustomersGroup')
            ->willReturn($customerGroup);
        $this->tierPriceResource->expects($this->atLeastOnce())->method('savePriceData')->willReturnSelf();

        $this->assertEquals($product, $this->saveHandler->execute($product));
    }

    /**
     * @return void
     */
    public function testExecuteWithException(): void
    {
        $this->expectException('Magento\Framework\Exception\InputException');
        $this->expectExceptionMessage('Tier prices data should be array, but actually other type is received');
        /** @var ProductAttributeInterface $attribute */
        $attribute = new class {
            private $name = '';
            private $isScopeGlobal = false;
            
            public function __construct()
            {
            }
            
            public function getName()
            {
                return $this->name;
            }
            
            public function setName($name)
            {
                $this->name = $name;
                return $this;
            }
            
            public function isScopeGlobal()
            {
                return $this->isScopeGlobal;
            }
            
            public function setIsScopeGlobal($isScopeGlobal)
            {
                $this->isScopeGlobal = $isScopeGlobal;
                return $this;
            }
        };
        $attribute->setName('tier_price');
        $this->attributeRepository->expects($this->atLeastOnce())->method('get')->with('tier_price')
            ->willReturn($attribute);
        /** @var ProductInterface $product */
        $product = new class {
            private $data = [];
            private $storeId = 0;
            private $origData = [];
            
            public function __construct()
            {
            }
            
            public function getData($key = null)
            {
                if ($key === null) {
                    return $this->data;
                }
                return $this->data[$key] ?? null;
            }
            
            public function setData($key, $value = null)
            {
                if (is_array($key)) {
                    $this->data = array_merge($this->data, $key);
                } else {
                    $this->data[$key] = $value;
                }
                return $this;
            }
            
            public function getStoreId()
            {
                return $this->storeId;
            }
            
            public function setStoreId($storeId)
            {
                $this->storeId = $storeId;
                return $this;
            }
            
            public function getOrigData($key = null)
            {
                if ($key === null) {
                    return $this->origData;
                }
                return $this->origData[$key] ?? null;
            }
            
            public function setOrigData($key, $value = null)
            {
                if (is_array($key)) {
                    $this->origData = array_merge($this->origData, $key);
                } else {
                    $this->origData[$key] = $value;
                }
                return $this;
            }
            
            public function setGetDataResult($key, $value)
            {
                $this->data[$key] = $value;
                return $this;
            }
        };
        $product->setGetDataResult('tier_price', 1);

        $this->saveHandler->execute($product);
    }

    /**
     * @param array $tierPrices
     * @param array $tierPricesStored
     * @param array $tierPricesExpected
     *
     * @return void
     */
    #[DataProvider('executeWithWebsitePriceDataProvider')]
    public function testExecuteWithWebsitePrice(
        array $tierPrices,
        array $tierPricesStored,
        array $tierPricesExpected
    ): void {
        $productId = 10;
        $linkField = 'entity_id';

        /** @var ProductInterface $product */
        $product = new class {
            private $data = [];
            private $storeId = 0;
            
            public function __construct()
            {
            }
            
            public function getData($key = null)
            {
                if ($key === null) {
                    return $this->data;
                }
                return $this->data[$key] ?? null;
            }
            
            public function setData($key, $value = null)
            {
                if (is_array($key)) {
                    $this->data = array_merge($this->data, $key);
                } else {
                    $this->data[$key] = $value;
                }
                return $this;
            }
            
            public function getStoreId()
            {
                return $this->storeId;
            }
            
            public function setStoreId($storeId)
            {
                $this->storeId = $storeId;
                return $this;
            }
            
            public function setGetDataResult($key, $value)
            {
                $this->data[$key] = $value;
                return $this;
            }
        };
        $product->setGetDataResult('tier_price', $tierPrices);
        $product->setGetDataResult('entity_id', $productId);
        $product->setStoreId(0);
        $product->setData('tier_price_changed', 1);
        $store = $this->createMock(StoreInterface::class);
        $store->expects($this->atLeastOnce())->method('getWebsiteId')->willReturn(1);
        $this->storeManager->expects($this->atLeastOnce())->method('getStore')->willReturn($store);
        /** @var ProductAttributeInterface $attribute */
        $attribute = new class {
            private $name = '';
            private $isScopeGlobal = false;
            
            public function __construct()
            {
            }
            
            public function getName()
            {
                return $this->name;
            }
            
            public function setName($name)
            {
                $this->name = $name;
                return $this;
            }
            
            public function isScopeGlobal()
            {
                return $this->isScopeGlobal;
            }
            
            public function setIsScopeGlobal($isScopeGlobal)
            {
                $this->isScopeGlobal = $isScopeGlobal;
                return $this;
            }
        };
        $attribute->setName('tier_price');
        $attribute->setIsScopeGlobal(false);
        $this->attributeRepository->expects($this->atLeastOnce())->method('get')->with('tier_price')
            ->willReturn($attribute);
        $productMetadata = $this->createMock(EntityMetadataInterface::class);
        $productMetadata->expects($this->atLeastOnce())->method('getLinkField')->willReturn($linkField);
        $this->metadataPoll->expects($this->atLeastOnce())->method('getMetadata')
            ->with(ProductInterface::class)
            ->willReturn($productMetadata);
        $customerGroup = $this->createMock(GroupInterface::class);
        $customerGroup->expects($this->atLeastOnce())->method('getId')->willReturn(3200);
        $this->groupManagement->expects($this->atLeastOnce())->method('getAllCustomersGroup')
            ->willReturn($customerGroup);
        $this->tierPriceResource
            ->method('savePriceData')
            ->willReturnCallback(function (...$args) use ($tierPricesExpected) {
                static $index = 0;
                $expectedArgs = [
                    [new DataObject($tierPricesExpected[0])],
                    [new DataObject($tierPricesExpected[1])],
                    [new DataObject($tierPricesExpected[2])]
                ];
                $returnValue = $this->tierPriceResource;
                $index++;
                return $args === $expectedArgs[$index - 1] ? $returnValue : null;
            });
        
        $this->tierPriceResource
            ->expects($this->atLeastOnce())
            ->method('loadPriceData')
            ->willReturn($tierPricesStored);

        $this->assertEquals($product, $this->saveHandler->execute($product));
    }

    /**
     * @return array
     */
    public static function executeWithWebsitePriceDataProvider(): array
    {
        $productId = 10;
        return [[
            'tierPrices' => [
                [
                    'price_id' => 1,
                    'website_id' => 0,
                    'price_qty' => 1,
                    'cust_group' => 0,
                    'price' => 10,
                    'product_id' => $productId
                ],
                [
                    'price_id' => 2,
                    'website_id' => 1,
                    'price_qty' => 2,
                    'cust_group' => 3200,
                    'price' => null,
                    'percentage_value' => 20,
                    'product_id' => $productId
                ]
            ],
            'tierPricesStored' => [
                [
                    'price_id' => 3,
                    'website_id' => 1,
                    'price_qty' => 3,
                    'cust_group' => 0,
                    'price' => 30,
                    'product_id' => $productId
                ]
            ],
            'tierPricesExpected' => [
                [
                    'website_id' => 0,
                    'qty' => 1,
                    'customer_group_id' => 0,
                    'all_groups' => 0,
                    'value' => 10,
                    'percentage_value' => null,
                    'entity_id' => $productId
                ],
                [
                    'website_id' => 1,
                    'qty' => 2,
                    'customer_group_id' => 0,
                    'all_groups' => 1,
                    'value' => null,
                    'percentage_value' => 20,
                    'entity_id' => $productId
                ],
                [
                    'website_id' => 1,
                    'qty' => 3,
                    'customer_group_id' => 0,
                    'all_groups' => 0,
                    'value' => 30,
                    'percentage_value' => null,
                    'entity_id' => $productId
                ]
            ]
        ]];
    }
}
