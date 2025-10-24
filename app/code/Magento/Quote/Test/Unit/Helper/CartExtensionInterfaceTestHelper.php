<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Api\Data\CartExtensionInterface;

/**
 * Test helper for CartExtensionInterface
 *
 * This helper provides the custom getNegotiableQuote() method that is not available
 * in the auto-generated CartExtensionInterface. All other interface methods return null
 * or default values as they are not used in the tests.
 */
class CartExtensionInterfaceTestHelper implements CartExtensionInterface
{
    /**
     * @var mixed
     */
    private $negotiableQuote = null;

    /**
     * Get negotiable quote
     *
     * @return mixed
     */
    public function getNegotiableQuote()
    {
        return $this->negotiableQuote;
    }

    /**
     * Set negotiable quote
     *
     * @param mixed $negotiableQuote
     * @return $this
     */
    public function setNegotiableQuote($negotiableQuote)
    {
        $this->negotiableQuote = $negotiableQuote;
        return $this;
    }
}
