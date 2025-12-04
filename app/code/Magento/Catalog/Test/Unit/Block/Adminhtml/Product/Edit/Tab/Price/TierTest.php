<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Block\Adminhtml\Product\Edit\Tab\Price;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Price\Tier;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for Tier price block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TierTest extends TestCase
{
    /**
     * @var Tier
     */
    private $block;

    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var GroupRepositoryInterface|MockObject
     */
    private $groupRepositoryMock;

    /**
     * @var DirectoryHelper|MockObject
     */
    private $directoryHelperMock;

    /**
     * @var ModuleManager|MockObject
     */
    private $moduleManagerMock;

    /**
     * @var Registry|MockObject
     */
    private $registryMock;

    /**
     * @var GroupManagementInterface|MockObject
     */
    private $groupManagementMock;

    /**
     * @var SearchCriteriaBuilder|MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var CurrencyInterface|MockObject
     */
    private $localeCurrencyMock;

    /**
     * @var JsonHelper|MockObject
     */
    private $jsonHelperMock;

    /**
     * @var LayoutInterface|MockObject
     */
    private $layoutMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->contextMock = $this->createMock(Context::class);
        $this->groupRepositoryMock = $this->getMockForAbstractClass(GroupRepositoryInterface::class);
        $this->directoryHelperMock = $this->createMock(DirectoryHelper::class);
        $this->moduleManagerMock = $this->createMock(ModuleManager::class);
        $this->registryMock = $this->createMock(Registry::class);
        $this->groupManagementMock = $this->getMockForAbstractClass(GroupManagementInterface::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);
        $this->localeCurrencyMock = $this->getMockForAbstractClass(CurrencyInterface::class);
        $this->jsonHelperMock = $this->createMock(JsonHelper::class);
        $this->layoutMock = $this->getMockForAbstractClass(LayoutInterface::class);

        $this->contextMock->expects($this->any())
            ->method('getLayout')
            ->willReturn($this->layoutMock);

        // Prepare ObjectManager to handle JsonHelper fallback
        $objects = [
            [
                JsonHelper::class,
                $this->jsonHelperMock
            ]
        ];
        $objectManager->prepareObjectManager($objects);

        $this->block = $objectManager->getObject(
            Tier::class,
            [
                'context' => $this->contextMock,
                'groupRepository' => $this->groupRepositoryMock,
                'directoryHelper' => $this->directoryHelperMock,
                'moduleManager' => $this->moduleManagerMock,
                'registry' => $this->registryMock,
                'groupManagement' => $this->groupManagementMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'localeCurrency' => $this->localeCurrencyMock,
                'jsonHelper' => $this->jsonHelperMock
            ]
        );
    }

    /**
     * Test constructor injects JsonHelper through data array when provided
     *
     * @return void
     */
    public function testConstructorInjectsJsonHelperThroughDataArray(): void
    {
        $jsonHelperMock = $this->createMock(JsonHelper::class);
        $objectManager = new ObjectManager($this);

        $block = $objectManager->getObject(
            Tier::class,
            [
                'context' => $this->contextMock,
                'groupRepository' => $this->groupRepositoryMock,
                'directoryHelper' => $this->directoryHelperMock,
                'moduleManager' => $this->moduleManagerMock,
                'registry' => $this->registryMock,
                'groupManagement' => $this->groupManagementMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'localeCurrency' => $this->localeCurrencyMock,
                'jsonHelper' => $jsonHelperMock
            ]
        );

        $this->assertInstanceOf(Tier::class, $block);
    }

    /**
     * Test getInitialCustomerGroups returns all customers group
     *
     * @return void
     */
    public function testGetInitialCustomerGroupsReturnsAllCustomersGroup(): void
    {
        $allCustomersGroupId = 0;
        $expectedLabel = 'ALL GROUPS';

        $groupMock = $this->getMockForAbstractClass(GroupInterface::class);
        $groupMock->expects($this->once())
            ->method('getId')
            ->willReturn($allCustomersGroupId);

        $this->groupManagementMock->expects($this->once())
            ->method('getAllCustomersGroup')
            ->willReturn($groupMock);

        // Use reflection to call protected method
        $reflection = new \ReflectionClass($this->block);
        $method = $reflection->getMethod('_getInitialCustomerGroups');
        $method->setAccessible(true);
        $result = $method->invoke($this->block);

        $this->assertIsArray($result);
        $this->assertArrayHasKey($allCustomersGroupId, $result);
        $this->assertEquals($expectedLabel, $result[$allCustomersGroupId]);
    }

    /**
     * Test sortValues calls usort with sortTierPrices callback
     *
     * @return void
     */
    public function testSortValuesCallsUsortWithSortTierPricesCallback(): void
    {
        $data = [
            ['website_id' => 1, 'cust_group' => 0, 'price_qty' => 10],
            ['website_id' => 0, 'cust_group' => 1, 'price_qty' => 5]
        ];

        // Use reflection to call protected method
        $reflection = new \ReflectionClass($this->block);
        $method = $reflection->getMethod('_sortValues');
        $method->setAccessible(true);
        $result = $method->invoke($this->block, $data);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    /**
     * Data provider for testing sortTierPrices method
     *
     * @return array
     */
    public static function sortTierPricesDataProvider(): array
    {
        return [
            'sorts by website ID ascending when first is larger' => [
                'item1' => ['website_id' => 2, 'cust_group' => 0, 'price_qty' => 10],
                'item2' => ['website_id' => 1, 'cust_group' => 0, 'price_qty' => 10],
                'needsGroupMock' => false,
                'expectedResult' => 1
            ],
            'returns negative when first website ID is smaller' => [
                'item1' => ['website_id' => 1, 'cust_group' => 0, 'price_qty' => 10],
                'item2' => ['website_id' => 2, 'cust_group' => 0, 'price_qty' => 10],
                'needsGroupMock' => false,
                'expectedResult' => -1
            ],
            'sorts by customer group when website IDs are equal' => [
                'item1' => ['website_id' => 1, 'cust_group' => 2, 'price_qty' => 10],
                'item2' => ['website_id' => 1, 'cust_group' => 1, 'price_qty' => 10],
                'needsGroupMock' => true,
                'expectedResult' => 1
            ],
            'sorts by price quantity when website and group are equal - first larger' => [
                'item1' => ['website_id' => 1, 'cust_group' => 1, 'price_qty' => 20],
                'item2' => ['website_id' => 1, 'cust_group' => 1, 'price_qty' => 10],
                'needsGroupMock' => true,
                'expectedResult' => 1
            ],
            'returns negative when first price quantity is smaller' => [
                'item1' => ['website_id' => 1, 'cust_group' => 1, 'price_qty' => 5],
                'item2' => ['website_id' => 1, 'cust_group' => 1, 'price_qty' => 10],
                'needsGroupMock' => true,
                'expectedResult' => -1
            ],
            'returns zero when all values are equal' => [
                'item1' => ['website_id' => 1, 'cust_group' => 1, 'price_qty' => 10],
                'item2' => ['website_id' => 1, 'cust_group' => 1, 'price_qty' => 10],
                'needsGroupMock' => true,
                'expectedResult' => 0
            ]
        ];
    }

    /**
     * Test sortTierPrices method returns expected comparison result
     *
     * @dataProvider sortTierPricesDataProvider
     * @param array $item1
     * @param array $item2
     * @param bool $needsGroupMock
     * @param int $expectedResult
     * @return void
     */
    public function testSortTierPricesReturnsExpectedResult(
        array $item1,
        array $item2,
        bool $needsGroupMock,
        int $expectedResult
    ): void {
        if ($needsGroupMock) {
            $groupMock = $this->getMockForAbstractClass(GroupInterface::class);
            $groupMock->method('getId')->willReturn(0);
            $groupMock->method('getCode')->willReturn('General');
            $this->groupManagementMock->method('getAllCustomersGroup')->willReturn($groupMock);
            $this->moduleManagerMock->method('isEnabled')->willReturn(false);
        }

        // Use reflection to call protected method
        $reflection = new \ReflectionClass($this->block);
        $method = $reflection->getMethod('_sortTierPrices');
        $method->setAccessible(true);
        $result = $method->invoke($this->block, $item1, $item2);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Test prepareLayout creates and configures add button
     *
     * @return void
     */
    public function testPrepareLayoutCreatesAndConfiguresAddButton(): void
    {
        $buttonMock = $this->getMockBuilder(Button::class)
            ->disableOriginalConstructor()
            ->addMethods(['setName'])
            ->onlyMethods(['setData'])
            ->getMock();

        $this->layoutMock->expects($this->once())
            ->method('createBlock')
            ->with(Button::class)
            ->willReturn($buttonMock);

        $buttonMock->expects($this->once())
            ->method('setData')
            ->with([
                'label' => __('Add Price'),
                'onclick' => 'return tierPriceControl.addItem()',
                'class' => 'add'
            ])
            ->willReturnSelf();

        $buttonMock->expects($this->once())
            ->method('setName')
            ->with('add_tier_price_item_button')
            ->willReturnSelf();

        // Use reflection to call protected method
        $reflection = new \ReflectionClass($this->block);
        $method = $reflection->getMethod('_prepareLayout');
        $method->setAccessible(true);
        $result = $method->invoke($this->block);

        $this->assertSame($this->block, $result);
    }

    /**
     * Test prepareLayout returns block instance for method chaining
     *
     * @return void
     */
    public function testPrepareLayoutReturnsBlockInstanceForMethodChaining(): void
    {
        $buttonMock = $this->getMockBuilder(Button::class)
            ->disableOriginalConstructor()
            ->addMethods(['setName'])
            ->onlyMethods(['setData'])
            ->getMock();

        $this->layoutMock->expects($this->once())
            ->method('createBlock')
            ->willReturn($buttonMock);

        $buttonMock->expects($this->any())
            ->method('setData')
            ->willReturnSelf();

        $buttonMock->expects($this->any())
            ->method('setName')
            ->willReturnSelf();

        // Use reflection to call protected method
        $reflection = new \ReflectionClass($this->block);
        $method = $reflection->getMethod('_prepareLayout');
        $method->setAccessible(true);
        $result = $method->invoke($this->block);

        // Verify method returns block for chaining
        $this->assertInstanceOf(Tier::class, $result);
    }

    /**
     * Test sortValues maintains data integrity after sorting
     *
     * @return void
     */
    public function testSortValuesMaintainsDataIntegrityAfterSorting(): void
    {
        $groupMock = $this->getMockForAbstractClass(GroupInterface::class);
        $groupMock->method('getId')->willReturn(0);
        $groupMock->method('getCode')->willReturn('General');

        $this->groupManagementMock->method('getAllCustomersGroup')->willReturn($groupMock);
        $this->moduleManagerMock->method('isEnabled')->willReturn(false);

        $data = [
            ['website_id' => 2, 'cust_group' => 1, 'price_qty' => 20, 'price' => 100.00],
            ['website_id' => 1, 'cust_group' => 0, 'price_qty' => 10, 'price' => 50.00],
            ['website_id' => 1, 'cust_group' => 1, 'price_qty' => 5, 'price' => 75.00]
        ];

        // Use reflection to call protected method
        $reflection = new \ReflectionClass($this->block);
        $method = $reflection->getMethod('_sortValues');
        $method->setAccessible(true);
        $result = $method->invoke($this->block, $data);

        $this->assertCount(3, $result);
        // Verify first item should be website_id=1 (smallest)
        $this->assertEquals(1, $result[0]['website_id']);
        // All original data fields should be preserved
        $this->assertArrayHasKey('price', $result[0]);
        $this->assertArrayHasKey('cust_group', $result[0]);
        $this->assertArrayHasKey('price_qty', $result[0]);
    }

    /**
     * Test template is set correctly
     *
     * @return void
     */
    public function testTemplateIsSetCorrectly(): void
    {
        $expectedTemplate = 'Magento_Catalog::catalog/product/edit/price/tier.phtml';
        $actualTemplate = $this->block->getTemplate();

        $this->assertEquals($expectedTemplate, $actualTemplate);
    }
}
