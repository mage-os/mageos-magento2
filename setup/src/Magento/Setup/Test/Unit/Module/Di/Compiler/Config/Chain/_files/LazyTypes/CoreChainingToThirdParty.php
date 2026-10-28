<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes;

/**
 * Stand-in for a core class that extends a third-party one and delegates to its constructor
 * (e.g. Framework\DB\Adapter\Pdo\Mysql over Zend_Db_Adapter_Abstract)
 */
class CoreChainingToThirdParty extends \Vendor\LazyTypesFixture\SideEffectParent
{
    /**
     * @var mixed
     */
    private mixed $extra;

    public function __construct(mixed $value, mixed $extra = null)
    {
        $this->extra = $extra;
        parent::__construct($value);
    }
}
