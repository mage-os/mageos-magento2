<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Block\Adminhtml\Product\Edit\Tab\Price\Group;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Price\Group\AbstractGroup;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\Data\GroupSearchResultsInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Currency;
use Magento\Framework\Currency as FrameworkCurrency;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for AbstractGroup price block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class AbstractGroupTest extends TestCase
{
    /**
     * @var AbstractGroup|MockObject
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
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        // Prepare ObjectManager to handle JsonHelper and DirectoryHelper fallback
        $objects = [
            [
                JsonHelper::class,
                $this->createMock(JsonHelper::class)
            ],
            [
                DirectoryHelper::class,
                $this->createMock(DirectoryHelper::class)
            ]
        ];
        $objectManager->prepareObjectManager($objects);

        $this->contextMock = $this->createMock(Context::class);
        $this->groupRepositoryMock = $this->getMockForAbstractClass(GroupRepositoryInterface::class);
        $this->directoryHelperMock = $this->createMock(DirectoryHelper::class);
        $this->moduleManagerMock = $this->createMock(ModuleManager::class);
        $this->registryMock = $this->createMock(Registry::class);
        $this->groupManagementMock = $this->getMockForAbstractClass(GroupManagementInterface::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);
        $this->localeCurrencyMock = $this->getMockForAbstractClass(CurrencyInterface::class);
        $this->storeManagerMock = $this->getMockForAbstractClass(StoreManagerInterface::class);

        $this->contextMock->expects($this->any())
            ->method('getStoreManager')
            ->willReturn($this->storeManagerMock);

        // Create anonymous class extending AbstractGroup for testing
        $this->block = new class(
            $this->contextMock,
            $this->groupRepositoryMock,
            $this->directoryHelperMock,
            $this->moduleManagerMock,
            $this->registryMock,
            $this->groupManagementMock,
            $this->searchCriteriaBuilderMock,
            $this->localeCurrencyMock
        ) extends AbstractGroup {
        };
    }

    /**
     * Test getProduct returns product from registry
     *
     * @return void
     */
    public function testGetProductReturnsProductFromRegistry(): void
    {
        $productMock = $this->createMock(Product::class);

        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('product')
            ->willReturn($productMock);

        $result = $this->block->getProduct();

        $this->assertSame($productMock, $result);
    }

    /**
     * Test render calls setElement and returns HTML
     *
     * @return void
     */
    public function testRenderCallsSetElementAndReturnsHtml(): void
    {
        $elementMock = $this->getMockBuilder(AbstractElement::class)
            ->disableOriginalConstructor()
            ->getMock();

        $blockMock = $this->getMockBuilder(AbstractGroup::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setElement', 'toHtml'])
            ->getMockForAbstractClass();

        $blockMock->expects($this->once())
            ->method('setElement')
            ->with($elementMock)
            ->willReturnSelf();

        $blockMock->expects($this->once())
            ->method('toHtml')
            ->willReturn('<html>');

        $result = $blockMock->render($elementMock);

        $this->assertEquals('<html>', $result);
    }

    /**
     * Test setElement sets element property
     *
     * @return void
     */
    public function testSetElementSetsElementProperty(): void
    {
        $elementMock = $this->getMockBuilder(AbstractElement::class)
            ->disableOriginalConstructor()
            ->getMock();

        $result = $this->block->setElement($elementMock);

        $this->assertSame($this->block, $result);
        $this->assertSame($elementMock, $this->block->getElement());
    }

    /**
     * Test setElement returns self for method chaining
     *
     * @return void
     */
    public function testSetElementReturnsSelfForMethodChaining(): void
    {
        $elementMock = $this->getMockBuilder(AbstractElement::class)
            ->disableOriginalConstructor()
            ->getMock();

        $result = $this->block->setElement($elementMock);

        $this->assertSame($this->block, $result);
    }

    /**
     * Test getElement returns set element
     *
     * @return void
     */
    public function testGetElementReturnsSetElement(): void
    {
        $elementMock = $this->getMockBuilder(AbstractElement::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->block->setElement($elementMock);

        $result = $this->block->getElement();

        $this->assertSame($elementMock, $result);
    }

    /**
     * Test getCustomerGroups returns empty array when Customer module is disabled
     *
     * @return void
     */
    public function testGetCustomerGroupsReturnsEmptyArrayWhenCustomerModuleDisabled(): void
    {
        $this->moduleManagerMock->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_Customer')
            ->willReturn(false);

        $result = $this->block->getCustomerGroups();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test getCustomerGroups returns groups when Customer module is enabled
     *
     * @return void
     */
    public function testGetCustomerGroupsReturnsGroupsWhenCustomerModuleEnabled(): void
    {
        $groupMock1 = $this->getMockForAbstractClass(GroupInterface::class);
        $groupMock1->expects($this->once())->method('getId')->willReturn(1);
        $groupMock1->expects($this->once())->method('getCode')->willReturn('General');

        $groupMock2 = $this->getMockForAbstractClass(GroupInterface::class);
        $groupMock2->expects($this->once())->method('getId')->willReturn(2);
        $groupMock2->expects($this->once())->method('getCode')->willReturn('Wholesale');

        $searchResultsMock = $this->getMockForAbstractClass(GroupSearchResultsInterface::class);
        $searchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$groupMock1, $groupMock2]);

        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);

        $this->moduleManagerMock->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_Customer')
            ->willReturn(true);

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $this->groupRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($searchResultsMock);

        $result = $this->block->getCustomerGroups();

        $this->assertIsArray($result);
        $this->assertArrayHasKey(1, $result);
        $this->assertArrayHasKey(2, $result);
        $this->assertEquals('General', $result[1]);
        $this->assertEquals('Wholesale', $result[2]);
    }

    /**
     * Test getCustomerGroups with specific group ID returns group name
     *
     * @return void
     */
    public function testGetCustomerGroupsWithGroupIdReturnsGroupName(): void
    {
        $groupId = 1;
        $groupMock = $this->getMockForAbstractClass(GroupInterface::class);
        $groupMock->expects($this->once())->method('getId')->willReturn($groupId);
        $groupMock->expects($this->once())->method('getCode')->willReturn('General');

        $searchResultsMock = $this->getMockForAbstractClass(GroupSearchResultsInterface::class);
        $searchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$groupMock]);

        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);

        $this->moduleManagerMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $this->groupRepositoryMock->expects($this->once())
            ->method('getList')
            ->willReturn($searchResultsMock);

        $result = $this->block->getCustomerGroups($groupId);

        $this->assertEquals('General', $result);
    }

    /**
     * Test getCustomerGroups with non-existent group ID returns empty array
     *
     * @return void
     */
    public function testGetCustomerGroupsWithNonExistentGroupIdReturnsEmptyArray(): void
    {
        $nonExistentGroupId = 999;
        $searchResultsMock = $this->getMockForAbstractClass(GroupSearchResultsInterface::class);
        $searchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([]);

        $searchCriteriaMock = $this->getMockForAbstractClass(SearchCriteriaInterface::class);

        $this->moduleManagerMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $this->groupRepositoryMock->expects($this->once())
            ->method('getList')
            ->willReturn($searchResultsMock);

        $result = $this->block->getCustomerGroups($nonExistentGroupId);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test getWebsiteCount returns count of websites
     *
     * @return void
     */
    public function testGetWebsiteCountReturnsCountOfWebsites(): void
    {
        $blockMock = $this->getMockBuilder(AbstractGroup::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getWebsites'])
            ->getMockForAbstractClass();

        $websites = [
            0 => ['name' => 'All Websites'],
            1 => ['name' => 'Main Website'],
            2 => ['name' => 'Second Website']
        ];

        $blockMock->expects($this->once())
            ->method('getWebsites')
            ->willReturn($websites);

        $result = $blockMock->getWebsiteCount();

        $this->assertEquals(3, $result);
    }

    /**
     * Test isMultiWebsites returns false when single store mode
     *
     * @return void
     */
    public function testIsMultiWebsitesReturnsFalseWhenSingleStoreMode(): void
    {
        $this->storeManagerMock->expects($this->once())
            ->method('isSingleStoreMode')
            ->willReturn(true);

        $result = $this->block->isMultiWebsites();

        $this->assertFalse($result);
    }

    /**
     * Test isMultiWebsites returns true when not single store mode
     *
     * @return void
     */
    public function testIsMultiWebsitesReturnsTrueWhenNotSingleStoreMode(): void
    {
        $this->storeManagerMock->expects($this->once())
            ->method('isSingleStoreMode')
            ->willReturn(false);

        $result = $this->block->isMultiWebsites();

        $this->assertTrue($result);
    }

    /**
     * Test getDefaultCustomerGroup returns all customers group ID
     *
     * @return void
     */
    public function testGetDefaultCustomerGroupReturnsAllCustomersGroupId(): void
    {
        $allCustomersGroupId = 0;
        $groupMock = $this->getMockForAbstractClass(GroupInterface::class);
        $groupMock->expects($this->once())
            ->method('getId')
            ->willReturn($allCustomersGroupId);

        $this->groupManagementMock->expects($this->once())
            ->method('getAllCustomersGroup')
            ->willReturn($groupMock);

        $result = $this->block->getDefaultCustomerGroup();

        $this->assertEquals($allCustomersGroupId, $result);
    }

    /**
     * Test getPriceColumnHeader returns custom header when set
     *
     * @return void
     */
    public function testGetPriceColumnHeaderReturnsCustomHeaderWhenSet(): void
    {
        $customHeader = 'Custom Price Header';
        $defaultHeader = 'Default Header';

        $this->block->setData('price_column_header', $customHeader);

        $result = $this->block->getPriceColumnHeader($defaultHeader);

        $this->assertEquals($customHeader, $result);
    }

    /**
     * Test getPriceColumnHeader returns default when custom not set
     *
     * @return void
     */
    public function testGetPriceColumnHeaderReturnsDefaultWhenCustomNotSet(): void
    {
        $defaultHeader = 'Default Header';

        $result = $this->block->getPriceColumnHeader($defaultHeader);

        $this->assertEquals($defaultHeader, $result);
    }

    /**
     * Test getPriceValidation returns custom validation when set
     *
     * @return void
     */
    public function testGetPriceValidationReturnsCustomValidationWhenSet(): void
    {
        $customValidation = 'validate-number';
        $defaultValidation = 'validate-price';

        $this->block->setData('price_validation', $customValidation);

        $result = $this->block->getPriceValidation($defaultValidation);

        $this->assertEquals($customValidation, $result);
    }

    /**
     * Test getPriceValidation returns default when custom not set
     *
     * @return void
     */
    public function testGetPriceValidationReturnsDefaultWhenCustomNotSet(): void
    {
        $defaultValidation = 'validate-price';

        $result = $this->block->getPriceValidation($defaultValidation);

        $this->assertEquals($defaultValidation, $result);
    }

    /**
     * Test getAttribute returns entity attribute from element
     *
     * @return void
     */
    public function testGetAttributeReturnsEntityAttributeFromElement(): void
    {
        $attributeMock = $this->createMock(Attribute::class);
        $elementMock = $this->getMockBuilder(AbstractElement::class)
            ->disableOriginalConstructor()
            ->addMethods(['getEntityAttribute'])
            ->getMock();

        $elementMock->expects($this->once())
            ->method('getEntityAttribute')
            ->willReturn($attributeMock);

        $this->block->setElement($elementMock);

        $result = $this->block->getAttribute();

        $this->assertSame($attributeMock, $result);
    }

    /**
     * Test isScopeGlobal returns true when attribute scope is global
     *
     * @return void
     */
    public function testIsScopeGlobalReturnsTrueWhenAttributeScopeIsGlobal(): void
    {
        $attributeMock = $this->createMock(Attribute::class);
        $attributeMock->expects($this->once())
            ->method('isScopeGlobal')
            ->willReturn(true);

        $elementMock = $this->getMockBuilder(AbstractElement::class)
            ->disableOriginalConstructor()
            ->addMethods(['getEntityAttribute'])
            ->getMock();
        $elementMock->expects($this->once())
            ->method('getEntityAttribute')
            ->willReturn($attributeMock);

        $this->block->setElement($elementMock);

        $result = $this->block->isScopeGlobal();

        $this->assertTrue($result);
    }

    /**
     * Test isScopeGlobal returns false when attribute scope is not global
     *
     * @return void
     */
    public function testIsScopeGlobalReturnsFalseWhenAttributeScopeIsNotGlobal(): void
    {
        $attributeMock = $this->createMock(Attribute::class);
        $attributeMock->expects($this->once())
            ->method('isScopeGlobal')
            ->willReturn(false);

        $elementMock = $this->getMockBuilder(AbstractElement::class)
            ->disableOriginalConstructor()
            ->addMethods(['getEntityAttribute'])
            ->getMock();
        $elementMock->expects($this->once())
            ->method('getEntityAttribute')
            ->willReturn($attributeMock);

        $this->block->setElement($elementMock);

        $result = $this->block->isScopeGlobal();

        $this->assertFalse($result);
    }

    /**
     * Test getAddButtonHtml returns child HTML
     *
     * @return void
     */
    public function testGetAddButtonHtmlReturnsChildHtml(): void
    {
        $expectedHtml = '<button>Add</button>';

        $blockMock = $this->getMockBuilder(AbstractGroup::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getChildHtml'])
            ->getMockForAbstractClass();

        $blockMock->expects($this->once())
            ->method('getChildHtml')
            ->with('add_button')
            ->willReturn($expectedHtml);

        $result = $blockMock->getAddButtonHtml();

        $this->assertEquals($expectedHtml, $result);
    }

    /**
     * Test isShowWebsiteColumn returns false when scope is global
     *
     * @return void
     */
    public function testIsShowWebsiteColumnReturnsFalseWhenScopeIsGlobal(): void
    {
        $attributeMock = $this->createMock(Attribute::class);
        $attributeMock->expects($this->once())
            ->method('isScopeGlobal')
            ->willReturn(true);

        $elementMock = $this->getMockBuilder(AbstractElement::class)
            ->disableOriginalConstructor()
            ->addMethods(['getEntityAttribute'])
            ->getMock();
        $elementMock->expects($this->once())
            ->method('getEntityAttribute')
            ->willReturn($attributeMock);

        $this->block->setElement($elementMock);

        $result = $this->block->isShowWebsiteColumn();

        $this->assertFalse($result);
    }

    /**
     * Test isShowWebsiteColumn returns false when single store mode
     *
     * @return void
     */
    public function testIsShowWebsiteColumnReturnsFalseWhenSingleStoreMode(): void
    {
        $attributeMock = $this->createMock(Attribute::class);
        $attributeMock->expects($this->once())
            ->method('isScopeGlobal')
            ->willReturn(false);

        $elementMock = $this->getMockBuilder(AbstractElement::class)
            ->disableOriginalConstructor()
            ->addMethods(['getEntityAttribute'])
            ->getMock();
        $elementMock->expects($this->once())
            ->method('getEntityAttribute')
            ->willReturn($attributeMock);

        $this->storeManagerMock->expects($this->once())
            ->method('isSingleStoreMode')
            ->willReturn(true);

        $this->block->setElement($elementMock);

        $result = $this->block->isShowWebsiteColumn();

        $this->assertFalse($result);
    }

    /**
     * Test isShowWebsiteColumn returns true when not global and not single store
     *
     * @return void
     */
    public function testIsShowWebsiteColumnReturnsTrueWhenNotGlobalAndNotSingleStore(): void
    {
        $attributeMock = $this->createMock(Attribute::class);
        $attributeMock->expects($this->once())
            ->method('isScopeGlobal')
            ->willReturn(false);

        $elementMock = $this->getMockBuilder(AbstractElement::class)
            ->disableOriginalConstructor()
            ->addMethods(['getEntityAttribute'])
            ->getMock();
        $elementMock->expects($this->once())
            ->method('getEntityAttribute')
            ->willReturn($attributeMock);

        $this->storeManagerMock->expects($this->once())
            ->method('isSingleStoreMode')
            ->willReturn(false);

        $this->block->setElement($elementMock);

        $result = $this->block->isShowWebsiteColumn();

        $this->assertTrue($result);
    }

    /**
     * Test isAllowChangeWebsite returns false when website column not shown
     *
     * @return void
     */
    public function testIsAllowChangeWebsiteReturnsFalseWhenWebsiteColumnNotShown(): void
    {
        $attributeMock = $this->createMock(Attribute::class);
        $attributeMock->expects($this->once())
            ->method('isScopeGlobal')
            ->willReturn(true);

        $elementMock = $this->getMockBuilder(AbstractElement::class)
            ->disableOriginalConstructor()
            ->addMethods(['getEntityAttribute'])
            ->getMock();
        $elementMock->expects($this->once())
            ->method('getEntityAttribute')
            ->willReturn($attributeMock);

        $this->block->setElement($elementMock);

        $result = $this->block->isAllowChangeWebsite();

        $this->assertFalse($result);
    }

    /**
     * Test isAllowChangeWebsite returns false when product has store ID
     *
     * @return void
     */
    public function testIsAllowChangeWebsiteReturnsFalseWhenProductHasStoreId(): void
    {
        $storeId = 1;
        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);

        $attributeMock = $this->createMock(Attribute::class);
        $attributeMock->expects($this->once())
            ->method('isScopeGlobal')
            ->willReturn(false);

        $elementMock = $this->getMockBuilder(AbstractElement::class)
            ->disableOriginalConstructor()
            ->addMethods(['getEntityAttribute'])
            ->getMock();
        $elementMock->expects($this->once())
            ->method('getEntityAttribute')
            ->willReturn($attributeMock);

        $this->storeManagerMock->expects($this->once())
            ->method('isSingleStoreMode')
            ->willReturn(false);

        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('product')
            ->willReturn($productMock);

        $this->block->setElement($elementMock);

        $result = $this->block->isAllowChangeWebsite();

        $this->assertFalse($result);
    }

    /**
     * Test isAllowChangeWebsite returns true when conditions met
     *
     * @return void
     */
    public function testIsAllowChangeWebsiteReturnsTrueWhenConditionsMet(): void
    {
        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn(null);

        $attributeMock = $this->createMock(Attribute::class);
        $attributeMock->expects($this->once())
            ->method('isScopeGlobal')
            ->willReturn(false);

        $elementMock = $this->getMockBuilder(AbstractElement::class)
            ->disableOriginalConstructor()
            ->addMethods(['getEntityAttribute'])
            ->getMock();
        $elementMock->expects($this->once())
            ->method('getEntityAttribute')
            ->willReturn($attributeMock);

        $this->storeManagerMock->expects($this->once())
            ->method('isSingleStoreMode')
            ->willReturn(false);

        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('product')
            ->willReturn($productMock);

        $this->block->setElement($elementMock);

        $result = $this->block->isAllowChangeWebsite();

        $this->assertTrue($result);
    }

    /**
     * Test getDefaultWebsite returns store website ID when conditions met
     *
     * @return void
     */
    public function testGetDefaultWebsiteReturnsStoreWebsiteIdWhenConditionsMet(): void
    {
        $storeId = 1;
        $websiteId = 2;

        $storeMock = $this->createMock(Store::class);
        $storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($websiteId);

        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->any())
            ->method('getStoreId')
            ->willReturn($storeId);

        $attributeMock = $this->createMock(Attribute::class);
        $attributeMock->expects($this->any())
            ->method('isScopeGlobal')
            ->willReturn(false);

        $elementMock = $this->getMockBuilder(AbstractElement::class)
            ->disableOriginalConstructor()
            ->addMethods(['getEntityAttribute'])
            ->getMock();
        $elementMock->expects($this->any())
            ->method('getEntityAttribute')
            ->willReturn($attributeMock);

        $this->storeManagerMock->expects($this->any())
            ->method('isSingleStoreMode')
            ->willReturn(false);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($storeMock);

        $this->registryMock->expects($this->any())
            ->method('registry')
            ->with('product')
            ->willReturn($productMock);

        $this->block->setElement($elementMock);

        $result = $this->block->getDefaultWebsite();

        $this->assertEquals($websiteId, $result);
    }

    /**
     * Test getDefaultWebsite returns zero when not showing website column
     *
     * @return void
     */
    public function testGetDefaultWebsiteReturnsZeroWhenNotShowingWebsiteColumn(): void
    {
        $attributeMock = $this->createMock(Attribute::class);
        $attributeMock->expects($this->once())
            ->method('isScopeGlobal')
            ->willReturn(true);

        $elementMock = $this->getMockBuilder(AbstractElement::class)
            ->disableOriginalConstructor()
            ->addMethods(['getEntityAttribute'])
            ->getMock();
        $elementMock->expects($this->once())
            ->method('getEntityAttribute')
            ->willReturn($attributeMock);

        $this->block->setElement($elementMock);

        $result = $this->block->getDefaultWebsite();

        $this->assertEquals(0, $result);
    }

    /**
     * Test getDefaultWebsite returns zero when change website is allowed
     *
     * @return void
     */
    public function testGetDefaultWebsiteReturnsZeroWhenChangeWebsiteAllowed(): void
    {
        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn(null);

        $attributeMock = $this->createMock(Attribute::class);
        $attributeMock->expects($this->any())
            ->method('isScopeGlobal')
            ->willReturn(false);

        $elementMock = $this->getMockBuilder(AbstractElement::class)
            ->disableOriginalConstructor()
            ->addMethods(['getEntityAttribute'])
            ->getMock();
        $elementMock->expects($this->any())
            ->method('getEntityAttribute')
            ->willReturn($attributeMock);

        $this->storeManagerMock->expects($this->any())
            ->method('isSingleStoreMode')
            ->willReturn(false);

        $this->registryMock->expects($this->any())
            ->method('registry')
            ->with('product')
            ->willReturn($productMock);

        $this->block->setElement($elementMock);

        $result = $this->block->getDefaultWebsite();

        $this->assertEquals(0, $result);
    }

    /**
     * Test getWebsites returns cached websites when already loaded
     *
     * @return void
     */
    public function testGetWebsitesReturnsCachedWebsitesWhenAlreadyLoaded(): void
    {
        $cachedWebsites = [
            0 => ['name' => 'All Websites', 'currency' => 'USD'],
            1 => ['name' => 'Main Website', 'currency' => 'USD']
        ];

        // Use reflection to set the private _websites property
        $reflection = new \ReflectionClass($this->block);
        $property = $reflection->getProperty('_websites');
        $property->setAccessible(true);
        $property->setValue($this->block, $cachedWebsites);

        $result = $this->block->getWebsites();

        $this->assertSame($cachedWebsites, $result);
    }

    /**
     * Test getWebsites returns only all websites when scope is global
     *
     * @return void
     */
    public function testGetWebsitesReturnsOnlyAllWebsitesWhenScopeIsGlobal(): void
    {
        $baseCurrency = 'USD';

        $attributeMock = $this->createMock(Attribute::class);
        $attributeMock->expects($this->any())
            ->method('isScopeGlobal')
            ->willReturn(true);

        $elementMock = $this->getMockBuilder(AbstractElement::class)
            ->disableOriginalConstructor()
            ->addMethods(['getEntityAttribute'])
            ->getMock();
        $elementMock->expects($this->any())
            ->method('getEntityAttribute')
            ->willReturn($attributeMock);

        $this->directoryHelperMock->expects($this->once())
            ->method('getBaseCurrencyCode')
            ->willReturn($baseCurrency);

        $this->block->setElement($elementMock);

        $result = $this->block->getWebsites();

        $this->assertCount(1, $result);
        $this->assertArrayHasKey(0, $result);
        $this->assertEquals($baseCurrency, $result[0]['currency']);
    }

    /**
     * Test getValues returns formatted price values
     *
     * @return void
     */
    public function testGetValuesReturnsFormattedPriceValues(): void
    {
        $baseCurrency = 'USD';
        $priceData = [
            ['website_id' => 1, 'price' => 10.50],
            ['website_id' => 2, 'price' => 20.00]
        ];
        $formattedPrice1 = '10.50';
        $formattedPrice2 = '20.00';

        $currencyMock = $this->createMock(FrameworkCurrency::class);
        $currencyMock->expects($this->exactly(2))
            ->method('toCurrency')
            ->willReturnOnConsecutiveCalls($formattedPrice1, $formattedPrice2);

        $this->localeCurrencyMock->expects($this->once())
            ->method('getCurrency')
            ->with($baseCurrency)
            ->willReturn($currencyMock);

        $this->directoryHelperMock->expects($this->once())
            ->method('getBaseCurrencyCode')
            ->willReturn($baseCurrency);

        // Mock isScopeGlobal to make isShowWebsiteColumn return false
        $attributeMock = $this->createMock(Attribute::class);
        $attributeMock->expects($this->any())
            ->method('isScopeGlobal')
            ->willReturn(true);

        $elementMock = $this->getMockBuilder(AbstractElement::class)
            ->disableOriginalConstructor()
            ->addMethods(['getValue', 'getEntityAttribute'])
            ->getMock();
        $elementMock->expects($this->once())
            ->method('getValue')
            ->willReturn($priceData);
        $elementMock->expects($this->any())
            ->method('getEntityAttribute')
            ->willReturn($attributeMock);

        $this->block->setElement($elementMock);

        $result = $this->block->getValues();

        $this->assertCount(2, $result);
        $this->assertEquals($formattedPrice1, $result[0]['price']);
        $this->assertEquals($formattedPrice2, $result[1]['price']);
        $this->assertFalse($result[0]['readonly']);
        $this->assertFalse($result[1]['readonly']);
    }

    /**
     * Test getValues returns empty array when element value is not array
     *
     * @return void
     */
    public function testGetValuesReturnsEmptyArrayWhenElementValueIsNotArray(): void
    {
        $baseCurrency = 'USD';

        $currencyMock = $this->createMock(FrameworkCurrency::class);
        $currencyMock->expects($this->never())
            ->method('toCurrency');

        $this->localeCurrencyMock->expects($this->once())
            ->method('getCurrency')
            ->with($baseCurrency)
            ->willReturn($currencyMock);

        $this->directoryHelperMock->expects($this->once())
            ->method('getBaseCurrencyCode')
            ->willReturn($baseCurrency);

        $elementMock = $this->getMockBuilder(AbstractElement::class)
            ->disableOriginalConstructor()
            ->addMethods(['getValue'])
            ->getMock();
        $elementMock->expects($this->once())
            ->method('getValue')
            ->willReturn(null);

        $this->block->setElement($elementMock);

        $result = $this->block->getValues();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test getValues sets readonly flag correctly
     *
     * @return void
     */
    public function testGetValuesSetsReadonlyFlagCorrectly(): void
    {
        $baseCurrency = 'USD';
        $priceData = [
            ['website_id' => 0, 'price' => 10.00],
            ['website_id' => 1, 'price' => 15.00]
        ];

        $currencyMock = $this->createMock(FrameworkCurrency::class);
        $currencyMock->expects($this->exactly(2))
            ->method('toCurrency')
            ->willReturn('10.00');

        $this->localeCurrencyMock->expects($this->once())
            ->method('getCurrency')
            ->willReturn($currencyMock);

        $this->directoryHelperMock->expects($this->once())
            ->method('getBaseCurrencyCode')
            ->willReturn($baseCurrency);

        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn(1);

        $attributeMock = $this->createMock(Attribute::class);
        $attributeMock->expects($this->any())
            ->method('isScopeGlobal')
            ->willReturn(false);

        $elementMock = $this->getMockBuilder(AbstractElement::class)
            ->disableOriginalConstructor()
            ->addMethods(['getEntityAttribute', 'getValue'])
            ->getMock();
        $elementMock->expects($this->any())
            ->method('getEntityAttribute')
            ->willReturn($attributeMock);
        $elementMock->expects($this->once())
            ->method('getValue')
            ->willReturn($priceData);

        $this->storeManagerMock->expects($this->any())
            ->method('isSingleStoreMode')
            ->willReturn(false);

        $this->registryMock->expects($this->any())
            ->method('registry')
            ->willReturn($productMock);

        $this->block->setElement($elementMock);

        $result = $this->block->getValues();

        $this->assertCount(2, $result);
        // First item has website_id = 0 and conditions met, should be readonly
        $this->assertTrue($result[0]['readonly']);
        // Second item has website_id != 0, should not be readonly
        $this->assertFalse($result[1]['readonly']);
    }
}
