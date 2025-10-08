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
        // Support callback pattern used in Bundle tests
        if (isset($this->data['option_by_code_callback']) && is_callable($this->data['option_by_code_callback'])) {
            return call_user_func($this->data['option_by_code_callback'], $code);
        }
        
        return $this->data['options'][$code] ?? $this->data['option_by_code_callback'] ?? null;
    }

    /**
     * Set option by code for testing
     * Supports both individual options and callback patterns
     *
     * @param string|callable|null $codeOrCallback
     * @param mixed $option
     * @return self
     */
    public function setOptionByCode($codeOrCallback, $option = null): self
    {
        // If only one parameter is provided, it's either a callback or null
        if (func_num_args() === 1) {
            $this->data['option_by_code_callback'] = $codeOrCallback;
        } else {
            // Two parameters: traditional code => option mapping
            $this->data['options'][$codeOrCallback] = $option;
        }
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

    /**
     * Get quantity for testing
     *
     * @return mixed
     */
    public function getQty()
    {
        return $this->data['qty'] ?? null;
    }

    /**
     * Set quantity for testing
     *
     * @param mixed $qty
     * @return self
     */
    public function setQty($qty): self
    {
        $this->data['qty'] = $qty;
        return $this;
    }
}
