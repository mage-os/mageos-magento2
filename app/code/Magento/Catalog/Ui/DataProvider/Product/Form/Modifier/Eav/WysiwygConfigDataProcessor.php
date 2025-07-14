<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Eav;

/**
 * Add data to related to $meta['arguments']['data']['config']['wysiwygConfigData']
 */
class WysiwygConfigDataProcessor implements WysiwygConfigDataProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(\Magento\Catalog\Api\Data\ProductAttributeInterface $attribute)
    {
        return [
            'add_variables' => false,
            'add_widgets' => false,
            'add_directives' => true,
            'use_container' => true,
            'container_class' => 'hor-scroll',
        ];
    }
}
