<?php
/**
 * (c) Mage-OS
 *
 * For the full copyright and license information, please view the LICENSE
 * files distributed with this source code.
 */
namespace Magento\Framework\App;

/**
 * Mage-OS Distribution metadata
 *
 * @api
 * @since 1.0.6
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
     * Get Distribution name
     *
     * @return string
     */
    public function getDistributionName();
}
