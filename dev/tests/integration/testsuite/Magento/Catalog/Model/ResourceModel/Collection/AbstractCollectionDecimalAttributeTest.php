<?php

/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\Catalog\Model\ResourceModel\Collection;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Test\Fixture\ProductWithStoreScopedDecimalAttribute;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Integration test for loading store-scoped decimal attributes alongside a globally-scoped price attribute.
 *
 * Validates the fix for issue #40218: store-scoped decimal attributes were loaded with the wrong
 * store ID when the price attribute (globally scoped) was also selected. The root cause was a loop
 * in _getLoadAttributesSelect() that overrode $storeId to getDefaultStoreId() for all attributes
 * whenever price was present, causing store-scoped attributes to return the admin/default value
 * instead of the requested store view's value.
 *
 * @magentoAppArea frontend
 * @magentoDbIsolation disabled
 * @magentoAppIsolation enabled
 */
class AbstractCollectionDecimalAttributeTest extends TestCase
{
    /**
     * @var Collection
     */
    private Collection $collection;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->collection = Bootstrap::getObjectManager()->create(Collection::class);
    }

    /**
     * Verify that a store-scoped decimal attribute returns the store view value when price is also selected.
     *
     * Before the fix, including price (global scope) in the attribute select caused all attributes to
     * be loaded from the default store, so the store-specific value (200.00) was never returned.
     */
    #[DataFixture(ProductWithStoreScopedDecimalAttribute::class)]
    public function testStoreScopedDecimalAttributeIsNotOverriddenByGlobalPrice(): void
    {
        $this->collection->setStoreId(1);
        $this->collection->addAttributeToSelect('price');
        $this->collection->addAttributeToSelect('decimal_store_scoped');
        $this->collection->addFieldToFilter('sku', 'simple_with_store_scoped_decimal');
        $this->collection->load();

        $product = $this->collection->getFirstItem();

        $this->assertNotEmpty($product->getId(), 'Product should be found in collection');
        $this->assertEquals(50.00, (float)$product->getPrice(), 'Price (global scope) should be correct');

        // Critical assertion: store view 1 value (200.00), not the admin/default value (100.00).
        // Before the fix, the loop that checked for global price overrode $storeId to 0 for all
        // attributes, so this would return 100.00 instead.
        $this->assertEquals(
            200.00,
            (float)$product->getData('decimal_store_scoped'),
            'Store-scoped decimal attribute must return the store view value, not the default store value'
        );
    }

    /**
     * Verify that a store-scoped decimal attribute without price selected also works correctly.
     */
    #[DataFixture(ProductWithStoreScopedDecimalAttribute::class)]
    public function testStoreScopedDecimalAttributeWithoutPrice(): void
    {
        $this->collection->setStoreId(1);
        $this->collection->addAttributeToSelect('decimal_store_scoped');
        $this->collection->addFieldToFilter('sku', 'simple_with_store_scoped_decimal');
        $this->collection->load();

        $product = $this->collection->getFirstItem();

        $this->assertNotEmpty($product->getId(), 'Product should be found in collection');
        $this->assertEquals(
            200.00,
            (float)$product->getData('decimal_store_scoped'),
            'Store-scoped decimal attribute should return store view value when loaded without price'
        );
    }
}
