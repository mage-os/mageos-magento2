<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Widget\Test\Unit\Helper;

use Magento\Widget\Model\Widget\Instance;

/**
 * Test helper for Widget Instance class
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class InstanceTestHelper extends Instance
{
    /**
     * @var string
     */
    private $type = 'some type';

    /**
     * @var string
     */
    private $code = '';

    /**
     * @var int
     */
    private $themeId = 777;

    /**
     * @var bool
     */
    private $isCompleteToCreate = true;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get theme ID
     *
     * @return int
     */
    public function getThemeId()
    {
        return $this->themeId;
    }

    /**
     * Set theme ID
     *
     * @param int $themeId
     * @return $this
     */
    public function setThemeId($themeId)
    {
        $this->themeId = $themeId;
        return $this;
    }

    /**
     * Check if complete to create
     *
     * @return bool
     */
    public function isCompleteToCreate()
    {
        return $this->isCompleteToCreate;
    }

    /**
     * Set complete to create
     *
     * @param bool $isCompleteToCreate
     * @return $this
     */
    public function setIsCompleteToCreate($isCompleteToCreate)
    {
        $this->isCompleteToCreate = $isCompleteToCreate;
        return $this;
    }
}
