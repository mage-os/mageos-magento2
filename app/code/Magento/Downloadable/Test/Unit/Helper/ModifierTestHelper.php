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
class ModifierTestHelper extends \stdClass
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Custom modifyData method for testing
     *
     * @param array $data
     * @return array
     */
    public function modifyData($data)
    {
        return $this->data['modify_data'] ?? $data;
    }

    /**
     * Set modify data for testing
     *
     * @param array $data
     * @return self
     */
    public function setModifyData($data): self
    {
        $this->data['modify_data'] = $data;
        return $this;
    }

    /**
     * Custom modifyMeta method for testing
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta($meta)
    {
        return $this->data['modify_meta'] ?? $meta;
    }

    /**
     * Set modify meta for testing
     *
     * @param array $meta
     * @return self
     */
    public function setModifyMeta($meta): self
    {
        $this->data['modify_meta'] = $meta;
        return $this;
    }
}
