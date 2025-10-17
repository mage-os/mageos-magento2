<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Widget\Test\Unit\Helper;

use Magento\Framework\View\Element\BlockInterface as ViewBlockInterface;
use Magento\Widget\Block\BlockInterface;

class WidgetBlockTestHelper implements BlockInterface, ViewBlockInterface
{
    /** @var string */
    private string $result = '';

    /** @var array */
    private array $data = [];

    public function setResult(string $result): self
    {
        $this->result = $result;
        return $this;
    }

    public function addData(array $arr)
    {
        $this->data = array_merge($this->data, $arr);
        return $this;
    }

    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = $key;
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    public function toHtml()
    {
        return $this->result;
    }
}
