<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\View\Element\Template;

/**
 * Test helper for Template block with custom methods
 */
class TemplateTestHelper extends Template
{
    /**
     * @var string|null
     */
    private $email = null;

    /**
     * @var string|null
     */
    private $loginUrl = null;

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set email (custom method for tests)
     *
     * @param string|null $email
     * @return $this
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get email
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set login URL (custom method for tests)
     *
     * @param string|null $url
     * @return $this
     */
    public function setLoginUrl(?string $url): self
    {
        $this->loginUrl = $url;
        return $this;
    }

    /**
     * Get login URL
     *
     * @return string|null
     */
    public function getLoginUrl(): ?string
    {
        return $this->loginUrl;
    }
}
