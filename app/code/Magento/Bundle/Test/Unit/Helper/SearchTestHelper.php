<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Test\Unit\Helper;

use Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Search;

/**
 * Test helper for Bundle Search block with custom methods
 *
 */
class SearchTestHelper extends Search
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor - clean initialization
    }

    /**
     * Set index for testing
     *
     * @param mixed $index
     * @return self
     */
    public function setIndex($index): self
    {
        $this->data['index'] = $index;
        return $this;
    }

    /**
     * Set first show flag for testing
     *
     * @param mixed $firstShow
     * @return self
     */
    public function setFirstShow($firstShow): self
    {
        $this->data['first_show'] = $firstShow;
        return $this;
    }

    /**
     * Produce and return block's html output
     *
     * @return string
     */
    public function toHtml(): string
    {
        return $this->data['html_result'] ?? '';
    }
}
