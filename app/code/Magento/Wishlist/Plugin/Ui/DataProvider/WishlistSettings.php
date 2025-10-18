<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace Magento\Wishlist\Plugin\Ui\DataProvider;

use Magento\Wishlist\Helper\Data;

/**
 * Plugin on Data Provider for frontend ui components (Components are responsible
 * for rendering product on front)
 * This plugin provides allowWishlist setting
 */
class WishlistSettings
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * WishlistSettings constructor.
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Add tax data to result
     *
     * @param \Magento\Catalog\Ui\DataProvider\Product\Listing\DataProvider $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetData(\Magento\Catalog\Ui\DataProvider\Product\Listing\DataProvider $subject, $result)
    {
        $result['allowWishlist'] = $this->helper->isAllow();

        return $result;
    }
}
