<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Theme\Block\Html\Pager;

class PagerTestHelper extends Pager
{
    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @param mixed $useContainer
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setUseContainer($useContainer)
    {
        return $this;
    }

    /**
     * @param mixed $showAmounts
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setShowAmounts($showAmounts)
    {
        return $this;
    }

    /**
     * @param mixed $showPerPage
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setShowPerPage($showPerPage)
    {
        return $this;
    }

    /**
     * @param mixed $frameLength
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setFrameLength($frameLength)
    {
        return $this;
    }

    /**
     * @param mixed $jump
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setJump($jump)
    {
        return $this;
    }

    /**
     * @param mixed $limit
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setLimit($limit)
    {
        return $this;
    }

    /**
     * @param mixed $collection
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setCollection($collection)
    {
        return $this;
    }

    /**
     * @param mixed $pageVarName
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setPageVarName($pageVarName)
    {
        return $this;
    }

    /**
     * @param mixed $limitVarName
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setLimitVarName($limitVarName)
    {
        return $this;
    }

    /**
     * @return bool
     */
    public function toHtml()
    {
        return true;
    }
}

