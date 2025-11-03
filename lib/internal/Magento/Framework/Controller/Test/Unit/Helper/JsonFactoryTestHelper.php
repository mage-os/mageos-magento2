<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Controller\Test\Unit\Helper;

use Magento\Framework\Controller\Result\JsonFactory;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class JsonFactoryTestHelper extends JsonFactory
{
    /**
     * @var mixed
     */
    private $createReturn = null;

    /**
     * @var mixed
     */
    private $setDataReturn = null;

    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    public function setCreateReturn($return)
    {
        $this->createReturn = $return;
        return $this;
    }

    public function setSetDataReturn($return)
    {
        $this->setDataReturn = $return;
        return $this;
    }

    public function create(array $data = [])
    {
        if ($this->createReturn !== null) {
            return $this->createReturn;
        }
        return $this;
    }

    public function setData($data)
    {
        if ($this->setDataReturn !== null) {
            return $this->setDataReturn;
        }
        return $this;
    }
}

