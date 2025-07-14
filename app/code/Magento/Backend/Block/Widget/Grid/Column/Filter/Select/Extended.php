<?php
/**
 * Copyright 2013 Adobe
 * All Rights Reserved.
 */
namespace Magento\Backend\Block\Widget\Grid\Column\Filter\Select;

class Extended extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Select
{
    /**
     * Get options for filter value
     *
     * @return array
     */
    protected function _getOptions()
    {
        $emptyOption = ['value' => null, 'label' => ''];

        $optionGroups = $this->getColumn()->getOptionGroups();
        if ($optionGroups) {
            array_unshift($optionGroups, $emptyOption);
            return $optionGroups;
        }

        $colOptions = $this->getColumn()->getOptions();
        if (!empty($colOptions) && is_array($colOptions)) {
            $options = [$emptyOption];
            foreach ($colOptions as $value => $label) {
                $options[] = ['value' => $value, 'label' => $label];
            }
            return $options;
        }
        return [];
    }
}
