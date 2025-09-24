<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Block\Rss;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Rss\Category;
use Magento\Catalog\Helper\Data;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\Tree;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Rss\UrlBuilderInterface;
use Magento\Framework\Config\View;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\DataObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Framework\View\ConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CategoryTest extends TestCase
{
    /**
     * @var Category
     */
    protected $block;

    /**
     * @var Context|MockObject
     */
    protected $httpContext;

    /**
     * @var Data|MockObject
     */
    protected $catalogHelper;

    /**
     * @var MockObject
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Catalog\Model\Rss\Category|MockObject
     */
    protected $rssModel;

    /**
     * @var UrlBuilderInterface|MockObject
     */
    protected $rssUrlBuilder;

    /**
     * @var Image|MockObject
     */
    protected $imageHelper;

    /**
     * @var Session|MockObject
     */
    protected $customerSession;

    /**
     * @var StoreManagerInterface|MockObject
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    protected $scopeConfig;

    /**
     * @var RequestInterface|MockObject
     */
    protected $request;

    /**
     * @var CategoryRepositoryInterface|MockObject
     */
    protected $categoryRepository;

    /**
     * @var ConfigInterface|MockObject
     */
    protected $viewConfig;

    /**
     * @var View
     */
    protected $configView;

    /**
     * @var array
     */
    protected $rssFeed = [
        'title' => 'Category Name',
        'description' => 'Category Name',
        'link' => 'http://magento.com/category-name.html',
        'charset' => 'UTF-8',
        'entries' => [
            [
                'title' => 'Product Name',
                'link' => 'http://magento.com/product.html'
            ]
        ]
    ];

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->request = $this->createMock(RequestInterface::class);
        $this->request
            ->method('getParam')
            ->willReturnCallback(fn($param) => match ([$param]) {
                ['cid'] => 1,
                ['store_id'] => null
            });

        $this->httpContext = $this->createMock(Context::class);
        $this->catalogHelper = $this->createMock(Data::class);
        $this->categoryFactory = $this->getMockBuilder(CategoryFactory::class)
            ->onlyMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->rssModel = $this->createPartialMock(
            \Magento\Catalog\Model\Rss\Category::class,
            ['getProductCollection']
        );
        $this->rssUrlBuilder = $this->createMock(UrlBuilderInterface::class);
        $this->imageHelper = $this->createMock(Image::class);
        $this->customerSession = $this->createPartialMock(Session::class, ['getId']);
        $this->customerSession->method('getId')->willReturn(1);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $store = $this->getMockBuilder(Store::class)
            ->onlyMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMock();
        $store->method('getId')->willReturn(1);
        $this->storeManager->method('getStore')->willReturn($store);
        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $this->categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $this->viewConfig = $this->getMockBuilder(ConfigInterface::class)
            ->getMock();
        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->block = $objectManagerHelper->getObject(
            Category::class,
            [
                'request' => $this->request,
                'scopeConfig' => $this->scopeConfig,
                'httpContext' => $this->httpContext,
                'catalogData' => $this->catalogHelper,
                'categoryFactory' => $this->categoryFactory,
                'rssModel' => $this->rssModel,
                'rssUrlBuilder' => $this->rssUrlBuilder,
                'imageHelper' => $this->imageHelper,
                'customerSession' => $this->customerSession,
                'storeManager' => $this->storeManager,
                'categoryRepository' => $this->categoryRepository,
                'viewConfig' => $this->viewConfig
            ]
        );
    }

    /**
     * @return void
     */
    public function testGetRssData(): void
    {
        $category = $this->getMockBuilder(\Magento\Catalog\Model\Category::class)
            ->onlyMethods(['__sleep', 'load', 'getId', 'getUrl', 'getName'])
            ->disableOriginalConstructor()
            ->getMock();
        $category->expects($this->once())->method('getName')->willReturn('Category Name');
        $category->expects($this->once())->method('getUrl')
            ->willReturn('http://magento.com/category-name.html');

        $this->categoryRepository->expects($this->once())->method('get')->willReturn($category);

        $configViewMock = $this->getMockBuilder(View::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->viewConfig->expects($this->once())
            ->method('getViewConfig')
            ->willReturn($configViewMock);

        $product = new class extends Product {
            public function __construct()
            {
                // Empty constructor for test
            }
            
            public function __sleep()
            {
                return [];
            }
            
            public function getName()
            {
                return 'Product Name';
            }
            
            public function getProductUrl($useSid = null)
            {
                return 'http://magento.com/product.html';
            }
            
            public function getAllowedInRss()
            {
                return true;
            }
            
            public function getDescription()
            {
                return 'Product Description';
            }
            
            public function getAllowedPriceInRss()
            {
                return true;
            }
        };

        $this->rssModel->expects($this->once())->method('getProductCollection')
            ->willReturn([$product]);
        $this->imageHelper->expects($this->once())->method('init')
            ->with($product, 'rss_thumbnail')->willReturnSelf();
        $this->imageHelper->expects($this->once())->method('getUrl')
            ->willReturn('image_link');

        $data = $this->block->getRssData();
        $this->assertEquals($this->rssFeed['link'], $data['link']);
        $this->assertEquals($this->rssFeed['title'], $data['title']);
        $this->assertEquals($this->rssFeed['description'], $data['description']);
        $this->assertEquals($this->rssFeed['entries'][0]['title'], $data['entries'][0]['title']);
        $this->assertEquals($this->rssFeed['entries'][0]['link'], $data['entries'][0]['link']);
        $this->assertStringContainsString(
            '<a href="http://magento.com/product.html">',
            $data['entries'][0]['description']
        );
        $this->assertStringContainsString(
            '<img src="image_link" border="0" align="left" height="75" width="75">',
            $data['entries'][0]['description']
        );

        $this->assertStringContainsString(
            '<td  style="text-decoration:none;">Product Description </td>',
            $data['entries'][0]['description']
        );
    }

    /**
     * @return void
     */
    public function testGetCacheLifetime(): void
    {
        $this->assertEquals(600, $this->block->getCacheLifetime());
    }

    /**
     * @return void
     */
    public function testIsAllowed(): void
    {
        $this->scopeConfig->expects($this->once())->method('isSetFlag')
            ->with('rss/catalog/category', ScopeInterface::SCOPE_STORE)
            ->willReturn(true);
        $this->assertTrue($this->block->isAllowed());
    }

    /**
     * @return void
     */
    public function testGetFeeds(): void
    {
        $this->scopeConfig->expects($this->once())->method('isSetFlag')
            ->with('rss/catalog/category', ScopeInterface::SCOPE_STORE)
            ->willReturn(true);

        $category = $this->getMockBuilder(\Magento\Catalog\Model\Category::class)
            ->onlyMethods(['__sleep', 'getTreeModel', 'getResourceCollection', 'getId', 'getName'])
            ->disableOriginalConstructor()
            ->getMock();

        $collection = $this->getMockBuilder(Collection::class)
            ->onlyMethods(
                [
                    'addIdFilter',
                    'addAttributeToSelect',
                    'addAttributeToSort',
                    'load',
                    'addAttributeToFilter',
                    'getIterator'
                ]
            )
            ->disableOriginalConstructor()
            ->getMock();
        $collection->expects($this->once())->method('addIdFilter')->willReturnSelf();
        $collection->expects($this->exactly(3))->method('addAttributeToSelect')->willReturnSelf();
        $collection->expects($this->once())->method('addAttributeToSort')->willReturnSelf();
        $collection->expects($this->once())->method('addAttributeToFilter')->willReturnSelf();
        $collection->expects($this->once())->method('load')->willReturnSelf();
        $collection->expects($this->once())->method('getIterator')
            ->willReturn(new \ArrayIterator([$category]));
        $category->expects($this->once())->method('getId')->willReturn(1);
        $category->expects($this->once())->method('getName')->willReturn('Category Name');
        $category->expects($this->once())->method('getResourceCollection')->willReturn($collection);
        $this->categoryFactory->expects($this->once())->method('create')->willReturn($category);

        $node = new DataObject(['id' => 1]);
        $nodes = $this->getMockBuilder(Node::class)
            ->onlyMethods(['getChildren'])
            ->disableOriginalConstructor()
            ->getMock();
        $nodes->expects($this->once())->method('getChildren')->willReturn([$node]);

        $tree = new class extends Tree {
            private $nodes;
            
            public function __construct()
            {
                // Empty constructor for test
            }
            
            public function loadNode($nodeId)
            {
                return $this;
            }
            
            public function loadChildren()
            {
                return $this->nodes;
            }
            
            public function setNodes($nodes)
            {
                $this->nodes = $nodes;
                return $this;
            }
        };
        $tree->setNodes($nodes);

        $category->expects($this->once())->method('getTreeModel')->willReturn($tree);
        $category->expects($this->once())->method('getResourceCollection')->willReturn('');

        $this->rssUrlBuilder->expects($this->once())->method('getUrl')
            ->willReturn('http://magento.com/category-name.html');
        $feeds = [
            'group' => 'Categories',
            'feeds' => [
                ['label' => 'Category Name', 'link' => 'http://magento.com/category-name.html'],
            ]
        ];
        $this->assertEquals($feeds, $this->block->getFeeds());
    }
}
