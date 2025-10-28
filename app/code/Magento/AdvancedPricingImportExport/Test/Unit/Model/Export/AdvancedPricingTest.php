<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\AdvancedPricingImportExport\Test\Unit\Model\Export;

use Magento\AdvancedPricingImportExport\Model\Export\AdvancedPricing;
use Magento\Catalog\Model\Product\LinkTypeProvider;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogImportExport\Model\Export\Product;
use Magento\CatalogImportExport\Model\Export\Product\Type\Factory;
use Magento\CatalogImportExport\Model\Export\RowCustomizer\Composite;
use Magento\CatalogImportExport\Model\Import\Product\StoreResolver;
use Magento\CatalogInventory\Model\ResourceModel\Stock\ItemFactory;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Type;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as AttributeSetCollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Logger\Monolog;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\ImportExport\Model\Export\Adapter\AbstractAdapter;
use Magento\ImportExport\Model\Export\ConfigInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\AdvancedPricingImportExport\Test\Unit\Helper\AdvancedPricingExportTestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection as MagentoAbstractCollection;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\ImportExport\Model\Export\Config as ExportConfig;
use ReflectionClass;
use stdClass;

/**
 * @SuppressWarnings(PHPMD)
 */
class AdvancedPricingTest extends TestCase
{
    /**
     * @var Timezone|MockObject
     */
    protected $localeDate;

    /**
     * @var Config|MockObject
     */
    protected $config;

    /**
     * @var ResourceConnection|MockObject
     */
    protected $resource;

    /**
     * @var StoreManagerInterface|MockObject
     */
    protected $storeManager;

    /**
     * @var LoggerInterface|MockObject
     */
    protected $logger;

    /**
     * @var ProductCollection|MockObject
     */
    protected $collection;

    /**
     * @var MagentoAbstractCollection|MockObject
     */
    protected $abstractCollection;

    /**
     * @var ConfigInterface|MockObject
     */
    protected $exportConfig;

    /**
     * @var ProductFactory|MockObject
     */
    protected $productFactory;

    /**
     * @var MockObject
     */
    protected $attrSetColFactory;

    /**
     * @var CategoryCollectionFactory|MockObject
     */
    protected $categoryColFactory;

    /**
     * @var ItemFactory|MockObject
     */
    protected $itemFactory;

    /**
     * @var MockObject
     */
    protected $optionColFactory;

    /**
     * @var MockObject
     */
    protected $attributeColFactory;

    /**
     * @var Factory|MockObject
     */
    protected $typeFactory;

    /**
     * @var LinkTypeProvider|MockObject
     */
    protected $linkTypeProvider;

    /**
     * @var Composite|MockObject
     */
    protected $rowCustomizer;

    /**
     * @var StoreResolver|MockObject
     */
    protected $storeResolver;

    /**
     * @var GroupRepositoryInterface|MockObject
     */
    protected $groupRepository;

    /**
     * @var AbstractAdapter|MockObject
     */
    protected $writer;

    /**
     * @var AdvancedPricing|MockObject
     */
    protected $advancedPricing;

    /**
     * @var StubProduct|Product
     */
    protected $object;

    /**
     * Set Up
     */
    protected function setUp(): void
    {
        $this->localeDate = $this->createMock(Timezone::class);
        $this->config = $this->createMock(Config::class);
        $this->resource = $this->createMock(ResourceConnection::class);
        $this->storeManager = $this->createMock(StoreManager::class);
        $this->logger = $this->createMock(Monolog::class);
        $this->collection = $this->createMock(ProductCollection::class);
        $this->abstractCollection = $this->createPartialMock(MagentoAbstractCollection::class, [
            'count',
            // 'setOrder',
            'setStoreId',
            'getCurPage',
            'getLastPageNumber',
        ]);
        $this->exportConfig = $this->createMock(ExportConfig::class);
        $this->productFactory = $this->createMock(stdClass::class);
        
        $this->attrSetColFactory = $this->createMock(stdClass::class);
        $this->categoryColFactory = $this->createMock(stdClass::class);
        $this->itemFactory = $this->createMock(stdClass::class);
        $this->optionColFactory = $this->createMock(stdClass::class);
        $this->attributeColFactory = $this->createMock(stdClass::class);
        $this->typeFactory = $this->createMock(Factory::class);
        $this->linkTypeProvider = $this->createMock(LinkTypeProvider::class);
        $this->rowCustomizer = $this->createMock(
            Composite::class
        );
        $this->storeResolver = $this->createMock(
            StoreResolver::class
        );
        $this->groupRepository = $this->createMock(GroupRepositoryInterface::class);
        $this->writer = $this->createPartialMock(
            AbstractAdapter::class,
            [
                'setHeaderCols',
                'writeRow',
                'getContents',
            ]
        );
        $constructorMethods = [
            'initTypeModels',
            'initAttributes',
            '_initStores',
            'initAttributeSets',
            'initWebsites',
            'initCategories'
        ];
        $mockAddMethods = [
            '_headerColumns'
        ];
        $mockMethods = array_merge($constructorMethods, [
            '_customHeadersMapping',
            '_prepareEntityCollection',
            '_getEntityCollection',
            'getWriter',
            'getExportData',
            '_customFieldsMapping',
            'getItemsPerPage',
            'paginateCollection',
            '_getHeaderColumns',
            '_getWebsiteCode',
            '_getCustomerGroupById',
            'correctExportData'
        ]);
        
        $this->advancedPricing = new AdvancedPricingExportTestHelper();
        
        // Manually set the required properties that would normally be set by the parent constructor
        $this->setPropertyValue($this->advancedPricing, '_storeResolver', $this->storeResolver);
        $this->setPropertyValue($this->advancedPricing, '_groupRepository', $this->groupRepository);
        $this->setPropertyValue($this->advancedPricing, '_resource', $this->resource);
    }

