<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\CurrencyInterface;
use Magento\Quote\Api\Data\AddressInterface;

/**
 * Test helper for Quote
 *
 * This helper extends the concrete Quote class to provide
 * test-specific functionality without dependency injection issues.
 */
class QuoteTestHelper extends Quote
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor that skips parent initialization
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set data
     *
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * Get data
     *
     * @param string $key
     * @param mixed $index
     * @return mixed
     */
    public function getData($key = '', $index = null)
    {
        if ($key === '') {
            return $this->data;
        }
        return $this->data[$key] ?? null;
    }

    /**
     * Set super mode
     *
     * @param mixed $value
     * @return $this
     */
    public function setIsSuperMode($value)
    {
        return $this;
    }

    /**
     * Get all visible items
     *
     * @return array
     */
    public function getAllVisibleItems()
    {
        return $this->data['allVisibleItems'] ?? [];
    }

    /**
     * Set visible items (alias for setAllVisibleItems)
     *
     * @param array $items
     * @return $this
     */
    public function setVisibleItems($items)
    {
        $this->data['allVisibleItems'] = $items;
        return $this;
    }

    /**
     * Set all visible items
     *
     * @param array $items
     * @return $this
     */
    public function setAllVisibleItems($items)
    {
        $this->data['allVisibleItems'] = $items;
        return $this;
    }

    public function getBaseSubtotalWithDiscount()
    {
        return $this->data['baseSubtotalWithDiscount'] ?? 0;
    }

    public function setBaseSubtotalWithDiscount($value)
    {
        $this->data['baseSubtotalWithDiscount'] = $value;
        return $this;
    }

    public function getSubtotalWithDiscount()
    {
        return $this->data['subtotalWithDiscount'] ?? 0;
    }

    public function setSubtotalWithDiscount($value)
    {
        $this->data['subtotalWithDiscount'] = $value;
        return $this;
    }

    public function getGrandTotal()
    {
        return $this->data['grandTotal'] ?? 0;
    }

    public function setGrandTotal($value)
    {
        $this->data['grandTotal'] = $value;
        return $this;
    }

    public function getBaseGrandTotal()
    {
        return $this->data['baseGrandTotal'] ?? 0;
    }

    public function setBaseGrandTotal($value)
    {
        $this->data['baseGrandTotal'] = $value;
        return $this;
    }

    public function getExtensionAttributes()
    {
        return $this->data['extensionAttributes'] ?? null;
    }

    public function setExtensionAttributes($extensionAttributes)
    {
        $this->data['extensionAttributes'] = $extensionAttributes;
        return $this;
    }

    public function getCurrency()
    {
        return $this->data['currency'] ?? null;
    }

    public function setCurrency(?CurrencyInterface $currency = null)
    {
        $this->data['currency'] = $currency;
        return $this;
    }

    public function getIsVirtual()
    {
        return $this->data['isVirtual'] ?? false;
    }

    public function setIsVirtual($isVirtual)
    {
        $this->data['isVirtual'] = $isVirtual;
        return $this;
    }

    public function isVirtual()
    {
        return $this->data['isVirtual'] ?? false;
    }

    public function getBillingAddress()
    {
        return $this->data['billingAddress'] ?? null;
    }

    public function setBillingAddress(?AddressInterface $billingAddress = null)
    {
        $this->data['billingAddress'] = $billingAddress;
        return $this;
    }

    public function getShippingAddress()
    {
        return $this->data['shippingAddress'] ?? null;
    }

    public function setShippingAddress(?AddressInterface $shippingAddress = null)
    {
        $this->data['shippingAddress'] = $shippingAddress;
        return $this;
    }

    public function getBaseCurrencyCode()
    {
        return $this->data['baseCurrencyCode'] ?? null;
    }

    public function setBaseCurrencyCode($code)
    {
        $this->data['baseCurrencyCode'] = $code;
        return $this;
    }

    public function getQuoteCurrencyCode()
    {
        return $this->data['quoteCurrencyCode'] ?? null;
    }

    public function setQuoteCurrencyCode($code)
    {
        $this->data['quoteCurrencyCode'] = $code;
        return $this;
    }

    public function getBaseToQuoteRate()
    {
        return $this->data['baseToQuoteRate'] ?? null;
    }

    public function setBaseToQuoteRate($rate)
    {
        $this->data['baseToQuoteRate'] = $rate;
        return $this;
    }

    public function getCustomerId()
    {
        return $this->data['customerId'] ?? null;
    }

    public function setCustomerId($customerId)
    {
        $this->data['customerId'] = $customerId;
        return $this;
    }

    public function setTotalsCollectedFlag($flag)
    {
        $this->data['totalsCollectedFlag'] = $flag;
        return $this;
    }

    public function collectTotals()
    {
        return $this;
    }

    public function getAllItems()
    {
        return $this->data['allItems'] ?? [];
    }

    public function setAllItems($items)
    {
        $this->data['allItems'] = $items;
        return $this;
    }

    public function getAppliedRuleIds()
    {
        return $this->data['appliedRuleIds'] ?? '1,2,3';
    }

    public function setAppliedRuleIds($ruleIds)
    {
        $this->data['appliedRuleIds'] = $ruleIds;
        return $this;
    }

    public function getId()
    {
        return $this->data['id'] ?? null;
    }

    public function setId($id)
    {
        $this->data['id'] = $id;
        return $this;
    }

    public function getGiftCards()
    {
        return $this->data['giftCards'] ?? null;
    }

    public function setGiftCards($giftCards)
    {
        $this->data['giftCards'] = $giftCards;
        return $this;
    }

    public function getCouponCode()
    {
        return $this->data['couponCode'] ?? null;
    }

    public function setCouponCode($couponCode)
    {
        $this->data['couponCode'] = $couponCode;
        return $this;
    }

    public function removeAllAddresses()
    {
        $this->data['allAddresses'] = [];
        return $this;
    }

    public function removeAllItems()
    {
        $this->data['itemsCollection'] = [];
        $this->data['allVisibleItems'] = [];
        $this->data['allItems'] = [];
        return $this;
    }

    public function getItemsCollection($useCache = true)
    {
        return $this->data['itemsCollection'] ?? [];
    }

    public function setItemsCollection($items)
    {
        $this->data['itemsCollection'] = $items;
        return $this;
    }

    public function getItemById($id)
    {
        return $this->data['itemById'][$id] ?? null;
    }

    public function setItemById($id, $item)
    {
        $this->data['itemById'][$id] = $item;
        return $this;
    }

    public function removeItem($id)
    {
        unset($this->data['itemById'][$id]);
        return $this;
    }

    public function getAllAddresses()
    {
        return $this->data['allAddresses'] ?? [];
    }

    public function setAllAddresses($addresses)
    {
        $this->data['allAddresses'] = $addresses;
        return $this;
    }

    public function setUpdatedAt($date)
    {
        $this->data['updatedAt'] = $date;
        return $this;
    }

    public function getPayment()
    {
        return $this->data['payment'] ?? null;
    }

    public function setPayment($payment)
    {
        $this->data['payment'] = $payment;
        return $this;
    }

    /**
     * Get store ID
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->data['storeId'] ?? null;
    }

    /**
     * Set store ID
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->data['storeId'] = $storeId;
        return $this;
    }
}
