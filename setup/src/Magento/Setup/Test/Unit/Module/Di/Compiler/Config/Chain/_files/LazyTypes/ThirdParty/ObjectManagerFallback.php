<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Vendor\LazyTypesFixture;

use Magento\Framework\App\ObjectManager;

class ObjectManagerFallback
{
    /**
     * @var PlainThirdParty|null
     */
    private ?PlainThirdParty $plain;

    /**
     * @var NoConstructor|null
     */
    private ?NoConstructor $other;

    public function __construct(?PlainThirdParty $plain = null, ?NoConstructor $other = null)
    {
        $this->plain = $plain ?? ObjectManager::getInstance()->get(PlainThirdParty::class);
        $this->other = $other ?? \Magento\Framework\App\ObjectManager::getInstance()->create(NoConstructor::class);
    }
}
