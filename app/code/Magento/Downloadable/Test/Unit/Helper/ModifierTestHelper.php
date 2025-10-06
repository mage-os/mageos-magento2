<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Downloadable\Test\Unit\Helper;

/**
 * Test helper class for Modifier with custom methods
 */
class ModifierTestHelper
{
    /**
     * Custom modifyData method for testing
     *
     * @param array $data
     * @return array
     */
    public function modifyData($data)
    {
        return $data;
    }

    /**
     * Custom modifyMeta method for testing
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta($meta)
    {
        return $meta;
    }
}
