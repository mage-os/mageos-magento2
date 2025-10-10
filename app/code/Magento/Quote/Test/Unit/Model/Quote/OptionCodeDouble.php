<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Model\Quote;

use Magento\Quote\Model\Quote\Item\Option;

class OptionCodeDouble extends Option
{
    public function __construct()
    {
        // Skip parent constructor to avoid resource initialization
    }

    public function save()
    {
        // No-op in unit tests
        return $this;
    }

    public function delete()
    {
        // No-op in unit tests
        return $this;
    }

    public function getItem()
    {
        return parent::getItem();
    }

    public function getCode()
    {
        return (string)$this->getData('code');
    }

    public function setCode($code)
    {
        return $this->setData('code', $code);
    }
}


