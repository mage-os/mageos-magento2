<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\ObjectManager\Attribute;

use Attribute;

/**
 * Marker attribute that opts a class outside the Magento\ and MageOS\ namespaces into PHP 8.4
 * lazy-ghost construction by the compiled DI factory. Third-party classes are constructed eagerly
 * by default, because a deferred constructor silently skips side effects that callers may rely on.
 * Only add this to classes whose constructors merely store their dependencies. The class must
 * still pass the compile-time LazyTypes eligibility checks, and #[NonLazy] takes precedence.
 *
 * Cross-compatible: PHP does not autoload attribute target classes when a marked class loads,
 * so referencing #[\Magento\Framework\ObjectManager\Attribute\Lazy] is a silent no-op on
 * environments without the lazy-loading code (e.g. upstream Magento).
 *
 * @api
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class Lazy
{
}
