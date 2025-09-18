<?php
/**
 * Copyright 2019 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogRule\Test\Unit\Observer;

use Magento\Catalog\Model\Product;
use Magento\CatalogRule\Model\ResourceModel\RuleFactory;
use Magento\CatalogRule\Observer\ProcessAdminFinalPriceObserver;
use Magento\CatalogRule\Observer\RulePricesStorage;
use Magento\Framework\Event;
use Magento\Framework\Event\Observer;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Element\DataType\Date;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ProcessAdminFinalPriceObserverTest
 *
 * Test class for Observer for applying catalog rules on product for admin area
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class ProcessAdminFinalPriceObserverTest extends TestCase
{
    /**
     * @var ProcessAdminFinalPriceObserver
     */
    private $observer;

    /**
     * @var StoreManagerInterface
     */
    private $storeManagerMock;

    /**
     * @var TimezoneInterface
     */
    private $localeDateMock;

    /**
     * @var RuleFactory
     */
    private $resourceRuleFactoryMock;

    /**
     * @var RulePricesStorage
     */
    private $rulePricesStorageMock;

    /**
     * @var Event|MockObject
     */
    private $eventMock;

    /**
     * @var Observer|MockObject
     */
    private $observerMock;

    protected function setUp(): void
    {
        $this->observerMock = $this
            ->getMockBuilder(Observer::class)
            ->disableOriginalConstructor()
            ->getMock();
        // Create anonymous class extending Event with dynamic methods
        $this->eventMock = new class extends Event {
            /** @var mixed */
            private $product = null;

            public function __construct()
            {
                // Skip parent constructor to avoid complex dependencies
            }

            public function getProduct()
            {
                return $this->product;
            }

            public function setProduct($value)
            {
                $this->product = $value;
                return $this;
            }
        };
        // Create anonymous class extending RulePricesStorage with dynamic methods
        $this->rulePricesStorageMock = new class extends RulePricesStorage {
            /** @var int|null */
            private $websiteId = null;
            /** @var int|null */
            private $customerGroupId = null;
            /** @var float|null */
            private $rulePrice = null;

            public function __construct()
            {
                // Skip parent constructor to avoid complex dependencies
            }

            // Dynamic methods from addMethods
            public function getWebsiteId()
            {
                return $this->websiteId;
            }

            public function setWebsiteId($value)
            {
                $this->websiteId = $value;
                return $this;
            }

            public function getCustomerGroupId()
            {
                return $this->customerGroupId;
            }

            public function setCustomerGroupId($value)
            {
                $this->customerGroupId = $value;
                return $this;
            }

            // Methods from onlyMethods
            public function getRulePrice($id)
            {
                return $this->rulePrice;
            }

            public function setRulePrice($id, $price)
            {
                $this->rulePrice = $price;
                return $this;
            }
        };
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->resourceRuleFactoryMock = $this->getMockBuilder(RuleFactory::class)
            ->onlyMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->localeDateMock = $this->createMock(TimezoneInterface::class);
        $objectManagerHelper = new ObjectManager($this);
        $this->observer = $objectManagerHelper->getObject(
            ProcessAdminFinalPriceObserver::class,
            [
                'rulePricesStorage' => $this->rulePricesStorageMock,
                'storeManager' => $this->storeManagerMock,
                'resourceRuleFactory' => $this->resourceRuleFactoryMock,
                'localeDate' => $this->localeDateMock
            ]
        );
    }

    public function testExecute()
    {
        $finalPrice = 20.00;
        $rulePrice = 10.00;
        $storeId = 2;
        $wId = 1;
        $gId = 4;
        $pId = 20;
        $localeDateFormat = 'Y-m-d H:i:s';
        $date = '2019-12-02 08:00:00';
        $storeMock = $this->createMock(Store::class);
        $this->observerMock
            ->expects($this->atLeastOnce())
            ->method('getEvent')
            ->willReturn($this->eventMock);

        // Create anonymous class extending Product with dynamic methods
        $productMock = new class extends Product {
            /** @var int|null */
            private $websiteId = null;
            /** @var int|null */
            private $customerGroupId = null;
            /** @var int|null */
            private $storeId = null;
            /** @var int|null */
            private $id = null;
            /** @var array */
            private $data = [];
            /** @var float|null */
            private $finalPrice = null;

            public function __construct()
            {
                // Skip parent constructor to avoid complex dependencies
            }

            // Dynamic methods from addMethods
            public function getWebsiteId()
            {
                return $this->websiteId;
            }

            public function setWebsiteId($value)
            {
                $this->websiteId = $value;
                return $this;
            }

            public function getCustomerGroupId()
            {
                return $this->customerGroupId;
            }

            public function setCustomerGroupId($value)
            {
                $this->customerGroupId = $value;
                return $this;
            }

            // Methods from onlyMethods
            public function getStoreId()
            {
                return $this->storeId;
            }

            public function setStoreId($value)
            {
                $this->storeId = $value;
                return $this;
            }

            public function getId()
            {
                return $this->id;
            }

            public function setId($value)
            {
                $this->id = $value;
                return $this;
            }

            public function getData($key = '', $index = null)
            {
                if ($key === '') {
                    return $this->data;
                }
                return isset($this->data[$key]) ? $this->data[$key] : null;
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

            public function setFinalPrice($value)
            {
                $this->finalPrice = $value;
                return $this;
            }

            public function getFinalPrice($qty = null)
            {
                return $this->finalPrice;
            }
        };
        // Create anonymous class extending Date with dynamic methods
        $dateMock = new class extends Date {
            /** @var string|null */
            private $formatValue = null;

            public function __construct()
            {
                // Skip parent constructor to avoid complex dependencies
            }

            public function format($format)
            {
                return $this->formatValue;
            }

            public function setFormatValue($value)
            {
                $this->formatValue = $value;
                return $this;
            }
        };

        $this->localeDateMock->expects($this->once())
            ->method('scopeDate')
            ->with($storeId)
            ->willReturn($dateMock);
        $dateMock->setFormatValue($date);
        $storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($wId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($storeMock);
        $productMock->setStoreId($storeId);
        $productMock->setCustomerGroupId($gId);
        $productMock->setId($pId);
        $productMock->setData('final_price', $finalPrice);
        $this->rulePricesStorageMock->setCustomerGroupId($gId);
        $this->resourceRuleFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->rulePricesStorageMock);
        $this->rulePricesStorageMock->setRulePrice($pId, $rulePrice);
        $this->eventMock->setProduct($productMock);
        $this->assertEquals($this->observer, $this->observer->execute($this->observerMock));
    }
}
