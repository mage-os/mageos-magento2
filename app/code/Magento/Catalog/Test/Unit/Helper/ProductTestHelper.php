<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product;

/**
 * Test helper for Product class
 *
 * Provides minimal custom methods needed for testing.
 * Most functionality is inherited from parent Product class.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ProductTestHelper extends Product
{
    /**
     * @var bool
     */
    private $linksPurchasedSeparately = true;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
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
        $reflection = new \ReflectionClass($this);
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
}
