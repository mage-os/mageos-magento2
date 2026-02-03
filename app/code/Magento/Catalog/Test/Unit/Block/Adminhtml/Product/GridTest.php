<?php
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Block\Adminhtml\Product;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Catalog\Block\Adminhtml\Product\Grid;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ProductFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory as SetsFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\WebsiteFactory;
use Magento\Catalog\Model\Product\Visibility;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GridTest extends TestCase
{
    /**
     * @var UrlInterface|MockObject
     */
    private $urlBuilder;

    /**
     * @var RequestInterface|MockObject
     */
    private $request;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private $storeManager;

    /**
     * @var Store|MockObject
     */
    private $store;

    /** @var AuthorizationInterface|MockObject */
    private $authorization;

    /** @var EventManager|MockObject */
    private $eventManager;

    /** @var Context|MockObject */
    private $context;

    /** @var BackendHelper|MockObject */
    private $backendHelper;

    /** @var WebsiteFactory|MockObject */
    private $websiteFactory;

    /** @var SetsFactory|MockObject */
    private $setsFactory;

    /** @var ProductFactory|MockObject */
    private $productFactory;

    /** @var Type|MockObject */
    private $type;

    /** @var Status|MockObject */
    private $status;

    /** @var Visibility|MockObject */
    private $visibility;

    /** @var ModuleManager|MockObject */
    private $moduleManager;

    /** @var Grid */
    private $grid;

    protected function setUp(): void
    {
        $this->urlBuilder     = $this->createMock(UrlInterface::class);
        $this->request        = $this->createMock(RequestInterface::class);
        $this->storeManager   = $this->createMock(StoreManagerInterface::class);
        $this->store          = $this->createMock(Store::class);
        $this->authorization  = $this->createMock(AuthorizationInterface::class);
        $this->eventManager   = $this->createMock(EventManager::class);

        // Prepare global ObjectManager for optional helpers used inside Backend Template constructor
        $jsonHelperMock = $this->createMock(\Magento\Framework\Json\Helper\Data::class);
        $directoryHelperMock = $this->createMock(\Magento\Directory\Helper\Data::class);
        $deploymentConfigMock = $this->createMock(\Magento\Framework\App\DeploymentConfig::class);

        $objectManager = new ObjectManager($this);
        $objectManager->prepareObjectManager([
            [\Magento\Framework\Json\Helper\Data::class, $jsonHelperMock],
            [\Magento\Directory\Helper\Data::class, $directoryHelperMock],
            [\Magento\Framework\App\DeploymentConfig::class, $deploymentConfigMock],
        ]);

        // Build a rich Context mock stubbing all getters invoked by parent constructors
        $layout                = $this->createMock(\Magento\Framework\View\LayoutInterface::class);
        $cache                 = $this->createMock(\Magento\Framework\App\CacheInterface::class);
        $design                = $this->createMock(\Magento\Framework\View\DesignInterface::class);
        $session               = $this->createMock(\Magento\Framework\Session\SessionManagerInterface::class);
        $sidResolver           = $this->createMock(\Magento\Framework\Session\SidResolverInterface::class);
        $scopeConfig           = $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        $assetRepo             = $this->createMock(\Magento\Framework\View\Asset\Repository::class);
        $viewConfig            = $this->createMock(\Magento\Framework\View\ConfigInterface::class);
        $cacheState            = $this->createMock(\Magento\Framework\App\Cache\StateInterface::class);
        $logger                = $this->createMock(\Psr\Log\LoggerInterface::class);
        $escaper               = $this->createMock(\Magento\Framework\Escaper::class);
        $filterManager         = $this->createMock(\Magento\Framework\Filter\FilterManager::class);
        $localeDate            = $this->createMock(\Magento\Framework\Stdlib\DateTime\TimezoneInterface::class);
        $inlineTranslation     = $this->createMock(\Magento\Framework\Translate\Inline\StateInterface::class);
        $lockGuardedCacheLoader= $this->createMock(\Magento\Framework\Cache\LockGuardedCacheLoader::class);
        $filesystem            = $this->createMock(\Magento\Framework\Filesystem::class);
        $writeInterface        = $this->createMock(\Magento\Framework\Filesystem\Directory\WriteInterface::class);
        $enginePool            = $this->createMock(\Magento\Framework\View\TemplateEnginePool::class);
        $appState              = $this->createMock(\Magento\Framework\App\State::class);
        $pageConfig            = $this->createMock(\Magento\Framework\View\Page\Config::class);
        $validator             = $this->createMock(\Magento\Framework\View\Element\Template\File\Validator::class);
        $resolver              = $this->createMock(\Magento\Framework\View\Element\Template\File\Resolver::class);
        $mathRandom            = $this->createMock(\Magento\Framework\Math\Random::class);
        $backendSession        = $this->createMock(\Magento\Backend\Model\Session::class);
        $formKey               = $this->createMock(\Magento\Framework\Data\Form\FormKey::class);
        $nameBuilder           = $this->createMock(\Magento\Framework\Code\NameBuilder::class);

        // Filesystem is used in Extended::_construct()
        $filesystem->method('getDirectoryWrite')->willReturn($writeInterface);

        $this->context = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getUrlBuilder',
                'getRequest',
                'getStoreManager',
                'getAuthorization',
                'getEventManager',
                'getLayout',
                'getCache',
                'getDesignPackage',
                'getSession',
                'getSidResolver',
                'getScopeConfig',
                'getAssetRepository',
                'getViewConfig',
                'getCacheState',
                'getLogger',
                'getEscaper',
                'getFilterManager',
                'getLocaleDate',
                'getInlineTranslation',
                'getLockGuardedCacheLoader',
                'getFilesystem',
                'getEnginePool',
                'getAppState',
                'getPageConfig',
                'getValidator',
                'getResolver',
                'getMathRandom',
                'getBackendSession',
                'getFormKey',
                'getNameBuilder',
            ])
            ->getMock();

        $this->context->method('getUrlBuilder')->willReturn($this->urlBuilder);
        $this->context->method('getRequest')->willReturn($this->request);
        $this->context->method('getStoreManager')->willReturn($this->storeManager);
        $this->context->method('getAuthorization')->willReturn($this->authorization);
        $this->context->method('getEventManager')->willReturn($this->eventManager);

        $this->context->method('getLayout')->willReturn($layout);
        $this->context->method('getCache')->willReturn($cache);
        $this->context->method('getDesignPackage')->willReturn($design);
        $this->context->method('getSession')->willReturn($session);
        $this->context->method('getSidResolver')->willReturn($sidResolver);
        $this->context->method('getScopeConfig')->willReturn($scopeConfig);
        $this->context->method('getAssetRepository')->willReturn($assetRepo);
        $this->context->method('getViewConfig')->willReturn($viewConfig);
        $this->context->method('getCacheState')->willReturn($cacheState);
        $this->context->method('getLogger')->willReturn($logger);
        $this->context->method('getEscaper')->willReturn($escaper);
        $this->context->method('getFilterManager')->willReturn($filterManager);
        $this->context->method('getLocaleDate')->willReturn($localeDate);
        $this->context->method('getInlineTranslation')->willReturn($inlineTranslation);
        $this->context->method('getLockGuardedCacheLoader')->willReturn($lockGuardedCacheLoader);
        $this->context->method('getFilesystem')->willReturn($filesystem);
        $this->context->method('getEnginePool')->willReturn($enginePool);
        $this->context->method('getAppState')->willReturn($appState);
        $this->context->method('getPageConfig')->willReturn($pageConfig);
        $this->context->method('getValidator')->willReturn($validator);
        $this->context->method('getResolver')->willReturn($resolver);
        $this->context->method('getMathRandom')->willReturn($mathRandom);
        $this->context->method('getBackendSession')->willReturn($backendSession);
        $this->context->method('getFormKey')->willReturn($formKey);
        $this->context->method('getNameBuilder')->willReturn($nameBuilder);

        $this->backendHelper  = $this->createMock(BackendHelper::class);
        $this->websiteFactory = $this->createMock(WebsiteFactory::class);
        $this->setsFactory    = $this->createMock(SetsFactory::class);
        $this->productFactory = $this->createMock(ProductFactory::class);
        $this->type           = $this->createMock(Type::class);
        $this->status         = $this->createMock(Status::class);
        $this->visibility     = $this->createMock(Visibility::class);
        $this->moduleManager  = $this->createMock(ModuleManager::class);

        $this->storeManager->method('getStore')->willReturn($this->store);

        $this->grid = $objectManager->getObject(
            Grid::class,
            [
                'context'        => $this->context,
                'backendHelper'  => $this->backendHelper,
                'websiteFactory' => $this->websiteFactory,
                'setsFactory'    => $this->setsFactory,
                'productFactory' => $this->productFactory,
                'type'           => $this->type,
                'status'         => $this->status,
                'visibility'     => $this->visibility,
                'moduleManager'  => $this->moduleManager,
                'data'           => [],
            ]
        );
    }

    public function testConstructSetsDefaults(): void
    {
        // Ensures the protected _construct() set basic defaults.
        $this->assertSame('productGrid', $this->grid->getId(), 'Grid ID should be productGrid');
        $this->assertTrue($this->grid->getUseAjax(), 'Grid should use Ajax by default');
        // These are set in _construct(), but getters are not always public for all:
        // $this->assertSame('entity_id', $this->grid->getDefaultSort());
        // $this->assertSame('DESC', $this->grid->getDefaultDir());
    }

    public function testGetGridUrl(): void
    {
        $expected = 'https://magento.local/admin/catalog/product/grid/current/1';
        $this->urlBuilder
            ->expects($this->once())
            ->method('getUrl')
            ->with('catalog/*/grid', ['_current' => true])
            ->willReturn($expected);

        $this->assertSame($expected, $this->grid->getGridUrl());
    }

    public function testGetRowUrlBuildsEditUrlWithStoreParam(): void
    {
        $storeId = 3;
        $entityId = 42;
        $expectedUrl = 'https://magento.local/admin/catalog/product/edit/id/42/store/3';

        $this->request
            ->expects($this->once())
            ->method('getParam')
            ->with('store')
            ->willReturn($storeId);

        $rowMock = $this->getMockBuilder(\Magento\Framework\DataObject::class)
            ->onlyMethods(['getId'])
            ->getMock();
        $rowMock->expects($this->once())->method('getId')->willReturn($entityId);

        $this->urlBuilder
            ->expects($this->once())
            ->method('getUrl')
            ->with(
                'catalog/*/edit',
                ['store' => $storeId, 'id' => $entityId]
            )
            ->willReturn($expectedUrl);

        $this->assertSame($expectedUrl, $this->grid->getRowUrl($rowMock));
    }

    public function testGetStoreResolvesFromRequest(): void
    {
        $storeId = 5;

        $this->request
            ->expects($this->once())
            ->method('getParam')
            ->with('store', 0)
            ->willReturn($storeId);

        $this->storeManager
            ->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($this->store);

        // _getStore is protected—call via reflection to unit test its behavior.
        $method = (new \ReflectionClass(Grid::class))->getMethod('_getStore');
        $method->setAccessible(true);

        $resolvedStore = $method->invoke($this->grid);
        $this->assertSame($this->store, $resolvedStore);
    }
}
