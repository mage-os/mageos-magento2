<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Rule\Model\Condition\Combine;

/**
 * TestHelper for Combine with dynamic methods
 */
class CombineTestHelper extends Combine
{
    /** @var mixed */
    private $rule = null;
    /** @var bool */
    private $validateResult = false;

    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    public function setRule($rule)
    {
        $this->rule = $rule;
        return $this;
    }

    public function getRule()
    {
        return $this->rule;
    }

    public function validate($object)
    {
        return $this->validateResult;
    }

    public function setValidateResult($value)
    {
        $this->validateResult = $value;
        return $this;
    }
}
