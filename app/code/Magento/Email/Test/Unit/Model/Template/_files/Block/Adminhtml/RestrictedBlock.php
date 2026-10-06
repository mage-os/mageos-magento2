<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Email\Test\Unit\Model\Template\_files\Block\Adminhtml;

use Magento\Framework\View\Element\BlockInterface;

/**
 * Fixture whose class name sits in a restricted namespace, standing in for a backend block that a
 * DI preference or virtual type resolved from an innocuous-looking {{block class="..."}} value.
 */
class RestrictedBlock implements BlockInterface
{
    /**
     * @var bool
     */
    public $rendered = false;

    /**
     * @inheritdoc
     */
    public function toHtml()
    {
        $this->rendered = true;

        return 'RESTRICTED';
    }
}
