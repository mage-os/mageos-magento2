<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Model\Test\Unit\Helper;

use Magento\Framework\Model\AbstractModel;
use Magento\MediaStorage\Model\File\Storage\Database;

/**
 * Test helper for AbstractModel class
 */
class AbstractModelTestHelper extends AbstractModel
{
    /**
     * @var Database
     */
    private $fileMock;

    /**
     * Constructor - skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Load by filename
     *
     * @param string $filename
     * @return Database
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function loadByFileName($filename)
    {
        return $this->fileMock;
    }

    /**
     * Set file mock
     *
     * @param Database $fileMock
     * @return $this
     */
    public function setFileMock($fileMock)
    {
        $this->fileMock = $fileMock;
        return $this;
    }

    /**
     * Get file mock
     *
     * @return Database
     */
    public function getFileMock()
    {
        return $this->fileMock;
    }
}
