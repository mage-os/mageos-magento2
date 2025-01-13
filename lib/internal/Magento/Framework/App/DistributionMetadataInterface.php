<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\App;

/**
 * Magento application product metadata
 *
 * @api
 * @since 100.0.2
 */
interface DistributionMetadataInterface
{
    /**
     * Get Distribution version
     *
     * @return string
     */
    public function getDistributionVersion();

    /**
     * Get Product name
     *
     * @return string
     */
    public function getProductName();
}
