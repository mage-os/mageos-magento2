<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
namespace Magento\Usps\Model\Source;

/**
 * Freemethod source
 */
class Freemethod extends Method
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();

        array_unshift($options, ['value' => '', 'label' => __('None')]);
        return $options;
    }
}
