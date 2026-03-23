<?php

/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

// phpcs:disable PSR2.Methods.MethodDeclaration.Underscore
namespace Magento\Catalog\Test\Unit\Model\ResourceModel\Collection\Stub;

use Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection;

/**
 * Concrete subclass of AbstractCollection for unit testing.
 * Overrides _construct() to initialize the entity via _init(),
 * mirroring how real subclasses (e.g. Product\Collection) set up the entity.
 */
class ConcreteCollection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected function _construct(): void
    {
        $this->_init(
            \Magento\Framework\DataObject::class,
            \Magento\Eav\Model\Entity\AbstractEntity::class
        );
    }
}
