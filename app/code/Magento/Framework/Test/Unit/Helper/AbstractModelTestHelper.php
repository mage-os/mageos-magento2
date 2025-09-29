<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\Model\AbstractModel;

/**
 * TestHelper for AbstractModel with dynamic methods
 */
class AbstractModelTestHelper extends AbstractModel
{
    /** @var bool|null */
    private $isMassupdate = null;
    /** @var int|null */
    private $id = null;

    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    public function getIsMassupdate()
    {
        return $this->isMassupdate;
    }

    public function setIsMassupdate($value)
    {
        $this->isMassupdate = $value;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
        return $this;
    }
}
