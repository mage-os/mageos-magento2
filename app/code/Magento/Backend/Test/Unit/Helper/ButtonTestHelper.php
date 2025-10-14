<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\Block\Widget\Button;

/**
 * Test helper for Magento\Backend\Block\Widget\Button
 *
 * This helper provides custom toHtml() implementation with call counting
 * for testing button rendering behavior.
 *
 * WHY THIS HELPER IS REQUIRED:
 * - Parent Button class has complex constructor requiring Context with many dependencies
 * - Custom toHtml() logic needed: returns different values on first vs subsequent calls
 * - Call counter (htmlCount) is test-specific functionality not in parent
 * - Cannot use createPartialMock because we need stateful behavior across multiple calls
 *
 * Used By:
 * - Magento\NegotiableQuote\Test\Unit\Block\Adminhtml\Quote\View\SkuTest
 */
class ButtonTestHelper extends Button
{
    /**
     * @var int
     */
    private $htmlCount = 0;

    /**
     * Constructor
     *
     * Skip parent constructor to avoid Context dependency
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Render block HTML with call tracking
     *
     * Returns different HTML on first call vs subsequent calls
     *
     * @return string
     */
    public function toHtml()
    {
        $this->htmlCount++;
        return $this->htmlCount === 1 ? 'block_html_' : 'another_block_html';
    }
}

