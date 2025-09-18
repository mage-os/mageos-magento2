<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Weee\Test\Unit\Model;

use Magento\Catalog\Model\Product;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Session;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Eav\Model\Entity\AttributeFactory;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\Model\Context;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\Group;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use Magento\Tax\Helper\Data;
use Magento\Tax\Model\Calculation;
use Magento\Tax\Model\CalculationFactory;
use Magento\Weee\Model\Config;
use Magento\Weee\Model\ResourceModel\Tax as ResourceModelTax;
use Magento\Weee\Model\Tax;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class TaxTest extends TestCase
{
    /**
     * @var Tax
     */
    protected $model;

    /**
     * @var MockObject
     */
    protected $context;

    /**
     * @var MockObject
     */
    protected $registry;

    /**
     * @var MockObject
     */
    protected $attributeFactory;

    /**
     * @var MockObject
     */
    protected $storeManager;

    /**
     * @var MockObject
     */
    protected $calculationFactory;

    /**
     * @var MockObject
     */
    protected $customerSession;

    /**
     * @var MockObject
     */
    protected $accountManagement;

    /**
     * @var MockObject
     */
    protected $taxData;

    /**
     * @var MockObject
     */
    protected $resource;

    /**
     * @var MockObject
     */
    protected $weeeConfig;

    /**
     * @var MockObject
     */
    protected $priceCurrency;

    /**
     * @var MockObject
     */
    protected $resourceCollection;

    /**
     * @var MockObject
     */
    protected $data;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->context = $this->createMock(Context::class);
        $this->registry = $this->createMock(Registry::class);

        $this->attributeFactory = $this->createPartialMock(AttributeFactory::class, ['create']);

        $this->storeManager = $this->createPartialMock(\Magento\Store\Model\StoreManager::class, ['getWebsite']);

        $this->calculationFactory = $this->createPartialMock(CalculationFactory::class, ['create']);

        $this->customerSession = $this->createCustomerSessionMock();
        $this->customerSession->setCustomerId(null);
        $this->customerSession->setDefaultTaxShippingAddress(null);
        $this->customerSession->setDefaultTaxBillingAddress(null);
        $this->customerSession->setCustomerTaxClassId(null);

        $className = AccountManagementInterface::class;
        $this->accountManagement = $this->createMock($className);

        $className = Data::class;
        $this->taxData = $this->createMock($className);

        $className = ResourceModelTax::class;
        $this->resource = $this->createMock($className);

        $className = Config::class;
        $this->weeeConfig = $this->createMock($className);

        $className = PriceCurrencyInterface::class;
        $this->priceCurrency = $this->createMock($className);

        $className = AbstractDb::class;
        $this->resourceCollection = $this->createMock($className);

        $this->model = new Tax(
            $this->context,
            $this->registry,
            $this->attributeFactory,
            $this->storeManager,
            $this->calculationFactory,
            $this->customerSession,
            $this->accountManagement,
            $this->taxData,
            $this->resource,
            $this->weeeConfig,
            $this->priceCurrency,
            $this->resourceCollection
        );
    }

    /**
     * Create customer session mock with all required methods
     *
     * @return Session
     */
    private function createCustomerSessionMock(): Session
    {
        return new class extends Session {
            /**
             * @var mixed
             */
            private $defaultTaxShippingAddress = null;
            /**
             * @var mixed
             */
            private $defaultTaxBillingAddress = null;
            /**
             * @var mixed
             */
            private $customerTaxClassId = null;
            /**
             * @var mixed
             */
            private $customerId = null;

            public function __construct()
            {
            }

            public function getDefaultTaxShippingAddress()
            {
                return $this->defaultTaxShippingAddress;
            }

            public function setDefaultTaxShippingAddress($address)
            {
                $this->defaultTaxShippingAddress = $address;
                return $this;
            }

            public function getDefaultTaxBillingAddress()
            {
                return $this->defaultTaxBillingAddress;
            }

            public function setDefaultTaxBillingAddress($address)
            {
                $this->defaultTaxBillingAddress = $address;
                return $this;
            }

            public function getCustomerTaxClassId()
            {
                return $this->customerTaxClassId;
            }

            public function setCustomerTaxClassId($id)
            {
                $this->customerTaxClassId = $id;
                return $this;
            }

            public function getCustomerId()
            {
                return $this->customerId;
            }

            public function setCustomerId($id)
            {
                $this->customerId = $id;
                return $this;
            }
        };
    }

    /**
     * @param array  $weeeTaxCalculationsByEntity
     * @param mixed  $websitePassed
     * @param string $expectedFptLabel
     *
     * @return void
     */
    #[DataProvider('getProductWeeeAttributesDataProvider')]
    public function testGetProductWeeeAttributes(
        array $weeeTaxCalculationsByEntity,
        $websitePassed,
        string $expectedFptLabel
    ): void {
        $product = $this->createMock(Product::class);
        $website = $this->createMock(Website::class);
        $store = $this->createMock(Store::class);
        $group = $this->createMock(Group::class);

        $attribute = $this->createMock(Attribute::class);
        $calculation = $this->createMock(Calculation::class);

        $obj = new DataObject(['country' => 'US', 'region' => 'TX']);
        $calculation->expects($this->once())
            ->method('getRateRequest')
            ->willReturn($obj);
        $calculation->expects($this->once())
            ->method('getDefaultRateRequest')
            ->willReturn($obj);
        $calculation->expects($this->any())
            ->method('getRate')
            ->willReturn('10');

        $attribute->expects($this->once())
            ->method('getAttributeCodesByFrontendType')
            ->willReturn(['0'=>'fpt']);

        $this->storeManager->expects($this->any())
            ->method('getWebsite')
            ->willReturn($website);
        $website->expects($this->any())
            ->method('getId')
            ->willReturn($websitePassed);
        $website->expects($this->any())
            ->method('getDefaultGroup')
            ->willReturn($group);
        $group->expects($this->any())
            ->method('getDefaultStore')
            ->willReturn($store);
        $store->expects($this->any())
            ->method('getId')
            ->willReturn(1);

        if ($websitePassed) {
            $product->expects($this->never())
                ->method('getStore')
                ->willReturn($store);
        } else {
            $product->expects($this->once())
                ->method('getStore')
                ->willReturn($store);
            $store->expects($this->once())
                ->method('getWebsiteId')
                ->willReturn(1);
        }

        $product->expects($this->any())
            ->method('getId')
            ->willReturn(1);

        $this->weeeConfig->expects($this->any())
            ->method('isEnabled')
            ->willReturn(true);

        $this->weeeConfig->expects($this->any())
            ->method('isTaxable')
            ->willReturn(true);

        $this->attributeFactory->expects($this->any())
            ->method('create')
            ->willReturn($attribute);

        $this->calculationFactory->expects($this->any())
            ->method('create')
            ->willReturn($calculation);

        $this->priceCurrency->expects($this->any())
            ->method('round')
            ->with(0.1)
            ->willReturn(0.25);

        $this->resource->expects($this->any())
            ->method('fetchWeeeTaxCalculationsByEntity')
            ->willReturn(
                [
                0 => $weeeTaxCalculationsByEntity
                ]
            );

        $result = $this->model->getProductWeeeAttributes($product, null, null, $websitePassed, true);
        $this->assertIsArray($result);
        $this->assertArrayHasKey(0, $result);
        $obj = $result[0];
        $this->assertEquals(1, $obj->getAmount());
        $this->assertEquals(0.25, $obj->getTaxAmount());
        $this->assertEquals($weeeTaxCalculationsByEntity['attribute_code'], $obj->getCode());
        $this->assertEquals(__($expectedFptLabel), $obj->getName());
    }

    /**
     * Test getWeeeAmountExclTax method.
     *
     * @param string $productTypeId
     * @param string $productPriceType
     *
     * @return void
     */
    #[DataProvider('getWeeeAmountExclTaxDataProvider')]
    public function testGetWeeeAmountExclTax($productTypeId, $productPriceType): void
    {
        $product = new class extends Product {
            /**
             * @var mixed
             */
            private $priceType = null;
            /**
             * @var mixed
             */
            private $typeId = null;

            public function __construct()
            {
            }

            public function getPriceType()
            {
                return $this->priceType;
            }

            public function setPriceType($type)
            {
                $this->priceType = $type;
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
        };
        $product->setTypeId($productTypeId);
        $product->setPriceType($productPriceType);
        $weeeDataHelper = new class extends DataObject {
            /**
             * @var array
             */
            private $amountExclTax = [];
            /**
             * @var int
             */
            private $callCount = 0;

            public function __construct()
            {
            }

            public function getAmountExclTax()
            {
                $value = $this->amountExclTax[$this->callCount] ?? null;
                $this->callCount++;
                return $value;
            }

            public function setAmountExclTax($amount)
            {
                $this->amountExclTax[] = $amount;
                return $this;
            }
        };
        $weeeDataHelper->setAmountExclTax(10);
        $weeeDataHelper->setAmountExclTax(30);
        $tax = $this->getMockBuilder(Tax::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProductWeeeAttributes'])
            ->getMock();
        $tax->expects($this->once())->method('getProductWeeeAttributes')
            ->willReturn([$weeeDataHelper, $weeeDataHelper]);
        $this->assertEquals(40, $tax->getWeeeAmountExclTax($product));
    }

    /**
     * Test getWeeeAmountExclTax method for dynamic bundle product.
     *
     * @return void
     */
    public function testGetWeeeAmountExclTaxForDynamicBundleProduct(): void
    {
        $product = new class extends Product {
            /**
             * @var mixed
             */
            private $priceType = null;
            /**
             * @var mixed
             */
            private $typeId = null;

            public function __construct()
            {
            }

            public function getPriceType()
            {
                return $this->priceType;
            }

            public function setPriceType($type)
            {
                $this->priceType = $type;
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
        };
        $product->setTypeId('bundle');
        $product->setPriceType(0);
        $weeeDataHelper = $this->getMockBuilder(DataObject::class)
            ->disableOriginalConstructor()
            ->getMock();
        $tax = $this->getMockBuilder(Tax::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getProductWeeeAttributes'])
            ->getMock();
        $tax->expects($this->once())->method('getProductWeeeAttributes')->willReturn([$weeeDataHelper]);
        $this->assertEquals(0, $tax->getWeeeAmountExclTax($product));
    }

    /**
     * @return array
     */
    public static function getProductWeeeAttributesDataProvider(): array
    {
        return [
            'store_label_defined' => [
                'weeeTaxCalculationsByEntity' => [
                    'weee_value' => 1,
                    'label_value' => 'fpt_label',
                    'frontend_label' => 'fpt_label_frontend',
                    'attribute_code' => 'fpt_code'
                ],
                'websitePassed' => 1,
                'expectedFptLabel' => 'fpt_label'
            ],
            'store_label_not_defined' => [
                'weeeTaxCalculationsByEntity' => [
                    'weee_value' => 1,
                    'label_value' => '',
                    'frontend_label' => 'fpt_label_frontend',
                    'attribute_code' => 'fpt_code'
                ],
                'websitePassed' => 1,
                'expectedFptLabel' => 'fpt_label_frontend'
            ],
            'website_not_passed' => [
                'weeeTaxCalculationsByEntity' => [
                    'weee_value' => 1,
                    'label_value' => '',
                    'frontend_label' => 'fpt_label_frontend',
                    'attribute_code' => 'fpt_code'
                ],
                'websitePassed' => null,
                'expectedFptLabel' => 'fpt_label_frontend'
            ]
        ];
    }

    /**
     * @return array
     */
    public static function getWeeeAmountExclTaxDataProvider(): array
    {
        return [
            [
                'bundle', 1
            ],
            [
                'simple', 0
            ],
            [
                'simple', 1
            ]
        ];
    }
}
