<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractModifierTestCase extends TestCase
{
    /**
     * @var ModifierInterface
     */
    private $model;

    /**
     * @var LocatorInterface|MockObject
     */
    protected $locatorMock;

    /**
     * @var ProductInterface|MockObject
     */
    protected $productMock;

    /**
     * @var StoreInterface|MockObject
     */
    protected $storeMock;

    /**
     * @var ArrayManager|MockObject
     */
    protected $arrayManagerMock;

    protected function setUp(): void
    {
        $this->locatorMock = $this->createMock(LocatorInterface::class);
        
        // Create anonymous class for ProductInterface with all required methods
        $this->productMock = new class implements ProductInterface {
            private $id = null;
            private $typeId = null;
            private $storeId = null;
            private $resource = null;
            private $data = [];
            private $attributes = [];
            private $store = null;
            private $attributeDefaultValue = null;
            private $existsStoreValueFlag = null;
            private $lockedAttribute = null;
            private $sku = null;
            private $name = null;
            private $attributeSetId = null;
            private $price = null;
            private $status = null;
            private $visibility = null;
            private $typeInstance = null;
            private $extensionAttributes = null;
            private $weight = null;

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

            public function getTypeId()
            {
                return $this->typeId;
            }

            public function setTypeId($typeId)
            {
                $this->typeId = $typeId;
                return $this;
            }

            public function getStoreId()
            {
                return $this->storeId;
            }

            public function setStoreId($storeId)
            {
                $this->storeId = $storeId;
                return $this;
            }

            public function getResource()
            {
                return $this->resource;
            }

            public function setResource($resource)
            {
                $this->resource = $resource;
                return $this;
            }

            public function getData($key = '', $index = null)
            {
                return $this->data;
            }

            public function setData($key, $value = null)
            {
                $this->data = $value;
                return $this;
            }

            public function getAttributes()
            {
                return $this->attributes;
            }

            public function setAttributes($attributes)
            {
                $this->attributes = $attributes;
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

            public function getAttributeDefaultValue($attributeCode)
            {
                return $this->attributeDefaultValue;
            }

            public function setAttributeDefaultValue($attributeDefaultValue)
            {
                $this->attributeDefaultValue = $attributeDefaultValue;
                return $this;
            }

            public function getExistsStoreValueFlag($attributeCode)
            {
                return $this->existsStoreValueFlag;
            }

            public function setExistsStoreValueFlag($existsStoreValueFlag)
            {
                $this->existsStoreValueFlag = $existsStoreValueFlag;
                return $this;
            }

            public function isLockedAttribute($attributeCode)
            {
                return $this->lockedAttribute;
            }

            public function setLockedAttribute($lockedAttribute)
            {
                $this->lockedAttribute = $lockedAttribute;
                return $this;
            }

            public function getSku()
            {
                return $this->sku;
            }

            public function setSku($sku)
            {
                $this->sku = $sku;
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

            public function getAttributeSetId()
            {
                return $this->attributeSetId;
            }

            public function setAttributeSetId($attributeSetId)
            {
                $this->attributeSetId = $attributeSetId;
                return $this;
            }

            public function getPrice()
            {
                return $this->price;
            }

            public function setPrice($price)
            {
                $this->price = $price;
                return $this;
            }

            public function getStatus()
            {
                return $this->status;
            }

            public function setStatus($status)
            {
                $this->status = $status;
                return $this;
            }

            public function getVisibility()
            {
                return $this->visibility;
            }

            public function setVisibility($visibility)
            {
                $this->visibility = $visibility;
                return $this;
            }

            public function getTypeInstance()
            {
                return $this->typeInstance;
            }

            public function setTypeInstance($typeInstance)
            {
                $this->typeInstance = $typeInstance;
                return $this;
            }

            public function getExtensionAttributes()
            {
                return $this->extensionAttributes;
            }

            public function setExtensionAttributes(\Magento\Catalog\Api\Data\ProductExtensionInterface $extensionAttributes)
            {
                $this->extensionAttributes = $extensionAttributes;
                return $this;
            }

            public function getWeight()
            {
                return $this->weight;
            }

            public function setWeight($weight)
            {
                $this->weight = $weight;
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

            public function getIsSalable()
            {
                return null;
            }

            public function setIsSalable($isSalable)
            {
                return $this;
            }

            public function getRequestPath()
            {
                return null;
            }

            public function setRequestPath($requestPath)
            {
                return $this;
            }

            public function getUrlKey()
            {
                return null;
            }

            public function setUrlKey($urlKey)
            {
                return $this;
            }

            public function getCanonicalUrl()
            {
                return null;
            }

            public function setCanonicalUrl($canonicalUrl)
            {
                return $this;
            }

            public function getCategoryIds()
            {
                return null;
            }

            public function setCategoryIds($categoryIds)
            {
                return $this;
            }

            public function getCategoryCollection()
            {
                return null;
            }

            public function setCategoryCollection($categoryCollection)
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

            public function getProductLinks()
            {
                return null;
            }

            public function setProductLinks(?array $links = null)
            {
                return $this;
            }

            public function getOptionsByType($type)
            {
                return null;
            }

            public function getDefaultAttributeSetId()
            {
                return null;
            }

            public function setDefaultAttributeSetId($defaultAttributeSetId)
            {
                return $this;
            }

            public function getAttributeText($attributeCode)
            {
                return null;
            }

            public function getDefaultValues()
            {
                return null;
            }

            public function getPreconfiguredValues()
            {
                return null;
            }

            public function setPreconfiguredValues($preconfiguredValues)
            {
                return $this;
            }

            public function getPriceInfo()
            {
                return null;
            }

            public function getConfigurableAttributes()
            {
                return null;
            }

            public function setConfigurableAttributes($configurableAttributes)
            {
                return $this;
            }

            public function getConfigurableOptions()
            {
                return null;
            }

            public function setConfigurableOptions($configurableOptions)
            {
                return $this;
            }

            public function getConfigurableProductLinks()
            {
                return null;
            }

            public function setConfigurableProductLinks($configurableProductLinks)
            {
                return $this;
            }

            public function getConfigurableProductOptions()
            {
                return null;
            }

            public function setConfigurableProductOptions($configurableProductOptions)
            {
                return $this;
            }

            public function getConfigurableProductSelections()
            {
                return null;
            }

            public function setConfigurableProductSelections($configurableProductSelections)
            {
                return $this;
            }

            public function getConfigurableProductVariations()
            {
                return null;
            }

            public function setConfigurableProductVariations($configurableProductVariations)
            {
                return $this;
            }


        };

        // Create anonymous class for StoreInterface with all required methods
        $this->storeMock = new class implements StoreInterface {
            private $id = null;
            private $loadResult = null;
            private $config = null;
            private $extensionAttributes = null;

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

            public function load($id)
            {
                return $this->loadResult;
            }

            public function setLoadResult($loadResult)
            {
                $this->loadResult = $loadResult;
                return $this;
            }

            public function getConfig($path)
            {
                return $this->config;
            }

            public function setConfig($config)
            {
                $this->config = $config;
                return $this;
            }

            public function getCode()
            {
                return null;
            }

            public function setCode($code)
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

            public function getWebsiteId()
            {
                return null;
            }

            public function setWebsiteId($websiteId)
            {
                return $this;
            }

            public function getStoreGroupId()
            {
                return null;
            }

            public function setStoreGroupId($storeGroupId)
            {
                return $this;
            }

            public function getIsActive()
            {
                return null;
            }

            public function setIsActive($isActive)
            {
                return $this;
            }

            public function getSortOrder()
            {
                return null;
            }

            public function setSortOrder($sortOrder)
            {
                return $this;
            }

            public function getExtensionAttributes()
            {
                return $this->extensionAttributes;
            }

            public function setExtensionAttributes($extensionAttributes)
            {
                $this->extensionAttributes = $extensionAttributes;
                return $this;
            }
        };

        $this->arrayManagerMock = $this->createMock(ArrayManager::class);

        $this->arrayManagerMock->expects($this->any())
            ->method('replace')
            ->willReturnArgument(1);
        $this->arrayManagerMock->expects($this->any())
            ->method('get')
            ->willReturnArgument(2);
        $this->arrayManagerMock->expects($this->any())
            ->method('set')
            ->willReturnArgument(1);
        $this->arrayManagerMock->expects($this->any())
            ->method('remove')
            ->willReturnArgument(1);

        $this->locatorMock->method('getProduct')->willReturn($this->productMock);
        $this->locatorMock->method('getStore')->willReturn($this->storeMock);
    }

    /**
     * @return ModifierInterface
     */
    abstract protected function createModel();

    /**
     * @return ModifierInterface
     */
    protected function getModel()
    {
        if (null === $this->model) {
            $this->model = $this->createModel();
        }

        return $this->model;
    }

    /**
     * @return array
     */
    protected function getSampleData()
    {
        return ['data_key' => 'data_value'];
    }
}
