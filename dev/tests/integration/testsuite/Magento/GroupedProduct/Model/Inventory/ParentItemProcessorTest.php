<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\GroupedProduct\Model\Inventory;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Catalog\Test\Fixture\Product as ProductFixture;
use Magento\Framework\App\ResourceConnection;
use Magento\GroupedProduct\Test\Fixture\Product as GroupedProductFixture;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use PHPUnit\Framework\TestCase;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\ObjectManagerInterface;

/**
 * Test stock status parent product
 */
class ParentItemProcessorTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    /**
     * Test stock status parent product if children are out of stock
     *
     * @magentoDataFixture Magento/GroupedProduct/_files/product_grouped_with_simple_out_of_stock.php
     *
     * @return void
     */
    public function testOutOfStockParentProduct(): void
    {
        $productRepository = $this->objectManager->create(ProductRepositoryInterface::class);
        /** @var Product $product */
        $product = $productRepository->get('simple_100000001');
        $product->setStockData(['qty' => 0, 'is_in_stock' => 0]);
        $productRepository->save($product);
        /** @var StockItemRepository $stockItemRepository */
        $stockItemRepository = $this->objectManager->create(StockItemRepository::class);
        /** @var StockRegistryInterface $stockRegistry */
        $stockRegistry = $this->objectManager->create(StockRegistryInterface::class);
        $stockItem = $stockRegistry->getStockItemBySku('grouped');
        $stockItem = $stockItemRepository->get($stockItem->getItemId());

        $this->assertEquals(false, $stockItem->getIsInStock());
    }

    /**
     * Grouped product whose only child has required options has no eligible children
     *
     * @return void
     */
    #[
        DataFixture(ProductFixture::class, as: 'child'),
        DataFixture(GroupedProductFixture::class, ['product_links' => ['$child$']], 'grouped'),
    ]
    public function testParentWithoutEligibleChildrenGoesOutOfStock(): void
    {
        $fixtures = DataFixtureStorageManager::getStorage();
        $childId = (int)$fixtures->get('child')->getId();
        $resource = $this->objectManager->get(ResourceConnection::class);
        $resource->getConnection()->update(
            $resource->getTableName('catalog_product_entity'),
            ['required_options' => 1],
            ['entity_id = ?' => $childId]
        );

        $this->objectManager->get(ChangeParentStockStatus::class)->execute($childId);

        $stockItem = $this->objectManager->create(StockRegistryInterface::class)
            ->getStockItemBySku($fixtures->get('grouped')->getSku());
        $stockItem = $this->objectManager->create(StockItemRepository::class)->get($stockItem->getItemId());
        $this->assertFalse((bool)$stockItem->getIsInStock());
    }
}
