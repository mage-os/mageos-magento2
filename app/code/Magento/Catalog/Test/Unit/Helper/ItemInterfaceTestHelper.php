<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;

/**
 * Test helper for Magento\Catalog\Model\Product\Configuration\Item\ItemInterface
 * 
 * Implements ItemInterface to provide custom methods for testing
 */
class ItemInterfaceTestHelper implements ItemInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // No parent constructor to call for interface
    }

    /**
     * Custom getValue method for testing
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->data['value'] ?? null;
    }

    /**
     * Set value for testing
     *
     * @param mixed $value
     * @return self
     */
    public function setValue($value): self
    {
        $this->data['value'] = $value;
        return $this;
    }

    /**
     * Get product
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
     * Get option by code
     *
     * @param string $code
     * @return mixed
     */
    public function getOptionByCode($code)
    {
        return $this->data['options'][$code] ?? null;
    }

    /**
     * Set option by code for testing
     *
     * @param string $code
     * @param mixed $option
     * @return self
     */
    public function setOptionByCode($code, $option): self
    {
        $this->data['options'][$code] = $option;
        return $this;
    }

    /**
     * Get file download params
     *
     * @return mixed
     */
    public function getFileDownloadParams()
    {
        return $this->data['file_download_params'] ?? null;
    }

    /**
     * Set file download params for testing
     *
     * @param mixed $params
     * @return self
     */
    public function setFileDownloadParams($params): self
    {
        $this->data['file_download_params'] = $params;
        return $this;
    }
}
