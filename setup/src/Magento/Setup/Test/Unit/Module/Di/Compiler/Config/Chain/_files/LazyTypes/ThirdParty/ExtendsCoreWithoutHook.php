<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Vendor\LazyTypesFixture;

use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\CoreParentWithHook;

class ExtendsCoreWithoutHook extends CoreParentWithHook
{
    /**
     * @var mixed
     */
    private mixed $extra;

    public function __construct(mixed $extra = null, array $data = [])
    {
        $this->extra = $extra;
        parent::__construct($data);
    }
}
