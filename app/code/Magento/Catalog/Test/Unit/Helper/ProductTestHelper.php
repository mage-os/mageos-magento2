<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product;
use Magento\Framework\Pricing\PriceInfoInterface;

/**
 * Test helper for Product class
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ProductTestHelper extends Product
{
    /**
     * @var int
     */
    private $productId;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var mixed
     */
    private $typeInstance;

    /**
     * @var bool
     */
    private $isSalable;

    /**
     * @var string
     */
    private $name;

    /**
     * @var PriceInfoInterface
     */
    private $priceInfo;

    /**
     * @var mixed
     */
    private $priceType = null;

    /**
     * @var mixed
     */
    private $typeId = null;

    /**
     * @var bool
     */
    private $linksPurchasedSeparately = true;

    /**
     * @var array
     */
    private $customOptions = [];

    /**
     * @var bool
     */
    private $allowedInRss = true;

    /**
     * @var bool
     */
    private $allowedPriceInRss = true;

    /**
     * @var bool
     */
    private $disableAddToCart = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set ID
     *
     * @param mixed $productId
     * @return $this
     */
    public function setId($productId)
    {
        $this->productId = $productId;
        return $this;
    }

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->productId;
    }

    /**
     * Has wishlist store ID
     *
     * @return bool
     */
    public function hasWishlistStoreId()
    {
        return false;
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
     * Get store ID
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * Set type instance
     *
     * @param mixed $typeInstance
     * @return $this
     */
    public function setTypeInstance($typeInstance)
    {
        $this->typeInstance = $typeInstance;
        return $this;
    }

    /**
     * Get type instance
     *
     * @return mixed
     */
    public function getTypeInstance()
    {
        return $this->typeInstance;
    }

    /**
     * Set is salable
     *
     * @param bool $isSalable
     * @return $this
     */
    public function setIsSalable($isSalable)
    {
        $this->isSalable = $isSalable;
        return $this;
    }

    /**
     * Get is salable
     *
     * @return bool
     */
    public function isSalable()
    {
        return $this->isSalable;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set price info
     *
     * @param mixed $priceInfo
     * @return $this
     */
    public function setPriceInfo($priceInfo)
    {
        $this->priceInfo = $priceInfo;
        return $this;
    }

    /**
     * Get price info
     *
     * @return PriceInfoInterface
     */
    public function getPriceInfo()
    {
        return $this->priceInfo;
    }

    /**
     * Set custom option
     *
     * @param string $code
     * @param mixed $value
     * @return $this
     */
    public function setCustomOption($code, $value)
    {
        $this->customOptions[$code] = $value;
        return $this;
    }

    /**
     * Get custom option
     *
     * @param string $code
     * @return mixed
     */
    public function getCustomOption($code)
    {
        return $this->customOptions[$code] ?? null;
    }

    /**
     * Set links purchased separately
     *
     * @param bool $value
     * @return $this
     */
    public function setLinksPurchasedSeparately($value)
    {
        $this->linksPurchasedSeparately = $value;
        return $this;
    }

    /**
     * Get links purchased separately
     *
     * @return bool
     */
    public function isLinksPurchasedSeparately()
    {
        return $this->linksPurchasedSeparately;
    }

    /**
     * Get links purchased separately (alias for compatibility)
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getLinksPurchasedSeparately()
    {
        return $this->linksPurchasedSeparately;
    }

    /**
     * Set allowed in RSS
     *
     * @param mixed $value
     * @return $this
     */
    public function setAllowedInRss($value)
    {
        return $this;
    }

    /**
     * Set allowed price in RSS
     *
     * @param mixed $value
     * @return $this
     */
    public function setAllowedPriceInRss($value)
    {
        return $this;
    }

    /**
     * Set product URL
     *
     * @param mixed $url
     * @return $this
     */
    public function setProductUrl($url)
    {
        return $this;
    }

    /**
     * Get product URL
     *
     * @param mixed $useSid
     * @return string
     */
    public function getProductUrl($useSid = null)
    {
        return 'http://example.com/product';
    }

    /**
     * Set image URL
     *
     * @param mixed $url
     * @return $this
     */
    public function setImageUrl($url)
    {
        return $this;
    }

    /**
     * Get image URL
     *
     * @return string
     */
    public function getImageUrl()
    {
        return 'http://example.com/image.jpg';
    }

    /**
     * Get allowed in RSS
     *
     * @return bool
     */
    public function isAllowedInRss()
    {
        return $this->allowedInRss;
    }

    /**
     * Get allowed in RSS (alias for compatibility)
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getAllowedInRss()
    {
        return $this->allowedInRss;
    }

    /**
     * Get allowed price in RSS
     *
     * @return bool
     */
    public function isAllowedPriceInRss()
    {
        return $this->allowedPriceInRss;
    }

    /**
     * Get allowed price in RSS (alias for compatibility)
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getAllowedPriceInRss()
    {
        return $this->allowedPriceInRss;
    }

    /**
     * Get short description
     *
     * @return string
     */
    public function getShortDescription()
    {
        return 'Product short description';
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return 'Product description';
    }

    /**
     * Get price type
     *
     * @return mixed
     */
    public function getPriceType()
    {
        return $this->priceType;
    }

    /**
     * Set price type
     *
     * @param mixed $type
     * @return $this
     */
    public function setPriceType($type)
    {
        $this->priceType = $type;
        return $this;
    }

    /**
     * Get type ID
     *
     * @return mixed
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * Set type ID
     *
     * @param mixed $id
     * @return $this
     */
    public function setTypeId($id)
    {
        $this->typeId = $id;
        return $this;
    }

    /**
     * Set disable add to cart
     *
     * @param bool $value
     * @return $this
     */
    public function setDisableAddToCart($value)
    {
        $this->disableAddToCart = $value;
        return $this;
    }

    /**
     * Is disable add to cart
     *
     * @return bool
     */
    public function isDisableAddToCart()
    {
        return $this->disableAddToCart;
    }

    /**
     * Get disable add to cart (alias for compatibility)
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getDisableAddToCart()
    {
        return $this->disableAddToCart;
    }

    /**
     * Set rating summary
     *
     * @param mixed $summary
     * @return $this
     */
    public function setRatingSummary($summary)
    {
        return $this;
    }

    /**
     * Wakeup method
     *
     * @return $this
     */
    public function __wakeup()
    {
        return $this;
    }

    /**
     * Get review ID
     *
     * @return int
     */
    public function getReviewId()
    {
        return 1;
    }

    /**
     * Get nick name
     *
     * @return string
     */
    public function getNickName()
    {
        return 'Product Nick';
    }

    /**
     * Get detail
     *
     * @return string
     */
    public function getDetail()
    {
        return 'Product Detail';
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Product Title';
    }
}
