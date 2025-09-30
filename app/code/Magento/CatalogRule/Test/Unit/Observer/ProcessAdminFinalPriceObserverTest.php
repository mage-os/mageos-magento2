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
use Magento\CatalogRule\Test\Unit\Helper\RulePricesStorageTestHelper;
use Magento\Catalog\Test\Unit\Helper\ProductTestHelperForCatalogRule;
use Magento\Framework\Test\Unit\Helper\DateTestHelperForCatalogRule;
use Magento\Framework\Test\Unit\Helper\EventTestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class ProcessAdminFinalPriceObserverTest
 *
 * Test class for Observer for applying catalog rules on product for admin area
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.TooManyFields)
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
        $this->eventMock = new EventTestHelper();
        $this->rulePricesStorageMock = new RulePricesStorageTestHelper();
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

        $productMock = new ProductTestHelperForCatalogRule();
        $dateMock = new DateTestHelperForCatalogRule();

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
