<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Eav\Test\Unit\Helper;

use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Eav\Model\Entity\Attribute\Exception as EavAttributeException;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class BackendValidatorTestHelper extends AbstractBackend
{
    /**
     * @var bool
     */
    private $shouldThrowException = false;

    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    public function validate($object)
    {
        if ($this->shouldThrowException) {
            throw new EavAttributeException(__('Make sure the To Date is later than or the same as the From Date.'));
        }
        return true;
    }

    public function setShouldThrowException($value)
    {
        $this->shouldThrowException = $value;
        return $this;
    }
}

