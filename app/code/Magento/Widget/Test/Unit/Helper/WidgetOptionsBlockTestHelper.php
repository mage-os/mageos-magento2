<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Widget\Test\Unit\Helper;

use Magento\Framework\View\Element\BlockInterface;

class WidgetOptionsBlockTestHelper implements BlockInterface
{
    /** @var string */
    private $widgetType = '';

    /** @var array */
    private $widgetValues = [];

    public function setWidgetType($type)
    {
        $this->widgetType = $type;
        return $this;
    }

    public function getWidgetType()
    {
        return $this->widgetType;
    }

    public function setWidgetValues($values)
    {
        $this->widgetValues = $values;
        return $this;
    }

    public function getWidgetValues()
    {
        return $this->widgetValues;
    }

    public function toHtml()
    {
        return '';
    }
}
