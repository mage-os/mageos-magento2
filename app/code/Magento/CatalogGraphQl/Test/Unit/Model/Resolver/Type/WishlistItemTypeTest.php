<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CatalogGraphQl\Test\Unit\Model\Resolver\Type;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogGraphQl\Model\Resolver\Type\WishlistItemType;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;

class WishlistItemTypeTest extends TestCase
{
    /**
     * @var WishlistItemType
     */
    private $resolver;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->resolver = new WishlistItemType(['simple' => 'SimpleWishlistItem']);
    }

    /**
     * @return void
     */
    public function testResolvesSupportedProductType(): void
    {
        $product = $this->createStub(ProductInterface::class);
        $product->method('getTypeId')->willReturn('simple');

        $this->assertSame('SimpleWishlistItem', $this->resolver->resolveType(['model' => $product]));
    }

    /**
     * @return void
     */
    public function testThrowsForUnsupportedProductType(): void
    {
        $product = $this->createStub(ProductInterface::class);
        $product->method('getTypeId')->willReturn('unknown');

        $this->expectException(LocalizedException::class);
        $this->resolver->resolveType(['model' => $product]);
    }

    /**
     * @return void
     */
    public function testThrowsWhenModelIsNotAProduct(): void
    {
        $this->expectException(LocalizedException::class);
        $this->resolver->resolveType(['model' => new DataObject()]);
    }
}
