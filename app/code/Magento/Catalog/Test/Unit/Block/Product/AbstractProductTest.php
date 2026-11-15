<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Block\Product;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\Image;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Catalog\Block\Product\View\Type\Simple;
use Magento\Catalog\Helper\Wishlist\AddToWishlistInterface;
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Stdlib\ArrayUtils;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\Store;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
#[CoversClass(AbstractProduct::class)]
class AbstractProductTest extends TestCase
{
    /**
     * @var Simple
     */
    protected $block;

    /**
     * @var Context|MockObject
     */
    protected $productContextMock;

    /**
     * @var LayoutInterface|MockObject
     */
    protected $layoutMock;

    /**
     * @var StockRegistryInterface|MockObject
     */
    protected $stockRegistryMock;

    /**
     * @var ImageBuilder|MockObject
     */
    protected $imageBuilder;

    /**
     * @var AddToWishlistInterface|MockObject
     */
    private $addToWishlistMock;

    /**
     * Set up mocks and tested class
     * Child class is used as the tested class is declared abstract
     */
    protected function setUp(): void
    {
        $this->productContextMock = $this->createPartialMock(
            Context::class,
            ['getLayout', 'getStockRegistry', 'getImageBuilder', 'getWishlistHelper']
        );
        $this->addToWishlistMock = $this->createMock(AddToWishlistInterface::class);
        $this->productContextMock->method('getWishlistHelper')
            ->willReturn($this->addToWishlistMock);
        $arrayUtilsMock = $this->createMock(ArrayUtils::class);
        $this->layoutMock = $this->createPartialMock(Layout::class, ['getBlock']);
        $this->stockRegistryMock = $this->createMock(StockRegistryInterface::class);

        $this->imageBuilder = $this->createPartialMock(ImageBuilder::class, ['create']);

        $this->productContextMock->expects($this->once())
            ->method('getStockRegistry')
            ->willReturn($this->stockRegistryMock);
        $this->productContextMock->expects($this->once())
            ->method('getLayout')
            ->willReturn($this->layoutMock);
        $this->productContextMock->expects($this->once())
            ->method('getImageBuilder')
            ->willReturn($this->imageBuilder);

        $this->block = new Simple(
            $this->productContextMock,
            $arrayUtilsMock
        );
    }

    /**
     * Adding to the wish list is allowed when the wish list implementation allows it
     *
     * @return void
     */
    public function testIsAddToWishlistAllowed(): void
    {
        $this->addToWishlistMock->expects($this->once())
            ->method('isAllow')
            ->willReturn(true);

        $this->assertTrue($this->block->isAddToWishlistAllowed());
    }

    /**
     * Adding to the wish list is not allowed when the wish list implementation refuses it
     *
     * @return void
     */
    public function testIsAddToWishlistNotAllowed(): void
    {
        $this->addToWishlistMock->expects($this->once())
            ->method('isAllow')
            ->willReturn(false);

        $this->assertFalse($this->block->isAddToWishlistAllowed());
    }

    /**
     * The add to wish list post data comes from the wish list implementation
     *
     * @return void
     */
    public function testGetAddToWishlistParams(): void
    {
        $product = $this->createMock(Product::class);
        $this->addToWishlistMock->expects($this->once())
            ->method('getAddParams')
            ->with($product)
            ->willReturn('{"action":"wishlist\\/index\\/add"}');

        $this->assertSame('{"action":"wishlist\\/index\\/add"}', $this->block->getAddToWishlistParams($product));
    }

    /**
     * Test for method getProductPrice
     */
    public function testGetProductPrice()
    {
        $expectedPriceHtml = '<html>Expected Price html with price $30</html>';
        $priceRenderBlock = $this->createPartialMock(Render::class, ['render']);
        $product = $this->createMock(Product::class);

        $this->layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('product.price.render.default')
            ->willReturn($priceRenderBlock);
        $priceRenderBlock->expects($this->once())
            ->method('render')
            ->willReturn($expectedPriceHtml);

        $this->assertEquals($expectedPriceHtml, $this->block->getProductPrice($product));
    }

    /**
     * Test testGetProductPriceHtml
     */
    public function testGetProductPriceHtml()
    {
        $expectedPriceHtml = '<html>Expected Price html with price $30</html>';
        $priceRenderBlock = $this->createPartialMock(Render::class, ['render']);
        $product = $this->createMock(Product::class);

        $this->layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('product.price.render.default')
            ->willReturn($priceRenderBlock);

        $priceRenderBlock->expects($this->once())
            ->method('render')
            ->willReturn($expectedPriceHtml);

        $this->assertEquals(
            $expectedPriceHtml,
            $this->block->getProductPriceHtml($product, 'price_code', 'zone_code')
        );
    }

    /**
     * Run test getMinimalQty method
     *
     * @param int $minSale
     * @param int|null $result
     * @return void
     */
    #[DataProvider('dataProviderGetMinimalQty')]
    public function testGetMinimalQty($minSale, $result)
    {
        $id = 10;
        $websiteId = 99;

        $productMock = $this->createPartialMock(Product::class, ['getId', 'getStore']);
        $storeMock = $this->createPartialMock(Store::class, ['getWebsiteId']);
        $stockItemMock = $this->createMock(StockItemInterface::class);

        $this->stockRegistryMock->expects($this->once())
            ->method('getStockItem')
            ->with($id, $websiteId)
            ->willReturn($stockItemMock);
        $productMock->expects($this->once())
            ->method('getId')
            ->willReturn($id);
        $productMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);
        $storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($websiteId);
        $stockItemMock->expects($this->once())
            ->method('getMinSaleQty')
            ->willReturn($minSale);

        /** @var Product|MockObject $productMock */
        $this->assertEquals($result, $this->block->getMinimalQty($productMock));
    }

    /**
     * Data for getMinimalQty method
     *
     * @return array
     */
    public static function dataProviderGetMinimalQty()
    {
        return [
            [
                'minSale' => 10,
                'result' => 10,
            ],
            [
                'minSale' => 0,
                'result' => null
            ]
        ];
    }

    public function testGetImage()
    {
        $imageId = 'test_image_id';
        $attributes = [];
        $productMock = $this->createMock(Product::class);
        $imageMock = $this->createMock(Image::class);
        $this->imageBuilder->expects(static::once())
            ->method('create')
            ->willReturn($imageMock);

        $image = $this->block->getImage($productMock, $imageId, $attributes);

        static::assertInstanceOf(Image::class, $image);
    }
}
