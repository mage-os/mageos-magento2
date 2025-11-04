<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\ProductLink\Link;

/**
 * Test helper for ProductLink\Link
 */
class ProductLinkInterfaceTestHelper extends Link
{
    /**
     * @var mixed
     */
    private $linkType = null;

    /**
     * @var mixed
     */
    private $linkedProductSku = null;

    /**
     * @var mixed
     */
    private $extensionAttributesOverride = null;

    public function __construct()
    {
        // Empty constructor - skip parent dependencies
    }

    /**
     * @return mixed
     */
    public function getLinkType()
    {
        return $this->linkType;
    }

    /**
     * @param mixed $linkType
     * @return $this
     */
    public function setLinkType($linkType)
    {
        $this->linkType = $linkType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLinkedProductSku()
    {
        return $this->linkedProductSku;
    }

    /**
     * @param mixed $sku
     * @return $this
     */
    public function setLinkedProductSku($sku)
    {
        $this->linkedProductSku = $sku;
        return $this;
    }

    /**
     * Override to bypass type checking for testing
     *
     * @param mixed $attributes
     * @return $this
     */
    public function setExtensionAttributes($attributes)
    {
        $this->extensionAttributesOverride = $attributes;
        return $this;
    }

    /**
     * Override to bypass type checking for testing
     *
     * @return mixed
     */
    public function getExtensionAttributes()
    {
        return $this->extensionAttributesOverride;
    }
}