    /**
     * Test export with zero condition
     */
    public function testExportZeroConditionCalls()
    {
        $page = 1;
        $itemsPerPage = 10;

        $this->advancedPricing->setWriter($this->writer);
        $this->advancedPricing->_getEntityCollection();
        $this->advancedPricing->_prepareEntityCollection($this->abstractCollection);
        $this->advancedPricing->_setEntityCollection($this->abstractCollection);
        $this->advancedPricing->_setHeaderColumns([]);
        // $this->abstractCollection->expects($this->once())->method('setOrder')->with('has_options', 'asc');
        // $this->abstractCollection->expects($this->once())->method('setStoreId')->with(Store::DEFAULT_STORE_ID);
        $this->abstractCollection->expects($this->once())->method('count')->willReturn(0);
        $this->abstractCollection->expects($this->never())->method('getCurPage');
        $this->abstractCollection->expects($this->never())->method('getLastPageNumber');
        $this->writer->expects($this->never())->method('setHeaderCols');
        $this->writer->expects($this->never())->method('writeRow');
        $this->writer->expects($this->once())->method('getContents');
        $this->advancedPricing->export();
    }

    /**
     * Test export for current page
     */
    public function testExportCurrentPageCalls()
    {
        $curPage = $lastPage = $page = 1;
        $itemsPerPage = 10;
        $this->advancedPricing->setWriter($this->writer);
        $this->advancedPricing->_getEntityCollection();
        $this->advancedPricing->_prepareEntityCollection($this->abstractCollection);
        $this->advancedPricing->_setEntityCollection($this->abstractCollection);
        // $this->abstractCollection->expects($this->once())->method('setOrder')->with('has_options', 'asc');
        // $this->abstractCollection->expects($this->once())->method('setStoreId')->with(Store::DEFAULT_STORE_ID);
        $this->abstractCollection->expects($this->once())->method('count')->willReturn(1);
        $this->abstractCollection->expects($this->once())->method('getCurPage')->willReturn($curPage);
        $this->abstractCollection->expects($this->once())->method('getLastPageNumber')->willReturn($lastPage);
        $headers = ['headers'];
        $this->advancedPricing->_setHeaderColumns($headers);
        $webSite = 'All Websites [USD]';
        $userGroup = 'General';
        $this->advancedPricing->setWebsiteCode($webSite);
        $this->advancedPricing->setCustomerGroup($userGroup);
        $data = [
            [
                'sku' => 'simpletest',
                'tier_price_website' => $webSite,
                'tier_price_customer_group' => $userGroup,
                'tier_price_qty' => '2',
                'tier_price' => '23',
            ]
        ];
        $this->advancedPricing->setExportData($data);
        $exportData = [
            'sku' => 'simpletest',
            'tier_price_website' => $webSite,
            'tier_price_customer_group' => $userGroup,
            'tier_price_qty' => '2',
            'tier_price' => '23',
        ];
        $this->advancedPricing->setCustomExportData($exportData);
        $this->writer->expects($this->once())->method('writeRow')->with($exportData);
        $this->writer->expects($this->once())->method('getContents');
        $this->advancedPricing->export();
    }

    /**
     * tearDown
     */
    protected function tearDown(): void
    {
        unset($this->object);
    }

    /**
     * Get any object property value.
     *
     * @param $object
     * @param $property
     * @return mixed
     * @throws \ReflectionException
     */
    protected function getPropertyValue($object, $property)
    {
        $reflection = new ReflectionClass(get_class($object));
        $reflectionProperty = $reflection->getProperty($property);
        $reflectionProperty->setAccessible(true);
        return $reflectionProperty->getValue($object);
    }

    /**
     * Set object property value.
     *
     * @param $object
     * @param $property
     * @param $value
     * @return mixed
     * @throws \ReflectionException
     */
    protected function setPropertyValue(&$object, $property, $value)
    {
        $reflection = new ReflectionClass(get_class($object));
        $reflectionProperty = $reflection->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
        return $object;
    }
}
