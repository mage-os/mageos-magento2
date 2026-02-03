<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Controller\Product;

use Magento\Catalog\Test\Fixture\AttributeSet as AttributeSetFixture;
use Magento\Catalog\Test\Fixture\Product as ProductFixture;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorage;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Helper\LayoutHandles;
use PHPUnit\Framework\TestCase;

/**
 * Integration test to verify layout handles are generated based on product attribute sets.
 *
 * Tests verify that:
 * - Different attribute sets generate different layout handles
 * - Handle follows naming convention: catalog_product_view_attribute_set_{id}
 * - Products with same attribute set get same layout handle
 *
 * Test Coverage: AC-14322
 *
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 * @magentoDbIsolation disabled
 */
class AttributeSetLayoutHandleTest extends TestCase
{
    /**
     * @var DataFixtureStorage
     */
    private DataFixtureStorage $fixtures;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->fixtures = $objectManager->get(DataFixtureStorageManager::class)->getStorage();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        // Reset isolation flag to ensure clean state for next test method
        LayoutHandles::resetIsolationFlag();
        parent::tearDown();
    }

    /**
     * Test that product with unique attribute set gets its own layout handle
     *
     * @return void
     */
    #[
        DataFixture(AttributeSetFixture::class, ['attribute_set_name' => 'Custom Set A'], as: 'attribute_set_a'),
        DataFixture(
            ProductFixture::class,
            ['sku' => 'product-set-a-1', 'attribute_set_id' => '$attribute_set_a.attribute_set_id$'],
            as: 'product_a'
        )
    ]
    public function testProductWithUniqueAttributeSetGetsItsOwnHandle(): void
    {
        $product = $this->fixtures->get('product_a');
        $handles = LayoutHandles::getProductLayoutHandles($product);

        // Verify product has its own attribute set handle
        LayoutHandles::assertHasAttributeSetHandle($product, $handles);

        // Verify product also gets product type handle (dynamic based on product type)
        LayoutHandles::assertHasProductTypeHandle($product, $handles);

        // Verify handles contain the default base handle
        $this->assertContains(
            'default',
            $handles,
            'Product layout should include default handle'
        );
    }

    /**
     * Test that two products with same attribute set get the same layout handle
     *
     * @return void
     */
    #[
        DataFixture(AttributeSetFixture::class, ['attribute_set_name' => 'Custom Set B'], as: 'attribute_set_b'),
        DataFixture(
            ProductFixture::class,
            ['sku' => 'product-set-b-1', 'attribute_set_id' => '$attribute_set_b.attribute_set_id$'],
            as: 'product_b1'
        ),
        DataFixture(
            ProductFixture::class,
            ['sku' => 'product-set-b-2', 'attribute_set_id' => '$attribute_set_b.attribute_set_id$'],
            as: 'product_b2'
        )
    ]
    public function testProductsWithSameAttributeSetGetSameHandle(): void
    {
        $product1 = $this->fixtures->get('product_b1');
        $product2 = $this->fixtures->get('product_b2');

        // Verify both products have same attribute set
        $this->assertEquals(
            $product1->getAttributeSetId(),
            $product2->getAttributeSetId(),
            'Both products should have the same attribute set'
        );

        // Get handles for both products
        $handles1 = LayoutHandles::getProductLayoutHandles($product1);
        $handles2 = LayoutHandles::getProductLayoutHandles($product2);

        // Both should have their attribute set handle
        LayoutHandles::assertHasAttributeSetHandle($product1, $handles1);
        LayoutHandles::assertHasAttributeSetHandle($product2, $handles2);

        // Verify both have THE SAME attribute set handle
        $expectedHandleFull = LayoutHandles::getAttributeSetHandleName($product1);
        $expectedHandleShort = LayoutHandles::getAttributeSetHandleShorthand($product1);

        $product1HasHandle = in_array($expectedHandleFull, $handles1, true)
            || in_array($expectedHandleShort, $handles1, true);
        $product2HasHandle = in_array($expectedHandleFull, $handles2, true)
            || in_array($expectedHandleShort, $handles2, true);

        $this->assertTrue($product1HasHandle, 'Product 1 should have the attribute set handle');
        $this->assertTrue($product2HasHandle, 'Product 2 should have the SAME attribute set handle');

        // Verify both get product type handle
        LayoutHandles::assertHasProductTypeHandle($product1, $handles1);
        LayoutHandles::assertHasProductTypeHandle($product2, $handles2);
    }
}
