<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Data\Test\Unit\Helper;

use Magento\Framework\DataObject;

class DataObjectExtendedTestHelper extends DataObject
{
    /**
     * @var mixed
     */
    private $optionsResult;

    /**
     * @var mixed
     */
    private $downloadableLinksResult;

    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->optionsResult;
    }

    /**
     * @param mixed $result
     * @return $this
     */
    public function setOptions($result)
    {
        $this->optionsResult = $result;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDownloadableLinks()
    {
        return $this->downloadableLinksResult;
    }

    /**
     * @param mixed $result
     * @return $this
     */
    public function setDownloadableLinks($result)
    {
        $this->downloadableLinksResult = $result;
        return $this;
    }
}

