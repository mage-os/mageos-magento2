<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\View\Test\Unit\Helper;

use Magento\Framework\View\Element\AbstractBlock;

/**
 * Test helper for AbstractBlock class
 */
class AbstractBlockTestHelper extends AbstractBlock
{
    /**
     * @var string
     */
    private $csvFile;

    /**
     * @var string
     */
    private $clearUrl;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get CSV file
     *
     * @return string|null
     */
    public function getCsvFile()
    {
        return $this->csvFile;
    }

    /**
     * Set CSV file
     *
     * @param string $file
     * @return $this
     */
    public function setCsvFile($file)
    {
        $this->csvFile = $file;
        return $this;
    }

    /**
     * Set clear URL
     *
     * @param string $url
     * @return void
     */
    public function setClearUrl($url)
    {
        $this->clearUrl = $url;
    }

    /**
     * Get clear URL
     *
     * @return string
     */
    public function getClearUrl()
    {
        return $this->clearUrl;
    }
}
