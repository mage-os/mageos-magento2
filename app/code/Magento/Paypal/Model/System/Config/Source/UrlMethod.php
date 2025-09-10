<?php
/**
 * Copyright 2013 Adobe
 * All Rights Reserved.
 */
namespace Magento\Paypal\Model\System\Config\Source;

/**
 * Source model for url method: GET/POST
 */
class UrlMethod implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [['value' => 'GET', 'label' => 'GET'], ['value' => 'POST', 'label' => 'POST']];
    }
}
