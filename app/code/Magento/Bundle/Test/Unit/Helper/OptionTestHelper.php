<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Test\Unit\Helper;

use Magento\Bundle\Model\Option;

/**
 * Test helper for Magento\Bundle\Model\Option
 */
class OptionTestHelper extends Option
{
    private $optionId = null;
    private $selections = [];
    
    public function __construct($selections = [])
    {
        $this->selections = $selections;
    }
    
    /**
     * Set option ID
     * 
     * @param mixed $optionId
     * @return $this
     */
    public function setOptionId($optionId)
    {
        $this->optionId = $optionId;
        return $this;
    }
    
    /**
     * Get option ID
     * 
     * @return mixed
     */
    public function getOptionId()
    {
        return $this->optionId;
    }
    
    /**
     * Set selections
     * 
     * @param array $selections
     * @return $this
     */
    public function setSelections($selections)
    {
        $this->selections = $selections;
        return $this;
    }
    
    /**
     * Get selections
     * 
     * @return array
     */
    public function getSelections()
    {
        return $this->selections;
    }
}
