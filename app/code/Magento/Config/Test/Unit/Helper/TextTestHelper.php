<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Config\Test\Unit\Helper;

use Magento\Framework\Data\Form\Element\Text;

/**
 * Test helper for Text
 */
class TextTestHelper extends Text
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Skip parent constructor
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * setOriginalData (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setOriginalData($value)
    {
        $this->data['originalData'] = $value;
        return $this;
    }

    /**
     * getLegend (custom method for testing)
     *
     * @return mixed
     */
    public function getLegend()
    {
        return $this->data['legend'] ?? null;
    }

    /**
     * getComment (custom method for testing)
     *
     * @return mixed
     */
    public function getComment()
    {
        return $this->data['comment'] ?? null;
    }

    /**
     * getIsNested (custom method for testing)
     *
     * @return mixed
     */
    public function getIsNested()
    {
        return $this->data['isNested'] ?? null;
    }

    /**
     * getExpanded (custom method for testing)
     *
     * @return mixed
     */
    public function getExpanded()
    {
        return $this->data['expanded'] ?? null;
    }

    /**
     * getTooltip (custom method for testing)
     *
     * @return mixed
     */
    public function getTooltip()
    {
        return $this->data['tooltip'] ?? null;
    }

    /**
     * getLabel (custom method for testing)
     *
     * @return mixed
     */
    public function getLabel()
    {
        return $this->data['label'] ?? null;
    }

    /**
     * getHint (custom method for testing)
     *
     * @return mixed
     */
    public function getHint()
    {
        return $this->data['hint'] ?? null;
    }

    /**
     * getScope (custom method for testing)
     *
     * @return mixed
     */
    public function getScope()
    {
        return $this->data['scope'] ?? null;
    }

    /**
     * getScopeLabel (custom method for testing)
     *
     * @return mixed
     */
    public function getScopeLabel()
    {
        return $this->data['scopeLabel'] ?? null;
    }

    /**
     * getInherit (custom method for testing)
     *
     * @return mixed
     */
    public function getInherit()
    {
        return $this->data['inherit'] ?? null;
    }

    /**
     * getIsDisableInheritance (custom method for testing)
     *
     * @return mixed
     */
    public function getIsDisableInheritance()
    {
        return $this->data['isDisableInheritance'] ?? null;
    }

    /**
     * getCanUseWebsiteValue (custom method for testing)
     *
     * @return mixed
     */
    public function getCanUseWebsiteValue()
    {
        return $this->data['canUseWebsiteValue'] ?? null;
    }

    /**
     * getCanUseDefaultValue (custom method for testing)
     *
     * @return mixed
     */
    public function getCanUseDefaultValue()
    {
        return $this->data['canUseDefaultValue'] ?? null;
    }

    /**
     * setDisabled (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setDisabled($value)
    {
        $this->data['disabled'] = $value;
        return $this;
    }
}
