<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Websites;
use Magento\Store\Api\GroupRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Model\Group;
use Magento\Store\Model\ResourceModel\Group\Collection;
use Magento\Store\Model\Store as StoreView;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class WebsitesTest extends AbstractModifierTestCase
{
    public const PRODUCT_ID = 1;
    public const WEBSITE_ID = 1;
    public const GROUP_ID = 1;
    public const STORE_VIEW_NAME = 'StoreView';
    public const STORE_VIEW_ID = 1;
    public const SECOND_WEBSITE_ID = 2;

    /**
     * @var WebsiteRepositoryInterface|MockObject
     */
    protected $websiteRepositoryMock;

    /**
     * @var GroupRepositoryInterface|MockObject
     */
    protected $groupRepositoryMock;

    /**
     * @var StoreRepositoryInterface|MockObject
     */
    protected $storeRepositoryMock;

    /**
     * @var StoreManagerInterface|MockObject
     */
    protected $storeManagerMock;

    /**
     * @var Website|MockObject
     */
    protected $websiteMock;

    /**
     * @var Website|MockObject
     */
    protected $secondWebsiteMock;

    /**
     * @var array
     */
    protected $assignedWebsites;

    /**
     * @var Group|MockObject
     */
    protected $groupMock;

    /**
     * @var StoreView|MockObject
     */
    protected $storeViewMock;

    /**
     * @var array
     */
    private $websitesList;

    /**
     * @var int
     */
    private $productId;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->assignedWebsites = [self::SECOND_WEBSITE_ID];
        $this->productId = self::PRODUCT_ID;
        $this->websiteMock = $this->getMockBuilder(Website::class)
            ->onlyMethods(['getId', 'getName'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->secondWebsiteMock = $this->getMockBuilder(Website::class)
            ->onlyMethods(['getId', 'getName'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->websitesList = [$this->websiteMock, $this->secondWebsiteMock];
        $this->websiteRepositoryMock = $this->createMock(WebsiteRepositoryInterface::class);
        $this->websiteRepositoryMock->method('getDefault')->willReturn($this->websiteMock);
        $this->groupRepositoryMock = $this->createMock(GroupRepositoryInterface::class);
        $this->storeRepositoryMock = $this->createMock(StoreRepositoryInterface::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->storeManagerMock->method('isSingleStoreMode')->willReturn(false);
        
        // PHPUnit 12 compatible: Replace addMethods with anonymous class
        $this->groupMock = new class extends Collection {
            private $id;
            private $name;
            private $websiteId;
            
            public function __construct()
            {
            }
            
            public function getId()
            {
                return $this->id;
            }
            public function setId($id)
            {
                $this->id = $id;
                return $this;
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
            public function getWebsiteId()
            {
                return $this->websiteId;
            }
            public function setWebsiteId($websiteId)
            {
                $this->websiteId = $websiteId;
                return $this;
            }
        };
        
        $this->groupMock->setWebsiteId(self::WEBSITE_ID);
        $this->groupMock->setId(self::GROUP_ID);
        $this->groupRepositoryMock->method('getList')->willReturn([$this->groupMock]);
        $this->storeViewMock = $this->getMockBuilder(StoreView::class)
            ->onlyMethods(['getName', 'getId', 'getStoreGroupId'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeViewMock->method('getName')->willReturn(self::STORE_VIEW_NAME);
        $this->storeViewMock->method('getStoreGroupId')->willReturn(self::GROUP_ID);
        $this->storeViewMock->method('getId')->willReturn(self::STORE_VIEW_ID);
        $this->storeRepositoryMock->method('getList')->willReturn([$this->storeViewMock]);
        $this->secondWebsiteMock->method('getId')->willReturn($this->assignedWebsites[0]);
        $this->websiteMock->method('getId')->willReturn(self::WEBSITE_ID);
        
        // Override parent mocks with anonymous classes that have setter methods
        /** @var \Magento\Catalog\Api\Data\ProductInterface $productMock */
        $this->productMock = new class {
            private $id;
            private $lockedAttribute;
            
            public function getId()
            {
                return $this->id;
            }
            public function setId($id)
            {
                $this->id = $id;
                return $this;
            }
            public function isLockedAttribute($attribute)
            {
                return $this->lockedAttribute;
            }
            public function setLockedAttribute($lockedAttribute)
            {
                $this->lockedAttribute = $lockedAttribute;
                return $this;
            }
        };
        
        /** @var \Magento\Catalog\Model\Locator\LocatorInterface $locatorMock */
        $this->locatorMock = new class implements \Magento\Catalog\Model\Locator\LocatorInterface {
            private $websiteIds;
            private $product;
            private $store;
            private $baseCurrencyCode;
            
            public function getWebsiteIds()
            {
                return $this->websiteIds;
            }
            public function setWebsiteIds($websiteIds)
            {
                $this->websiteIds = $websiteIds;
                return $this;
            }
            public function getProduct()
            {
                return $this->product;
            }
            public function setProduct($product)
            {
                $this->product = $product;
                return $this;
            }
            public function getStore()
            {
                return $this->store;
            }
            public function setStore($store)
            {
                $this->store = $store;
                return $this;
            }
            public function getBaseCurrencyCode()
            {
                return $this->baseCurrencyCode;
            }
            public function setBaseCurrencyCode($baseCurrencyCode)
            {
                $this->baseCurrencyCode = $baseCurrencyCode;
                return $this;
            }
        };
    }

    /**
     * @return Websites
     */
    protected function createModel()
    {
        // Create the model directly instead of using objectManager
        return new Websites(
            $this->locatorMock,
            $this->storeManagerMock,
            $this->websiteRepositoryMock,
            $this->groupRepositoryMock,
            $this->storeRepositoryMock
        );
    }

    /**
     * Initialize return values
     * @return void
     */
    private function init()
    {
        $this->productMock->setId($this->productId);
        $this->locatorMock->setWebsiteIds($this->assignedWebsites);
        $this->locatorMock->setProduct($this->productMock);
        $this->storeManagerMock->method('getWebsites')
            ->willReturn($this->websitesList);
    }

    /**
     * @return void
     */
    public function testModifyMeta()
    {
        $this->init();
        $meta = $this->getModel()->modifyMeta([]);

        $this->assertArrayHasKey('websites', $meta);
        $this->assertArrayHasKey(self::SECOND_WEBSITE_ID, $meta['websites']['children']);
        $this->assertArrayHasKey(self::WEBSITE_ID, $meta['websites']['children']);
        $this->assertArrayHasKey('copy_to_stores.' . self::WEBSITE_ID, $meta['websites']['children']);
        $this->assertEquals(
            $meta['websites']['children'][self::SECOND_WEBSITE_ID]['arguments']['data']['config']['value'],
            (string) self::SECOND_WEBSITE_ID
        );
        $this->assertEquals(
            $meta['websites']['children'][self::WEBSITE_ID]['arguments']['data']['config']['value'],
            '0'
        );
    }

    /**
     * @return void
     */
    public function testModifyData()
    {
        $expectedData = [
            self::PRODUCT_ID => [
                'product' => [
                    'copy_to_stores' => [
                        self::WEBSITE_ID => [
                            [
                                'storeView' => self::STORE_VIEW_NAME,
                                'copy_from' => 0,
                                'copy_to' => self::STORE_VIEW_ID,
                            ]
                        ]
                    ]
                ]
            ],
        ];
        $this->init();

        $this->assertEquals(
            $expectedData,
            $this->getModel()->modifyData([])
        );
    }

    public function testModifyDataNoWebsitesExistingProduct()
    {
        $this->assignedWebsites = [];
        $this->websitesList = [$this->websiteMock];
        $this->init();

        $meta = $this->getModel()->modifyMeta([]);

        $this->assertArrayHasKey(self::WEBSITE_ID, $meta['websites']['children']);
        $this->assertArrayHasKey('copy_to_stores.' . self::WEBSITE_ID, $meta['websites']['children']);
        $this->assertEquals(
            '0',
            $meta['websites']['children'][self::WEBSITE_ID]['arguments']['data']['config']['value']
        );
    }

    public function testModifyDataNoWebsitesNewProduct()
    {
        $this->assignedWebsites = [];
        $this->websitesList = [$this->websiteMock];
        $this->productId = false;
        $this->init();
        $this->productMock->setId(false);

        $meta = $this->getModel()->modifyMeta([]);

        $this->assertArrayHasKey(self::WEBSITE_ID, $meta['websites']['children']);
        $this->assertEquals(
            '1',
            $meta['websites']['children'][self::WEBSITE_ID]['arguments']['data']['config']['value']
        );
    }
}
