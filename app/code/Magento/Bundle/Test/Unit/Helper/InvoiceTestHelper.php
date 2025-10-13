<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Test\Unit\Helper;

use Magento\Bundle\Model\Sales\Order\Pdf\Items\Invoice;

/**
 * Test helper for Magento\Bundle\Model\Sales\Order\Pdf\Items\Invoice
 *
 * Extends the concrete Invoice class to add custom methods for testing
 */
class InvoiceTestHelper extends Invoice
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor - skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set font regular for testing
     *
     * @param mixed $font
     * @return self
     */
    public function _setFontRegular($font = null): self
    {
        $this->data['font_regular'] = $font;
        return $this;
    }

    /**
     * Get children for testing
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->data['children'] ?? [];
    }

    /**
     * Set children for testing
     *
     * @param array $children
     * @return self
     */
    public function setChildren($children): self
    {
        $this->data['children'] = $children;
        return $this;
    }

    /**
     * Is shipment separately for testing
     *
     * @return bool
     */
    public function isShipmentSeparately()
    {
        return $this->data['shipment_separately'] ?? false;
    }

    /**
     * Set shipment separately for testing
     *
     * @param bool $separately
     * @return self
     */
    public function setShipmentSeparately($separately): self
    {
        $this->data['shipment_separately'] = $separately;
        return $this;
    }

    /**
     * Is child calculated for testing
     *
     * @return bool
     */
    public function isChildCalculated()
    {
        return $this->data['child_calculated'] ?? false;
    }

    /**
     * Set child calculated for testing
     *
     * @param bool $calculated
     * @return self
     */
    public function setChildCalculated($calculated): self
    {
        $this->data['child_calculated'] = $calculated;
        return $this;
    }

    /**
     * Get value HTML for testing
     *
     * @param mixed $item
     * @return string
     */
    public function getValueHtml($item)
    {
        return $this->data['value_html'] ?? '';
    }

    /**
     * Set value HTML for testing
     *
     * @param string $html
     * @return self
     */
    public function setValueHtml($html): self
    {
        $this->data['value_html'] = $html;
        return $this;
    }

    /**
     * Get selection attributes for testing
     *
     * @param mixed $item
     * @return array
     */
    public function getSelectionAttributes($item)
    {
        return $this->data['selection_attributes'] ?? [];
    }

    /**
     * Set selection attributes for testing
     *
     * @param array $attributes
     * @return self
     */
    public function setSelectionAttributes($attributes): self
    {
        $this->data['selection_attributes'] = $attributes;
        return $this;
    }
}
