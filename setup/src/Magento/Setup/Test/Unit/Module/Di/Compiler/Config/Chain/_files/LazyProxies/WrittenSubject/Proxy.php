<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies\WrittenSubject;

use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies\WrittenSubject;

/**
 * Hand-written proxy that must never be substituted by a native proxy.
 */
class Proxy extends WrittenSubject
{
}
