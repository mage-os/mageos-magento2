<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\ObjectManager\Attribute;

use Attribute;

/**
 * Marker attribute that excludes a class from PHP 8.4 lazy-ghost construction by the compiled
 * DI factory. Use when a constructor has side effects or invariants that are incompatible with
 * deferred initialization. The attribute is detected by the compile-time NonLazyTypes scan and
 * baked into the deny-list.
 *
 * Cross-compatible: PHP does not autoload attribute target classes when a marked class loads,
 * so referencing #[\Magento\Framework\ObjectManager\Attribute\NonLazy] is a silent no-op on
 * environments without the lazy-loading code (e.g. upstream Magento). One module distribution
 * can therefore declare opt-outs for Mage-OS without breaking on stock Magento.
 *
 * @api
 */
#[Attribute(Attribute::TARGET_CLASS)]
class NonLazy
{
}
