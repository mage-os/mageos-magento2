<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\Session\Generic;

/**
 * Test helper for Generic Session
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class GenericTestHelper extends Generic
{
    /**
     * @var mixed
     */
    private $formData = null;

    /**
     * @var mixed
     */
    private $redirectUrl = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get form data
     *
     * @param bool $clear
     * @return mixed
     */
    public function getFormData($clear = false)
    {
        return $this->formData;
    }

    /**
     * Set form data
     *
     * @param mixed $data
     * @return $this
     */
    public function setFormData($data): self
    {
        $this->formData = $data;
        return $this;
    }

    /**
     * Get redirect URL
     *
     * @param bool $clear
     * @return mixed
     */
    public function getRedirectUrl($clear = false)
    {
        return $this->redirectUrl;
    }

    /**
     * Set redirect URL
     *
     * @param mixed $url
     * @return $this
     */
    public function setRedirectUrl($url): self
    {
        $this->redirectUrl = $url;
        return $this;
    }

    /**
     * Get rating data
     *
     * @return mixed
     */
    public function getRatingData()
    {
        return null;
    }

    /**
     * Set rating data
     *
     * @param mixed $data
     * @return $this
     */
    public function setRatingData($data)
    {
        return $this;
    }
}
