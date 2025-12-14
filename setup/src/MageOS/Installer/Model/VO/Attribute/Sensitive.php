<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\VO\Attribute;

use Attribute;

/**
 * Marks a property as containing sensitive data (passwords, tokens, etc.)
 * Used for serialization to exclude sensitive fields from saved configuration
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Sensitive
{
}
