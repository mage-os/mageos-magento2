<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model\Product\Price\Validation;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Price\Validation\InvalidSkuProcessor;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ProductIdLocatorInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for model Magento\Catalog\Product\Price\Validation\InvalidSkuProcessor.
 */
class InvalidSkuProcessorTest extends TestCase
{
    /**
     * @var InvalidSkuProcessor
     */
    private $invalidSkuProcessor;

    /**
     * @var ProductIdLocatorInterface|MockObject
     */
    private $productIdLocator;

    /**
     * @var ProductRepositoryInterface|MockObject
     */
    private $productRepository;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->productIdLocator = $this->createMock(ProductIdLocatorInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);

        $objectManager = new ObjectManager($this);
        $this->invalidSkuProcessor = $objectManager->getObject(
            InvalidSkuProcessor::class,
            [
                'productIdLocator' => $this->productIdLocator,
                'productRepository' => $this->productRepository
            ]
        );
    }

    /**
     * Prepare retrieveInvalidSkuList().
     *
     * @param string $productType
     * @param string $productSku
     * @return void
     */
    private function prepareRetrieveInvalidSkuListMethod($productType, $productSku)
    {
        $idsBySku = [$productSku => [235235235 => $productType]];
        $this->productIdLocator->expects($this->atLeastOnce())->method('retrieveProductIdsBySkus')
            ->willReturn($idsBySku);
        /** @var ProductInterface $product */
        $product = new class implements ProductInterface {
            private $priceType = null;
            
            public function getPriceType()
            {
                return $this->priceType;
            }
            
            public function setPriceType($priceType)
            {
                $this->priceType = $priceType;
                return $this;
            }
            
            // Required ProductInterface methods with default implementations
            public function getId()
            {
                return null;
            }
            public function setId($id)
            {
                return $this;
            }
            public function getSku()
            {
                return null;
            }
            public function setSku($sku)
            {
                return $this;
            }
            public function getName()
            {
                return null;
            }
            public function setName($name)
            {
                return $this;
            }
            public function getAttributeSetId()
            {
                return null;
            }
            public function setAttributeSetId($attributeSetId)
            {
                return $this;
            }
            public function getPrice()
            {
                return null;
            }
            public function setPrice($price)
            {
                return $this;
            }
            public function getStatus()
            {
                return null;
            }
            public function setStatus($status)
            {
                return $this;
            }
            public function getVisibility()
            {
                return null;
            }
            public function setVisibility($visibility)
            {
                return $this;
            }
            public function getTypeId()
            {
                return null;
            }
            public function setTypeId($typeId)
            {
                return $this;
            }
            public function getCreatedAt()
            {
                return null;
            }
            public function setCreatedAt($createdAt)
            {
                return $this;
            }
            public function getUpdatedAt()
            {
                return null;
            }
            public function setUpdatedAt($updatedAt)
            {
                return $this;
            }
            public function getWeight()
            {
                return null;
            }
            public function setWeight($weight)
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
                return null;
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
        };
        $productPriceType = 0;
        $product->setPriceType($productPriceType);
        $this->productRepository->expects($this->atLeastOnce())->method('get')->willReturn($product);
    }

    /**
     * Test for retrieveInvalidSkuList().
     *
     * @return void
     */
    public function testRetrieveInvalidSkuList()
    {
        $productSku = 'LKJKJ2233636';
        $productType = Type::TYPE_BUNDLE;
        $methodParamSku = 'SDFSDF3242355';
        $skus = [$methodParamSku];
        $allowedProductTypes = [$productType];
        $allowedPriceTypeValue = true;
        $this->prepareRetrieveInvalidSkuListMethod($productType, $productSku);

        $this->assertEquals(
            [$methodParamSku, $productSku],
            $this->invalidSkuProcessor->retrieveInvalidSkuList($skus, $allowedProductTypes, $allowedPriceTypeValue)
        );
    }
}
