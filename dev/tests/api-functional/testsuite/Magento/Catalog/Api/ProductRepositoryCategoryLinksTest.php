<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Api;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Test for \Magento\Catalog\Api\ProductRepositoryInterface category links
 *
 * @magentoAppIsolation enabled
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductRepositoryCategoryLinksTest extends ProductRepositoryInterfaceTest
{
    private const KEY_CATEGORY_LINKS = 'category_links';

    /**
     * Test product category links
     *
     * @magentoApiDataFixture Magento/Catalog/_files/category_product.php
     */
    public function testProductCategoryLinks()
    {
        // Create simple product
        $productData = $this->getSimpleProductData();
        $productData[ProductInterface::EXTENSION_ATTRIBUTES_KEY] = [
            self::KEY_CATEGORY_LINKS => [['category_id' => 333, 'position' => 0]],
        ];
        $response = $this->saveProduct($productData);
        $this->assertEquals(
            [['category_id' => 333, 'position' => 0]],
            $response[ProductInterface::EXTENSION_ATTRIBUTES_KEY][self::KEY_CATEGORY_LINKS]
        );
        $response = $this->getProduct($productData[ProductInterface::SKU]);
        $this->assertArrayHasKey(ProductInterface::EXTENSION_ATTRIBUTES_KEY, $response);
        $extensionAttributes = $response[ProductInterface::EXTENSION_ATTRIBUTES_KEY];
        $this->assertArrayHasKey(self::KEY_CATEGORY_LINKS, $extensionAttributes);
        $this->assertEquals([['category_id' => 333, 'position' => 0]], $extensionAttributes[self::KEY_CATEGORY_LINKS]);
    }

    /**
     * Test update product category without categories
     *
     * @magentoApiDataFixture Magento/Catalog/_files/category_product.php
     */
    public function testUpdateProductCategoryLinksNullOrNotExists()
    {
        $response = $this->getProduct('simple333');
        // update product without category_link or category_link is null
        $response[ProductInterface::EXTENSION_ATTRIBUTES_KEY][self::KEY_CATEGORY_LINKS] = null;
        $response = $this->updateProduct($response);
        $this->assertEquals(
            [['category_id' => 333, 'position' => 0]],
            $response[ProductInterface::EXTENSION_ATTRIBUTES_KEY][self::KEY_CATEGORY_LINKS]
        );
        unset($response[ProductInterface::EXTENSION_ATTRIBUTES_KEY][self::KEY_CATEGORY_LINKS]);
        $response = $this->updateProduct($response);
        $this->assertEquals(
            [['category_id' => 333, 'position' => 0]],
            $response[ProductInterface::EXTENSION_ATTRIBUTES_KEY][self::KEY_CATEGORY_LINKS]
        );
    }

    /**
     * Test update product category links position
     *
     * @magentoApiDataFixture Magento/Catalog/_files/category_product.php
     */
    public function testUpdateProductCategoryLinksPosition()
    {
        $response = $this->getProduct('simple333');
        // update category_link position
        $response[ProductInterface::EXTENSION_ATTRIBUTES_KEY][self::KEY_CATEGORY_LINKS] = [
            ['category_id' => 333, 'position' => 10],
        ];
        $response = $this->updateProduct($response);
        $this->assertEquals(
            [['category_id' => 333, 'position' => 10]],
            $response[ProductInterface::EXTENSION_ATTRIBUTES_KEY][self::KEY_CATEGORY_LINKS]
        );
    }

    /**
     * Test update product category links unassing
     *
     * @magentoApiDataFixture Magento/Catalog/_files/category_product.php
     */
    public function testUpdateProductCategoryLinksUnassign()
    {
        $response = $this->getProduct('simple333');
        // unassign category_links from product
        $response[ProductInterface::EXTENSION_ATTRIBUTES_KEY][self::KEY_CATEGORY_LINKS] = [];
        $response = $this->updateProduct($response);
        $this->assertArrayNotHasKey(
            self::KEY_CATEGORY_LINKS,
            $response[ProductInterface::EXTENSION_ATTRIBUTES_KEY]
        );
    }
}
