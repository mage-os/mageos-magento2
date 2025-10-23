<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogSearch\Test\Unit\Helper;

use Magento\CatalogSearch\Model\ResourceModel\EngineInterface;

/**
 * Mock class for EngineInterface with additional methods
 */
class EngineInterfaceTestHelper implements EngineInterface
{
    /**
     * @var mixed
     */
    private $isAvailable = null;

    /**
     * Mock method for isAvailable
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->isAvailable;
    }

    /**
     * Set the isAvailable value
     *
     * @param bool $value
     * @return $this
     */
    public function setIsAvailable($value)
    {
        $this->isAvailable = $value;
        return $this;
    }

    // Required methods from EngineInterface
    public function processEntityIndex($index, $separator = ' ')
    {
        return '';
    }
    public function prepareEntityIndex($index, $separator = ' ')
    {
        return '';
    }
    public function allowAdvancedIndex()
    {
        return true;
    }
    public function processIndexUpdate($index, $separator = ' ')
    {
        return '';
    }
    public function getAllowedVisibility()
    {
        return [];
    }
    public function processAttributeValue($attributeCode, $value)
    {
        return $value;
    }
}
