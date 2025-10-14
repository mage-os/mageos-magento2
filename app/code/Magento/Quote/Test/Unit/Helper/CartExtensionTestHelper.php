<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Api\Data\CartExtension;

/**
 * Test helper for Magento\Quote\Api\Data\CartExtension
 *
 * WHY THIS HELPER IS REQUIRED:
 * - CartExtension is dynamically generated extension attributes interface
 * - Methods like getNegotiableQuote/setNegotiableQuote are added dynamically
 * - Cannot use createPartialMock on dynamically generated methods
 * - Provides explicit implementation for testing negotiable quote functionality
 *
 * Used By:
 * - magento2b2b/app/code/Magento/NegotiableQuote/Test/Unit/Controller/Adminhtml/Quote/AddConfiguredTest.php
 */
class CartExtensionTestHelper extends CartExtension
{
    /**
     * @var mixed
     */
    private $negotiableQuote;

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
