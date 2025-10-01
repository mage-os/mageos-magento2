<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\MediaStorage\Test\Unit\Helper;

use Magento\MediaStorage\Model\File\Storage\Directory\Database;

/**
 * Test helper for Directory\Database class
 */
class DirectoryDatabaseTestHelper extends Database
{
    /**
     * @var string
     */
    private $path = '';

    /**
     * @var string
     */
    private $name = '';

    /**
     * Constructor - skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set path
     *
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Save
     *
     * @return $this
     */
    public function save()
    {
        return $this;
    }

    /**
     * Get parent ID
     *
     * @return int
     */
    public function getParentId()
    {
        return 1;
    }
}
