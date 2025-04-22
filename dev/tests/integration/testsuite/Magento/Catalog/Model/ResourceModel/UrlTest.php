<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Catalog\Model\ResourceModel;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Catalog Url Resource Model.
 */
class UrlTest extends TestCase
{
    private Url $urlResource;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->urlResource = $objectManager->create(Url::class);
    }

    /**
     * Test that scope is respected for the is_active flag.
     *
     * @return void
     * @magentoDataFixture Magento/Catalog/_files/categories_disabled_different_scopes.php
     */
    public function testIsActiveScope(): void
    {
        $categories = $this->urlResource->getCategories([3, 4, 5], 1);
        $this->assertTrue((bool) $categories[3]->getIsActive());
        $this->assertFalse((bool) $categories[4]->getIsActive());
        $this->assertFalse((bool) $categories[5]->getIsActive());
    }
}
