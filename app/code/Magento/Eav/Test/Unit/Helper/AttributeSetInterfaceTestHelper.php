<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Eav\Test\Unit\Helper;

use Magento\Eav\Model\Entity\Attribute\Set;

class AttributeSetInterfaceTestHelper extends Set
{
    /**
     * @var mixed
     */
    private $idReturn = null;

    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * @param mixed $return
     * @return $this
     */
    public function setIdReturn($return)
    {
        $this->idReturn = $return;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->idReturn;
    }
}

