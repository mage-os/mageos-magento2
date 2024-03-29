<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Setup;

/**
 * Class to generate random backend URI
 */
class BackendFrontnameGenerator
{
    /**
     * Prefix for admin area path
     */
    public const ADMIN_AREA_PATH_PREFIX = 'admin_';

    /**
     * Length of the backend frontname random part
     */
    public const ADMIN_AREA_PATH_RANDOM_PART_LENGTH = 7;

    /**
     * Generate Backend name
     *
     * @return string
     */
    public static function generate() : string
    {
        return self::ADMIN_AREA_PATH_PREFIX . strrev(
            substr(base_convert(random_int(0, PHP_INT_MAX), 10, 36), 0, self::ADMIN_AREA_PATH_RANDOM_PART_LENGTH)
        );
    }
}
