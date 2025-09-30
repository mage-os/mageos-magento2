<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product;

/**
 * Test helper class for Catalog Product with custom methods
 * 
 * This helper is placed in Magento_Catalog module as it's the core module
 * that contains the Product class and is used by many other modules
 * including Bundle, ConfigurableProduct, GroupedProduct, etc.
 */
class ProductTestHelper extends Product
{
    private $data = [];

    /**
     * Skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * Override getId to work without constructor
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->data['id'] ?? $this->data['entity_id'] ?? null;
    }

    /**
     * Override setId to work without constructor
     *
     * @param mixed $value
     * @return self
     */
    public function setId($value): self
    {
        $this->data['id'] = $value;
        $this->data['entity_id'] = $value;
        return $this;
    }

    /**
     * Custom getPriceType method for testing (used in Bundle products)
     *
     * @return mixed
     */
    public function getPriceType()
    {
        return $this->data['price_type'] ?? null;
    }

    /**
     * Set price type for testing
     *
     * @param mixed $priceType
     * @return self
     */
    public function setPriceType($priceType): self
    {
        $this->data['price_type'] = $priceType;
        return $this;
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
     * Custom getLowestPrice method for testing
     *
     * @param mixed $product
     * @param mixed $price
     * @return float|null
     */
    public function getLowestPrice($product = null, $price = null): ?float
    {
        // Check if we have a callback configured
        if (isset($this->data['lowest_price_callback']) && is_callable($this->data['lowest_price_callback'])) {
            return call_user_func($this->data['lowest_price_callback'], $product, $price);
        }
        
        return $this->data['lowest_price'] ?? null;
    }

    /**
     * Set lowest price for testing
     *
     * @param float|null $price
     * @return self
     */
    public function setLowestPrice(?float $price): self
    {
        $this->data['lowest_price'] = $price;
        return $this;
    }

    /**
     * Set lowest price callback for testing complex scenarios
     *
     * @param callable $callback
     * @return self
     */
    public function setLowestPriceCallback(callable $callback): self
    {
        $this->data['lowest_price_callback'] = $callback;
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
        return $this->data['type_instance'] ?? null;
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
     * Get options collection (stub for type instance)
     *
     * @param mixed $product
     * @return mixed
     */
    public function getOptionsCollection($product = null)
    {
        return $this->data['options_collection'] ?? null;
    }

    /**
     * Get options IDs (stub for type instance)
     *
     * @param mixed $product
     * @return array
     */
    public function getOptionsIds($product = null)
    {
        return $this->data['options_ids'] ?? [];
    }

    /**
     * Append selections (stub for type instance)
     *
     * @param mixed $optionCollection
     * @param mixed $selectionIds
     * @param mixed $product
     * @return self
     */
    public function appendSelections($optionCollection, $selectionIds = [], $product = null): self
    {
        return $this;
    }

    /**
     * Override getPriceInfo for testing
     *
     * @return mixed
     */
    public function getPriceInfo()
    {
        return $this->data['price_info'] ?? null;
    }

    /**
     * Set price info for testing
     *
     * @param mixed $priceInfo
     * @return self
     */
    public function setPriceInfo($priceInfo): self
    {
        $this->data['price_info'] = $priceInfo;
        return $this;
    }

    /**
     * Override getStoreId for testing
     *
     * @return mixed
     */
    public function getStoreId()
    {
        return $this->data['store_id'] ?? null;
    }

    /**
     * Set store ID for testing
     *
     * @param mixed $storeId
     * @return self
     */
    public function setStoreId($storeId): self
    {
        $this->data['store_id'] = $storeId;
        return $this;
    }

    /**
     * Custom getStore method for testing
     *
     * @return mixed
     */
    public function getStore()
    {
        return $this->data['store'] ?? null;
    }

    /**
     * Set store for testing
     *
     * @param mixed $store
     * @return self
     */
    public function setStore($store): self
    {
        $this->data['store'] = $store;
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
     * Generic data setter for flexible testing
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setTestData(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Generic data getter for flexible testing
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getTestData(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
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
     */
    public function getIsDefault(): bool
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
     * Override getName for testing
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    /**
     * Set name for testing
     *
     * @param mixed $name
     * @return self
     */
    public function setName($name): self
    {
        $this->data['name'] = $name;
        return $this;
    }

    /**
     * Override isSalable for testing
     *
     * @return bool
     */
    public function isSalable(): bool
    {
        return $this->data['is_salable'] ?? true;
    }

    /**
     * Set is salable for testing
     *
     * @param bool $isSalable
     * @return self
     */
    public function setIsSalable(bool $isSalable): self
    {
        $this->data['is_salable'] = $isSalable;
        return $this;
    }

    /**
     * Custom getShipmentType method for Bundle testing
     *
     * @return string|null
     */
    public function getShipmentType(): ?string
    {
        return $this->data['shipment_type'] ?? null;
    }

    /**
     * Set shipment type for testing
     *
     * @param string|null $shipmentType
     * @return self
     */
    public function setShipmentType(?string $shipmentType): self
    {
        $this->data['shipment_type'] = $shipmentType;
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
     * Custom getCopyFromView method for Bundle testing
     *
     * @return bool
     */
    public function getCopyFromView(): bool
    {
        return $this->data['copy_from_view'] ?? false;
    }

    /**
     * Set copy from view for testing
     *
     * @param bool $copyFromView
     * @return self
     */
    public function setCopyFromView(bool $copyFromView): self
    {
        $this->data['copy_from_view'] = $copyFromView;
        return $this;
    }

    /**
     * Custom getTypeId method for testing
     *
     * @return string|null
     */
    public function getTypeId(): ?string
    {
        return $this->data['type_id'] ?? null;
    }

    /**
     * Set type ID for testing
     *
     * @param mixed $typeId
     * @return self
     */
    public function setTypeId($typeId): self
    {
        $this->data['type_id'] = $typeId;
        return $this;
    }

    /**
     * Custom getData method for testing
     *
     * @param string $key
     * @param mixed $index
     * @return mixed
     */
    public function getData($key = '', $index = null)
    {
        // Check if there's a callback set for getData
        if (isset($this->data['get_data_callback'])) {
            return call_user_func($this->data['get_data_callback'], $key);
        }
        
        // Use separate productData array for getData/setData to avoid conflicts
        if ($key === '' || $key === null) {
            return $this->data['product_data'] ?? [];
        }
        $productData = $this->data['product_data'] ?? [];
        return $productData[$key] ?? $index;
    }

    /**
     * Set data for testing
     *
     * @param string|array $key
     * @param mixed $value
     * @return self
     */
    public function setData($key, $value = null): self
    {
        // Use separate productData array for getData/setData to avoid conflicts
        if (!isset($this->data['product_data'])) {
            $this->data['product_data'] = [];
        }
        
        if (is_array($key)) {
            $this->data['product_data'] = array_merge($this->data['product_data'], $key);
        } else {
            $this->data['product_data'][$key] = $value;
        }
        return $this;
    }

    /**
     * Custom getSku method for testing
     *
     * @return string|null
     */
    public function getSku(): ?string
    {
        return $this->data['sku'] ?? null;
    }

    /**
     * Set SKU for testing
     *
     * @param mixed $sku
     * @return self
     */
    public function setSku($sku): self
    {
        $this->data['sku'] = $sku;
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
     */
    public function getIsRelationsChanged(): bool
    {
        return $this->data['is_relations_changed'] ?? false;
    }

    /**
     * Custom getWebsiteId method for testing
     *
     * @return mixed
     */
    public function getWebsiteId()
    {
        return $this->data['website_id'] ?? null;
    }

    /**
     * Set website ID for testing
     *
     * @param mixed $websiteId
     * @return self
     */
    public function setWebsiteId($websiteId): self
    {
        $this->data['website_id'] = $websiteId;
        return $this;
    }

    /**
     * Custom hasCustomerGroupId method for testing
     *
     * @return bool
     */
    public function hasCustomerGroupId(): bool
    {
        return isset($this->data['customer_group_id']);
    }

    /**
     * Custom getCustomerGroupId method for testing
     *
     * @return mixed
     */
    public function getCustomerGroupId()
    {
        return $this->data['customer_group_id'] ?? null;
    }

    /**
     * Set customer group ID for testing
     *
     * @param mixed $customerGroupId
     * @return self
     */
    public function setCustomerGroupId($customerGroupId): self
    {
        $this->data['customer_group_id'] = $customerGroupId;
        return $this;
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
     * Set options for testing
     *
     * @param ?array $options
     * @return self
     */
    public function setOptions(?array $options = null): self
    {
        $this->data['options'] = $options;
        return $this;
    }

    /**
     * Get options for testing
     *
     * @return array
     */
    public function getOptions(): array
    {
        return $this->data['options'] ?? [];
    }

    /**
     * Custom prepareCustomOptions method for testing
     *
     * @return self
     */
    public function prepareCustomOptions(): self
    {
        // Mock implementation - just return self
        return $this;
    }

    /**
     * Custom addCustomOption method for testing
     *
     * @param mixed $code
     * @param mixed $value
     * @param mixed $product
     * @return self
     */
    public function addCustomOption($code, $value, $product = null): self
    {
        if (!isset($this->data['custom_options'])) {
            $this->data['custom_options'] = [];
        }
        $this->data['custom_options'][$code] = $value;
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
     * Custom setQty method for testing
     *
     * @param mixed $qty
     * @return self
     */
    public function setQty($qty): self
    {
        $this->data['qty'] = $qty;
        return $this;
    }

    /**
     * Custom getHasOptions method for testing
     *
     * @return mixed
     */
    public function getHasOptions()
    {
        return $this->data['has_options'] ?? false;
    }

    /**
     * Set has options for testing
     *
     * @param mixed $hasOptions
     * @return self
     */
    public function setHasOptions($hasOptions): self
    {
        $this->data['has_options'] = $hasOptions;
        return $this;
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
     * Custom getCartQty method for testing
     *
     * @return mixed
     */
    public function getCartQty()
    {
        return $this->data['cart_qty'] ?? null;
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
     * Set skip check required option for testing
     *
     * @param mixed $skip
     * @return self
     */
    public function setSkipCheckRequiredOption($skip): self
    {
        $this->data['skip_check_required_option'] = $skip;
        return $this;
    }

    /**
     * Custom hasData method for testing
     *
     * @param string $key
     * @return mixed
     */
    public function hasData($key = '')
    {
        return $this->data['has_data'] ?? true;
    }

    /**
     * Set hasData return value for testing
     *
     * @param mixed $value
     * @return self
     */
    public function setHasData($value): self
    {
        $this->data['has_data'] = $value;
        return $this;
    }

    /**
     * Set getData callback for testing
     *
     * @param callable $callback
     * @return self
     */
    public function setGetDataCallback(callable $callback): self
    {
        $this->data['get_data_callback'] = $callback;
        return $this;
    }

    /**
     * Set custom option for testing
     * Supports both single option and key-value pair
     *
     * @param mixed $optionOrKey - Either the option object or the option key
     * @param mixed $value - Optional value when using key-value pair
     * @return self
     */
    public function setCustomOption($optionOrKey, $value = null): self
    {
        // If value is provided, this is a key-value pair
        if ($value !== null) {
            $this->data['custom_options'][$optionOrKey] = $value;
        } else {
            // Single option (backward compatibility)
            $this->data['custom_option'] = $optionOrKey;
        }
        return $this;
    }

    /**
     * Set getOptions callback for testing
     *
     * @param callable $callback
     * @return self
     */
    public function setGetOptionsCallback(callable $callback): self
    {
        $this->data['get_options_callback'] = $callback;
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
     * Custom getQuantity method for Bundle selection testing
     *
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->data['quantity'] ?? null;
    }

    /**
     * Set quantity for testing
     *
     * @param mixed $quantity
     * @return self
     */
    public function setQuantity($quantity): self
    {
        $this->data['quantity'] = $quantity;
        return $this;
    }

    /**
     * Custom getAmount method for Bundle selection testing
     *
     * @return mixed
     */
    public function getAmount()
    {
        return $this->data['amount'] ?? null;
    }

    /**
     * Set amount for testing
     *
     * @param mixed $amount
     * @return self
     */
    public function setAmount($amount): self
    {
        $this->data['amount'] = $amount;
        return $this;
    }

    /**
     * Custom getProduct method for Bundle selection testing
     *
     * @return mixed
     */
    public function getProduct()
    {
        return $this->data['product'] ?? null;
    }

    /**
     * Set product for testing
     *
     * @param mixed $product
     * @return self
     */
    public function setProduct($product): self
    {
        $this->data['product'] = $product;
        return $this;
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

}
