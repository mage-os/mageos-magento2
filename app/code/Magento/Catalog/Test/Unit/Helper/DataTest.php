<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Category;
use Magento\Catalog\Helper\Product;
use Magento\Catalog\Model\Session;
use Magento\Catalog\Model\Template\Filter\Factory;
use Magento\CatalogUrlRewrite\Model\ProductScopeRewriteGenerator;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Api\Data\QuoteDetailsInterfaceFactory;
use Magento\Tax\Api\Data\QuoteDetailsItemInterfaceFactory;
use Magento\Tax\Api\Data\TaxClassKeyInterfaceFactory;
use Magento\Tax\Api\TaxCalculationInterface;
use Magento\Tax\Model\Config;
use Magento\Catalog\Helper\Data;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DataTest extends TestCase
{
    /**
     * @var Context|MockObject
     */
    private Context $context;

    /**
     * @var StoreManagerInterface|MockObject
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var Session|MockObject
     */
    private Session $catalogSession;

    /**
     * @var StringUtils|MockObject
     */
    private StringUtils $string;

    /**
     * @var Category|MockObject
     */
    private Category $catalogCategory;

    /**
     * @var Product|MockObject
     */
    private Product $catalogProduct;

    /**
     * @var Registry|MockObject
     */
    private Registry $coreRegistry;

    /**
     * @var Factory|MockObject
     */
    private Factory $templateFilterFactory;

    /**
     * @var string
     */
    private string $templateFilterModel;

    /**
     * @var TaxClassKeyInterfaceFactory|MockObject
     */
    private TaxClassKeyInterfaceFactory $taxClassKeyFactory;

    /**
     * @var Config|MockObject
     */
    private Config $taxConfig;

    /**
     * @var QuoteDetailsInterfaceFactory|MockObject
     */
    private QuoteDetailsInterfaceFactory $quoteDetailsFactory;

    /**
     * @var QuoteDetailsItemInterfaceFactory|MockObject
     */
    private QuoteDetailsItemInterfaceFactory $quoteDetailsItemFactory;

    /**
     * @var TaxCalculationInterface|MockObject
     */
    private TaxCalculationInterface $taxCalculationService;

    /**
     * @var CustomerSession|MockObject
     */
    private CustomerSession $customerSession;

    /**
     * @var PriceCurrencyInterface|MockObject
     */
    private PriceCurrencyInterface $priceCurrency;

    /**
     * @var ProductRepositoryInterface|MockObject
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @var CategoryRepositoryInterface|MockObject
     */
    private CategoryRepositoryInterface $categoryRepository;

    /**
     * @var GroupRepositoryInterface|MockObject
     */
    private GroupRepositoryInterface $customerGroupRepository;

    /**
     * @var AddressInterfaceFactory|MockObject
     */
    private AddressInterfaceFactory $addressFactory;

    /**
     * @var RegionInterfaceFactory|MockObject
     */
    private RegionInterfaceFactory $regionFactory;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->context = $this->createMock(Context::class);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->catalogSession = $this->createMock(Session::class);
        $this->string = $this->createMock(StringUtils::class);
        $this->catalogCategory = $this->createMock(Category::class);
        $this->catalogProduct = $this->createMock(Product::class);
        $this->coreRegistry = $this->createMock(Registry::class);
        $this->templateFilterFactory = $this->createMock(Factory::class);
        $this->templateFilterModel = 'Magento\Catalog\Model\Template\Filter';
        $this->taxClassKeyFactory = $this->createMock(TaxClassKeyInterfaceFactory::class);
        $this->taxConfig = $this->createMock(Config::class);
        $this->quoteDetailsFactory = $this->createMock(QuoteDetailsInterfaceFactory::class);
        $this->quoteDetailsItemFactory = $this->createMock(QuoteDetailsItemInterfaceFactory::class);
        $this->taxCalculationService = $this->createMock(TaxCalculationInterface::class);
        $this->customerSession = $this->createMock(CustomerSession::class);
        $this->priceCurrency = $this->createMock(PriceCurrencyInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $this->customerGroupRepository = $this->createMock(GroupRepositoryInterface::class);
        $this->addressFactory = $this->createMock(AddressInterfaceFactory::class);
        $this->regionFactory = $this->createMock(RegionInterfaceFactory::class);

        parent::setUp();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testIsUrlScopeWebsite(): void
    {
        $scopeConfig = $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        $this->context->expects($this->once())
            ->method('getScopeConfig')
            ->willReturn($scopeConfig);
        $scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(ProductScopeRewriteGenerator::URL_REWRITE_SCOPE_CONFIG_PATH, ScopeInterface::SCOPE_STORE)
            ->willReturn('website');

        $data = new Data(
            $this->context,
            $this->storeManager,
            $this->catalogSession,
            $this->string,
            $this->catalogCategory,
            $this->catalogProduct,
            $this->coreRegistry,
            $this->templateFilterFactory,
            $this->templateFilterModel,
            $this->taxClassKeyFactory,
            $this->taxConfig,
            $this->quoteDetailsFactory,
            $this->quoteDetailsItemFactory,
            $this->taxCalculationService,
            $this->customerSession,
            $this->priceCurrency,
            $this->productRepository,
            $this->categoryRepository,
            $this->customerGroupRepository,
            $this->addressFactory,
            $this->regionFactory
        );
        $this->assertTrue($data->isUrlScopeWebsite());
    }
}
