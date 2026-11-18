<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies;

use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\AnInterface;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\PlainClass;

/**
 * Concrete subject for an interface preference.
 */
class Subject extends PlainClass implements AnInterface
{
}
