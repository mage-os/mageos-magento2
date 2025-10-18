<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\Config\Model\Config\Source\Website;

use Magento\Store\Model\System\Store;

/**
 * Admin OptionHash will include the default store (Admin) with the OptionHash.
 *
 * This class is needed until the layout file supports supplying arguments to an option model.
 * @api
 * @since 100.0.2
 */
class AdminOptionHash extends OptionHash
{
    /**
     * @param Store $systemStore
     * @param bool $withDefaultWebsite
     */
    public function __construct(Store $systemStore, $withDefaultWebsite = true)
    {
        parent::__construct($systemStore, $withDefaultWebsite);
    }
}
