<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogSearch\Test\Unit\Helper;

use Magento\Search\Model\Autocomplete\Item;

/**
 * Mock class for Item with additional methods
 */
class ItemMock extends Item
{
    private $title = null;
    private $titleSequence = [];
    private $currentIndex = 0;

    /**
     * Mock method for getTitle with sequence support
     *
     * @return string|null
     */
    public function getTitle()
    {
        if (!empty($this->titleSequence)) {
            $title = $this->titleSequence[$this->currentIndex] ?? null;
            $this->currentIndex++;
            return $title;
        }
        return $this->title;
    }

    /**
     * Set the title value
     *
     * @param string|null $value
     * @return $this
     */
    public function setTitle($value)
    {
        $this->title = $value;
        return $this;
    }

    /**
     * Set a sequence of titles for consecutive calls
     *
     * @param array $titles
     * @return $this
     */
    public function setTitleSequence(array $titles)
    {
        $this->titleSequence = $titles;
        $this->currentIndex = 0;
        return $this;
    }

    /**
     * Required method from Item
     */
    protected function _construct(): void
    {
        // Mock implementation
    }
}
