<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Store\Test\Unit\Helper;

use Magento\Store\Model\Store;
use Magento\Framework\UrlInterface;

/**
 * Test helper for Store with custom methods
 */
class StoreTestHelper extends Store
{
    /**
     * @var string
     */
    private $baseUrl = '';
  
    /** 
     * @var array
     */
    private $data = [];

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get base URL (custom method for tests)
     *
     * @param string $type
     * @param bool|null $secure
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getBaseUrl($type = UrlInterface::URL_TYPE_LINK, $secure = null): string
    {
        return $this->baseUrl;
    }

    /**
     * Set base URL
     *
     * @param string $baseUrl
     * @return $this
     */
    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }
    

    /**
     * Custom roundPrice method for testing
     *
     * @param float $price
     * @return float
     */
    public function roundPrice($price)
    {
        return $this->data['round_price_callback'] ?
            call_user_func($this->data['round_price_callback'], $price) :
            $price;
    }

    /**
     * Set round price callback for testing
     *
     * @param callable $callback
     * @return self
     */
    public function setRoundPriceCallback(callable $callback): self
    {
        $this->data['round_price_callback'] = $callback;
        return $this;
    }

    /**
     * Set test data for flexible state management
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
     * Get test data
     *
     * @param string $key
     * @return mixed
     */
    public function getTestData(string $key)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * Get base currency code (custom method for testing)
     *
     * @return string
     */
    public function getBaseCurrencyCode(): string
    {
        return $this->data['base_currency_code'] ?? 'USD';
    }

    /**
     * Set base currency code (custom method for testing)
     *
     * @param string $code
     * @return self
     */
    public function setBaseCurrencyCode(string $code): self
    {
        $this->data['base_currency_code'] = $code;
        return $this;
    }

    /**
     * Get base currency (custom method for testing)
     *
     * @return mixed
     */
    public function getBaseCurrency()
    {
        return $this->data['base_currency'] ?? null;
    }

    /**
     * Set base currency (custom method for testing)
     *
     * @param mixed $currency
     * @return self
     */
    public function setBaseCurrency($currency): self
    {
        $this->data['base_currency'] = $currency;
        return $this;
    }

    /**
     * Get store ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->data['store_id'] ?? parent::getId();
    }

    /**
     * Set store ID
     *
     * @param int $id
     * @return self
     */
    public function setId($id)
    {
        $this->data['store_id'] = $id;
        return $this;
    }

    /**
     * Get website ID
     *
     * @return int|null
     */
    public function getWebsiteId()
    {
        return $this->data['website_id'] ?? parent::getWebsiteId();
    }

    /**
     * Set website ID
     *
     * @param int $id
     * @return self
     */
    public function setWebsiteId($id)
    {
        $this->data['website_id'] = $id;
        return $this;
    }
}
