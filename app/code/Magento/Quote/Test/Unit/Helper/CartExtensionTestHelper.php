<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Api\Data\CartExtensionInterface;

/**
 * Test helper that implements CartExtensionInterface
 * 
 * Provides getNegotiableQuote/setNegotiableQuote methods for testing
 */
class CartExtensionTestHelper implements CartExtensionInterface
{
    /**
     * @var mixed
     */
    private $negotiableQuote;

    /**
     * Constructor
     *
     * @param mixed $negotiableQuote
     */
    public function __construct($negotiableQuote = null)
    {
        $this->negotiableQuote = $negotiableQuote;
    }

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

