<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Widget\Test\Unit\Helper;

use Magento\Widget\Block\Adminhtml\Widget\Catalog\Category\Chooser;

/**
 * Test helper for Category Chooser block
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CategoryChooserTestHelper extends Chooser
{
    /**
     * @var string
     */
    private $htmlContent = 'block_content';

    /**
     * @var array
     */
    private $selectedCategories = [];

    /**
     * @var bool
     */
    private $useMassaction = false;

    /**
     * @var string
     */
    private $id = '';

    /**
     * @var bool
     */
    private $isAnchorOnly = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set HTML content
     *
     * @param string $htmlContent
     * @return $this
     */
    public function setHtmlContent($htmlContent)
    {
        $this->htmlContent = $htmlContent;
        return $this;
    }

    /**
     * Get HTML content
     *
     * @return string
     */
    public function getHtmlContent()
    {
        return $this->htmlContent;
    }

    /**
     * Set selected categories
     *
     * @param array|string $selectedCategories
     * @return $this
     */
    public function setSelectedCategories($selectedCategories)
    {
        $this->selectedCategories = is_array($selectedCategories) ? $selectedCategories : [$selectedCategories];
        return $this;
    }

    /**
     * Get selected categories
     *
     * @return array
     */
    public function getSelectedCategories()
    {
        return $this->selectedCategories;
    }

    /**
     * Set use massaction
     *
     * @param bool $useMassaction
     * @return $this
     */
    public function setUseMassaction($useMassaction)
    {
        $this->useMassaction = $useMassaction;
        return $this;
    }

    /**
     * Check if use massaction
     *
     * @return bool
     */
    public function isUseMassaction()
    {
        return $this->useMassaction;
    }

    /**
     * Set ID
     *
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set is anchor only
     *
     * @param bool $isAnchorOnly
     * @return $this
     */
    public function setIsAnchorOnly($isAnchorOnly)
    {
        $this->isAnchorOnly = $isAnchorOnly;
        return $this;
    }

    /**
     * Check if is anchor only
     *
     * @return bool
     */
    public function isAnchorOnly()
    {
        return $this->isAnchorOnly;
    }

    /**
     * Produce and return block's html output
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->htmlContent;
    }
}
