<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Test\Unit\Helper;

use Magento\Framework\Filesystem\Driver\Https;

class HttpsDriverTestHelper extends Https
{
    public function __construct()
    {
        // Empty constructor to avoid complex dependencies
    }

    /**
     * @param string $path
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isExists($path)
    {
        return true;
    }

    /**
     * @return null
     */
    public function readAll()
    {
        return null;
    }
}

