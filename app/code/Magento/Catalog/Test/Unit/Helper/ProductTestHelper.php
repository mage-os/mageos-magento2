<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product;
use ReflectionClass;

/**
 * Test helper for Product class
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.BooleanGetMethodName)
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
     * Constructor
     *
     * @param mixed $resource Optional resource parameter
     */
    public function __construct($resource = null)
    {
        $this->resource = $resource;
        $this->_data = [];
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
        $reflection = new ReflectionClass($this);
        $property = $reflection->getProperty('_priceInfo');
        $property->setValue($this, $priceInfo);
        return $this;
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
        $reflection = new ReflectionClass($this);
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
            //echo "[getAllAttributeValues] returned: " . json_encode($result) . "\n";
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
            return !empty($this->_data);
        }
        return isset($this->_data[$key]) || isset($this->attributesByCode[$key]);
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
            return $this->dataValues ? $this->dataValues : $this->_data;
        }

        if (isset($this->attributesByCode[$keyword])) {
            return $this->attributesByCode[$keyword];
        }

        if ($this->dataValues && $this->dataCallCount < count($this->dataValues)) {
            $result = $this->dataValues[$this->dataCallCount];
            $this->dataCallCount++;
            return $result;
        }

        $value = $this->_data[$keyword] ?? null;
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
                $this->_data = array_merge($this->_data, $key);
            }
        } else {
            if ($this->dataValues) {
                $this->attributesByCode[$key] = $value;
            } else {
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
            } else {
                unset($this->_data[$key]);
            }
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
}
