<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes;

/**
 * Stand-in for a core class (e.g. AbstractModel, AbstractBlock) whose constructor calls overridable methods
 */
class CoreParentWithHook
{
    /**
     * @var array
     */
    protected array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
        $this->_construct();
        $this->_initSelect();
    }

    protected function _construct(): void
    {
    }

    protected function _initSelect(): void
    {
    }
}
