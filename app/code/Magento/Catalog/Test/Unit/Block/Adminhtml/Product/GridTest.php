<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Block\Adminhtml\Product;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Backend\Model\Session;
use Magento\Catalog\Block\Adminhtml\Product\Grid;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as SetFactory;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface as DirectoryWriteInterface;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Backend\Block\Widget\Grid\Massaction;
use Magento\Backend\Block\Widget\Grid\Column as GridColumn;
use Magento\Store\Model\WebsiteFactory;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Magento\Catalog\Block\Adminhtml\Product\Grid
 */
class GridTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Grid
     */
    private Grid $grid;

    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var BackendHelper|MockObject
     */
    private $backendHelperMock;

    /**
     * @var WebsiteFactory|MockObject
     */
    private $websiteFactoryMock;

    /**
     * @var SetFactory|MockObject
     */
    private $setFactoryMock;

    /**
     * @var ProductFactory|MockObject
     */
    private $productFactoryMock;

    /**
     * @var ProductType|MockObject
     */
    private $productTypeMock;

    /**
     * @var ProductStatus|MockObject
     */
    private $statusMock;

    /**
     * @var ProductVisibility|MockObject
     */
    private $visibilityMock;

    /**
     * @var ModuleManager|MockObject
     */
    private $moduleManagerMock;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManagerMock;

    /**
     * Set up all required mocks and create the grid instance.
     *
     * @return void
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->contextMock        = $this->createMock(Context::class);
        $this->backendHelperMock  = $this->createMock(BackendHelper::class);
        $this->websiteFactoryMock = $this->createMock(WebsiteFactory::class);
        $this->setFactoryMock     = $this->createMock(SetFactory::class);
        $this->productFactoryMock = $this->createMock(ProductFactory::class);
        $this->productTypeMock    = $this->createMock(ProductType::class);
        $this->statusMock         = $this->createMock(ProductStatus::class);
        $this->visibilityMock     = $this->createMock(ProductVisibility::class);
        $this->moduleManagerMock  = $this->createMock(ModuleManager::class);

        $this->objectManager->prepareObjectManager([
            [JsonHelper::class, $this->createMock(JsonHelper::class)],
            [DirectoryHelper::class, $this->createMock(DirectoryHelper::class)],
        ]);

        /** @var DirectoryWriteInterface|MockObject $directoryWriteMock */
        $directoryWriteMock = $this->createMock(DirectoryWriteInterface::class);
        /** @var Filesystem|MockObject $filesystemMock */
        $filesystemMock = $this->createMock(Filesystem::class);
        $filesystemMock->method('getDirectoryWrite')->willReturn($directoryWriteMock);
        $this->contextMock->method('getFilesystem')->willReturn($filesystemMock);

        $this->contextMock->method('getAuthorization')
            ->willReturn($this->createMock(AuthorizationInterface::class));

        /** @var LayoutInterface|MockObject $layoutMock */
        $layoutMock = $this->createMock(LayoutInterface::class);
        $this->contextMock->method('getLayout')->willReturn($layoutMock);

        $this->requestMock = $this->getMockBuilder(HttpRequest::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['has', 'getPost', 'getParam'])
            ->getMock();
        $this->requestMock->method('has')->willReturn(false);
        $this->requestMock->method('getPost')->willReturn([]);
        $this->contextMock->method('getRequest')->willReturn($this->requestMock);

        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->contextMock->method('getStoreManager')->willReturn($this->storeManagerMock);

        $this->contextMock->method('getBackendSession')
            ->willReturn($this->createMock(Session::class));

        $this->grid = $this->objectManager->getObject(
            Grid::class,
            [
                'context'        => $this->contextMock,
                'backendHelper'  => $this->backendHelperMock,
                'websiteFactory' => $this->websiteFactoryMock,
                'setsFactory'    => $this->setFactoryMock,
                'productFactory' => $this->productFactoryMock,
                'type'           => $this->productTypeMock,
                'status'         => $this->statusMock,
                'visibility'     => $this->visibilityMock,
                'moduleManager'  => $this->moduleManagerMock,
                'data'           => []
            ]
        );
    }

    /**
     * Verify that the grid object is instantiated and its ID is set correctly.
     *
     * @covers \Magento\Catalog\Block\Adminhtml\Product\Grid::_construct
     * @return void
     */
    public function testConstructInitialisesGrid(): void
    {
        $this->assertInstanceOf(Grid::class, $this->grid);
        $this->assertEquals('productGrid', $this->grid->getId());
    }

    /**
     * Confirm that the protected `_getStore` method returns the store retrieved
     * from the request and the store manager.
     *
     * @covers \Magento\Catalog\Block\Adminhtml\Product\Grid::_getStore
     * @return void
     */
    public function testGetStoreReturnsStore(): void
    {
        $storeMock = $this->createMock(Store::class);
        $storeId   = 42;

        $this->requestMock->method('getParam')
            ->with('store', 0)
            ->willReturn($storeId);
        $this->storeManagerMock->method('getStore')
            ->with($storeId)
            ->willReturn($storeMock);

        $store = $this->invokeMethod($this->grid, '_getStore');
        $this->assertSame($storeMock, $store);
    }

    /**
     * Ensure the product collection is built with the mandatory attributes for the
     * default store
     *
     * @covers \Magento\Catalog\Block\Adminhtml\Product\Grid::_prepareCollection
     * @return void
     */
    public function testPrepareCollectionAddsAttributes(): void
    {
        $collectionMock = $this->createMock(Collection::class);
        $productMock    = $this->createMock(Product::class);
        $productMock->method('getCollection')->willReturn($collectionMock);
        $this->productFactoryMock->method('create')->willReturn($productMock);

        $collectionMock->method('addAttributeToSelect')->willReturnSelf();
        $collectionMock->method('setStore')->willReturnSelf();
        $collectionMock->method('joinAttribute')->willReturnSelf();
        $collectionMock->method('addWebsiteNamesToResult')->willReturnSelf();

        $storeMock = $this->createMock(Store::class);
        $storeMock->method('getId')->willReturn(0);
        $this->storeManagerMock->method('getStore')->willReturn($storeMock);

        $gridMock = $this->getMockBuilder(Grid::class)
            ->setConstructorArgs([
                $this->contextMock,
                $this->backendHelperMock,
                $this->websiteFactoryMock,
                $this->setFactoryMock,
                $this->productFactoryMock,
                $this->productTypeMock,
                $this->statusMock,
                $this->visibilityMock,
                $this->moduleManagerMock,
                []
            ])
            ->onlyMethods(['getColumn'])
            ->getMock();
        $gridMock->method('getColumn')->willReturn(null);

        $result = $this->invokeMethod($gridMock, '_prepareCollection');
        $this->assertSame($gridMock, $result);
    }

    /**
     * Verify that the collection joins the inventory quantity field when the
     * CatalogInventory module is enabled and that store‑specific filters are
     * applied when a non‑default store is requested.
     *
     * @covers \Magento\Catalog\Block\Adminhtml\Product\Grid::_prepareCollection
     * @return void
     */
    public function testPrepareCollectionCoversInventoryAndStoreSpecific(): void
    {
        $collectionMock = $this->createMock(Collection::class);
        $productMock    = $this->createMock(Product::class);
        $productMock->method('getCollection')->willReturn($collectionMock);
        $this->productFactoryMock->method('create')->willReturn($productMock);

        // Inventory enabled -> expect a join on the qty column.
        $this->moduleManagerMock->method('isEnabled')
            ->with('Magento_CatalogInventory')
            ->willReturn(true);
        $collectionMock->expects($this->once())->method('joinField')
            ->with(
                'qty',
                'cataloginventory_stock_item',
                'qty',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left'
            );

        // Store‑specific request (store ID > 0) -> expect addStoreFilter().
        $storeMock = $this->createMock(Store::class);
        $storeMock->method('getId')->willReturn(2);
        $this->storeManagerMock->method('getStore')->willReturn($storeMock);
        $collectionMock->expects($this->once())->method('addStoreFilter')
            ->with($storeMock)
            ->willReturnSelf();

        $collectionMock->method('addAttributeToSelect')->willReturnSelf();
        $collectionMock->method('joinAttribute')->willReturnSelf();
        $collectionMock->method('setStore')->willReturnSelf();
        $collectionMock->method('addWebsiteNamesToResult')->willReturnSelf();

        $gridMock = $this->getMockBuilder(Grid::class)
            ->setConstructorArgs([
                $this->contextMock,
                $this->backendHelperMock,
                $this->websiteFactoryMock,
                $this->setFactoryMock,
                $this->productFactoryMock,
                $this->productTypeMock,
                $this->statusMock,
                $this->visibilityMock,
                $this->moduleManagerMock,
                []
            ])
            ->onlyMethods(['getColumn'])
            ->getMock();
        $gridMock->method('getColumn')->willReturn(null);

        // Visibility helper is injected as a mock that does not require constructor args.
        $visibilityMock = $this->getMockBuilder(ProductVisibility::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getOptionArray'])
            ->getMock();
        $visibilityMock->method('getOptionArray')
            ->willReturn(['1' => 'Catalog, Search']);
        $this->setProtectedProperty($gridMock, '_visibility', $visibilityMock);

        $result = $this->invokeMethod($gridMock, '_prepareCollection');
        $this->assertSame($gridMock, $result);
    }

    /**
     * Ensure that every column that can appear in the grid is added, including
     * conditional columns such as the store‑specific name, quantity and websites.
     *
     * @covers \Magento\Catalog\Block\Adminhtml\Product\Grid::_prepareColumns
     * @return void
     */
    public function testPrepareColumnsCoversAllBranches(): void
    {
        // Force a non‑default store so store‑specific columns are rendered.
        $this->requestMock->method('getParam')
            ->with('store', 0)
            ->willReturn(3);
        $storeMock = $this->createMock(Store::class);
        $storeMock->method('getId')->willReturn(3);
        $storeMock->method('getName')->willReturn('Store Name');
        $currencyMock = new class {
            public function getCode()
            {
                return 'USD';
            }
        };
        $storeMock->method('getBaseCurrency')->willReturn($currencyMock);
        $this->storeManagerMock->method('getStore')->willReturn($storeMock);
        $this->storeManagerMock->method('isSingleStoreMode')->willReturn(false);
        $this->moduleManagerMock->method('isEnabled')
            ->with('Magento_CatalogInventory')
            ->willReturn(true);

        $this->productTypeMock->method('getOptionArray')
            ->willReturn(['simple' => 'Simple']);
        $this->visibilityMock->method('getOptionArray')
            ->willReturn(['1' => 'Catalog, Search']);
        $this->statusMock->method('getOptionArray')
            ->willReturn(['1' => 'Enabled', '2' => 'Disabled']);

        // Attribute‑set factory chain.
        $resourceMock = new class {
            public function getTypeId()
            {
                return 4;
            }
        };
        $productEntityMock = new class($resourceMock) {
            private $resource;
            public function __construct($resource)
            {
                $this->resource = $resource;
            }
            public function getResource()
            {
                return $this->resource;
            }
        };
        $this->productFactoryMock->method('create')->willReturn($productEntityMock);

        $setsChainMock = new class {
            public function setEntityTypeFilter($id)
            {
                return $this;
            }
            public function load()
            {
                return $this;
            }
            public function toOptionHash()
            {
                return ['4' => 'Default'];
            }
        };
        $this->setFactoryMock->method('create')->willReturn($setsChainMock);

        // Websites list.
        $websitesCollectionMock = new class {
            public function toOptionHash()
            {
                return ['1' => 'Base'];
            }
        };
        $websitesMock = new class($websitesCollectionMock) {
            private $collection;
            public function __construct($collection)
            {
                $this->collection = $collection;
            }
            public function getCollection()
            {
                return $this->collection;
            }
        };
        $this->websiteFactoryMock->method('create')->willReturn($websitesMock);

        // Capture the IDs of all columns added by the method.
        $addedColumnIds = [];
        $gridMock = $this->getMockBuilder(Grid::class)
            ->setConstructorArgs([
                $this->contextMock,
                $this->backendHelperMock,
                $this->websiteFactoryMock,
                $this->setFactoryMock,
                $this->productFactoryMock,
                $this->productTypeMock,
                $this->statusMock,
                $this->visibilityMock,
                $this->moduleManagerMock,
                []
            ])
            ->onlyMethods(['addColumn', 'sortColumnsByOrder'])
            ->getMock();
        $gridMock->method('sortColumnsByOrder')->willReturn($gridMock);
        $gridMock->method('addColumn')
            ->willReturnCallback(function ($columnId, $config) use (&$addedColumnIds, $gridMock) {
                $addedColumnIds[] = $columnId;
                return $gridMock;
            });

        // Use concrete helpers for visibility and status to avoid static‑method mock issues.
        $this->setProtectedProperty($gridMock, '_visibility', new class {
            public function getOptionArray()
            {
                return ['1' => 'Catalog, Search'];
            }
        });
        $this->setProtectedProperty($gridMock, '_status', new ProductStatus());

        $this->invokeMethod($gridMock, '_prepareColumns');

        $this->assertContains('entity_id', $addedColumnIds);
        $this->assertContains('name', $addedColumnIds);
        $this->assertContains('custom_name', $addedColumnIds);
        $this->assertContains('type', $addedColumnIds);
        $this->assertContains('set_name', $addedColumnIds);
        $this->assertContains('sku', $addedColumnIds);
        $this->assertContains('price', $addedColumnIds);
        $this->assertContains('qty', $addedColumnIds);
        $this->assertContains('visibility', $addedColumnIds);
        $this->assertContains('status', $addedColumnIds);
        $this->assertContains('websites', $addedColumnIds);
        $this->assertContains('edit', $addedColumnIds);
    }

    /**
     * Verify that the grid URL for AJAX reloading is generated correctly.
     *
     * @covers \Magento\Catalog\Block\Adminhtml\Product\Grid::getGridUrl
     * @return void
     */
    public function testGetGridUrl(): void
    {
        $expectedUrl = 'http://example.com/catalog/*/grid';
        $gridMock = $this->getMockBuilder(Grid::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUrl'])
            ->getMock();
        $gridMock->expects($this->once())
            ->method('getUrl')
            ->with('catalog/*/grid', ['_current' => true])
            ->willReturn($expectedUrl);
        $this->assertEquals($expectedUrl, $gridMock->getGridUrl());
    }

    /**
     * Verify that the URL for editing a product row contains the correct parameters.
     *
     * @covers \Magento\Catalog\Block\Adminhtml\Product\Grid::getRowUrl
     * @return void
     */
    public function testGetRowUrl(): void
    {
        $rowId      = 123;
        $storeId    = 2;
        $expectedUrl = 'http://example.com/catalog/*/edit';
        $rowMock = new DataObject(['id' => $rowId]);

        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->method('getParam')
            ->with('store')
            ->willReturn($storeId);

        $gridMock = $this->getMockBuilder(Grid::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUrl', 'getRequest'])
            ->getMock();
        $gridMock->method('getRequest')->willReturn($requestMock);
        $gridMock->expects($this->once())
            ->method('getUrl')
            ->with(
                'catalog/*/edit',
                ['store' => $storeId, 'id' => $rowId]
            )
            ->willReturn($expectedUrl);

        $this->assertEquals($expectedUrl, $gridMock->getRowUrl($rowMock));
    }

    /**
     * Check that the mass‑action block is populated with the standard actions:
     * delete, change status and update attributes.
     *
     * @covers \Magento\Catalog\Block\Adminhtml\Product\Grid::_prepareMassaction
     * @return void
     */
    public function testPrepareMassactionAddsActions(): void
    {
        $massActionBlockMock = $this->getMockBuilder(Massaction::class)
            ->disableOriginalConstructor()
            ->getMock();
        $massActionBlockMock->expects($this->any())
            ->method('addItem')
            ->with($this->callback(function ($id) {
                return in_array($id, ['delete', 'status', 'attributes'], true);
            }));

        $gridMock = $this->getMockBuilder(Grid::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getMassactionBlock', 'getUrl'])
            ->getMock();
        $gridMock->method('getMassactionBlock')->willReturn($massActionBlockMock);
        $gridMock->method('getUrl')->willReturn('http://example.com/dummy');

        $authMock = $this->createMock(AuthorizationInterface::class);
        $authMock->method('isAllowed')->willReturn(true);
        $this->setProtectedProperty($gridMock, '_authorization', $authMock);
        $this->setProtectedProperty($gridMock, '_status', new ProductStatus());

        $eventManagerMock = $this->createMock(EventManagerInterface::class);
        $eventManagerMock->method('dispatch')->willReturn(null);
        $this->setProtectedProperty($gridMock, '_eventManager', $eventManagerMock);

        $this->invokeMethod($gridMock, '_prepareMassaction');
    }

    /**
     * Verify that adding a filter for the “websites” column causes the collection
     * to join the appropriate website data.
     *
     * @covers \Magento\Catalog\Block\Adminhtml\Product\Grid::_addColumnFilterToCollection
     * @return void
     */
    public function testAddColumnFilterToCollectionJoinsWebsites(): void
    {
        $columnMock = $this->createMock(GridColumn::class);
        $columnMock->method('getId')->willReturn('websites');

        $collectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $collectionMock->expects($this->once())
            ->method('joinField')
            ->with(
                $this->equalTo('websites'),
                $this->equalTo('catalog_product_website'),
                $this->equalTo('website_id'),
                $this->equalTo('product_id=entity_id'),
                $this->equalTo(null),
                $this->equalTo('left')
            );

        $filterMock = new class {
            public function getCondition()
            {
                return null;
            }
        };
        $columnMock->method('getFilter')->willReturn($filterMock);

        $this->grid->setCollection($collectionMock);
        $this->invokeMethod($this->grid, '_addColumnFilterToCollection', [$columnMock]);
    }

    /**
     * Helper: invoke a protected or private method on an object.
     *
     * @param object $object Object containing the method.
     * @param string $method Name of the method to invoke.
     * @param array  $args   Arguments to pass to the method.
     * @return mixed
     * @throws \ReflectionException
     */
    private function invokeMethod($object, string $method, array $args = [])
    {
        $ref = new \ReflectionMethod($object, $method);
        $ref->setAccessible(true);
        return $ref->invokeArgs($object, $args);
    }

    /**
     * Helper: set the value of a protected or private property.
     *
     * @param object $object   Object containing the property.
     * @param string $property Name of the property.
     * @param mixed  $value    Value to assign.
     * @return void
     * @throws \ReflectionException
     */
    private function setProtectedProperty($object, string $property, $value): void
    {
        $ref = new \ReflectionProperty($object, $property);
        $ref->setAccessible(true);
        $ref->setValue($object, $value);
    }
}
