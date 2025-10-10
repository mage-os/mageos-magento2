<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Model\Quote;

use Magento\Quote\Model\Quote\Address\Total;

class TotalsTotalCodeDouble extends Total
{
    private $code;

    public function __construct()
    {
        // Skip parent constructor
    }

    public function setCode(string $code)
    {
        $this->code = $code;
        return $this;
    }

    public function getCode()
    {
        return $this->code ?? (string)$this->getData('code');
    }
}



