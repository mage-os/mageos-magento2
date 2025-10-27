<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CustomerImportExport\Test\Unit\Helper;

use Magento\Framework\App\Helper\Context;

/**
 * Test helper for Context
 */
class ContextTestHelper extends Context
{
    /**
     * @var mixed
     */
    private $translateInline = null;

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get translate inline
     *
     * @return mixed
     */
    public function getTranslateInline()
    {
        return $this->translateInline;
    }

    /**
     * Set translate inline
     *
     * @param mixed $translateInline
     * @return $this
     */
    public function setTranslateInline($translateInline): self
    {
        $this->translateInline = $translateInline;
        return $this;
    }
}
