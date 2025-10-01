<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\MediaStorage\Test\Unit\Helper;

use Magento\MediaStorage\Model\File\Storage\Database;

/**
 * Test helper for Database class
 */
class DatabaseTestHelper extends Database
{
    /**
     * @var string
     */
    private $content = 'content';

    /**
     * @var bool
     */
    private $id = true;

    /**
     * Constructor - skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get ID (alias for compatibility)
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ID
     *
     * @param bool $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Load by filename
     *
     * @param string $filename
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function loadByFilename($filename)
    {
        return $this;
    }
}
