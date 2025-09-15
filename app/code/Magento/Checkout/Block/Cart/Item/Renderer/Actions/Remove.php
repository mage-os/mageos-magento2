<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\Checkout\Block\Cart\Item\Renderer\Actions;

use Magento\Checkout\Helper\Cart;
use Magento\Framework\View\Element\Template;

/**
 * @api
 * @since 100.0.2
 */
class Remove extends Generic
{
    /**
     * @var Cart
     */
    protected $cartHelper;

    /**
     * @param Template\Context $context
     * @param Cart $cartHelper
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        Template\Context $context,
        Cart $cartHelper,
        array $data = []
    ) {
        $this->cartHelper = $cartHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get delete item POST JSON
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getDeletePostJson()
    {
        return $this->cartHelper->getDeletePostJson($this->getItem());
    }
}
