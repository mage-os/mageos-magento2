<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Vendor\LazyTypesFixture;

/**
 * Mirrors Fooman\PdfCore\Model\Tcpdf\Defaults: the constructor exists only for its define() side effect
 */
class DefinesConstants
{
    public function __construct()
    {
        define('LAZY_TYPES_FIXTURE_CONSTANT', true);
    }
}
