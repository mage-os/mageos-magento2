<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;

/**
 * Test helper class for Catalog Product with custom methods
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.BooleanGetMethodName)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class ProductTestHelper extends Product
{
    /**
     * @var array
     */
    private $data = [];

    /** @var DataObject|null */
    private $urlDataObject;

    /**
     * @var bool
     */
    private $setBundleOptionsDataCalled = false;
    /** @var array */
    private $setBundleOptionsDataParams = [];
    /** @var bool */
    private $setBundleSelectionsDataCalled = false;
    /** @var array */
    private $setBundleSelectionsDataParams = [];
    /** @var bool */
    private $setCanSaveCustomOptionsCalled = false;
    /** @var array */
    private $setCanSaveCustomOptionsParams = [];
    /** @var bool */
    private $setCanSaveBundleSelectionsCalled = false;
    /** @var array */
    private $setCanSaveBundleSelectionsParams = [];
    /** @var bool */
    private $setOptionsCalled = false;
    /** @var array */
    private $setOptionsParams = [];

    /**
     * @var bool
     */
    private $linksPurchasedSeparately = true;

    /**
     * @var bool
     */
    private $isChangedWebsites = false;

    /**
     * @var int|null
     */
    private $websiteId = null;

    /**
     * @var int|null
     */
    private $customerGroupId = null;

    /**
     * @var bool|null
     */
    private $statusChanged = null;

    /**
     * @var bool|null
     */
    private $isSalable = null;

    /**
     * @var float|null
     */
    private $finalPrice = null;

    /**
     * @var mixed
     */
    private $resource = null;

    /**
     * @var array
     */
    private $attributesByCode = [];

    /**
     * @var array
     */
    private $attributeSelect = [];

    /**
     * @var array
     */
    private $dataValues = [];

    /**
     * @var int
     */
    private $dataCallCount = 0;

    /**
     * @var array
     */
    private $customAttributes = [];

    /**
     * @var mixed
     */
    private $inventoryReadonly = null;

    /**
     * @var array
     */
    private $testData = [];

    /**
     * @var array
     */
    private $origData = [];

    /**
     * @var int
     */
    private $storeId = 0;

    /**
     * @var mixed
     */
    private $crossSellLinkData = null;

    /**
     * @var mixed
     */
    private $relatedLinkData = null;

    /**
     * @var mixed
     */
    private $upSellLinkData = null;

    /**
     * @var mixed
     */
    private $hasOptions = null;

    /**
     * @var mixed
     */
    private $status = null;

    /**
     * @var bool
     */
    private $relatedReadonly = false;

    /**
     * @var bool
     */
    private $upsellReadonly = false;

    /**
     * @var bool
     */
    private $crosssellReadonly = false;

    /**
     * @var mixed
     */
    private $priceType = null;

    /**
     * @var mixed
     */
    private $id = null;

    /**
     * @var bool
     */
    private $canShowPrice = true;

    /**
     * @var mixed
     */
    private $priceInfo = null;

    /**
     * @var bool
     */
    private $hasCustomerGroupId = false;

    /**
     * @var bool
     */
    private $allowedInRss = false;

    /**
     * @var bool
     */
    private $allowedPriceInRss = false;

    /**
     * @var string
     */
    private $description = 'Product Description';

    /**
     * @var string
     */
    private $name = 'Product Name';

    /**
     * @var string
     */
    private $productUrl = 'http://magento.com/product-name.html';

    /**
     * Initialize resources
     *
     * @return void
     */
    protected function _construct()
    {
        // Mock implementation - no actual resource initialization needed
    }

    /**
     * Constructor
     *
     * @param mixed $resource Optional resource parameter or DataObject for URL
     */
    public function __construct($resource = null)
    {
        if ($resource instanceof DataObject) {
            $this->urlDataObject = $resource;
            $this->resource = null;
        } else {
            $this->resource = $resource;
            $this->urlDataObject = null;
        }
        $this->_data = [];
        $this->testData = [];
    }

    public function __sleep()
    {
        return [];
    }

    public function setInventoryReadonly($value)
    {
        $this->inventoryReadonly = $value;
        return $this;
    }

    public function getInventoryReadonly()
    {
        return $this->inventoryReadonly;
    }

    /**
     * Set resource model for testing
     *
     * @param mixed $resource
     * @return self
     */
    public function setResource($resource): self
    {
        $this->resource = $resource;
        if (property_exists($this, '_resource')) {
            $this->_resource = $resource;
        }
        return $this;
    }

    /**
     * Set locked attribute for testing
     *
     * @param string $attributeCode
     * @param bool $locked
     * @return $this
     */
    public function setLockedAttribute($attributeCode, $locked = true)
    {
        if (!is_array($this->_lockedAttributes)) {
            $this->_lockedAttributes = [];
        }
        if ($locked) {
            $this->_lockedAttributes[$attributeCode] = true;
        } else {
            unset($this->_lockedAttributes[$attributeCode]);
        }
        return $this;
    }

    /**
     * Set price info
     *
     * Uses reflection to set parent's protected _priceInfo property
     *
     * @param mixed $priceInfo
     * @return $this
     */
    public function setPriceInfo($priceInfo)
    {
        $this->priceInfo = $priceInfo;
        $this->data['price_info'] = $priceInfo;
        $reflection = new \ReflectionClass($this);
        if ($reflection->hasProperty('_priceInfo')) {
            $property = $reflection->getProperty('_priceInfo');
            $property->setValue($this, $priceInfo);
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPriceInfo()
    {
        return $this->data['price_info'] ?? $this->priceInfo;
    }

    /**
     * Set custom option (not in parent - parent only has setCustomOptions with array)
     * Supports both single option and key-value pair
     *
     * @param mixed $optionOrKey - Either the option object/code or the option key
     * @param mixed $value - Optional value when using key-value pair
     * @return $this
     */
    public function setCustomOption($optionOrKey, $value = null)
    {
        if ($value !== null) {
            // Key-value pair
            $this->_customOptions[$optionOrKey] = $value;
            $this->data['custom_options'][$optionOrKey] = $value;
        } else {
            // Single option (backward compatibility)
            $this->data['custom_option'] = $optionOrKey;
        }
        return $this;
    }

    /**
     * Set URL model
     *
     * Uses reflection to set parent's protected _urlModel property
     *
     * @param mixed $urlModel
     * @return $this
     */
    public function setUrlModel($urlModel)
    {
        $reflection = new \ReflectionClass($this);
        $property = $reflection->getProperty('_urlModel');
        $property->setValue($this, $urlModel);
        return $this;
    }

    /**
     * Get links purchased separately
     *
     * @return bool|null
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getLinksPurchasedSeparately()
    {
        return $this->data['links_purchased_separately'] ?? $this->linksPurchasedSeparately;
    }

    /**
     * Set links purchased separately flag
     *
     * @param bool $flag
     * @return self
     */
    public function setLinksPurchasedSeparately(bool $flag): self
    {
        $this->linksPurchasedSeparately = $flag;
        $this->data['links_purchased_separately'] = $flag;
        return $this;
    }

    /**
     * Get nickname
     *
     * @return string|null
     */
    public function getNickname()
    {
        return $this->getData('nickname');
    }

    /**
     * Set nickname
     *
     * @param string $nickname
     * @return $this
     */
    public function setNickname($nickname)
    {
        $this->setData('nickname', $nickname);
        return $this;
    }

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->setData('title', $title);
        return $this;
    }

    /**
     * Get detail
     *
     * @return string|null
     */
    public function getDetail()
    {
        return $this->getData('detail');
    }

    /**
     * Set detail
     *
     * @param string $detail
     * @return $this
     */
    public function setDetail($detail)
    {
        $this->setData('detail', $detail);
        return $this;
    }

    /**
     * Get store
     *
     * @return mixed
     */
    public function getStore()
    {
        return $this->data['store'] ?? $this->getData('store');
    }

    /**
     * Set store
     *
     * @param mixed $store
     * @return $this
     */
    public function setStore($store): self
    {
        $this->data['store'] = $store;
        return $this->setData('store', $store);
    }

    /**
     * Get is changed websites
     *
     * @return bool
     */
    public function getIsChangedWebsites()
    {
        return $this->isChangedWebsites;
    }

    /**
     * Set is changed websites
     *
     * @param bool $value
     * @return $this
     */
    public function setIsChangedWebsites($value)
    {
        $this->isChangedWebsites = $value;
        return $this;
    }

    /**
     * Set product id
     *
     * @param int|null $id
     * @return $this
     */
    public function setProductId($id): self
    {
        return $this->setId($id);
    }

    /**
     * Get status changed
     *
     * @return bool|null
     */
    public function getStatusChanged()
    {
        return $this->statusChanged;
    }

    /**
     * Set status changed
     *
     * @param bool|null $statusChanged
     * @return $this
     */
    public function setStatusChanged($statusChanged)
    {
        $this->statusChanged = $statusChanged;
        return $this;
    }

    /**
     * Data has changed for
     *
     * @param string $field
     * @return bool
     */
    public function dataHasChangedFor($field)
    {
        if ($field === 'status') {
            return $this->statusChanged;
        }
        return false;
    }

    /**
     * Get is salable
     *
     * @return bool|null
     */
    public function getIsSalable()
    {
        return $this->isSalable;
    }

    /**
     * @return bool
     */
    public function isSalable(): bool
    {
        return $this->data['is_salable'] ?? ($this->isSalable ?? true);
    }

    /**
     * Set is salable
     *
     * @param bool|null $isSalable
     * @return $this
     */
    public function setIsSalable($isSalable)
    {
        $this->isSalable = $isSalable;
        $this->data['is_salable'] = $isSalable;
        return $this;
    }

    /**
     * Custom isSaleable method for Bundle testing
     *
     * @return bool
     */
    public function isSaleable(): bool
    {
        return $this->data['is_saleable'] ?? true;
    }

    /**
     * Set isSaleable for testing
     *
     * @param bool $isSaleable
     * @return self
     */
    public function setIsSaleable(bool $isSaleable): self
    {
        $this->data['is_saleable'] = $isSaleable;
        return $this;
    }

    /**
     * Get website id
     *
     * @return int|null
     */
    public function getWebsiteId()
    {
        return $this->data['website_id'] ?? $this->websiteId;
    }

    /**
     * Set website id
     *
     * @param int|null $value
     * @return $this
     */
    public function setWebsiteId($value)
    {
        $this->websiteId = $value;
        $this->data['website_id'] = $value;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasCustomerGroupId(): bool
    {
        return isset($this->data['customer_group_id']) || $this->hasCustomerGroupId;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setHasCustomerGroupId($value)
    {
        $this->hasCustomerGroupId = $value;
        return $this;
    }

    /**
     * Get customer group id
     *
     * @return int|null
     */
    public function getCustomerGroupId()
    {
        return $this->data['customer_group_id'] ?? $this->customerGroupId;
    }

    /**
     * Set customer group id
     *
     * @param int|null $value
     * @return $this
     */
    public function setCustomerGroupId($value)
    {
        $this->customerGroupId = $value;
        $this->data['customer_group_id'] = $value;
        return $this;
    }

    /**
     * Get product ID
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->data['id'] ?? ($this->id ?? 1);
    }

    /**
     * Set product ID
     *
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        $this->data['id'] = $id;
        return $this;
    }

    /**
     * Override getEntityId to work without constructor
     *
     * @return mixed
     */
    public function getEntityId()
    {
        return $this->data['entity_id'];
    }

    /**
     * Set entity ID for testing
     *
     * @param mixed $value
     * @return self
     */
    public function setEntityId($value): self
    {
        $this->data['entity_id'] = $value;
        return $this;
    }

    /**
     * Get store ID
     *
     * @return mixed
     */
    public function getStoreId()
    {
        return $this->data['store_id'] ?? ($this->storeId ?? 1);
    }

    /**
     * Set store ID
     *
     * @param mixed $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        $this->data['store_id'] = $storeId;
        return $this;
    }

    /**
     * Add attribute to select
     *
     * @param mixed $attribute
     * @return $this
     */
    public function addAttributeToSelect($attribute)
    {
        $this->attributeSelect[] = $attribute;
        return $this;
    }

    /**
     * Get all attribute values
     *
     * @param string $attribute
     * @return array|null
     */
    public function getAllAttributeValues($attribute)
    {
        if ($this->dataValues && $this->dataCallCount < count($this->dataValues)) {
            $result = $this->dataValues[$this->dataCallCount];
            $this->dataCallCount++;
            return $result;
        }
        return null;
    }

    /**
     * Get attributes by code
     *
     * @return array
     */
    public function getAttributesByCode()
    {
        return $this->attributesByCode;
    }

    /**
     * Set attributes by code
     *
     * @param mixed $value
     * @return $this
     */
    public function setAttributesByCode($value)
    {
        $this->attributesByCode = $value;
        return $this;
    }

    /**
     * Set data values for sequential getData calls
     *
     * Enables sequential mode: each getData() call returns next value
     *
     * @param array $values
     * @return $this
     */
    public function setDataValues($values)
    {
        $this->dataValues = $values;
        $this->dataCallCount = 0;
        return $this;
    }

    /**
     * Has data - supports both standard and sequential modes
     *
     * @param string|null $key
     * @return bool
     */
    public function hasData($key = null)
    {
        if (isset($this->data['has_data'])) {
            return $this->data['has_data'];
        }
        
        if ($this->dataValues) {
            return true;
        }
        if ($key === null) {
            return !empty($this->_data) || !empty($this->testData) || !empty($this->data);
        }
        return isset($this->_data[$key]) || isset($this->attributesByCode[$key]) || isset($this->testData[$key]) || isset($this->data[$key]);
    }

    /**
     * Get data - supports both standard and sequential modes
     *
     * @param string $keyword
     * @param mixed $index
     * @return mixed
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getData($keyword = '', $index = null)
    {
        // Check if there's a callback set for getData
        if (isset($this->data['get_data_callback'])) {
            return call_user_func($this->data['get_data_callback'], $keyword);
        }

        if ($keyword === '' || $keyword === null) {
            if ($this->dataValues) {
                return $this->dataValues;
            }
            // Check product_data first for 2.4-develop compatibility
            if (isset($this->data['product_data']) && !empty($this->data['product_data'])) {
                return $this->data['product_data'];
            }
            return !empty($this->testData) ? $this->testData : $this->_data;
        }

        if (isset($this->attributesByCode[$keyword])) {
            return $this->attributesByCode[$keyword];
        }

        if ($this->dataValues && $this->dataCallCount < count($this->dataValues)) {
            $result = $this->dataValues[$this->dataCallCount];
            $this->dataCallCount++;
            return $result;
        }

        // Check product_data array for 2.4-develop compatibility
        if (isset($this->data['product_data'][$keyword])) {
            $value = $this->data['product_data'][$keyword];
        } elseif (isset($this->testData[$keyword])) {
            $value = $this->testData[$keyword];
        } else {
            $value = $this->_data[$keyword] ?? $index;
        }

        if ($index !== null && is_array($value) && isset($value[$index])) {
            return $value[$index];
        }
        return $value;
    }

    /**
     * Set data - supports both standard and sequential modes
     *
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        // Use separate productData array for getData/setData to avoid conflicts with 2.4-develop
        if (!isset($this->data['product_data'])) {
            $this->data['product_data'] = [];
        }

        if (is_array($key)) {
            if ($this->dataValues) {
                $this->attributesByCode = $key;
            } else {
                $this->data['product_data'] = array_merge($this->data['product_data'], $key);
                $this->testData = array_merge($this->testData, $key);
                $this->_data = array_merge($this->_data, $key);
            }
        } else {
            if ($this->dataValues) {
                $this->attributesByCode[$key] = $value;
            } else {
                $this->data['product_data'][$key] = $value;
                $this->testData[$key] = $value;
                $this->_data[$key] = $value;
            }
        }
        return $this;
    }

    /**
     * Unset data
     *
     * @param string|null $key
     * @return $this
     */
    public function unsetData($key = null)
    {
        if ($this->dataValues) {
            if ($key === null) {
                $this->attributesByCode = [];
            } else {
                unset($this->attributesByCode[$key]);
            }
        } else {
            if ($key === null) {
                $this->_data = [];
                $this->testData = [];
                if (isset($this->data['product_data'])) {
                    $this->data['product_data'] = [];
                }
            } else {
                unset($this->_data[$key]);
                unset($this->testData[$key]);
                if (isset($this->data['product_data'][$key])) {
                    unset($this->data['product_data'][$key]);
                }
            }
        }
        return $this;
    }

    /**
     * @param string|null $key
     * @return mixed
     */
    public function getOrigData($key = null)
    {
        if ($key === null) {
            return $this->origData;
        }
        return $this->origData[$key] ?? null;
    }

    /**
     * @param string|array|null $key
     * @param mixed $data
     * @return $this
     */
    public function setOrigData($key = null, $data = null)
    {
        if ($key === null) {
            $this->origData = $this->testData;
        } elseif (is_array($key)) {
            $this->origData = array_merge($this->origData, $key);
        } else {
            $this->origData[$key] = $data;
        }
        return $this;
    }

    /**
     * Get resource
     *
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set final price
     *
     * @param float|null $value
     * @return $this
     */
    public function setFinalPrice($value)
    {
        $this->finalPrice = $value;
        return $this;
    }

    /**
     * Get final price
     *
     * @param mixed $qty
     * @return float|null
     */
    public function getFinalPrice($qty = null)
    {
        return $this->finalPrice;
    }

    /**
     * Mock method for getUrlKey
     *
     * @return string|null
     */
    public function getUrlKey()
    {
        return $this->getData('url_key');
    }

    /**
     * Mock method for formatUrlKey
     *
     * @param string $str
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function formatUrlKey($str)
    {
        return $str;
    }

    /**
     * Mock method for load
     *
     * @param int $modelId
     * @param string|null $field
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load($modelId, $field = null)
    {
        return $this;
    }

    /**
     * Mock method for setUrlKey
     *
     * @param string $urlKey
     * @return $this
     */
    public function setUrlKey($urlKey)
    {
        return $this->setData('url_key', $urlKey);
    }

    /**
     * Mock method for getIsChangedCategories
     *
     * @return bool|null
     */
    public function getIsChangedCategories()
    {
        return $this->getData('is_changed_categories');
    }

    /**
     * Mock method for getWebsiteIds
     *
     * @return array|string
     */
    public function getWebsiteIds()
    {
        return $this->data['website_ids'] ?? $this->getData('website_ids') ?: [];
    }

    /**
     * Get custom attribute value
     *
     * @param string $attributeCode
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getCustomAttribute($attributeCode)
    {
        return $this->customAttributes[$attributeCode] ?? null;
    }

    /**
     * Set custom attribute for testing
     *
     * @param string $attributeCode
     * @param mixed $attribute
     * @return $this
     */
    public function setCustomAttributeForTest($attributeCode, $attribute)
    {
        $this->customAttributes[$attributeCode] = $attribute;
        return $this;
    }

    /**
     * Get product options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = $this->data['options'] ?? ($this->data['product_data']['options'] ?? $this->getData('options'));
        return is_array($options) ? $options : [];
    }

    /**
     * Set options for testing with call tracking
     *
     * @param ?array $options
     * @return self
     */
    public function setOptions(?array $options = null): self
    {
        $this->data['options'] = $options;
        if (!isset($this->data['product_data'])) {
            $this->data['product_data'] = [];
        }
        $this->data['product_data']['options'] = $options;
        $this->setOptionsCalled = true;
        $this->setOptionsParams = $options;
        return $this;
    }

    /**
     * Get attribute set ID
     *
     * @return int|null
     */
    public function getAttributeSetId()
    {
        return $this->getData('attribute_set_id');
    }

    /**
     * Set attribute set id for testing
     *
     * @param int $attributeSetId
     * @return $this
     */
    public function setAttributeSetId($attributeSetId)
    {
        return $this->setData('attribute_set_id', $attributeSetId);
    }

    /**
     * Override getCustomAttributesCodes to avoid null pointer on filterCustomAttribute
     *
     * @return array
     */
    protected function getCustomAttributesCodes()
    {
        // Return empty array to avoid dependency on filterCustomAttribute
        return [];
    }

    /**
     * @return string|null
     */
    public function getSku()
    {
        return $this->data['sku'] ?? $this->getData('sku');
    }

    /**
     * @param string $sku
     * @return $this
     */
    public function setSku($sku)
    {
        $this->data['sku'] = $sku;
        return $this->setData('sku', $sku);
    }

    /**
     * @return string|null
     */
    public function getTypeId(): ?string
    {
        return $this->data['type_id'] ?? $this->getData('type_id');
    }

    /**
     * @param mixed $typeId
     * @return $this
     */
    public function setTypeId($typeId): self
    {
        $this->data['type_id'] = $typeId;
        return $this->setData('type_id', $typeId);
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setCrossSellLinkData($data)
    {
        $this->crossSellLinkData = $data;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCrossSellLinkData()
    {
        return $this->crossSellLinkData;
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setRelatedLinkData($data)
    {
        $this->relatedLinkData = $data;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRelatedLinkData()
    {
        return $this->relatedLinkData;
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setUpSellLinkData($data)
    {
        $this->upSellLinkData = $data;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpSellLinkData()
    {
        return $this->upSellLinkData;
    }

    /**
     * @return mixed
     */
    public function getHasOptions()
    {
        return $this->data['has_options'] ?? $this->hasOptions;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setHasOptions($value)
    {
        $this->hasOptions = $value;
        $this->data['has_options'] = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setStatus($value)
    {
        $this->status = $value;
        return $this;
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getRelatedReadonly()
    {
        return $this->relatedReadonly;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setRelatedReadonly($value)
    {
        $this->relatedReadonly = $value;
        return $this;
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getUpsellReadonly()
    {
        return $this->upsellReadonly;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setUpsellReadonly($value)
    {
        $this->upsellReadonly = $value;
        return $this;
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getCrosssellReadonly()
    {
        return $this->crosssellReadonly;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setCrosssellReadonly($value)
    {
        $this->crosssellReadonly = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPriceType()
    {
        return $this->data['price_type'] ?? $this->priceType;
    }

    /**
     * @param mixed $priceType
     * @return $this
     */
    public function setPriceType($priceType): self
    {
        $this->priceType = $priceType;
        $this->data['price_type'] = $priceType;
        return $this;
    }

    /**
     * @return bool
     */
    public function getCanShowPrice()
    {
        return $this->canShowPrice;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setCanShowPrice($value)
    {
        $this->canShowPrice = $value;
        return $this;
    }

    /**
     * @param bool $allowed
     * @return $this
     */
    public function setAllowedInRss($allowed)
    {
        $this->allowedInRss = $allowed;
        return $this;
    }

    /**
     * @return bool
     */
    public function getAllowedInRss()
    {
        return $this->allowedInRss;
    }

    /**
     * @param bool $allowed
     * @return $this
     */
    public function setAllowedPriceInRss($allowed)
    {
        $this->allowedPriceInRss = $allowed;
        return $this;
    }

    /**
     * @return bool
     */
    public function getAllowedPriceInRss()
    {
        return $this->allowedPriceInRss;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Override getName for testing
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->data['name'] ?? $this->name;
    }

    /**
     * @param mixed $name
     * @return $this
     */
    public function setName($name): self
    {
        $this->name = $name;
        $this->data['name'] = $name;
        return $this;
    }

    /**
     * @param mixed $useSid
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getProductUrl($useSid = null)
    {
        return $this->productUrl;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setProductUrl($url)
    {
        $this->productUrl = $url;
        return $this;
    }

    /**
     * @return string|false|null
     */
    public function getRequestPath()
    {
        return $this->getData('request_path');
    }

    /**
     * @param string|false $path
     * @return $this
     */
    public function setRequestPath($path)
    {
        return $this->setData('request_path', $path);
    }

    /**
     * @return int|null
     */
    public function getCategoryId()
    {
        return $this->getData('category_id');
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setCategoryId($id)
    {
        return $this->setData('category_id', $id);
    }

    /**
     * Custom hasPreconfiguredValues method for testing
     *
     * @return bool
     */
    public function hasPreconfiguredValues(): bool
    {
        return $this->data['has_preconfigured_values'] ?? false;
    }

    /**
     * Set has preconfigured values for testing
     *
     * @param bool $hasValues
     * @return self
     */
    public function setHasPreconfiguredValues(bool $hasValues): self
    {
        $this->data['has_preconfigured_values'] = $hasValues;
        return $this;
    }

    /**
     * Override getTypeInstance for testing
     *
     * @return mixed
     */
    public function getTypeInstance()
    {
        // Return from internal data array since parent's getTypeInstance()
        // relies on _catalogProductType which is null when constructor is skipped
        return $this->data['type_instance'] ?? new class {
            public function getSetAttributes($product)
            {
                return [];
            }
        };
    }

    /**
     * Set type instance for testing
     *
     * @param mixed $typeInstance
     * @return self
     */
    public function setTypeInstance($typeInstance): self
    {
        $this->data['type_instance'] = $typeInstance;
        return $this;
    }

    /**
     * Get attributes for testing
     *
     * @param int|null $groupId
     * @param bool $skipSuper
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getAttributes($groupId = null, $skipSuper = false)
    {
        return $this->data['attributes'] ?? [];
    }

    /**
     * Set attributes for testing
     *
     * @param array $attributes
     * @return self
     */
    public function setAttributes(array $attributes): self
    {
        $this->data['attributes'] = $attributes;
        return $this;
    }

    /**
     * Set store filter for testing (noop)
     *
     * @param mixed $storeFilter
     * @return self
     */
    public function setStoreFilter($storeFilter): self
    {
        $this->data['store_filter'] = $storeFilter;
        return $this;
    }

    /**
     * Get price
     *
     * @return mixed
     */
    public function getPrice()
    {
        return $this->data['price'] ?? null;
    }

    /**
     * Set price
     *
     * @param mixed $price
     * @return $this
     */
    public function setPrice($price): self
    {
        $this->data['price'] = $price;
        return $this;
    }

    /**
     * Override getPreconfiguredValues for testing
     *
     * @return mixed
     */
    public function getPreconfiguredValues()
    {
        return $this->data['preconfigured_values'] ?? null;
    }

    /**
     * Set preconfigured values for testing
     *
     * @param mixed $values
     * @return self
     */
    public function setPreconfiguredValues($values): self
    {
        $this->data['preconfigured_values'] = $values;
        return $this;
    }

    /**
     * Custom getSelectionId method for Bundle testing
     *
     * @return mixed
     */
    public function getSelectionId()
    {
        return $this->data['selection_id'] ?? null;
    }

    /**
     * Set selection ID for testing
     *
     * @param int|null $id
     * @return self
     */
    public function setSelectionId(?int $id): self
    {
        $this->data['selection_id'] = $id;
        return $this;
    }

    /**
     * Custom getSelectionQty method for Bundle testing
     *
     * @return float|null
     */
    public function getSelectionQty(): ?float
    {
        return $this->data['selection_qty'] ?? null;
    }

    /**
     * Set selection quantity for testing
     *
     * @param float|null $qty
     * @return self
     */
    public function setSelectionQty(?float $qty): self
    {
        $this->data['selection_qty'] = $qty;
        return $this;
    }

    /**
     * Custom getSelectionCanChangeQty method for Bundle testing
     *
     * @return mixed
     */
    public function getSelectionCanChangeQty()
    {
        return $this->data['selection_can_change_qty'] ?? false;
    }

    /**
     * Set selection can change quantity for testing
     *
     * @param mixed $canChange
     * @return self
     */
    public function setSelectionCanChangeQty($canChange): self
    {
        $this->data['selection_can_change_qty'] = $canChange;
        return $this;
    }

    /**
     * Custom getIsDefault method for Bundle testing
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsDefault()
    {
        return $this->data['is_default'] ?? false;
    }

    /**
     * Set is default for testing
     *
     * @param bool $isDefault
     * @return self
     */
    public function setIsDefault(bool $isDefault): self
    {
        $this->data['is_default'] = $isDefault;
        return $this;
    }

    /**
     * Custom getOptionId method for Bundle testing
     *
     * @return mixed
     */
    public function getOptionId()
    {
        return $this->data['option_id'] ?? null;
    }

    /**
     * Set option ID for testing
     *
     * @param int|null $optionId
     * @return self
     */
    public function setOptionId(?int $optionId): self
    {
        $this->data['option_id'] = $optionId;
        return $this;
    }

    /**
     * Custom setIsRelationsChanged method for testing
     *
     * @param bool $isChanged
     * @return self
     */
    public function setIsRelationsChanged(bool $isChanged): self
    {
        $this->data['is_relations_changed'] = $isChanged;
        return $this;
    }

    /**
     * Get is relations changed for testing
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsRelationsChanged()
    {
        return $this->data['is_relations_changed'] ?? false;
    }

    /**
     * Custom getPriceModel method for testing
     *
     * @return mixed
     */
    public function getPriceModel()
    {
        return $this->data['price_model'] ?? null;
    }

    /**
     * Set price model for testing
     *
     * @param mixed $priceModel
     * @return self
     */
    public function setPriceModel($priceModel): self
    {
        $this->data['price_model'] = $priceModel;
        return $this;
    }

    /**
     * Custom getSelectionPriceType method for testing
     *
     * @return mixed
     */
    public function getSelectionPriceType()
    {
        return $this->data['selection_price_type'] ?? null;
    }

    /**
     * Set selection price type for testing
     *
     * @param mixed $priceType
     * @return self
     */
    public function setSelectionPriceType($priceType): self
    {
        $this->data['selection_price_type'] = $priceType;
        return $this;
    }

    /**
     * Custom getSelectionPriceValue method for testing
     *
     * @return mixed
     */
    public function getSelectionPriceValue()
    {
        return $this->data['selection_price_value'] ?? null;
    }

    /**
     * Set selection price value for testing
     *
     * @param mixed $priceValue
     * @return self
     */
    public function setSelectionPriceValue($priceValue): self
    {
        $this->data['selection_price_value'] = $priceValue;
        return $this;
    }

    /**
     * Custom getExtensionAttributes method for testing
     *
     * @return mixed
     */
    public function getExtensionAttributes()
    {
        return $this->data['extension_attributes'] ?? null;
    }

    /**
     * Set extension attributes for testing
     *
     * @param mixed $extensionAttributes
     * @return self
     */
    public function setExtensionAttributes($extensionAttributes): self
    {
        $this->data['extension_attributes'] = $extensionAttributes;
        return $this;
    }

    /**
     * Custom getCustomOption method for testing
     *
     * @param mixed $code
     * @return mixed
     */
    public function getCustomOption($code = null)
    {
        // Check if a single custom option is set (for setCustomOption)
        if (isset($this->data['custom_option'])) {
            return $this->data['custom_option'];
        }

        if ($code === null) {
            return $this->data['custom_options'] ?? [];
        }
        return $this->data['custom_options'][$code] ?? null;
    }

    /**
     * Custom setCartQty method for testing
     *
     * @param mixed $qty
     * @return self
     */
    public function setCartQty($qty): self
    {
        $this->data['cart_qty'] = $qty;
        return $this;
    }

    /**
     * Custom getSkipCheckRequiredOption method for testing
     *
     * @return mixed
     */
    public function getSkipCheckRequiredOption()
    {
        return $this->data['skip_check_required_option'] ?? false;
    }

    /**
     * Get option for testing
     *
     * @return mixed
     */
    public function getOption()
    {
        return $this->data['option'] ?? null;
    }

    /**
     * Set option for testing
     *
     * @param mixed $option
     * @return self
     */
    public function setOption($option): self
    {
        $this->data['option'] = $option;
        return $this;
    }

    /**
     * Get position for testing
     *
     * @return mixed
     */
    public function getPosition()
    {
        return $this->data['position'] ?? null;
    }

    /**
     * Set position for testing
     *
     * @param mixed $position
     * @return self
     */
    public function setPosition($position): self
    {
        $this->data['position'] = $position;
        return $this;
    }

    /**
     * Set bundle options data with call tracking
     *
     * @param mixed $data
     * @return self
     */
    public function setBundleOptionsData($data): self
    {
        $this->data['bundle_options_data'] = $data;
        $this->setBundleOptionsDataCalled = true;
        $this->setBundleOptionsDataParams = $data;
        return $this;
    }

    /**
     * Set bundle selections data with call tracking
     *
     * @param mixed $data
     * @return self
     */
    public function setBundleSelectionsData($data): self
    {
        $this->data['bundle_selections_data'] = $data;
        $this->setBundleSelectionsDataCalled = true;
        $this->setBundleSelectionsDataParams = $data;
        return $this;
    }

    /**
     * Set can save custom options with call tracking
     *
     * @param mixed $value
     * @return self
     */
    public function setCanSaveCustomOptions($value): self
    {
        $this->data['can_save_custom_options'] = $value;
        $this->setCanSaveCustomOptionsCalled = true;
        $this->setCanSaveCustomOptionsParams = $value;
        return $this;
    }

    /**
     * Set can save bundle selections with call tracking
     *
     * @param mixed $value
     * @return self
     */
    public function setCanSaveBundleSelections($value): self
    {
        $this->data['can_save_bundle_selections'] = $value;
        $this->setCanSaveBundleSelectionsCalled = true;
        $this->setCanSaveBundleSelectionsParams = $value;
        return $this;
    }

    /**
     * @return bool
     */
    public function wasSetBundleOptionsDataCalled(): bool
    {
        return $this->setBundleOptionsDataCalled;
    }

    /**
     * @return array
     */
    public function getSetBundleOptionsDataParams()
    {
        return $this->setBundleOptionsDataParams;
    }

    /**
     * @return bool
     */
    public function wasSetBundleSelectionsDataCalled(): bool
    {
        return $this->setBundleSelectionsDataCalled;
    }

    /**
     * @return array
     */
    public function getSetBundleSelectionsDataParams()
    {
        return $this->setBundleSelectionsDataParams;
    }

    /**
     * @return bool
     */
    public function wasSetCanSaveCustomOptionsCalled(): bool
    {
        return $this->setCanSaveCustomOptionsCalled;
    }

    /**
     * @return array
     */
    public function getSetCanSaveCustomOptionsParams()
    {
        return $this->setCanSaveCustomOptionsParams;
    }

    /**
     * @return bool
     */
    public function wasSetCanSaveBundleSelectionsCalled(): bool
    {
        return $this->setCanSaveBundleSelectionsCalled;
    }

    /**
     * @return array
     */
    public function getSetCanSaveBundleSelectionsParams()
    {
        return $this->setCanSaveBundleSelectionsParams;
    }

    /**
     * @return bool
     */
    public function wasSetOptionsCalled(): bool
    {
        return $this->setOptionsCalled;
    }

    /**
     * @return array
     */
    public function getSetOptionsParams()
    {
        return $this->setOptionsParams;
    }

    /**
     * Custom method for ConfigurableProduct tests
     *
     * @param string|null $key
     * @return array
     */
    public function getMediaGallery($key = null)
    {
        return $this->data['media_gallery'][$key] ?? $this->data['media_gallery'] ?? [];
    }

    /**
     * Custom method for ConfigurableProduct variation tests
     *
     * @return mixed
     */
    public function getNewVariationsAttributeSetId()
    {
        return $this->data['new_variations_attribute_set_id'] ?? 'new_attr_set_id';
    }

    /**
     * Custom method for ConfigurableProduct variation tests
     *
     * @param mixed $value
     * @return self
     */
    public function setNewVariationsAttributeSetId($value): self
    {
        $this->data['new_variations_attribute_set_id'] = $value;
        return $this;
    }

    /**
     * Custom method for ConfigurableProduct tests
     *
     * @param array $websiteIds
     * @return self
     */
    public function setWebsiteIds($websiteIds): self
    {
        $this->data['website_ids'] = $websiteIds;
        return $this;
    }

    /**
     * Override save method to avoid resource dependency
     *
     * @return self
     */
    public function save(): self
    {
        // Mock save method - no actual saving
        return $this;
    }

    /**
     * Custom method for ConfigurableProduct tests
     *
     * @return array
     */
    public function getConfigurableAttributesData()
    {
        return $this->data['configurable_attributes_data'] ?? [];
    }

    /**
     * Custom method for ConfigurableProduct tests
     *
     * @param array $data
     * @return self
     */
    public function setConfigurableAttributesData(array $data): self
    {
        $this->data['configurable_attributes_data'] = $data;
        return $this;
    }

    /**
     * Custom method for ConfigurableProduct tests
     *
     * @param array $ids
     * @return self
     */
    public function setAssociatedProductIds(array $ids): self
    {
        $this->data['associated_product_ids'] = $ids;
        return $this;
    }

    /**
     * Override getTierPrice to prevent null errors
     *
     * @param mixed $qty
     * @param mixed $customerGroupId
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getTierPrice($qty = null, $customerGroupId = null)
    {
        return $this->data['tier_price'] ?? 0;
    }

    /**
     * Get media gallery images (custom method for testing)
     *
     * @return mixed
     */
    public function getMediaGalleryImages()
    {
        return $this->data['media_gallery_images'] ?? null;
    }

    /**
     * Set media gallery images (custom method for testing)
     *
     * @param mixed $images
     * @return self
     */
    public function setMediaGalleryImages($images): self
    {
        $this->data['media_gallery_images'] = $images;
        return $this;
    }

    /**
     * Get tax class ID for testing
     *
     * @return mixed
     */
    public function getTaxClassId()
    {
        if (isset($this->data['tax_class_id'])) {
            return $this->data['tax_class_id'];
        }
        $productData = $this->data['product_data'] ?? [];
        return $productData['tax_class_id'] ?? null;
    }

    /**
     * Set tax class ID for testing
     *
     * @param mixed $taxClassId
     * @return self
     */
    public function setTaxClassId($taxClassId): self
    {
        $this->data['tax_class_id'] = $taxClassId;
        return $this;
    }

    /**
     * Get grouped readonly for testing
     *
     * @return mixed
     */
    public function getGroupedReadonly()
    {
        return $this->data['grouped_readonly'] ?? null;
    }

    /**
     * Get grouped link data for testing
     *
     * @return mixed
     */
    public function getGroupedLinkData()
    {
        return $this->data['grouped_link_data'] ?? null;
    }

    /**
     * Set grouped link data for testing
     *
     * @param mixed $groupedLinkData
     * @return self
     */
    public function setGroupedLinkData($groupedLinkData): self
    {
        $this->data['grouped_link_data'] = $groupedLinkData;
        return $this;
    }

    // ========== DOWNLOADABLE-SPECIFIC METHODS ==========

    /**
     * Set downloadable data for testing
     *
     * @param mixed $data
     * @return self
     */
    public function setDownloadableData($data): self
    {
        $this->data['downloadable_data'] = $data;
        return $this;
    }

    /**
     * Get downloadable data for testing
     *
     * @return mixed
     */
    public function getDownloadableData()
    {
        return $this->data['downloadable_data'] ?? null;
    }

    /**
     * Get links title for downloadable products
     *
     * @return string|null
     */
    public function getLinksTitle()
    {
        return $this->data['links_title'] ?? null;
    }

    /**
     * Get samples title for downloadable products
     *
     * @return string|null
     */
    public function getSamplesTitle()
    {
        return $this->data['samples_title'] ?? null;
    }

    /**
     * Set custom option changed flag
     *
     * @param bool $changed
     * @return self
     */
    public function setIsCustomOptionChanged($changed = true): self
    {
        $this->data['is_custom_option_changed'] = $changed;
        return $this;
    }

    /**
     * Set type has required options flag
     *
     * @param bool $hasRequired
     * @return self
     */
    public function setTypeHasRequiredOptions($hasRequired): self
    {
        $this->data['type_has_required_options'] = $hasRequired;
        return $this;
    }

    /**
     * Set required options
     *
     * @param mixed $required
     * @return self
     */
    public function setRequiredOptions($required): self
    {
        $this->data['required_options'] = $required;
        return $this;
    }

    /**
     * Set type has options flag
     *
     * @param bool $hasOptions
     * @return self
     */
    public function setTypeHasOptions($hasOptions): self
    {
        $this->data['type_has_options'] = $hasOptions;
        return $this;
    }

    /**
     * Set links exist flag
     *
     * @param bool $exist
     * @return self
     */
    public function setLinksExist($exist): self
    {
        $this->data['links_exist'] = $exist;
        return $this;
    }

    /**
     * Get downloadable links
     *
     * @return array
     */
    public function getDownloadableLinks()
    {
        return $this->data['downloadable_links'] ?? [];
    }

    /**
     * Get cart qty for tests.
     *
     * @return mixed
     */
    public function getCartQty()
    {
        return $this->getData('cart_qty');
    }

    /**
     * Return stick within parent flag from internal data.
     *
     * @return mixed
     */
    public function getStickWithinParent()
    {
        return $this->getData('stick_within_parent');
    }

    /**
     * Set stick within parent flag for tests.
     *
     * @param mixed $flag
     * @return $this
     */
    public function setStickWithinParent($flag)
    {
        $this->setData('stick_within_parent', $flag);
        return $this;
    }

    /**
     * Get parent product id for tests.
     *
     * @return mixed
     */
    public function getParentProductId()
    {
        return $this->getData('parent_product_id');
    }

    /**
     * Set parent product id for tests.
     *
     * @param mixed $id
     * @return $this
     */
    public function setParentProductId($id)
    {
        $this->setData('parent_product_id', $id);
        return $this;
    }

    /**
     * Get stock item for tests.
     *
     * @return mixed
     */
    public function getStockItem()
    {
        return $this->getData('stock_item');
    }

    /**
     * Enable/disable super mode flag for tests.
     *
     * @param bool $flag
     * @return $this
     */
    public function setIsSuperMode($flag)
    {
        $this->setData('is_super_mode', (bool)$flag);
        return $this;
    }

    /**
     * Unset skip check required option flag for tests (no-op).
     *
     * @return $this
     */
    public function unsSkipCheckRequiredOption()
    {
        // No-op for unit tests, callable method required for mocks
        return $this;
    }

    /**
     * @return bool
     */
    public function isVisibleInSiteVisibility()
    {
        return false;
    }

    /**
     * @param DataObject $data
     * @return $this
     */
    public function setUrlDataObject($data)
    {
        $this->urlDataObject = $data;
        return $this;
    }

    /**
     * Get URL data object.
     *
     * @return DataObject|null
     */
    public function getUrlDataObject()
    {
        return $this->urlDataObject;
    }

    /**
     * Check if URL data object exists.
     *
     * @return bool
     */
    public function hasUrlDataObject()
    {
        return (bool)$this->urlDataObject;
    }
}
