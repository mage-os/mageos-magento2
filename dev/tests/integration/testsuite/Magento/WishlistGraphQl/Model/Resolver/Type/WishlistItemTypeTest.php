<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\WishlistGraphQl\Model\Resolver\Type;

use Magento\Catalog\Model\Product;
use Magento\CatalogGraphQl\Model\Resolver\Type\WishlistItemType as CatalogWishlistItemType;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Checks the WishlistItemInterface type resolver that the schema names in Magento_CatalogGraphQl.
 */
class WishlistItemTypeTest extends TestCase
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    /**
     * Each product type module registers its wish list item type on the CatalogGraphQl resolver
     *
     * @return void
     */
    #[AppArea('graphql')]
    public function testResolvesTypesRegisteredByProductTypeModules(): void
    {
        $resolver = $this->objectManager->create(CatalogWishlistItemType::class);
        $expectedTypes = [
            'simple' => 'SimpleWishlistItem',
            'virtual' => 'VirtualWishlistItem',
            'bundle' => 'BundleWishlistItem',
            'configurable' => 'ConfigurableWishlistItem',
            'downloadable' => 'DownloadableWishlistItem',
            'grouped' => 'GroupedProductWishlistItem',
        ];

        foreach ($expectedTypes as $productType => $expectedType) {
            $this->assertSame(
                $expectedType,
                $resolver->resolveType(['model' => $this->createProduct($productType)]),
                $productType
            );
        }
    }

    /**
     * Types registered on the deprecated WishlistGraphQl resolver are still resolved
     *
     * @return void
     */
    #[AppArea('graphql')]
    public function testResolvesTypesRegisteredOnDeprecatedResolver(): void
    {
        $this->objectManager->configure([
            WishlistItemType::class => [
                'arguments' => [
                    'supportedTypes' => ['custom_type' => 'CustomWishlistItem'],
                ],
            ],
        ]);
        $resolver = $this->objectManager->create(CatalogWishlistItemType::class);

        $this->assertInstanceOf(WishlistItemType::class, $resolver);
        $this->assertSame(
            'CustomWishlistItem',
            $resolver->resolveType(['model' => $this->createProduct('custom_type')])
        );
        $this->assertSame('SimpleWishlistItem', $resolver->resolveType(['model' => $this->createProduct('simple')]));
    }

    /**
     * Create an unsaved product of the given type
     *
     * @param string $productType
     * @return Product
     */
    private function createProduct(string $productType): Product
    {
        return $this->objectManager->create(Product::class)->setTypeId($productType);
    }
}
