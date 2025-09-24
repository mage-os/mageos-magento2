<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Block\Ui;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductRenderInterface;
use Magento\Catalog\Block\Ui\ProductViewCounter;
use Magento\Catalog\Model\ProductRenderFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Ui\DataProvider\Product\ProductRenderCollectorComposite;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\EntityManager\Hydrator;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Url;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductViewCounterTest extends TestCase
{
    /**
     * @var ProductViewCounter|MockObject
     */
    private $productViewCounter;

    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var ProductRepository|MockObject
     */
    private $productRepositoryMock;

    /**
     * @var ProductRenderCollectorComposite|MockObject
     */
    private $productRenderCollectorCompositeMock;

    /**
     * @var Hydrator|MockObject
     */
    private $hydratorMock;

    /**
     * @var SerializerInterface|MockObject
     */
    private $serializeMock;

    /**
     * @var Url|MockObject
     */
    private $urlMock;

    /**
     * @var Registry|MockObject
     */
    private $registryMock;

    /**
     * @var StoreManager|MockObject
     */
    private $storeManagerMock;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    private $scopeConfigMock;

    /**
     * @var ProductRenderFactory|MockObject
     */
    private $productRenderFactoryMock;

    protected function setUp(): void
    {
        $this->contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->productRepositoryMock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->productRenderCollectorCompositeMock = $this->getMockBuilder(ProductRenderCollectorComposite::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->productRenderFactoryMock = $this->getMockBuilder(ProductRenderFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->hydratorMock = $this->getMockBuilder(Hydrator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->serializeMock = $this->getMockBuilder(SerializerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->urlMock = $this->getMockBuilder(Url::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->registryMock = $this->getMockBuilder(Registry::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->storeManagerMock = $this->getMockBuilder(StoreManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->productViewCounter = new ProductViewCounter(
            $this->contextMock,
            $this->productRepositoryMock,
            $this->productRenderCollectorCompositeMock,
            $this->storeManagerMock,
            $this->productRenderFactoryMock,
            $this->hydratorMock,
            $this->serializeMock,
            $this->urlMock,
            $this->registryMock,
            $this->scopeConfigMock
        );
    }

    public function testGetCurrentProductDataWithEmptyProduct()
    {
        $productMock = $this->getMockBuilder(ProductInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storeMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('product')
            ->willReturn($productMock);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn(1);
        $storeMock->expects($this->once())
            ->method('getCurrentCurrency')
            ->willReturn($storeMock);
        $storeMock->expects($this->once())
            ->method('getCode')
            ->willReturn('USD');

        $this->productViewCounter->getCurrentProductData();
    }

    public function testGetCurrentProductDataWithNonEmptyProduct()
    {
        $productMock = new class implements ProductInterface {
            public function getId()
            {
                return 123;
            }
            public function setId($id)
            {
                return $this;
            }
            public function getSku()
            {
                return 'test-sku';
            }
            public function setSku($sku)
            {
                return $this;
            }
            public function getName()
            {
                return 'Test Product';
            }
            public function setName($name)
            {
                return $this;
            }
            public function getPrice()
            {
                return 10.00;
            }
            public function setPrice($price)
            {
                return $this;
            }
            public function getWeight()
            {
                return 1.0;
            }
            public function setWeight($weight)
            {
                return $this;
            }
            public function getStatus()
            {
                return 1;
            }
            public function setStatus($status)
            {
                return $this;
            }
            public function getVisibility()
            {
                return 4;
            }
            public function setVisibility($visibility)
            {
                return $this;
            }
            public function getAttributeSetId()
            {
                return 4;
            }
            public function setAttributeSetId($attributeSetId)
            {
                return $this;
            }
            public function getTypeId()
            {
                return 'simple';
            }
            public function setTypeId($typeId)
            {
                return $this;
            }
            public function getCreatedAt()
            {
                return '2023-01-01 00:00:00';
            }
            public function setCreatedAt($createdAt)
            {
                return $this;
            }
            public function getUpdatedAt()
            {
                return '2023-01-01 00:00:00';
            }
            public function setUpdatedAt($updatedAt)
            {
                return $this;
            }
            public function getMediaGalleryEntries()
            {
                return null;
            }
            public function setMediaGalleryEntries(?array $mediaGalleryEntries = null)
            {
                return $this;
            }
            public function getTierPrices()
            {
                return null;
            }
            public function setTierPrices(?array $tierPrices = null)
            {
                return $this;
            }
            public function getCustomAttributes()
            {
                return [];
            }
            public function setCustomAttributes($customAttributes)
            {
                return $this;
            }
            public function getCustomAttribute($attributeCode)
            {
                return null;
            }
            public function setCustomAttribute($attributeCode, $attributeValue)
            {
                return $this;
            }
            public function getExtensionAttributes()
            {
                return null;
            }
            public function setExtensionAttributes($extensionAttributes)
            {
                return $this;
            }
            public function getProductLinks()
            {
                return null;
            }
            public function setProductLinks(?array $links = null)
            {
                return $this;
            }
            public function getOptions()
            {
                return null;
            }
            public function setOptions(?array $options = null)
            {
                return $this;
            }
            
            public function isAvailable()
            {
                return true;
            }
        };
        $productRendererMock = $this->getMockBuilder(ProductRenderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $storeMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('product')
            ->willReturn($productMock);
        $this->productRenderFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($productRendererMock);
        $this->productRenderCollectorCompositeMock->expects($this->once())
            ->method('collect')
            ->with($productMock, $productRendererMock);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);
        $storeMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(1);
        $storeMock->expects($this->once())
            ->method('getCurrentCurrency')
            ->willReturn($storeMock);
        $storeMock->expects($this->once())
            ->method('getCode')
            ->willReturn('USD');

        $this->productViewCounter->getCurrentProductData();
    }
}
