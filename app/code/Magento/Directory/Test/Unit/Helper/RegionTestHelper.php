<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Directory\Test\Unit\Helper;

use Magento\Directory\Model\Region;

/**
 * Test helper for Region with custom methods
 */
class RegionTestHelper extends Region
{
    /**
     * @var string|null
     */
    private $code = null;

    /**
     * @var string|null
     */
    private $defaultName = null;

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get code (custom method for tests)
     *
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param string|null $code
     * @return $this
     */
    public function setTestCode(?string $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get default name (custom method for tests)
     *
     * @return string|null
     */
    public function getDefaultName(): ?string
    {
        return $this->defaultName;
    }

    /**
     * Set default name
     *
     * @param string|null $name
     * @return $this
     */
    public function setTestDefaultName(?string $name): self
    {
        $this->defaultName = $name;
        return $this;
    }
}
