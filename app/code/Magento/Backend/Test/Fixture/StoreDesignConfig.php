<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Fixture;

use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\TestFramework\Fixture\RevertibleDataFixtureInterface;

/**
 * Website, store group, and store view for store-save / design configuration integration tests.
 */
class StoreDesignConfig implements RevertibleDataFixtureInterface
{
    public const WEBSITE_CODE = StoreDesignHierarchy::WEBSITE_CODE;

    public const GROUP_CODE = StoreDesignHierarchy::GROUP_CODE;

    public const STORE_CODE = StoreDesignHierarchy::STORE_CODE;

    /**
     * @param StoreDesignHierarchy $hierarchy
     * @param Registry $registry
     */
    public function __construct(
        private readonly StoreDesignHierarchy $hierarchy,
        private readonly Registry $registry,
    ) {
    }

    /**
     * @inheritdoc
     */
    public function apply(array $data = []): ?DataObject
    {
        $this->hierarchy->apply();

        return new DataObject();
    }

    /**
     * @inheritdoc
     */
    public function revert(DataObject $data): void
    {
        $this->hierarchy->revert($this->registry);
    }
}
