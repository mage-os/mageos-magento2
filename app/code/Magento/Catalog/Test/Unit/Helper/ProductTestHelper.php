<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product;

/**
 * Test helper for Product class
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
     * @param mixed $resource Optional resource parameter
     */
    public function __construct($resource = null)
    {
        $this->resource = $resource;
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
        return $this->priceInfo;
    }

    /**
     * Set custom option (not in parent - parent only has setCustomOptions with array)
     *
     * @param string $code
     * @param mixed $value
     * @return $this
     */
    public function setCustomOption($code, $value)
    {
        $this->_customOptions[$code] = $value;
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
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getLinksPurchasedSeparately()
    {
        return $this->linksPurchasedSeparately;
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
        return $this->getData('store');
    }

    /**
     * Set store
     *
     * @param mixed $store
     * @return $this
     */
    public function setStore($store): self
    {
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
    public function isSalable()
    {
        return $this->isSalable ?? true;
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
        return $this;
    }

    /**
     * Get website id
     *
     * @return int|null
     */
    public function getWebsiteId()
    {
        return $this->websiteId;
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
        return $this;
    }

    /**
     * @return bool
     */
    public function hasCustomerGroupId()
    {
        return $this->hasCustomerGroupId;
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
        return $this->customerGroupId;
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
        return $this;
    }

    /**
     * Get product ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->id ?? 1;
    }

    /**
     * Set product ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get store ID
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeId ?? 1;
    }

    /**
     * Set store ID
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
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
        if ($this->dataValues) {
            return true;
        }
        if ($key === null) {
            return !empty($this->_data) || !empty($this->testData);
        }
        return isset($this->_data[$key]) || isset($this->attributesByCode[$key]) || isset($this->testData[$key]);
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
        if ($keyword === '') {
            if ($this->dataValues) {
                return $this->dataValues;
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

        if (isset($this->testData[$keyword])) {
            $value = $this->testData[$keyword];
        } else {
            $value = $this->_data[$keyword] ?? null;
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
        if (is_array($key)) {
            if ($this->dataValues) {
                $this->attributesByCode = $key;
            } else {
                $this->testData = array_merge($this->testData, $key);
                $this->_data = array_merge($this->_data, $key);
            }
        } else {
            if ($this->dataValues) {
                $this->attributesByCode[$key] = $value;
            } else {
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
            } else {
                unset($this->_data[$key]);
                unset($this->testData[$key]);
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
     * @param mixed $value
     * @return $this
     */
    public function setResource($value)
    {
        $this->resource = $value;
        return $this;
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
     * @return array
     */
    public function getWebsiteIds()
    {
        return $this->getData('website_ids') ?: [];
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
     * @return array|null
     */
    public function getOptions()
    {
        return $this->getData('options');
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
     * @return string|null
     */
    public function getSku()
    {
        return $this->getData('sku');
    }

    /**
     * @param string $sku
     * @return $this
     */
    public function setSku($sku)
    {
        return $this->setData('sku', $sku);
    }

    /**
     * @return string|null
     */
    public function getTypeId()
    {
        return $this->getData('type_id');
    }

    /**
     * @param string $typeId
     * @return $this
     */
    public function setTypeId($typeId)
    {
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
        return $this->hasOptions;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setHasOptions($value)
    {
        $this->hasOptions = $value;
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
        return $this->priceType;
    }

    /**
     * @param mixed $priceType
     * @return $this
     */
    public function setPriceType($priceType)
    {
        $this->priceType = $priceType;
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
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
}
