<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Model\Stock;

use Magento\Catalog\Test\Fixture\Product as ProductFixture;
use Magento\CatalogInventory\Api\StockItemCriteriaInterfaceFactory;
use Magento\CatalogInventory\Api\StockItemRepositoryInterface;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DataFixtureStorageManager;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

class StockItemRepositoryTest extends TestCase
{
    #[
        DataFixture(ProductFixture::class, as: 'p1'),
        DataFixture(ProductFixture::class, as: 'p2'),
        DataFixture(ProductFixture::class, as: 'p3'),
    ]
    public function testGetListFiltersByAllProductIds(): void
    {
        $productIds = $this->getFixtureProductIds();

        $this->assertSame($productIds, $this->getListProductIds($productIds));
    }

    #[
        DataFixture(ProductFixture::class, as: 'p1'),
        DataFixture(ProductFixture::class, as: 'p2'),
        DataFixture(ProductFixture::class, as: 'p3'),
    ]
    public function testGetListFiltersByNestedProductIds(): void
    {
        $productIds = $this->getFixtureProductIds();

        // Same shape as Grouped/Configurable getChildrenIds()
        $this->assertSame($productIds, $this->getListProductIds([3 => array_combine($productIds, $productIds)]));
    }

    public function testGetListWithEmptyNestedProductIdsReturnsNothing(): void
    {
        $this->assertSame([], $this->getListProductIds([3 => []]));
    }

    /**
     * @return int[]
     */
    private function getFixtureProductIds(): array
    {
        $fixtures = DataFixtureStorageManager::getStorage();

        return [
            (int)$fixtures->get('p1')->getId(),
            (int)$fixtures->get('p2')->getId(),
            (int)$fixtures->get('p3')->getId(),
        ];
    }

    /**
     * @param mixed $productsFilter
     * @return int[]
     */
    private function getListProductIds($productsFilter): array
    {
        $objectManager = Bootstrap::getObjectManager();
        $criteria = $objectManager->get(StockItemCriteriaInterfaceFactory::class)->create();
        $criteria->setProductsFilter($productsFilter);
        $items = $objectManager->get(StockItemRepositoryInterface::class)->getList($criteria)->getItems();

        $actualIds = array_map(static fn ($item) => (int)$item->getProductId(), array_values($items));
        sort($actualIds);

        return $actualIds;
    }
}
