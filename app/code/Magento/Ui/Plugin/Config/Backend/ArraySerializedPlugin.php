<?php
/**
 * Copyright 2025 Adobe
 * All rights reserved.
 */
declare(strict_types=1);

namespace Magento\Ui\Plugin\Config\Backend;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;
use Magento\Framework\App\Area;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State;

/**
 * Plugin for ArraySerialized backend model
 * Automatically converts row1/row2/row3 format to numerically indexed arrays
 * Only works in adminhtml area and on design config edit page
 */
class ArraySerializedPlugin
{
    private const DESIGN_CONFIG_EDIT_PAGE = ['theme_design_config_edit', '_design_config_edit'];

    /**
     * @param State $appState
     * @param RequestInterface $request
     */
    public function __construct(
        private readonly State $appState,
        private readonly RequestInterface $request
    ) {
    }

    /**
     * Convert string keys to numeric keys. Only applies in adminhtml area and on design config edit page
     *
     * @param ArraySerialized $subject
     * @param ArraySerialized $result
     * @return ArraySerialized
     */
    public function afterAfterLoad(ArraySerialized $subject, ArraySerialized $result)
    {
        // Only apply the conversion in adminhtml area and on design config edit page
        if ($this->appState->getAreaCode() !== Area::AREA_ADMINHTML || !$this->isDesignConfigEditPage()) {
            return $result;
        }

        $value = $subject->getValue();
        if (!is_array($value)) {
            return $result;
        }

        $keys = array_keys($value);
        // Check if keys are string-based (row1, row2, row3) instead of numeric
        if (empty($keys) || is_numeric($keys[0])) {
            return $result;
        }

        // Convert to numerically indexed array
        $convertedValue = array_values($value);
        $subject->setValue($convertedValue);

        return $result;
    }

    /**
     * Check if we're on the design config edit page.
     *
     * @return bool
     */
    private function isDesignConfigEditPage(): bool
    {
        return in_array($this->request->getFullActionName(), self::DESIGN_CONFIG_EDIT_PAGE);
    }
}
