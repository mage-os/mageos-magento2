<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Pricing\Price;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Test\Fixture\Product as ProductFixture;
use Magento\Framework\Pricing\PriceInfo\Base as PriceInfo;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DbIsolation;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Integration test for SpecialPrice pricing class
 */
#[
    DbIsolation(true)
]
class SpecialPriceTest extends TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->productCollectionFactory = $this->objectManager->get(CollectionFactory::class);
    }

    /**
     * Test that special price is loaded from database when attribute is not in product collection
     *
     * This tests the fix for the issue where special_price doesn't display when
     * the attribute has "Used in Product Listing = No" setting
     *
     * @return void
     */
    #[
        DataFixture(
            ProductFixture::class,
            ['sku' => 'simple-special-price', 'price' => 100, 'special_price' => 90]
        ),
    ]
    public function testGetSpecialPriceLoadsFromDatabaseWhenNotInCollection(): void
    {

        // Load product in a collection WITHOUT special_price attribute
        /** @var Collection $collection */
        $collection = $this->productCollectionFactory->create();
        $collection->addFieldToFilter('sku', 'simple-special-price')
            ->addAttributeToSelect(['name', 'price']) // Intentionally exclude special_price
            ->load();

        /** @var Product $product */
        $product = $collection->getFirstItem();

        // Verify special_price is not loaded in the product data
        $this->assertFalse(
            $product->hasData('special_price'),
            'Special price should not be loaded in product collection'
        );

        // Get the SpecialPrice pricing object
        /** @var PriceInfo $priceInfo */
        $priceInfo = $product->getPriceInfo();
        /** @var SpecialPrice $specialPriceObject */
        $specialPriceObject = $priceInfo->getPrice(SpecialPrice::PRICE_CODE);

        // Call getSpecialPrice() - it should load from database
        $specialPrice = $specialPriceObject->getSpecialPrice();

        // Assert special price was loaded correctly
        $this->assertNotNull($specialPrice, 'Special price should be loaded from database');
        $this->assertEquals(90, $specialPrice, 'Special price should match the saved value');

        // Verify the attribute is now cached in product data
        $this->assertTrue(
            $product->hasData('special_price'),
            'Special price should be cached in product after loading'
        );
    }
}
