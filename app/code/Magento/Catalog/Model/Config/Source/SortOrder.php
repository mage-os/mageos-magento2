<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

/**
 * Catalog Product List Sort Order
 *
 */
namespace Magento\Catalog\Model\Config\Source;

class SortOrder implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Retrieve option values array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'ASC', 'label' => __('Ascending')],
            ['value' => 'DESC', 'label' => __('Descending')],
        ];
    }
}
